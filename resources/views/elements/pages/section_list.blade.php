<ul class="section__list">
    @foreach ($sub_contents as $sub_content)
        @if ($sub_content['content1'] || $sub_content['content2'])
            @if ($sub_content['column_count'] < 2)
                {{-- 1カラムリスト --}}
                <li class="edit-content"
                    data-content-id="{{ $sub_content['page_content_id'] }}"
                    data-sub-content-id="{{ $sub_content['id'] }}"
                    data-column="content1"
                >{!! $sub_content['content1'] !!}</li>
            @elseif ($sub_content['column_count'] == 2)
                {{-- 2カラムリスト --}}
                <li class="uk-grid-small uk-margin-remove-left" uk-grid>
                    <div class="uk-padding-small uk-padding-remove-vertical uk-padding-remove-left uk-maring-right uk-width-1-4@s edit-content"
                        data-content-id="{{ $sub_content['page_content_id'] }}"
                        data-sub-content-id="{{ $sub_content['id'] }}"
                        data-column="content1"
                    >{!! $sub_content['content1'] !!}</div>

                    <div class="uk-padding-remove-left uk-width-3-4@s uk-margin-left@s edit-content"
                        data-content-id="{{ $sub_content['page_content_id'] }}"
                        data-sub-content-id="{{ $sub_content['id'] }}"
                        data-column="content2"
                    >{!! $sub_content['content2'] !!}</div>
                </li>
            @endif
        @endif
    @endforeach

    {{-- リスト追加（管理画面のみ表示） --}}
    @if (Route::current()->getName() === 'admin.pages.edit')
        @if ($sub_content['column_count'] < 2)
            {{-- 1カラムリスト --}}
            <li id="new_list_row_{{ $sub_content['page_content_id'] }}" class="edit-content" hidden
                data-content-id="{{ $sub_content['page_content_id'] }}"
                data-sub-content-id="new"
                data-column="content1"
                data-column-count="{{ $sub_content['column_count'] }}"
            >クリックして入力</li>
        @elseif ($sub_content['column_count'] == 2)
            {{-- 2カラムリスト --}}
            <li  id="new_list_row_{{ $sub_content['page_content_id'] }}" class="uk-grid-small uk-margin-remove-left" uk-grid hidden>
                <div class="uk-padding-small uk-padding-remove-vertical uk-padding-remove-left uk-maring-right uk-width-1-4@s edit-content"
                    data-content-id="{{ $sub_content['page_content_id'] }}"
                    data-sub-content-id="new"
                    data-column="content1"
                    data-column-count="{{ $sub_content['column_count'] }}"
                >クリックして入力</div>

                <div class="uk-padding-remove-left uk-width-3-4@s uk-margin-left@s edit-content"
                    data-content-id="{{ $sub_content['page_content_id'] }}"
                    data-sub-content-id="new"
                    data-column="content2"
                    data-column-count="{{ $sub_content['column_count'] }}"
                >クリックして入力</div>
            </li>
        @endif
        <div class="uk-text-right" uk-toggle>
            <a href="#" class="uk-icon-link" uk-icon="plus-circle" uk-toggle="target: #new_list_row_{{ $sub_content['page_content_id'] }}"></a>
        </div>
    @endif
</ul>
