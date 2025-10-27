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
}
