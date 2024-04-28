@extends('layouts.admin')

@section('title', $title)

@section('breadcrumb')
    @parent
    @foreach ($breadcrumbs as $breadcrumb)
        <li><a href="{{ $breadcrumb['url'] }}">{{ $breadcrumb['title'] }}</a></li>
    @endforeach
@endsection

@section('content')
    <div class="uk-container uk-container-large">
        <div class="uk-section-small">

            <form method="GET" action="" class="uk-form-horizontal" >
                @csrf
                <div class="uk-grid-column-small uk-grid-row-large uk-child-width-1-3@s "  uk-grid>
                    <div>
                        <input class="uk-input" type="text" name="search"  value="{{ old('code',$search)}}" placeholder="参加イベント・発表番号・ログインID・氏名で検索">
                    </div>
                    <div>
                        <button type="submit" class="uk-button uk-button-secondary">検索</button>

                        <a href="{{route('admin.presenters.csv', $form['category_prefix']) }}/?code={{$code}}" class="uk-button uk-button-primary">講演者CSV</a>

                    </div>
                    <input type="hidden" name="code" value="{{ $code }}" />
                    <!-- 講習会の場合のみ適用 -->
                    @if (
                        $form['category_prefix'] === config("pacd.category.kosyukai.prefix"))
                        <div>
                        <a href="{{ route('admin.zipdownloadtype',[
                            $event_id,0,0,1
                            ])}}" class="uk-button uk-button-primary" >配布資料一括ダウンロード</a>
                        </div>
                    @endif
                </div>
            </form>

            <div class="uk-overflow-auto uk-margin-small-top " id="scrollheight">
                <table id="presenters_table" class="uk-table uk-text-center tablesorter" style="table-layout: fixed;">
                    <thead>
                        <tr>
                            <th class="uk-width-small">詳細</th>
                            <th class="uk-width-small">参加イベント</th>
                            <th class="uk-width-small">発表番号</th>
                            <th class="uk-width-small">講演者番号</th>
                            <th class="uk-width-small">参加者番号</th>
                            <th class="uk-width-small">ログインID</th>
                            <th class="uk-width-small">氏名</th>

                            <th class="uk-width-small">
                            @if ($form['category_prefix'] === config("pacd.category.kosyukai.prefix"))
                            <input type="checkbox" id="all_check_1" class="all_check" value="1" />
                            @endif
                            @if($form['category_prefix'] == "reikai")
                                配布資料1
                            @elseif($form['category_prefix'] == "kosyukai")
                                配布資料1
                            @else
                                講演要旨
                            @endif

                            </th>
                            <th class="uk-width-small">
                            @if ($form['category_prefix'] === config("pacd.category.kosyukai.prefix"))
                            <input type="checkbox" id="all_check_2" class="all_check" value="2" />
                            @endif
                            @if($form['category_prefix'] == "reikai")
                                配布資料2
                            @elseif($form['category_prefix'] == "kosyukai")
                                配布資料2
                            @elseif( $form[ 'category_prefix' ] == "touronkai" && $event_id == 238)
                                プレゼンテーション資料
                            @else
                                フラッシュプレゼンテーションファイル
                            @endif
                            </th>
                            <th class="uk-width-small">
                            @if ($form['category_prefix'] === config("pacd.category.kosyukai.prefix"))
                            <input type="checkbox" id="all_check_3" class="all_check" value="3" />
                            @endif
                            @if($form['category_prefix'] == "reikai")
                                配布資料3
                            @elseif($form['category_prefix'] == "kosyukai")
                                配布資料3
                            @elseif( $form[ 'category_prefix' ] == "touronkai" && $event_id == 238)
                                配布資料
                            @else
                                ポスター・配布資料等
                            @endif
                            </th>
                            <th class="uk-width-small">更新日</th>
                            @if($inputs->count())
                                @foreach ($inputs as $item)
                                    <th class="uk-width-medium">{{ $item->name }}</th>
                                @endforeach
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        {{-- TODO: 各項目検索機能 --}}
                        @foreach ($presenters as $presenter)
                            @php
                                $attendee = $presenter->attendee;
                                $presentation = $presenter->presentation;
                            @endphp
                            <tr>
                                <td class="text-nowrap">
                                    <a href="{{ route('admin.presenters.edit', [$form['category_prefix'], $presenter->id]) }}" class="uk-button uk-button-small uk-button-primary">編集</a>
                                    <form action="{{ route('admin.presenters.destroy', [$form['category_prefix'], $presenter->id]) }}" method="post" class="uk-inline">
                                        @csrf
                                        @method('delete')
                                        <button type="submit" class="uk-button uk-button-small uk-button-danger" onclick="return confirm('削除します。よろしいですか？')">削除</button>
                                    </form>
                                </td>
                                <td>@if(isset($attendee->event->name)){{ $attendee->event->name }}@endif</td>
                                <td>{{ $presentation ? $presentation->number : '未発行' }}</td>
                                <td>{{ sprintf('%010d', $presenter->id) }}</td>
                                <td>@if(isset($attendee->event_number)){{ sprintf('%010d', $attendee->event_number) }}@endif</td>
                                <td>@if(isset($attendee->user->login_id)){{ $attendee->user->login_id }}@endif</td>
                                <td>
                                @if(isset($attendee->user->sei)){{ $attendee->user->sei }}@endif
                                @if(isset($attendee->user->mei)){{ $attendee->user->mei }}@endif
                                </td>
                                <td>

                                    @if ($presentation && $presentation->proceeding && $presentation->number)
                                        <?php
                                            $chk = "";
                                            if($presentation->proceeding_flag):
                                            $chk = "checked";
                                            endif;
                                        ?>
                                        @if ($form['category_prefix'] === config("pacd.category.kosyukai.prefix"))
                                        <input type="checkbox" class="display_status1" id="display_status_{{$presentation->id}}" value="on" <?=$chk?> />
                                        @endif
                                        <a href="{{ route('presentation.get.file', [@$presentation->number, 'proceeding',@$presentation->id]) }}" target="_blank">
                                            @if($form['category_prefix'] == "reikai")
                                                配布資料1
                                            @elseif($form['category_prefix'] == "kosyukai")
                                                配布資料1
                                            @else
                                                講演要旨
                                            @endif
                                        </a>
                                    @endif
                                </td>
                                <td>
                                    @if ($presentation && $presentation->flash && $presentation->number)
                                        <?php
                                            $chk = "";
                                            if($presentation->flash_flag):
                                            $chk = "checked";
                                            endif;
                                        ?>
                                        @if ($form['category_prefix'] === config("pacd.category.kosyukai.prefix"))
                                        <input type="checkbox" class="display_status2" id="display_status_{{$presentation->id}}" value="on" <?=$chk?> />
                                        @endif
                                        <a href="{{ route('presentation.get.file', [@$presentation->number, 'flash',@$presentation->id]) }}" target="_blank">

                                        @if($form['category_prefix'] == "reikai")
                                            配布資料2
                                        @elseif($form['category_prefix'] == "kosyukai")
                                            配布資料2
                                        @elseif( $form[ 'category_prefix' ] == "touronkai" && $attendee->event->id == 238)
                                            プレゼンテーション資料
                                        @else
                                            フラッシュプレゼンテーションファイル
                                        @endif

                                        </a>
                                    @endif
                                </td>
                                <td>
                                    @if ($presentation && $presentation->poster && $presentation->number)
                                        <?php
                                            $chk = "";
                                            if($presentation->poster_flag):
                                            $chk = "checked";
                                            endif;
                                        ?>
                                        @if ($form['category_prefix'] === config("pacd.category.kosyukai.prefix"))
                                        <input type="checkbox" class="display_status3" id="display_status_{{$presentation->id}}" value="on" <?=$chk?> />
                                        @endif
                                        <a href="{{ route('presentation.get.file', [@$presentation->number, 'poster',@$presentation->id]) }}" target="_blank">

                                        @if($form['category_prefix'] == "reikai")
                                            配布資料3
                                        @elseif($form['category_prefix'] == "kosyukai")
                                            配布資料3
                                        @elseif( $form[ 'category_prefix' ] == "touronkai" && $attendee->event->id == 238)
                                            配布資料
                                        @else
                                            ポスター・配布資料等
                                        @endif
                                        </a>
                                    @endif
                                </td>
                                <td>{{ date_format($presenter->updated_at, 'Y/m/d') }}</td>
                                @if($inputs->count())
                                    @php $custom_data = $presenter->custom_form_data->keyBy('form_input_value_id') @endphp
                                    @foreach ($inputs as $item)
                                        <td>
                                            @foreach ($item->values as $value)
                                                @isset($custom_data[$value->id])
                                                    {{ $custom_data[$value->id]->data }}
                                                    @if ($custom_data[$value->id]->data_sub)
                                                        <br>
                                                        {{ $custom_data[$value->id]->data_sub }}
                                                    @endif
                                                    @if (!$loop->last)
                                                        <br>
                                                    @endif
                                                @endisset
                                            @endforeach
                                        </td>
                                    @endforeach
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- ページネーション --}}
            <div>
                {{ $presenters->appends(['code'=> $code])->links() }}
            </div>
        </div>
    </div>
@endsection

@section('footer')
    <script>
        $('#presenters_table').tablesorter({
            headers: {
                0: {sorter:false},
            }
        });
        $(".display_status1").click(function(){
            var _id = $(this).attr("id").split("_");
            var _flag = $(this).prop('checked');
            flagCheck(1,_id,_flag);
        });
        $(".display_status2").click(function(){
            var _id = $(this).attr("id").split("_");
            var _flag = $(this).prop('checked');
            flagCheck(2,_id,_flag);
        });
        $(".display_status3").click(function(){
            var _id = $(this).attr("id").split("_");
            var _flag = $(this).prop('checked');
            flagCheck(3,_id,_flag);
        });
        function flagCheck(type,_id,_flag){

            var _url = location.pathname+"/checked"+location.search;
            var postData = {
                "code":type,
                "id":_id[2],
                "flag":_flag,
            };
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: _url,
                type: 'POST',
                data: postData,
                timeout: 5000,
            })
            .then(result => {

            })
            .catch(error => {
                console.log('データ更新エラー');
                console.log(error.status);
            });
            return true;
        }
        $(".all_check").click(function(){
            var _flag = $(this).prop('checked');
            var _id = $(this).attr("id").split("_");
            var _url = location.pathname+"/checked"+location.search;
            var postData = {
                "code":_id[2],
                "flag":_flag,
            };
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: _url,
                type: 'POST',
                data: postData,
                timeout: 5000,
            })
            .then(result => {
                console.log(result);
                if(_id[2] == 1) $(".display_status1").prop("checked",_flag);
                if(_id[2] == 2) $(".display_status2").prop("checked",_flag);
                if(_id[2] == 3) $(".display_status3").prop("checked",_flag);

            })
            .catch(error => {
                console.log('データ更新エラー');
                console.log(error.status);
            });
            return true;
        });
    </script>
@endsection
