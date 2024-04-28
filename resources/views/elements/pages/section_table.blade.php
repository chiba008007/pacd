<div class="uk-overflow-auto">
    <table class="uk-table">
        <tbody>
            @foreach ($sub_contents as $sub_content)
                @if ($sub_content['content1'] || $sub_content['content2'])
                    <tr>
                        <th class="contents_content1 text-nowrap edit-content"
                            data-content-id="{{ $sub_content['page_content_id'] }}"
                            data-sub-content-id="{{ $sub_content['id'] }}"
                            data-column="content1"
                        >{!! $sub_content['content1'] !!}</th>
                        <td class="contents_content2 width-min-medium edit-content"
                            data-content-id="{{ $sub_content['page_content_id'] }}"
                            data-sub-content-id="{{ $sub_content['id'] }}"
                            data-column="content2"
                        >{!! $sub_content['content2'] !!}</td>
                    </tr>
                @endif
            @endforeach

            {{-- 新しい行 --}}
            @if (Route::current()->getName() === 'admin.pages.edit')
                <tr id="new_table_row_{{ $sub_content['page_content_id'] }}" hidden>
                    <th class="contents_content1 text-nowrap edit-content"
                        data-content-id="{{ $sub_content['page_content_id'] }}"
                        data-sub-content-id="new"
                        data-column="content1"
                    >クリックして入力</th>
                    <td class="contents_content2 width-min-medium edit-content"
                        data-content-id="{{ $sub_content['page_content_id'] }}"
                        data-sub-content-id="new"
                        data-column="content2"
                    >クリックして入力</td>
                </tr>
            @endif
        </tbody>
    </table>
    {{-- 新しい行の追加ボタン --}}
    @if (Route::current()->getName() === 'admin.pages.edit')
        <div class="uk-text-right" uk-toggle>
            <a href="#" class="uk-icon-link" uk-icon="plus-circle" uk-toggle="target: #new_table_row_{{ $sub_content['page_content_id'] }}"></a>
        </div>
    @endif
</div>
