@extends('layouts.admin')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <link rel="stylesheet" href="{{ asset('css/pagenate.css') }}">
@endsection

@section('content')
    <div class="admin">
        <div class="admin__heading">
            <h2>Admin</h2>
        </div>
        <div class="search">
            <form class="search-form" action="/search" method="get">
                @csrf
                <div class="search-form__item">
                    <input name="text" class="search-form__text" type="text" placeholder="名前やメールアドレスを入力してください"
                        value="@if(isset($text)) {{ $text }} @endif" />
                    <select class="search-form__item" name="gender">
                        <option value="">性別</option>
                        <option value="1" @if(isset($gender) and 1 === (int) $gender) selected @endif>男性</option>
                        <option value="2" @if(isset($gender) and 2 === (int) $gender) selected @endif>女性</option>
                        <option value="3" @if(isset($gender) and 3 === (int) $gender) selected @endif>その他</option>
                    </select>
                    <select class="search-form__item-select" name="category_id">
                        <option value="">カテゴリ</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category['id'] }}" @if(isset($category_id) and $category['id'] === (int) $category_id) selected @endif>
                                {{ $category['content']}}
                            </option>
                        @endforeach
                    </select>
                    <input type="date" name="date" value="{{ old('date', request('date'))}}">
                </div>
                <div class="search-form__button">
                    <button class="search-form__button-submit" type="submit">検索</button>
                </div>
            </form>
            <form class="reset" action="/admin" method="get">
                @csrf
                <div class="reset__button">
                    <button type="submit">リセット</button>
                </div>
            </form>
        </div>
        <div class="page">
            <form class="export" action="/export" method="get">
                @csrf
                <div class="export__button">
                    <button type="submit">エクスポート</button>
                </div>
            </form>
            <div class="page__content">{{ $contacts->links('vendor.pagination.arrows-numbers') }}</div>
        </div>
        <table class="admin-table">
            <tr>
                <th>お名前</th>
                <th>性別</th>
                <th>メールアドレス</th>
                <th>お問い合わせの種類</th>
                <th></th>
            </tr>
            @foreach($contacts as $contact)
                <tr>
                    <td>{{ $contact['last_name'] }} {{ $contact['first_name'] }}</td>
                    <td>
                        @if(1 === $contact['gender'])
                            男性
                        @elseif(2 === $contact['gender'])
                            女性
                        @elseif(3 === $contact['gender'])
                            その他
                        @endif
                    </td>
                    <td>{{ $contact['email'] }}</td>
                    <td>
                        @if(1 === $contact['category_id'])
                            商品のお届けについて
                        @elseif(2 === $contact['category_id'])
                            商品の交換について
                        @elseif(3 === $contact['category_id'])
                            商品トラブル
                        @elseif(4 === $contact['category_id'])
                            ショップへのお問い合わせ
                        @elseif(5 === $contact['category_id'])
                            その他
                        @endif
                    </td>
                    <td>詳細</td>
                </tr>
            @endforeach
        </table>
    </div>
@endsection