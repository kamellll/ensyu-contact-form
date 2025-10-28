@extends('layouts.admin')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <link rel="stylesheet" href="{{ asset('css/pagenate.css') }}">
@endsection
@section('js')
    <script src="{{ asset('/js/admin.js') }}" defer></script>
@endsection
@section('content')
    <div class="admin">
        <div class="admin__alert">
            @if (session('message'))
                <div class="admin__alert--success">
                    {{ session('message') }}
                </div>
            @endif
        </div>
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
                    <td>
                        <button type="button" class="btn-detail" data-id="{{ $contact['id'] }}"
                            data-name="{{ $contact['last_name'] . $contact['first_name'] }}"
                            data-email="{{ $contact['email'] }}" data-tel="{{ $contact['tel'] }}"
                            data-address="{{ $contact['address'] }}" data-building="{{ $contact['building'] }}"
                            data-gender="@if(1 === $contact['gender']) 男性 @elseif(2 === $contact['gender']) 女性 @elseif(3 === $contact['gender']) その他 @endif "
                            data-category=" @if(1 === $contact['category_id']) 商品のお届けについて @elseif(2 === $contact['category_id']) 商品の交換について @elseif(3 === $contact['category_id']) 商品トラブル @elseif(4 === $contact['category_id']) ショップへのお問い合わせ @elseif(5 === $contact['category_id']) その他 @endif"
                            data-detail="{{ e($contact['detail']) }}">
                            詳細
                        </button>
                    </td>
                </tr>
            @endforeach
        </table>
        <div id="detailModal" class="modal" hidden>
            <div class="modal__overlay" data-close="true"></div>
            <div class="modal__panel" tabindex="-1">
                <form class="modal__form" action="/delete" method="post">
                    @method('DELETE')
                    @csrf
                    <input type="hidden" name="id" id="m-id" value="">
                    <div class="modal__content">
                        <div class="modal__column">お名前</div>
                        <div class="modal__contact" id="m-name"></div>
                    </div>
                    <div class="modal__content">
                        <div class="modal__column">性別</div>
                        <div class="modal__contact" id="m-gender"></div>
                    </div>
                    <div class="modal__content">
                        <div class="modal__column">メールアドレス</div>
                        <div class="modal__contact" id="m-email"></div>
                    </div>
                    <div class="modal__content">
                        <div class="modal__column">電話番号</div>
                        <div class="modal__contact" id="m-tel"></div>
                    </div>
                    <div class="modal__content">
                        <div class="modal__column">住所</div>
                        <div class="modal__contact" id="m-address"></div>
                    </div>
                    <div class="modal__content">
                        <div class="modal__column">建物名</div>
                        <div class="modal__contact" id="m-building"></div>
                    </div>
                    <div class="modal__content">
                        <div class="modal__column">お問い合わせの種類</div>
                        <div class="modal__contact" id="m-category"></div>
                    </div>
                    <div class="modal__content">
                        <div class="modal__column">お問い合わせ内容</div>
                        <div class="modal__contact" id="m-detail"></div>
                    </div>
                    <div class="modal__delete-button">
                        <button type="submit">削除</button>
                    </div>
                </form>
                <button type="submit" data-close="true" class="close-button"></button>
            </div>
        </div>
    </div>

@endsection