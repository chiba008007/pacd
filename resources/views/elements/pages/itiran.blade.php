@if (isset($is_itiran_page) && $is_itiran_page)
    {{-- 一覧テーブル --}}
    <div class="uk-section-xsmall uk-overflow-auto uk-padding">
        <div class="dtable uk-width-1-1">
            <div class="tr uk-text-center uk-background-muted">
                <div class="th uk-padding-small">タイトル</div>
                <div class="th">日時</div>
                <div class="th">会場</div>
                <div class="th">開催案内</div>
                <div class="th">報告</div>
                @if (Route::currentRouteName() != "reikai.history" )
                <div class="th">要旨</div>
                @endif
            </div>
            @foreach($events as $key=>$values)
                <div class="tr uk-text-left">
                    <div class="td uk-padding-small">{{$values['name']}}</div>
                    <div class="td uk-padding-small">
                        {{date("Y年m月d日",strtotime($values['date_start']))}}～
                        {{date("Y年m月d日",strtotime($values['date_end']))}}
                    </div>
                    <div class="td uk-padding-small">{{$values['place']}}</div>
                    <div class="td uk-padding-small">
                        @if(!empty($values['upload']['dispname'][0]))
                        <a class="uk-link-text downloadclick" href="/download/{{$values['upload'][ 'upload_id' ][0]}}">{{$values['upload']['dispname'][0]}} <i uk-icon="download"></i></a>
                        @endif
                    </div>
                    <div class="td uk-padding-small">
                        @if(!empty($values['upload']['dispname'][1]))
                        <a class="uk-link-text downloadclick" href="/download/{{$values['upload'][ 'upload_id' ][1]}}" >{{$values['upload']['dispname'][1]}} <i uk-icon="download"></i></a>
                        @endif
                    </div>
                    @if (Route::currentRouteName() != "reikai.history" )
                        <div class="td uk-padding-small">
                            @if(!empty($values['upload']['dispname'][2]))
                            {{--パスワードを入力しないでダウンロードを行いたい依頼があったため変更--}}
                            <a class="uk-link-text downloadclick" href="/download/{{$values['upload'][ 'upload_id' ][2]}}" >{{$values['upload']['dispname'][2]}} <i uk-icon="download"></i></a>
                            @endif
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

    </div>
@endif

<script type="text/javascript">
    $(function(){
        $(".downloadclick").click(function(){
            var _pw = "off";
            var _href = $(this).attr("href");
            location.href = _href+"/?pw="+_pw;
            return false;
        });
        $(".downloadclickPW").click(function(){
            var _pw = window.prompt("パスワードを入力してください", "");
            if(!_pw) return false;
            var _href = $(this).attr("href");
            location.href = _href+"/?pw="+_pw;
            return false;
        });
    });
</script>
