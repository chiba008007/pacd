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

        <form method="GET" action="{{url()->current()}}" class="uk-form-horizontal">
            @csrf
            <div class="uk-grid-column-small uk-child-width-1-3@s " uk-grid>
                <div>
                    <input class="uk-input" type="text" name="search" value="{{ old('code',$search)}}" placeholder="氏名・参加者番号で検索">
                </div>
                <div>
                    <select name='code' class="uk-select">
                        <option value="0">イベント名の検索</option>
                        @foreach ($events as $event)
                        <?php
                        $sel = "";
                        if ($code == $event->code) $sel = "SELECTED";
                        ?>
                        <option value="{{ $event->code }}" {{$sel}}>{{ $event->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <button type="submit" class="uk-button uk-button-secondary">検索</button>

                    <a href="{{route('admin.attendees.csv', $form['category_prefix']) }}?code={{$code}}&search={{$search}}" class="uk-button uk-button-primary">参加者CSV</a>
                </div>
                {{-- <input type="hidden" name="code" value="{{ $code }}" /> --}}
            </div>
        </form>
        <input type="hidden" id="currentPath" value="{{route('admin.reikai.invoiceDownloadAjax')}}" />

        <input type="hidden" id="currentPath_check" value="{{route('admin.members.docDownloadUpdateAjax')}}" />
        <input type="hidden" id="currentPath_ac" value="{{route('admin.members.docAllCheck')}}" />

        <div class="uk-flex">
            <!-- jsに渡す用 -->
            <?php $array = "0"; ?>
            @foreach ($all_attendees as $all_attendee)
            @php
            $doc_list[] = $all_attendee->doc_dl;
            $array = implode(',', $doc_list);
            @endphp
            @endforeach
            <div class="uk-child-width-1-1@s uk-margin-small-top">
                <div class="uk-flex">
                    <input type="hidden" id="invoiceStatus" value="{{route('admin.attendees.invoiceStatusDownload',$form['category_prefix'])}}" />

                    <button type="button" class="uk-button uk-button-primary" onClick="invoiceStatus(1)" style="text-wrap:nowrap">
                        <small>請求書・領収書DL可</small>
                    </button>

                    <button type="button" class="uk-button uk-button-danger" onClick="invoiceStatus(0)" style="text-wrap:nowrap">
                        <small>請求書・領収書DL不可</small>
                    </button>
                </div>
            </div>
            
            {{-- ページネーション --}}
            <div id="pagers" class='uk-margin-top'>
                {{ $attendees->appends(['search' => $search,'code'=>$code])->links() }}
            </div>
        </div>


        <div class="uk-overflow-auto uk-margin-small-top" id="scrollheight">

            <table id="attendees_table" class="uk-table uk-text-center tablesorter" style="table-layout: fixed;">
                <thead>
                    <tr>
                        <th class="uk-width-medium">詳細</th>
                        <th class="uk-width-small">参加イベント</th>
                        <th class="uk-width-small">参加者番号</th>
                        <th class="uk-width-small">ログインID</th>
                        <th class="uk-width-small">氏名</th>
                        <th class="uk-width-small">氏名（ふりがな）</th>
                        @if( $form['category_prefix'] == "kyosan")
                        <th class="uk-width-small">法人名</th>
                        <th class="uk-width-small">所属名</th>
                        @endif
                        <th class="uk-width-small">メールアドレス</th>
                        <th class="uk-width-small">所属列</th>
                        <th class="uk-width-small">
                            <input type="checkbox" id="changedl"><label for="changedl">資料<br>ダウンロード許可</label>
                        </th>
                        <!-- 講習会の場合のみ適用 -->
                        <!--

                            @foreach ($events as $event)
                                @if ($event->category_type === 4)
                                    <th class="uk-width-small"><input type="checkbox" id="changedl"><label for="changedl">資料<br>ダウンロード許可</label></th>
                                    @break
                                @endif
                            @endforeach
-->
                        <th class="uk-width-small">参加費</th>
                        <th class="uk-width-small">請求書</th>
                        <th class="uk-width-small">ダウンロード</th>
                        <th class="uk-width-small">領収書</th>
                        <th class="uk-width-small">参加受付</th>
                        <th class="uk-width-small">更新日</th>
                        <th class="uk-width-small">振込予定日</th>
                        @if($inputs->count())
                        @foreach ($inputs as $item)
                        <th class="uk-width-medium">{{ $item->name }}</th>
                        @endforeach
                        @endif
                    </tr>
                </thead>
                <tbody>

                    {{-- TODO: 各項目検索機能追加 --}}
                    @foreach ($attendees as $attendee)
                    <tr>
                        <td class="text-nowrap">
                            <a href="{{ route('admin.attendees.edit', [$form['category_prefix'], $attendee->id]) }}" class="uk-button uk-button-small uk-button-primary">編集</a>
                            <form action="{{ route('admin.attendees.destroy', [$form['category_prefix'], $attendee->id]) }}" method="post" class="uk-inline">
                                @csrf
                                @method('delete')
                                <button type="submit" class="uk-button uk-button-small uk-button-danger" onclick="return confirm('削除します。よろしいですか？')">削除</button>
                            </form>
                        </td>
                        <td>{{ $attendee->event->name }}</td>
                        <td>{{ sprintf('%010d', $attendee->event_number) }}</td>
                        <td style="word-break : break-all;">{{ $attendee->user->login_id }}</td>
                        <td>{{ $attendee->user->sei }} {{ $attendee->user->mei }}</td>
                        <td>{{ $attendee->user->sei_kana }} {{ $attendee->user->mei_kana }}</td>
                        @if( $form['category_prefix'] == "kyosan")
                        <td>{{ $attendee->user->cp_name }}</td>
                        <td>{{ $attendee->user->busyo }}</td>
                        @endif
                        <td style='word-wrap: break-word;'>{{ $attendee->user->email }}</td>
                        <td style='word-wrap: break-word;'>{{ $attendee->user->busyo }}</td>
                        {{-- TODO: 支払い状況変更機能追加（編集ページで変更は可能） --}}
                        <td>
                            <input type="checkbox" id="doc_dl-{{$attendee->id}}" value="1" name="checkbox_list" {{$attendee->doc_dl===1 ? "checked" : ""}}>
                        </td>
                        <!--

                                @if ($event->category_type === 4)
                                    <td>
                                        <input type="checkbox" id="doc_dl-{{$attendee->id}}" value="1" name="checkbox_list" {{$attendee->doc_dl===1 ? "checked" : ""}} >
                                    </td>
                                @endif
-->
                        <td>
                            <select class="uk-select payment_status" id="payment_status-{{$attendee->id}}">
                                @foreach(config('pacd.payment') as $k=>$val)
                                <?php
                                $sel = "";
                                if ($k == $attendee->is_paid) $sel = "SELECTED";
                                ?>
                                <option value="{{$k}}" {{$sel}}>{{$val}}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <select id="is_enabled_invoice-{{$attendee->id}}" class="is_enabled_invoice uk-select uk-width-1-1" style="font-size:0.85%;">
                                @foreach( config('pacd.is_enabled_invoice') as $key=>$val)
                                <?php $select = ""; ?>
                                @if ($key == $attendee->is_enabled_invoice)
                                <?php $select = "selected"; ?>
                                @endif
                                <option value="{{$key}}" {{$select}}>{{$val}}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <a href="{{ route('member.join.pdf', $attendee->id."/".config('pacd.category.kosyukai.key'))."/".$attendee->user_id."/invoice" }}" class="uk-button uk-button-default" target=_blank>請求書</a>
                            <a href="{{ route('member.join.pdf', $attendee->id."/".config('pacd.category.kosyukai.key'))."/".$attendee->user_id."/recipe" }}" class="uk-button uk-button-default" target=_blank>領収書</a>
                        </td>
                        <td>
                            <select class="uk-select is_recepe_status" name="recipe_status" id="recipe_status-{{$attendee->id}}">
                                @foreach(config('pacd.recipe_status') as $k=>$val)
                                <?php
                                $sel = "";
                                if ($k == $attendee->recipe_status) $sel = "selected";
                                ?>
                                <option value="{{$k}}" {{$sel}}>{{$val}}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <select class="uk-select is_join_status" name="join_status" id="join_status-{{$attendee->id}}">
                                @foreach(config('pacd.join_status') as $k=>$val)
                                <?php
                                $sel = "";
                                if ($k == $attendee->join_status) $sel = "selected";
                                ?>
                                <option value="{{$k}}" {{$sel}}>{{$val}}</option>
                                @endforeach
                            </select>


                        </td>
                        <td>{{ date_format($attendee->updated_at, 'Y/m/d') }}</td>
                        <td>{{ preg_replace("/\-/","/",$attendee->paydate) }}</td>
                        @if($inputs->count())
                        @php $custom_data = $attendee->custom_form_data->keyBy('form_input_value_id') @endphp
                        @foreach ($inputs as $item)
                        <td align=left>
                            <?php $no = 0; ?>
                            @foreach ($item->values as $value)
                            @php // タイトルが振込予定日の時 固定で条件分岐
                            @endphp
                            @if(preg_match("/振込予定日/",$item->name))
                            @isset($custom_data[$value->id])
                            {{ $custom_data[$value->id]->data }}
                            <?php if ($no === 0) : ?> 月<?php endif; ?>
                                <?php if ($no === 1) : ?> 日<?php endif; ?>
                                    @endisset
                                    @else
                                    <div uk-grid class="uk-margin-remove-top uk-margin-padding-left">
                                        @isset($custom_data[$value->id])
                                        <div class="uk-width-2-3">
                                            {{ $custom_data[$value->id]->data }}
                                        </div>
                                        @if ($custom_data[$value->id]->data_sub)
                                        <div class="uk-width-1-3 " align=right>【{{ $custom_data[$value->id]->data_sub }}】</div>
                                        @endif
                                        @endisset
                                    </div>
                                    @endif
                                    <?php $no++; ?>
                                    @endforeach
                        </td>
                        @endforeach
                        @endif
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>


    </div>
</div>
@endsection

@section('footer')
<script>
    $('#attendees_table').tablesorter({
        headers: {
            0: {
                sorter: false
            },
            7: {
                sorter: false
            },
        }
    });

    $(".is_recepe_status").change(function() {
        var _url = location.pathname + "/recipeStatusUpdate";
        _url = _url.replace("//recipeStatusUpdate", "/recipeStatusUpdate");
        var _id = $(this).attr("id").split("-")[1];
        var _val = $(this).val();
        var postData = {
            "id": _id,
            "recipe_status": _val
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
            .then(result => {})
            .catch(error => {
                console.log('データ更新エラー');
                console.log(error.status);
            });
        return false;
    });
    $(".is_join_status").change(function() {
        var _url = location.pathname + "/joinStatusUpdate";
        _url = _url.replace("//joinStatusUpdate", "/joinStatusUpdate");
        var _id = $(this).attr("id").split("-")[1];
        var _val = $(this).val();
        var postData = {
            "id": _id,
            "join_status": _val
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
            .then(result => {})
            .catch(error => {
                console.log('データ更新エラー');
                console.log(error.status);
            });
        return false;
    });

    $(".is_enabled_invoice").change(function() {
        var _currentPath = $("#currentPath").val();
        var _id = $(this).attr("id").split("-")[1];
        var _val = $(this).val();
        var postData = {
            "id": _id,
            "is_enabled_invoice": _val
        };
        $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: _currentPath,
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
        return false;
    });

    // checkbox関係
    // 一括選択チェックボックス
    const changeDl = document.querySelector("#changedl");
    // その他
    const checkbox = document.getElementsByName("checkbox_list");

    //一括選択チェックボックスクリック時
    const changeDlCk = (bool) => {
        for (let i = 0; i < checkbox.length; i++) {
            checkbox[i].checked = bool;
        }
    };

    //個々のチェックボックスクリック時
    const checkAll = () => {
        let count = 0;
        for (let i = 0; i < checkbox.length; i++) {
            if (checkbox[i].checked) {
                count += 1;
            }
        }

        if (checkbox.length === count) {
            changeDl.checked = true;
        } else {
            changeDl.checked = false;
        }

    };


    changeDl.addEventListener("change", () => {
        changeDlCk(changeDl.checked);
    });


    for (let i = 0; i < checkbox.length; i++) {
        checkbox[i].addEventListener("change", checkAll);
    };

    //checkboxのajax
    $("input[name='checkbox_list']").change(function() {
        var _currentPath_check = $("#currentPath_check").val();
        var _id = $(this).attr("id").split("-")[1];
        var _val = $(this).val();
        if (!($(`input[id='doc_dl-${_id}']`).prop("checked"))) {
            _val = 0;
        }
        var postData = {
            "id": _id,
            "checkbox_list": _val
        };

        $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: _currentPath_check,
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
        return false;

    });

    //一括チェック
    $("input[id='changedl']").change(function() {
        var _category = "<?= (isset($event->category_type)) ? $event->category_type : 0 ?>";
        var _search = "<?= $search ?>";
        var _code = "<?= $code ?>";
        var _currentPath_ac = $("#currentPath_ac").val();
        if ($("input[id='changedl']").prop("checked")) {
            var bool = true;
        } else {
            bool = false;
        }

        var postData = {
            "search": _search,
            "code": _code,
            "category": _category,
            "checked": bool
        };
        $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: _currentPath_ac,
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


        return false;

    });
    // 画面遷移時
    // 全てのチェックボックスにチェックが入っていればallチェックにもチェック
    if (<?= $array ?> !== "0") {
        $("input[id ='changedl']").ready(function() {

            var _all_attendees = "<?= $array ?>";
            _all_attendees = _all_attendees.split(',');
            let count = 0;

            for (let i = 0; i < _all_attendees.length; i++) {
                if (_all_attendees[i] === '1') {
                    count++;
                }
            }

            if (count === _all_attendees.length) {
                changeDl.checked = true;
            }
        });
    }

    function invoiceStatus(status) {
        var _code = $('select[name="code"]').val();
        var _invoiceStatus = $("#invoiceStatus").val();
        var postData = {};
        console.log(_invoiceStatus + "/" + status + "/" + _code);
        $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: _invoiceStatus + "/" + status + "/" + _code,
                type: 'GET',
                data: postData,
                timeout: 5000,
            })
            .then(result => {
                $(".is_enabled_invoice").val(status);
                alert("データの更新をおこないました。");
            })
            .catch(error => {
                console.log('データ更新エラー');
                console.log(error.status);
            });
        return false;

    }
</script>
@endsection