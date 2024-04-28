@extends('layouts.admin')

@section('title', $title)

@section('breadcrumb')
    @parent
    @foreach ($breadcrumbs as $breadcrumb)
        <li><a href="{{ $breadcrumb['url'] }}">{{ $breadcrumb['title'] }}</a></li>
    @endforeach
    <li><span>登録</span></li>
@endsection

@section('content')

    <div class="uk-section-small">
        <div class="uk-container uk-container-large">
            <form method="POST" action="{{ route("admin.attendees.store", [$form['category_prefix']]) }}" class="uk-form-horizontal" id="attendee_register_form">
                @csrf
                <fieldset class="uk-fieldset">

                    <div class="uk-margin">
                        <label for="event_id" class="uk-form-label uk-text-left">
                        @if($category_prefix == "kyosan")
                            イベント名
                        @else
                            参加イベント
                        @endif
                            <span class="uk-label uk-label-danger uk-margin-small-left">必須</span>
                        </label>
                        <div class="uk-form-controls">
                            <select name="event_id" id="event_id" class="uk-select">
                                @foreach ($events as $event)
                                    <option value="{{ $event->id }}" @if(old('event_id') == $event->id) selected @endif>{{ $event->name }}</option>
                                @endforeach
                            </select>
                            @error("event_id")
                                <div class="uk-text-danger">
                                    <p>{{ $message }}</p>
                                </div>
                            @enderror
                        </div>
                    </div>

                    <div class="uk-margin">
                        <label for="login_id" class="uk-form-label uk-text-left">
                            ログインID<span class="uk-label uk-label-danger uk-margin-small-left">必須</span>
                        </label>
                        <div class="uk-form-controls">
                            <input type="text" class="uk-input" name="login_id" id="login_id" value="{{ old('login_id') }}">
                            <div class="uk-text-danger" id="error_login_id">
                                @error("login_id")
                                    <p>{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>





                    <div class="uk-clearfix">
                        <label class="uk-form-label uk-text-left">会員番号</label>
                        <div class="uk-form-controls uk-form-controls-text" id="type_number">&nbsp;</div>
                    </div>
                    <div class="uk-clearfix">
                        <label class="uk-form-label uk-text-left">氏名</label>
                        <div class="uk-form-controls uk-form-controls-text" id="sei_mei">&nbsp;</div>
                    </div>

                    <div class="uk-clearfix">
                        <label class="uk-form-label uk-text-left">氏名（ふりがな）</label>
                        <div class="uk-form-controls uk-form-controls-text" id="sei_mei_kana">&nbsp;</div>
                    </div>
                    <div class="uk-clearfix">
                        <label class="uk-form-label uk-text-left">法人名</label>
                        <div class="uk-form-controls uk-form-controls-text" id="cp_name">&nbsp;</div>
                    </div>
                    <div class="uk-clearfix">
                        <label class="uk-form-label uk-text-left">所属</label>
                        <div class="uk-form-controls uk-form-controls-text" id="busyo">&nbsp;</div>
                    </div>
                    <div class="uk-clearfix">
                        <label class="uk-form-label uk-text-left">メールアドレス</label>
                        <div class="uk-form-controls uk-form-controls-text" id="email">&nbsp;</div>
                    </div>
                    <div class="uk-clearfix">
                        <label class="uk-form-label uk-text-left">電話番号</label>
                        <div class="uk-form-controls uk-form-controls-text" id="tel">&nbsp;</div>
                    </div>



                    {{-- カスタムインプット項目 --}}
                    @if($inputs->count())
                        @include('elements.forms.custom_inputs', [$inputs, 'custom_data' => []])
                    @endif

                    @if($category_prefix == "kyosan")
                        <div class="uk-margin">
                            <label class="uk-form-label">振込予定日</label>
                            <div class="uk-form-controls uk-form-controls-text">
                                <input type="date" name="paydate" class="uk-input w-50 uk-width-1-5">
                            </div>
                        </div>
                    @endif
                    <div class="uk-margin" id="join_box">
                        <label class="uk-form-label">参加料金</label>
                        <div class="uk-form-controls uk-form-controls-text">
                                @foreach ($events as $event)
                                    @php $joins = $event->event_joins->where('join_status', 1)->where('status', 1) @endphp
                                    <div id="joins_{{ $event->id }}" class="joins @if(!$loop->first) uk-hidden @endif">
                                        @if ($joins->count())
                                            @foreach ($joins as $join)
                                                <label><input type="checkbox" name="event_join_id[{{$join->id}}]" value="{{ $join->id }}" class="uk-checkbox" @if (old('event_join_id') == $join->id) checked @endif>
                                                    {{ $join->join_name }}：{{ number_format($join->join_price) }}円{{ ($join->join_fee) ? "（懇談会費：" . number_format($join->join_fee) . "円）" : '' }}
                                                </label>
                                                <br />
                                            @endforeach
                                        @else
                                            <span class="uk-text-meta">イベントに関連する参加費データがありません。</span>
                                        @endif
                                    </div>
                                @endforeach
                            @error('event_join_id')
                                <div class="uk-text-danger">
                                    <p>{{ $message }}</p>
                                </div>
                            @enderror
                        </div>
                    </div>

                    <div class="uk-margin">
                        <label class="uk-form-label">参加費支払い状況</label>
                        <div class="uk-form-controls uk-form-controls-text">
                            <label><input type="radio" name="is_paid" value="0" class="uk-radio"  @if(old('is_paid') == 0) checked @endif> 未払い</label>
                            <label><input type="radio" name="is_paid" value="1" class="uk-radio"  @if(old('is_paid') == 1) checked @endif> 支払い済み</label>
                            @error('is_paid')
                                <div class="uk-text-danger">
                                    <p>{{ $message }}</p>
                                </div>
                            @enderror
                        </div>
                    </div>

                    <div class="uk-margin">
                        <label class="uk-form-label">請求書・領収書ダウンロード</label>
                        <div class="uk-form-controls uk-form-controls-text">
                            <?php
                                $chk0 = "";
                                $chk1 = "";

                                if(is_null(old('is_enabled_invoice'))){
                                    $chk0 = "";
                                    $chk1 = "CHECKED";
                                }else
                                if(old('is_enabled_invoice') == "0"){
                                    $chk0 = "CHECKED";
                                    $chk1 = "";
                                }else
                                if(old('is_enabled_invoice') == "1"){
                                    $chk0 = "";
                                    $chk1 = "CHECKED";
                                }
                            ?>
                            <label><input type="radio" name="is_enabled_invoice" value="0" class="uk-radio"  {{$chk0}}> ダウンロード不可</label>
                            <label><input type="radio" name="is_enabled_invoice" value="1" class="uk-radio"  {{$chk1}}> ダウンロード可</label>
                            @error('is_enabled_invoice')
                                <div class="uk-text-danger">
                                    <p>{{ $message }}</p>
                                </div>
                            @enderror
                        </div>
                    </div>

                    <div class="uk-margin">
                        <label class="uk-form-label">参加登録メール送信</label>
                        <div class="uk-form-controls uk-form-controls-text">
                            <label><input name="send_mail" class="uk-checkbox" type="checkbox" value="1" @if(old("send_mail")) checked @endif> 送信する</label>
                            @error('send_mail')
                                <div class="uk-text-danger">
                                    <p>不正な値が入力されました。</p>
                                </div>
                            @enderror
                        </div>
                    </div>

                    <div class="uk-section-small">
                        <button type="submit" class="uk-button uk-button-primary">登録</button>
                    </div>

                </fieldset>
            </form>
        </div>
    </div>
@endsection

@section('footer')
    <script>
        const GET_USER_URL = "{{ route('admin.attendees.get.user_attendee.ajax', '') }}";
        const CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

        let event_id_input = $("select#event_id");
        let login_id_input = $("input#login_id");
        let jqxhr = null;

        // 会員情報セット関数
        let setUserInfo = function(name, name_kana, email,type_number,cp_name,busyo,tel) {
            $("#error_login_id").html('');
            $("#sei_mei").html(name);
            $("#sei_mei_kana").html(name_kana);
            $("#email").html(email);
            $("#type_number").html(type_number);
            $("#cp_name").html(cp_name);
            $("#busyo").html(busyo);
            $("#tel").html(tel);
        }

        // 会員情報取得関数
        let getUser = function() {
            // 前回通信キャンセル
            if (jqxhr != null && jqxhr.readyState != 4) {
                jqxhr.abort();
            }
            if (login_id_input.val()) {
                jqxhr = $.ajax({
                    headers: { 'X-CSRF-TOKEN': CSRF_TOKEN },
                    type: 'POST',
                    timeout: 5000,
                    url: GET_USER_URL + '/' + login_id_input.val() + '/' + event_id_input.val(),
                })
                .done(function(result) {
                    // console.log(result);
                    if (result) {
                        setUserInfo(
                            result.sei + ' ' + result.mei,
                            result.sei_kana + ' ' + result.mei_kana,
                            result.email,
                            result.type_number,
                            result.cp_name,
                            result.busyo,
                            result.tel,
                        );
                        if (result.attendee_id != false) {
                            $("#error_login_id").append('<p>選択された会員はすでに参加登録されています。</p>');
                        }
                    }
                })
                .fail(function(error) {
                    console.log(error.statusText);
                });
            }
        }

        // 参加料金表示切替
        let toggleJoins = function() {
            $(".joins").addClass('uk-hidden');
            $("#joins_" + event_id_input.val()).removeClass('uk-hidden');
        }

        getUser();
        toggleJoins();

        // 会員ID入力欄に入力した時
        login_id_input.on({
            'keyup': function(e) {
                if (e.keyCode != 13) {
                    setUserInfo('&nbsp;', '&nbsp;', '&nbsp;', false);
                    getUser();
                }
            },
            'keypress': function(e) {
                if (e.keyCode == 13) return false;  // enterキーでのsubmit無効
            }
        });

        // イベントを変更したとき
        event_id_input.change(function() {
            toggleJoins();
            getUser();
        });
    </script>
@endsection
