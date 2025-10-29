<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Contracts\LogoutResponse;
use Illuminate\Routing\Pipeline;
use Laravel\Fortify\Contracts\LoginResponse;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Features;
use Laravel\Fortify\Actions\EnsureLoginIsNotThrottled;
use Laravel\Fortify\Actions\PrepareAuthenticatedSession;
use Laravel\Fortify\Actions\RedirectIfTwoFactorAuthenticatable;
use Laravel\Fortify\Actions\CanonicalizeUsername;
use Laravel\Fortify\Actions\AttemptToAuthenticate;
use App\Models\Contact;
use App\Models\Category;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;
class AuthController extends Controller
{
    public function index()
    {
        return view('/auth/login');
    }
    public function store(RegisterRequest $request)
    {
        User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
        ]);
        return redirect('/login')->with('message', 'ユーザーを登録しました');
    }
    public function login(LoginRequest $request)
    {

        return $this->loginPipeline($request)->then(function ($request) {
            return app(LoginResponse::class);
        });

        //return redirect()->intended('/admin');
        //return view('/auth/admin');
    }
    protected function loginPipeline(LoginRequest $request)
    {
        if (Fortify::$authenticateThroughCallback) {
            return (new Pipeline(app()))->send($request)->through(array_filter(
                call_user_func(Fortify::$authenticateThroughCallback, $request)
            ));
        }

        if (is_array(config('fortify.pipelines.login'))) {
            return (new Pipeline(app()))->send($request)->through(array_filter(
                config('fortify.pipelines.login')
            ));
        }

        return (new Pipeline(app()))->send($request)->through(array_filter([
            config('fortify.limiters.login') ? null : EnsureLoginIsNotThrottled::class,
            config('fortify.lowercase_usernames') ? CanonicalizeUsername::class : null,
            Features::enabled(Features::twoFactorAuthentication()) ? RedirectIfTwoFactorAuthenticatable::class : null,
            AttemptToAuthenticate::class,
            PrepareAuthenticatedSession::class,
        ]));
    }
    public function admin(Request $request)
    {
        $contacts = Contact::with('category')->paginate(7);
        $categories = Category::all();
        return view('auth/admin', compact('contacts', 'categories'));
    }

    public function search(Request $request)
    {
        $text = $request->text;
        $gender = $request->gender;
        $category_id = $request->category_id;
        $date = $request->date;
        if ($date instanceof Carbon) {
            $date = $date->format('Y-m-d');
        } elseif (is_string($date) && $date !== '') {
            // 文字列で来る場合（例: '2025-10-27 00:00:00'）はここで整形してもOK
            try {
                $date = Carbon::parse($date)->format('Y-m-d');
            } catch (\Throwable $e) {
                $date = ''; // パース不可なら空
            }
        }
        $categories = Category::all();
        $contacts = Contact::with('category')
            ->CategorySearch($request->category_id)
            ->GenderSearch($request->gender)
            ->KeywordSearch($request->text) // ← まず完全一致、なければ部分一致を検索
            ->DateSearch($request->date)
            ->paginate(7)
            ->appends($request->query());//ページネーションでページを切り替えても検索条件を引き継ぐ
        return view('auth/admin', compact('contacts', 'categories', 'text', 'gender', 'category_id', 'date'));
    }
    public function destroy(Request $request)
    {
        Contact::find($request->id)->delete();

        return redirect('/admin')->with('message', 'お問い合わせを削除しました');
    }
    public function export(Request $request): StreamedResponse
    {
        $q = Contact::query()
            ->leftJoin('categories', 'contacts.category_id', '=', 'categories.id')
            ->select([
                'contacts.id',
                'contacts.last_name',
                'contacts.first_name',
                'contacts.email',
                'contacts.tel',
                'contacts.gender',
                'contacts.address',
                'contacts.building',
                'contacts.detail',
                'categories.content as content', // ← 統一
                'contacts.created_at',
            ])
            ->CategorySearch($request->category_id)
            ->GenderSearch($request->gender)
            ->KeywordSearch($request->text)
            ->DateSearch($request->date);

        $filename = 'contacts_' . now()->format('Ymd_His') . '.csv';

        // まずはUTF-8+BOMで様子を見る（文字化けしたら SJIS-win に変更）
        $encoding = 'UTF-8'; // ← 文字化けする環境なら 'SJIS-win' に
        $headers = [
            'Content-Type' => 'text/csv; charset=' . $encoding,
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control' => 'no-store, no-cache',
        ];

        return response()->stream(function () use ($q, $encoding) {
            // 出力バッファを空に（重要）
            while (ob_get_level() > 0) {
                @ob_end_clean();
            }

            $out = fopen('php://output', 'w');

            // UTF-8でExcel互換を上げるためBOM（SJISにするなら削除）
            if ($encoding === 'UTF-8') {
                fwrite($out, chr(0xEF) . chr(0xBB) . chr(0xBF));
            }

            // ヘッダ行
            fputcsv($out, [
                'ID',
                '苗字',
                '名前',
                'メール',
                '電話',
                '性別',
                '住所',
                '建物名',
                'お問い合わせ内容',
                'お問い合わせの種類',
                '作成日時'
            ]);

            foreach ($q->orderBy('contacts.id')->cursor() as $r) {
                // created_at を安全に文字列へ
                $created = '';
                if (!empty($r->created_at)) {
                    $created = $r->created_at instanceof Carbon
                        ? $r->created_at->format('Y-m-d H:i:s')
                        : Carbon::parse($r->created_at)->format('Y-m-d H:i:s');
                }

                $row = [
                    $r->id,
                    $r->last_name,
                    $r->first_name,
                    $r->email,
                    $r->tel,
                    $r->gender,
                    $r->address,
                    $r->building,
                    $r->detail,
                    $r->content,
                    $created,
                ];

                // SJIS出力にしたい場合：ここで変換
                if ($encoding === 'SJIS-win') {
                    $row = array_map(fn($v) => mb_convert_encoding((string) $v, 'SJIS-win', 'UTF-8'), $row);
                }

                // 改行の正規化（任意：LFに統一）
                $row = array_map(fn($v) => str_replace(["\r\n", "\r"], "\n", (string) $v), $row);

                fputcsv($out, $row);
            }

            fclose($out);
        }, 200, $headers);
    }
}
