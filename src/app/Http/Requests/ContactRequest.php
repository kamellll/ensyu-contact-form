<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'last_name' => ['required'],
            'first_name' => ['required'],
            'gender' => ['required'],
            'email' => ['required', 'email'],
            'tel' => ['required'],
            'address' => ['required'],
            'category_id' => ['required'],
            'detail' => ['required', 'max:120'],
        ];
    }
    public function messages()
    {
        return [
            'last_name.required' => '姓を入力してください',
            'first_name.required' => '名を入力してください',
            'gender.required' => '性別を選択してください',
            'email.required' => 'メールアドレスを入力してください',
            'email.email' => 'メールアドレスはメール形式で入力してください',
            'tel.required' => '電話番号を入力してください',
            'address.required' => '住所を入力してください',
            'category_id.required' => 'お問い合わせの種類を選択してください',
            'detail.required' => 'お問い合わせ内容を入力してください',
            'detail.max' => 'お問い合わせ内容は120文字以内で入力してください',
        ];
    }

    protected function prepareForValidation()
    {
        $tel = ($this->input('tel1', '') . $this->input('tel2', '') . $this->input('tel3', ''));
        // 数字以外を除去（念のため）
        //「/～/」は正規表現のデリミタ（正規表現の開始と終了）、
        // 「\D」は数字以外の1文字、
        // 「+」は直前のパターンの1回以上の繰り返し
        $tel = preg_replace('/\D+/', '', $tel);

        // 保存フォーマットを選ぶ：ハイフンなし or あり
        // 例：ハイフンなしで保存
        $this->merge(['tel' => $tel]);
    }
}
