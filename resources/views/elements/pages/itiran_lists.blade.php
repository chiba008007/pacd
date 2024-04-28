@if (isset($is_itiran_page_list) && $is_itiran_page_list)
    {{-- 一覧テーブル --}}
    <div class="uk-section-xsmall uk-overflow-auto uk-padding">
        <div class="dtable uk-width-1-1">
            <div class="tr uk-text-center uk-background-muted">
                <div class="th uk-padding-small">タイトル</div>
                <div class="th">日時</div>
                <div class="th">会場</div>
                {{-- <div class="th">開催場所</div> --}}
            </div>
            @foreach($events as $key=>$values)
                @if($values['category_type'] != 5)
                <div class="tr uk-text-left">
                    <div class="td uk-padding-small">{{$values['name']}}</div>
                    <div class="td uk-padding-small">
                        @if(preg_match("/^2999/",$values['date_start']))
                            日程未定(調整中)
                        @else
                        {{date("Y年m月d日",strtotime($values['date_start']))}}～
                        {{date("Y年m月d日",strtotime($values['date_end']))}}
                        @endif
                    </div>
                    <div class="td uk-padding-small">{{$values['place']}}</div>
                    {{-- <div class="td uk-padding-small">{{$values['event_address']}}</div> --}}
                </div>
                @endif
            @endforeach
        </div>

    </div>
@endif
