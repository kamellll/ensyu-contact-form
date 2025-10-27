@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/confirm.css') }}">
@endsection

@section('content')
    <div class="contact-form__content">
        <div class="contact-form__heading">
            <h2>Confirm</h2>
        </div>
        <table class="confirm-table">
            <tr>
                <th>お名前</th>
                <td>{{ $contact['last_name'] }} {{ $contact['first_name'] }}</td>
            </tr>
            <tr>
                <th>性別</th>
                <td>
                    @if("1" === $contact['gender'])
                        男性
                    @elseif("2" === $contact['gender'])
                        女性
                    @elseif("3" === $contact['gender'])
                        その他
                    @endif
                </td>
            </tr>
            <tr>
                <th>メールアドレス</th>
                <td>{{ $contact['email'] }}</td>
            </tr>
            <tr>
                <th>電話番号</th>
                <td>{{ $contact['tel1'] }}{{ $contact['tel2'] }}{{ $contact['tel3'] }}</td>
            </tr>
            <tr>
                <th>住所</th>
                <td>{{ $contact['address'] }}</td>
            </tr>
            <tr>
                <th>建物名</th>
                <td>{{ $contact['building'] }}</td>
            </tr>
            <tr>
                <th>お問い合わせの種類</th>
                <td>
                    @if("1" === $contact['category_id'])
                        商品のお届けについて
                    @elseif("2" === $contact['category_id'])
                        商品の交換について
                    @elseif("3" === $contact['category_id'])
                        商品トラブル
                    @elseif("4" === $contact['category_id'])
                        ショップへのお問い合わせ
                    @elseif("5" === $contact['category_id'])
                        その他
                    @endif
                </td>
            </tr>
            <tr>
                <th>お問い合わせ内容</th>
                <td>{{ $contact['detail'] }}</td>
            </tr>
        </table>
        <div class="confirm-button">
            <form class="form" action="/thanks" method="post">
                @csrf
                <input class="form__last-name" type="hidden" name="last_name" value="{{ $contact['last_name'] }}" />
                <input class="form__first-name" type="hidden" name="first_name" value="{{ $contact['first_name'] }}" />
                <input type="hidden" name="gender" value="{{ $contact['gender'] }}">
                <input type="hidden" name="email" value="{{ $contact['email'] }}">
                <input type="hidden" name="tel" value="{{ $contact['tel'] }}">
                <input type="hidden" name="address" value="{{ $contact['address'] }}">
                <input type="hidden" name="building" value="{{ $contact['building'] }}">
                <input type="hidden" name="category_id" value="{{ $contact['category_id'] }}">
                <input type="hidden" name="detail" value="{{ $contact['detail'] }}">
                <div class="form__button">
                    <button class="form__button-submit" type="submit">送信</button>
                </div>
            </form>
            <form class="form" action="/back" method="post">
                @csrf
                <input class="form__last-name" type="hidden" name="last_name" value="{{ $contact['last_name'] }}" />
                <input class="form__first-name" type="hidden" name="first_name" value="{{ $contact['first_name'] }}" />
                <input type="hidden" name="gender" value="{{ $contact['gender'] }}">
                <input type="hidden" name="email" value="{{ $contact['email'] }}">
                <input type="hidden" name="tel1" value="{{ $contact['tel1'] }}">
                <input type="hidden" name="tel2" value="{{ $contact['tel2'] }}">
                <input type="hidden" name="tel3" value="{{ $contact['tel3'] }}">
                <input type="hidden" name="address" value="{{ $contact['address'] }}">
                <input type="hidden" name="building" value="{{ $contact['building'] }}">
                <input type="hidden" name="category_id" value="{{ $contact['category_id'] }}">
                <input type="hidden" name="detail" value="{{ $contact['detail'] }}">
                <div class="form__correct">
                    <button type="submit">修正</button>
                </div>
            </form>
        </div>
    </div>
@endsection