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
            <form method="POST" action="{{ route("admin.presenters.store", [$form['category_prefix']]) }}" class="uk-form-horizontal" id="presenter_register_form"  enctype="multipart/form-data">
                @csrf
                <fieldset class="uk-fieldset">

                    <div class="uk-margin">
                        <label for="event_id" class="uk-form-label uk-text-left">
                            参加イベント<span class="uk-label uk-label-danger uk-margin-small-left">必須</span>
                        </label>
                        <div class="uk-form-controls">
                            <select name="event_id" id="event_id" class="uk-select">
                                @foreach ($events as $event)
                                    <?php
                                        $sel = "";
                                        if(old('event_id') ){
                                            if(old('event_id') == $event->id) $sel = "SELECTED";
                                        }else{
                                            if($event_id == $event->id) $sel = "SELECTED";
                                        }
                                    ?>
                                    <option value="{{ $event->id }}" {{$sel}} >{{ $event->name }}</option>
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
                            <input type="text" class="uk-input" name="login_id" id="login_id" autocomplete="off" value="{{ old('login_id', Request::get('login_id')) }}">
                            <div class="uk-text-danger" id="error_login_id">
                                @error("login_id")
                                    <p>{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="uk-form-label uk-text-left">参加者番号</label>
                        <div class="uk-form-controls uk-form-controls-text" id="attendee_id">
                            &nbsp;
                        </div>
                        <input type="hidden" name="attendee_id" value="{{ old('attendee_id') }}">
                    </div>

                    <div>
                        <label class="uk-form-label uk-text-left">氏名</label>
                        <div class="uk-form-controls uk-form-controls-text" id="sei_mei">&nbsp;</div>
                    </div>

                    <div>
                        <label class="uk-form-label uk-text-left">氏名（ふりがな）</label>
                        <div class="uk-form-controls uk-form-controls-text" id="sei_mei_kana">&nbsp;</div>
                    </div>

                    <div>
                        <label class="uk-form-label uk-text-left">メールアドレス</label>
                        <div class="uk-form-controls uk-form-controls-text" id="email">&nbsp;</div>
                    </div>

                    {{-- カスタムインプット項目 --}}
                    @if($inputs->count())
                        @include('elements.forms.custom_inputs', [$inputs, 'custom_data' => []])
                    @endif

                    <div class="uk-margin">
                        <label class="uk-form-label uk-text-left" for="number">
                            発表番号<span class="uk-label uk-label-danger uk-margin-small-left">必須</span>
                        </label>
                        <div class="uk-form-controls">
                            <input id="number" type="text" class="uk-input " name="number" value="{{ old('number') }}" required>
                            @error('number')
                                <div class="uk-text-danger uk-text-uppercase">
                                    <p>{{ $message }}</p>
                                </div>
                            @enderror
                        </div>
                    </div>
                    @if($form['category_prefix'] == "touronkai")
                        <div class="uk-margin">
                            <label class="uk-form-label uk-text-left" for="description">
                                題目
                            </label>
                            <div class="uk-form-controls">
                                <textarea name="daimoku" class="uk-textarea" rows="3">{{ old() ? old('description') : @$presentation->daimoku }}</textarea>
                                @error('daimoku')
                                    <div class="uk-text-danger uk-text-uppercase">
                                        <p>{{ $message }}</p>
                                    </div>
                                @enderror
                            </div>
                        </div>
                        <div class="uk-margin">
                            <label class="uk-form-label uk-text-left" for="description">
                                発表者/所属
                            </label>
                            <div class="uk-form-controls">
                            <p>ポスター発表者は発表者1、所属1へ記入。所属1は先頭に「○」をつけること（記入例）○高分子大学</p>
                                <table class="uk-table" >
                                    <tr>
                                        <th colspan=2>発表者</th>
                                        <th colspan=2>所属</th>
                                    </tr>
                                    @for($i=1;$i<=6;$i++)
                                    <tr>
                                        <td>発表者{{$i}}</td>
                                        <td>
                                            <input type="text" class="uk-input " name="enjya{{$i}}" value="" >
                                        </td>
                                        <td>所属{{$i}}</td>
                                        <td>
                                            <input type="text" class="uk-input " name="syozoku{{$i}}" value="" >
                                        </td>
                                    </tr>
                                    @endfor
                                </table>
                            <p>発表者、所属が書ききれない場合は、こちらに記入ください。氏名、所属の順で記入。</p>
                            <textarea name="enjya_other" class="uk-textarea" rows="3" placeholder="記入例）氏名、所属の順で記入。">{{ old() ? old('enjya_other') : @$presentation->enjya_other }}</textarea>
                            </div>
                        </div>
                        {{--
                        <div class="uk-margin">
                            <label class="uk-form-label uk-text-left" for="description">
                                発表演者
                            </label>
                            <div class="uk-form-controls">
                                <textarea name="enjya" class="uk-textarea" rows="3" placeholder="記入例）〇山田 一郎,　鈴木次郎　（発表者に〇印　区切りはカンマ「,」">{{ old() ? old('enjya') : @$presentation->enjya }}</textarea>
                                @error('enjya')
                                    <div class="uk-text-danger uk-text-uppercase">
                                        <p>{{ $message }}</p>
                                    </div>
                                @enderror
                            </div>
                        </div>
                        <div class="uk-margin">
                            <label class="uk-form-label uk-text-left" for="description">
                                所属
                            </label>
                            <div class="uk-form-controls">
                                <textarea name="syozoku" class="uk-textarea" rows="3" placeholder="記入例）□□大,　△△大院工　等 　略称で記入
法人格は株式会社・有限会社・財団法人・社団法人・独立行政法人・国立大学法人等は省略
（区切りはカンマ「,」　発表者所属に○印）
    ">{{ old() ? old('syozoku') : @$presentation->syozoku }}</textarea>
                                @error('syozoku')
                                    <div class="uk-text-danger uk-text-uppercase">
                                        <p>{{ $message }}</p>
                                    </div>
                                @enderror
                            </div>
                        </div>
                        --}}
                        <div class="uk-margin">
                            <label class="uk-form-label uk-text-left" for="description">
                                発表概要
                            </label>
                            <div class="uk-form-controls">
                                <textarea name="gaiyo" class="uk-textarea" rows="3">{{ old() ? old('gaiyo') : @$presentation->gaiyo }}</textarea>
                                @error('gaiyo')
                                    <div class="uk-text-danger uk-text-uppercase">
                                        <p>{{ $message }}</p>
                                    </div>
                                @enderror
                            </div>
                        </div>
                    @else
                        <div class="uk-margin">
                            <label class="uk-form-label uk-text-left" for="description">
                                講演内容
                            </label>
                            <div class="uk-form-controls">
                                <textarea name="description" class="uk-textarea" rows="5">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="uk-text-danger uk-text-uppercase">
                                        <p>{{ $message }}</p>
                                    </div>
                                @enderror
                            </div>
                    </div>
                    @endif
                    <div class="uk-margin">
                        <label class="uk-form-label uk-text-left" for="proceeding">

                            @if($form['category_prefix'] == "reikai")
                            配布資料1
                            @elseif($form['category_prefix'] == "kosyukai")
                            配布資料1
                            @elseif($form['category_prefix'] == "touronkai")
                            発表要旨（PDF形式）
                            @else
                            講演要旨
                            @endif
                        </label>
                        <div class="uk-form-controls">
                            <div class="uk-width-1-1" uk-form-custom="target: true">
                                <input type="file" name="file[proceeding]">
                                <input class="uk-input uk-width-1-1" type="text" placeholder="ファイルを選択" disabled>
                            </div>
                            @error('file.proceeding')
                                <div class="uk-text-danger uk-text-uppercase">
                                    <p>{{ $message }}</p>
                                </div>
                            @enderror
                        </div>
                    </div>

                    <div class="uk-margin">
                        <label class="uk-form-label uk-text-left" for="flash">
                            @if($form['category_prefix'] == "reikai")
                            配布資料2
                            @elseif($form['category_prefix'] == "kosyukai")
                            配布資料2
                            @elseif($form['category_prefix'] == "touronkai")
                            プレゼン資料（PDF形式）
                            @else
                            <span id="name1">フラッシュプレゼンテーションファイル</span>
                            @endif
                        </label>
                        <div class="uk-form-controls">
                            <div class="uk-width-1-1" uk-form-custom="target: true">
                                <input type="file" name="file[flash]">
                                <input class="uk-input uk-width-1-1" type="text" placeholder="ファイルを選択" disabled>
                            </div>
                            @error('file.flash')
                                <div class="uk-text-danger uk-text-uppercase">
                                    <p>{{ $message }}</p>
                                </div>
                            @enderror
                        </div>
                    </div>

                    <div class="uk-margin">
                        <label class="uk-form-label uk-text-left" for="poster">
                            @if($form['category_prefix'] == "reikai")
                            配布資料3
                            @elseif($form['category_prefix'] == "kosyukai")
                            配布資料3
                            @elseif($form['category_prefix'] == "touronkai")
                            ポスター・配布資料
                            @else
                            <span id="name2">ポスター・配布資料等</span>
                            @endif
                        </label>
                        <div class="uk-form-controls">
                            <div class="uk-width-1-1" uk-form-custom="target: true">
                                <input type="file" name="file[poster]">
                                <input class="uk-input uk-width-1-1" type="text" placeholder="ファイルを選択" disabled>
                            </div>
                            @error('file.poster')
                                <div class="uk-text-danger uk-text-uppercase">
                                    <p>{{ $message }}</p>
                                </div>
                            @enderror
                        </div>
                    </div>

                    <div class="uk-margin">
                        <label class="uk-form-label">講演登録メール送信</label>
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
        let setUserInfo = function(name, name_kana, email,event_number, attendee_id) {
            $("#error_login_id").html('');
            if (attendee_id) {
                $("input[name='attendee_id']").val(attendee_id);
                $("#attendee_id").html(event_number);
            } else {
                $("input[name='attendee_id']").val('');
                $("#attendee_id").html('&nbsp;');
            }
            $("#sei_mei").html(name);
            $("#sei_mei_kana").html(name_kana);
            $("#email").html(email);
        }
        // 第28回高分子分析討論会のみ対応表示名変更
        let changeReportName = function(id){
            if(id == 238){
                $("#name1").html("プレゼンテーション資料");
                $("#name2").html("配布資料");
            }else{
                $("#name1").html("プレゼンテーションファイル");
                $("#name2").html("ポスター・配布資料等");
            }
        }
        changeReportName(event_id_input.val());

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
                     console.log(result);
                    if (result) {
                        var num =  result.event_number;
                        var _num = ( '0000000000' + num ).slice( -10 );
                        setUserInfo(
                            result.sei + ' ' + result.mei,
                            result.sei_kana + ' ' + result.mei_kana,
                            result.email,
                            _num,
                            result.attendee_id
                        );
                        if (result.attendee_id == false) {
                            $("#error_login_id").append('<p>選択された会員は参加登録されていません。先に参加登録を行ってください。</p>');
                        }
                    }
                })
                .fail(function(error) {
                    console.log(error.statusText);
                });
            }
        }

        getUser();

        // 会員ID入力欄に入力した時
        login_id_input.on({
            'keyup': function(e) {
                if (e.keyCode != 13) {
                    setUserInfo('&nbsp;', '&nbsp;', '&nbsp;', '&nbsp;');
                    getUser();
                }
            },
            'keypress': function(e) {
                if (e.keyCode == 13) return false;  // enterキーでのsubmit無効
            }
        });

        // イベントを変更したとき
        event_id_input.change(function() {
            getUser();
            changeReportName($("#event_id").val());
        });
    </script>
@endsection
