<section>

    {{-- セクションタイトル --}}
    @if ($title)
        <h3 class="section__title edit-content uk-margin-top"
            data-content-id="{{ $id }}"
            data-column="title"
        >{!! $title !!}</h3>

    @endif

    {{-- セクションコンテンツ --}}
    @if ($content_type === 'text' && $content)
        {{-- テキストコンテンツ --}}
        <div class="uk-section-xsmall">
            <div class="contents_content edit-content"
                data-content-id="{{ $id }}"
                data-column="content"
            >{!! $content !!}</div>
        </div>
    @elseif($content_type === 'table' && $sub_contents)
        {{-- テーブルコンテンツ --}}
        <div class="uk-section-xsmall">
            @include('elements.pages.section_table', $sub_contents)
        </div>
    @elseif($content_type === 'list' && $sub_contents)
        {{-- リストコンテンツ --}}
        <div class="uk-section-xsmall">
            @include('elements.pages.section_list', $sub_contents)
        </div>
    @endif

</section>
