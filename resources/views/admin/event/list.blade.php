@extends('layouts.admin')

@section('title', $title)

@section('breadcrumb')
    @parent
    <li><span>{{ $title }}</span></li>
@endsection

@section('content')
    <div class="uk-section-small">
        <div class="uk-container uk-container-large">
            @if (session('status'))
                <div class="uk-alert-success" uk-alert>
                <a class="uk-alert-close" uk-close></a>
                    {{ session('status') }}
                </div>
            @endif
            @csrf
            <div id="pagers">
                {{ $events->links() }}
            </div>
            <form method="GET" action="" class="uk-form-horizontal" >
            @csrf
                <div class="uk-grid-column-small uk-grid-row-large uk-child-width-1-3@s "  uk-grid>
                    <div>
                        <input class="uk-input" type="text" name="code"  value="{{ old('code',$code)}}" placeholder="イベント名・イベントコード検索">
                    </div>
                    <div>
                        <button type="submit" class="uk-button uk-button-secondary">検索</button>
                    </div>
                </div>
            </form>
            <table class="uk-table">
                <thead>
                    <tr>
                        <th class="uk-table-expand" style="width:350px;">詳細</th>
                        <th >イベントコード</th>
                        <th style="width:300px;">イベント名</th>
                        <th class="uk-table-shrink">受付中</th>
                        <th style="width:200px;" class="uk-table-shrink">表示判定</th>
                    </tr>
                </thead>
                <tbody>
                @foreach ($events as $event)
                    <tr>
                        <td>
                            @if($category_prefix != "kyosan")
                            <a href="{{ route("admin.$category_prefix.event.program.register", $event->id) }}" class="uk-button uk-button-primary uk-text-nowrap">プログラム</a>
                            @endif

                            <?php $str = "参加者";?>
                            @if(isCurrent('admin.bunseki.event'))
                            <?php $str = "参加/協賛";?>
                            @endif
                            <?php $top = ""; ?>
                            @if($category_prefix == "kyosan")
                            <?php $top = "uk-margin-small-top"; ?>
                            @endif
                            <a class="<?=$top?> uk-button uk-button-primary uk-text-nowrap" href="{{ route("admin.attendees.index", [$category_prefix, 'code' => $event->code]) }}">{{$str}}</a>
                            @if($category_prefix != "kyosan")
                            <a class="uk-button uk-button-primary uk-text-nowrap" href="{{ route("admin.presenters.index", [$category_prefix, 'code' => $event->code]) }}">講演者</a>
                            @endif
                            @if($category_prefix != "kyosan")
                            <a href="{{ route("admin.$category_prefix.event.upload", $event->id) }}" class="uk-margin-small-top uk-button uk-button-primary uk-text-nowrap">原稿</a>
                            @endif

                            <a href="{{ route("admin.$category_prefix.event.register", $event->id) }}" class="uk-margin-small-top uk-button uk-button-primary uk-text-nowrap">編集</a>

                            <a href="{{ route("admin.$category_prefix.event.delete", $event->id) }}" class="uk-margin-small-top uk-button uk-button-danger uk-text-nowrap deletebutton">削除</a>

                        </td>
                        <td>{{$event->code}}</td>
                        <td>{{$event->name}}</td>
                        <td>
                            <div class="switchArea">
                                <?php $checked = "";?>
                                @if($event->enabled == 1)
                                <?php $checked = "checked";?>
                                @endif
                                <input type="checkbox" id="switch-{{$event->id}}" class="enableswitch" <?=$checked?> >
                                <label for="switch-{{$event->id}}"><span></span></label>
                                <div class="swImg"></div>
                            </div>
                        </td>
                        <td>
                            
                            @php $checked = [0,0,0]; @endphp
                            @if($event->attendFlag == 1)
                                @php $checked[0] = "checked"; @endphp
                            @endif
                            @if($event->speakerFlag == 1)
                                @php $checked[1] = "checked"; @endphp
                            @endif
                            @if($event->speakerMenuFlag == 1)
                                @php $checked[2] = "checked"; @endphp
                            @endif
                            <input type="checkbox" id="attendFlag-{{$event->id}}" class="onClick"  {{$checked[0]}} />
                            <label for="attendFlag-{{$event->id}}" >参加者情報確認</label><br  />
                            @if($category_prefix != 'kyosan')
                                <input type="checkbox"  id="speakerFlag-{{$event->id}}" class="onClick" {{$checked[1]}} />
                                <label for="speakerFlag-{{$event->id}}">講演申し込み</label><br />
                                <input type="checkbox"  id="speakerMenuFlag-{{$event->id}}" class="onClick" {{$checked[2]}} />
                                <label for="speakerMenuFlag-{{$event->id}}">講演者メニュー</label>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

        </div>
    </div>
@endsection

@section('footer')
    <script src="{{ asset('js/admin/event.js') }}"></script>
    <script type="text/javascript" >
    $(function(){
        $(".onClick").click(function(){
            var id = $(this).attr("id").split("-");
            var val = $(this).prop("checked");
             let postData = {
                id: id[0],
                chk: id[1],
                val: val
            };
            $.ajax({
                url: '../attendees/changeFlag',  // サーバーにリクエストを送る URL
                type: 'POST',       // リクエストの種類（GET）
                dataType: 'json',  // レスポンスの形式（JSON）
                data:postData,
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
            })
            .done((result) => {
                console.log(result);
            })
            .fail ((error) => {
                console.log("データ更新エラー");
                console.log(error);
            });
return true;
        });
    });
    </script>
@endsection
