@extends('layouts.admin')

@section('title', '公開ページ編集')

@section('breadcrumb')
    @parent
    <li><a href="{{ route('admin.pages.index') }}">公開ページ一覧</a></li>
    <li>{{ $page['title'] ?? '' }} 編集</li>
@endsection

@section('content')
    <div class="uk-section-small uk-container">
        @if ($page['route_name'] === 'admin.pages.test')
            <div class="uk-section-xsmall uk-text-bold uk-text-danger">
                このページは管理者用のテストページです。
            </div>
        @endif
        <div class="uk-margin">
            <button class="uk-button uk-button-default uk-margin-small-right" type="button" uk-toggle="target: #modal-example">順番変更</button>

        </div>
        <div id="modal-example" uk-modal>
            <div class="uk-modal-dialog uk-modal-body">
                <h2 class="uk-modal-title">並び順変更</h2>

                <form method="POST" action="">
                    @csrf
                    <table class="uk-table">
                    <tr>
                        <th class="uk-table-expand">タイトル</th>
                        <th >並順</th>
                        <th >削除</th>
                    </tr>
                    @foreach($page['contents'] as $key=>$value)
                        <tr>
                            <td >
                                @if($value['title'])
                                    【見出し】
                                    {{$value['title']}}

                                @elseif($value['content_type'] == "text")
                                    【文章】
                                @elseif($value['content_type'] == "list")
                                    【リスト】
                                @elseif($value['content_type'] == "table")
                                    【一覧】
                                @endif
                            </td>
                            <td><input type="text" name="display_order[{{$value['id']}}]" value="{{$value['display_order']}}" class="uk-input" /></td>
                            <td><input type="checkbox" name="delete[{{$value['id']}}]" value="on" class="uk-checkbox" /></td>

                        </tr>
                    @endforeach
                    </table>
                    <p class="uk-text-right">
                        <button class="uk-button uk-button-default uk-modal-close" type="button">閉じる</button>
                        <input type="submit" name="order" value="更新"  class="uk-button uk-button-primary" />

                    </p>
                </form>
            </div>
        </div>
        <div id="page" class="uk-background-default uk-padding">
            {{-- 登録済みコンテンツ --}}
            @include('elements.pages.contents', $page)

            {{-- コンテンツ追加 --}}
            <div class="new_section_toggle" hidden>

                {{-- 新しい見出し --}}
                <div id="new_title" class="add_reset_toggle uk-margin-medium-bottom" hidden>
                    <h3 class="section__title edit-content uk-margin-remove"
                        data-content-id="new"
                        data-column="title"
                    >クリックして入力</h3>
                </div>

                {{-- 新しい本文 --}}
                <div class="uk-padding uk-padding-remove-top">
                    {{-- 文章 --}}
                    <div id="new_text" class="add_reset_toggle" hidden>
                        <div class="edit-content"
                            data-content-id="new"
                            data-column="content"
                        >クリックして入力</div>
                    </div>
                    {{-- テーブル --}}
                    <div id="new_table" class="add_reset_toggle" hidden>
                        <div class="uk-overflow-auto">
                            <table class="uk-table">
                                <tbody>
                                    <tr>
                                        <th class="text-nowrap edit-content"
                                            data-content-id="new"
                                            data-sub-content-id="new"
                                            data-column="content1"
                                            data-content-type="table"
                                        >クリックして入力</th>
                                        <td class="width-min-medium edit-content"
                                            data-content-id="new"
                                            data-sub-content-id="new"
                                            data-column="content2"
                                            data-content-type="table"
                                        >クリックして入力</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    {{-- リスト --}}
                    <div id="new_list1" class="add_reset_toggle" hidden>
                        <ul class="section__list">
                            <li class="contents_list1_content1 edit-content"
                                data-content-id="new"
                                data-sub-content-id="new"
                                data-content-type="list"
                                data-column="content1"
                                data-column-count="1"
                            >クリックして入力</li>
                        </ul>
                    </div>
                    {{-- リスト(2列) --}}
                    <div id="new_list2" class="add_reset_toggle" hidden>
                        <ul class="section__list">
                            <li class="uk-grid-small uk-margin-remove-left" uk-grid>
                                <div class="uk-width-1-4@s edit-content uk-padding-remove-left"
                                    data-content-id="new"
                                    data-sub-content-id="new"
                                    data-content-type="list"
                                    data-column="content1"
                                >クリックして入力</div>
                                <div class="uk-width-3-4@s uk-margin-left@s edit-content"
                                    data-content-id="new"
                                    data-sub-content-id="new"
                                    data-content-type="list"
                                    data-column="content2"
                                >クリックして入力</div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- コンテンツ追加操作ボタン --}}
            <div class="uk-section-xsmall">
                <div class="uk-text-center uk-section-xsmall uk-background-muted">
                    <h3 class="uk-text-bold">新しい項目を追加</h3>
                    <div class="new_section_toggle" uk-toggle="target: .new_section_toggle">
                        <button class="uk-button uk-button-secondary" uk-toggle="target: #new_title;"><span uk-icon="plus"></span> 見出し</button>
                        <button class="uk-button uk-button-secondary" uk-toggle="target: #new_text ;"><span uk-icon="plus"></span> 文章</button>
                        <button class="uk-button uk-button-secondary" uk-toggle="target: #new_table;"><span uk-icon="plus"></span> テーブル</button>
                        <button class="uk-button uk-button-secondary" uk-toggle="target: #new_list1;"><span uk-icon="plus"></span> リスト</button>
                        <button class="uk-button uk-button-secondary" uk-toggle="target: #new_list2;"><span uk-icon="plus"></span> リスト(2列)</button>
                    </div>
                    <div class="new_section_toggle" hidden>
                        <button id="add_reset" class="uk-button uk-button-secondary" uk-toggle="target: .new_section_toggle">戻る</button>
                    </div>
                </div>
            </div>

            {{-- 次回イベント等TOPページコンテンツ --}}
            {{--
            @include('elements.pages.top_contents', $page)
--}}
            {{-- 一覧データ --}}
            @include('elements.pages.itiran', $page)
            @include('elements.pages.itiran_lists', $page)

            {{-- フォーム --}}
            @isset($page['inputs'])
                <div class="form_contents">
                    @if ($page['route_name'] == 'register')
                        @include('elements.forms.members.register', ['inputs' => $page['inputs']])
                    @elseif (strpos($page['route_name'], 'presenter') !== false)
                        @include('elements.forms.presenters.register', ['inputs' => $page['inputs']])
                    @else
                        @include('elements.forms.attendees.register', ['inputs' => $page['inputs']])
                    @endif
                </div>
            @endisset
        </div>

        {{-- 編集エディター --}}
        <div id="editor" uk-toggle hidden>
            <textarea name="content" cols="30" rows="10"></textarea>
            <div class="uk-text-right uk-background-secondary uk-width-1-1">
                <button id="cancel" class="uk-button-small">キャンセル</button>
                <button id="update" class="add_toggle uk-button-small uk-button-primary uk-margin-small uk-margin-small-right"
                    data-action="{{ route('admin.pages.update.ajax', [$page['id']]) }}">登録</button>
            </div>
        </div>
    </div>
@endsection

@section('footer')
    <script src="{{ asset('js/admin/pages.js') }}"></script>
@endsection
