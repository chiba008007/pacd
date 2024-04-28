        <fieldset class="uk-fieldset">
            @if (isCurrent('admin.*'))
            <div class="uk-form-horizontal">
                <div class="uk-margin">
                    <label class="uk-form-label uk-text-left">会員区分<span class="uk-label uk-label-danger uk-margin-small-left">必須</span></label>
                    <div class="uk-form-controls">
                        <select name="type" class="uk-select" id="type_number_select">
                            @foreach (config('pacd.user.type') as $type => $disp)
                            <?php
                            $sel = "";
                            if (isset($user->type) && $user->type == $type) $sel = "selected";
                            if (old('type') == $type) $sel = "selected";
                            ?>
                            <option value="{{ $type }}" {{$sel}}>{{ $disp }}</option>
                            @endforeach
                        </select>
                        @error('type')
                        <div class="uk-text-danger">
                            <p>{{ $message }}</p>
                        </div>
                        @enderror
                    </div>
                </div>
            </div>
            @endif
            @if(isset($isprofile) && $isprofile)
            <div class="uk-form-horizontal">
                <div class="uk-margin">
                    <label class="uk-form-label uk-text-left">会員区分</label>
                    <div class="uk-form-controls">
                        <input type="hidden" id="type_number_select" value="{{$user->type}}" />
                        {{config('pacd.user.type')[$user->type]}}
                    </div>
                </div>
            </div>
            @endif
            {{--会員登録時会員区分を「法人会員」または「会員外」を選択できるようにする--}}
            @if(isCurrent('register.*'))
            <div class="uk-form-horizontal">
                <div class="uk-margin">
                    <label class="uk-form-label uk-text-left">会員区分<span class="uk-label uk-label-danger uk-margin-small-left">必須</span></label>
                    <div class="uk-form-controls">
                        <select name="type" class="uk-select" form="user_register_form" id="type_number_select">
                            @foreach (config('pacd.user.type') as $type => $disp)
                            <?php
                            $sel = "";
                            if (isset($user->type) && $user->type == $type) $sel = "selected";
                            if (old('type') == $type) $sel = "selected";
                            //if($type == 1 || $type == 3):
                            //例会の時は法人会員のみ
                            //技術講習会は非会員、法人会員のみ
                            if (
                                (request()->input("key") == "" && $type == 1 || $type == 3 || $type == 5 || $type == 6) ||
                                (request()->input("key") == 2 && $type == 3) ||
                                (request()->input("key") == 4 && ($type == 1 || $type == 3)) ||
                                (request()->input("key") == 3 && ($type == 1 || $type == 3 || $type == 5 || $type == 6))
                            ) :
                            ?>
                                <option value="{{ $type }}" {{$sel}}>{{ $disp }}</option>
                            <?php endif; ?>
                            @endforeach
                        </select>
                        @error('type')
                        <div class="uk-text-danger">
                            <p>{{ $message }}</p>
                        </div>
                        @enderror
                    </div>
                </div>
            </div>
            @endif

            <div class="hidden" id="type_number_disp">
                <div class="uk-margin ">
                    <label class="uk-form-label uk-text-left" for="login_id">
                        <span class="hidden houjin" id="tanto_type_number"></span>
                        <span class="uk-label uk-label-danger uk-margin-small-left">必須</span>
                    </label>
                    <div class="uk-form-controls">
                        <?php
                        $type_number = "";
                        if (isset($user->type_number)) $type_number = $user->type_number;
                        if (old('type_number')) $type_number = old('type_number');
                        ?>
                        <input id="type_number" type="text" class="uk-input " required name="type_number" value="{{ $type_number }}">
                        @error('type_number')
                        <div class="uk-text-danger uk-text-uppercase">
                            <p>{{ $errors->first('type_number') }}</p>
                        </div>
                        @enderror
                        <div id="houjinnumber">
                            すでに法人会員にご入会されており会員窓口様が法人会員番号をお忘れの場合は<a href="mailto:infopacd＠pacd.jp?subject=法人会員番号お問い合わせメール">こちら</a>にメールでお問い合わせ
                        </div>
                    </div>
                </div>
            </div>

            <div class="hidden" id="cp_name_disp">
                <div class="uk-margin ">
                    <label class="uk-form-label uk-text-left" for="login_id">
                        <span class="hidden houjin">法人名</span>
                        <span class="uk-label uk-label-danger uk-margin-small-left">必須</span>
                    </label>
                    <div class="uk-form-controls">
                        <?php
                        $cp_name = "";
                        if (isset($user->cp_name)) $cp_name = $user->cp_name;
                        if (old('cp_name')) $cp_name = old('cp_name');
                        ?>
                        <input id="cp_name" type="text" required class="uk-input " name="cp_name" value="{{ $cp_name }}" placeholder="〇〇株式会社">
                        @error('cp_name')
                        <div class="uk-text-danger uk-text-uppercase">
                            <p>{{ $errors->first('cp_name') }}</p>
                        </div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="uk-margin">
                <label class="uk-form-label uk-text-left" for="login_id">
                    ログインID<span class="uk-label uk-label-danger uk-margin-small-left">必須</span>
                </label>
                <div class="uk-form-controls">
                    <?php
                    $login_id = "";
                    if (isset($user->login_id)) {
                        $login_id = $user->login_id;
                    }
                    if (old('login_id')) {
                        $login_id = old('login_id');
                    }
                    ?>
                    <input id="login_id" type="text" class="uk-input " name="login_id" value="{{ $login_id }}" required autocomplete="login_id" autofocus placeholder="4～50文字の半角英数字">
                    @error('login_id')
                    <div class="uk-text-danger uk-text-uppercase">
                        <p>{{ $message }}</p>
                    </div>
                    @enderror
                </div>
            </div>

            <div class="uk-margin">
                <label class="uk-form-label uk-text-left" for="sei">
                    <span id="tantoname"></span><span class="uk-label uk-label-danger uk-margin-small-left">必須</span>
                </label>
                <div class="uk-form-controls">
                    <div class="uk-grid-small" uk-grid>
                        <div class="uk-width-1-2">
                            <?php
                            $sei = "";
                            if (isset($user->sei)) {
                                $sei = $user->sei;
                            }
                            if (old('sei')) {
                                $sei = old('sei');
                            }
                            ?>
                            <input id="sei" type="sei" class="uk-input " name="sei" value="<?= $sei ?>" autocomplete="sei" placeholder="姓" required>
                        </div>
                        <div class="uk-width-1-2">
                            <?php
                            $mei = "";
                            if (isset($user->mei)) {
                                $mei = $user->mei;
                            }
                            if (old('mei')) {
                                $mei = old('mei');
                            }
                            ?>
                            <input id="mei" type="mei" class="uk-input " name="mei" value="{{$mei}}" autocomplete="mei" placeholder="名" required>
                        </div>
                    </div>
                    @if ($errors->has('sei') || $errors->has('mei'))
                    <div class="uk-text-danger uk-text-uppercase">
                        @if ($errors->has('sei'))
                        <p class="uk-margin-remove">{{ $errors->first('sei') }}</p>
                        @elseif ($errors->has('mei'))
                        <p class="uk-margin-remove">{{ $errors->first('mei') }}</p>
                        @endif
                    </div>
                    @endif
                </div>
            </div>

            <div class="uk-margin">
                <label class="uk-form-label uk-text-left" for="sei_kana">
                    <span id="tantonamekana"></span><span class="uk-label uk-label-danger uk-margin-small-left">必須</span>
                </label>
                <div class="uk-form-controls">
                    <div class="uk-grid-small" uk-grid>
                        <div class="uk-width-1-2">
                            <?php
                            $sei_kana = "";
                            if (isset($user->sei_kana)) {
                                $sei_kana = $user->sei_kana;
                            }
                            if (old('sei_kana')) {
                                $sei_kana = old('sei_kana');
                            }
                            ?>
                            <input id="sei_kana" type="sei_kana" class="uk-input " name="sei_kana" value="{{$sei_kana}}" autocomplete="sei_kana" placeholder="せい" required>
                        </div>
                        <div class="uk-width-1-2">
                            <?php
                            $mei_kana = "";
                            if (isset($user->mei_kana)) {
                                $mei_kana = $user->mei_kana;
                            }
                            if (old('mei_kana')) {
                                $mei_kana = old('mei_kana');
                            }
                            ?>
                            <input id="mei_kana" type="mei_kana" class="uk-input " name="mei_kana" value="{{$mei_kana}}" autocomplete="mei_kana" placeholder="めい" required>
                        </div>
                    </div>
                    @if ($errors->has('sei_kana') || $errors->has('mei_kana'))
                    <div class="uk-text-danger uk-text-uppercase">
                        @if ($errors->has('sei_kana'))
                        <p class="uk-margin-remove">{{ $errors->first('sei_kana') }}</p>
                        @elseif ($errors->has('mei_kana'))
                        <p class="uk-margin-remove">{{ $errors->first('mei_kana') }}</p>
                        @endif
                    </div>
                    @endif
                </div>
            </div>

            <div class="uk-margin">
                <label class="uk-form-label uk-text-left" for="email">
                    <span id="tantomail"></span>
                    <span class="uk-label uk-label-danger uk-margin-small-left">必須</span>
                </label>
                <div class="uk-form-controls">
                    <?php
                    $email = "";
                    if (isset($user->email)) {
                        $email = $user->email;
                    }
                    if (old('email')) {
                        $email = old('email');
                    }
                    ?>
                    <input id="email" type="email" class="uk-input " name="email" value="{{$email}}" autocomplete="email" required>
                    @error('email')
                    <div class="uk-text-danger uk-text-uppercase">
                        <p>{{ $message }}</p>
                    </div>
                    @enderror
                </div>
            </div>

            <div class="hidden" id="open_address_flag_disp">
                <label class="uk-form-label">メールアドレス公開</label>
                <div class="uk-form-controls uk-form-controls-text">
                    <?php
                    $chk0 = "";
                    $chk1 = "";

                    if (isset($user->open_address_flag) && $user->open_address_flag == 0) $chk0 = "checked";


                    if (isset($user->open_address_flag) && $user->open_address_flag == 1) $chk1 = "checked";
                    if ((old('open_address_flag') == 1)) $chk1 = "checked";
                    if (is_null(old('open_address_flag'))) $chk0 = "checked";
                    ?>
                    <label><input type="radio" name="open_address_flag" value="0" class="uk-radio" {{$chk0}}> 不可</label>
                    <label><input type="radio" name="open_address_flag" value="1" class="uk-radio" {{$chk1}}> 可</label>
                    <div>
                        <small>本研究懇談会の会員に限定してメールアドレスを公開可能な方はチェックをお願い致します</small>
                    </div>
                    @error('open_address_flag')
                    <div class="uk-text-danger">
                        <p>{{ $message }}</p>
                    </div>
                    @enderror
                </div>

            </div>

            <div class="uk-margin">
                <label class="uk-form-label uk-text-left" for="tel">
                    <span id="tantotel"></span>
                    <span class="uk-label uk-label-danger uk-margin-small-left">必須</span>
                </label>
                <div class="uk-form-controls">
                    <?php
                    $tel = "";
                    if (isset($user->tel)) {
                        $tel = $user->tel;
                    }
                    if (old('tel')) {
                        $tel = old('tel');
                    }
                    ?>
                    <input id="tel" type="text" class="uk-input " name="tel" value="{{$tel}}" autocomplete="tel" required placeholder="例）012-345-6789（半角数字・ハイフンあり）">
                    @error('tel')
                    <div class="uk-text-danger uk-text-uppercase">
                        <p>{{ $message }}</p>
                    </div>
                    @enderror
                </div>
            </div>
            <div class="uk-margin">
                <label class="uk-form-label uk-text-left" for="tel">
                    <span id="tantofax">FAX</span>
                </label>
                <div class="uk-form-controls">
                    <?php
                    $fax = "";
                    if (isset($user->fax)) {
                        $fax = $user->fax;
                    }
                    if (old('fax')) {
                        $fax = old('fax');
                    }
                    ?>
                    <input id="fax" type="text" class="uk-input " name="fax" value="{{$fax}}" autocomplete="fax" placeholder="例）012-345-6789（半角数字・ハイフンあり）">

                </div>
            </div>

            <div class="uk-margin">
                <script src="https://ajaxzip3.github.io/ajaxzip3.js" charset="UTF-8"></script>
                <label class="uk-form-label uk-text-left" for="postcode">
                    <span id="postcodetext">郵便番号</span>
                    <span class="uk-label uk-label-danger uk-margin-small-left">必須</span>
                </label>
                <div class="uk-form-controls">
                    <?php
                    $postcode = "";
                    if (isset($user->postcode)) {
                        $postcode = $user->postcode;
                    }
                    if (old('postcode')) {
                        $postcode = old('postcode');
                    }
                    ?>
                    <input id="postcode" type="text" class="uk-input " name="postcode" value="{{$postcode}}" autocomplete="postcode" required placeholder="例) 123-4567（半角数字・ハイフンあり）" style="width:300px;" maxlength=8 onKeyUp="AjaxZip3.zip2addr(this,'','address','address');">

                </div>
            </div>
            <div class="uk-margin">
                <label class="uk-form-label uk-text-left" for="address">
                    <span id="tantoaddress"></span>
                    <span class="uk-label uk-label-danger uk-margin-small-left">必須</span>
                </label>
                <div class="uk-form-controls">
                    <?php
                    $address = "";
                    if (isset($user->address)) {
                        $address = $user->address;
                    }
                    if (old('address')) {
                        $address = old('address');
                    }
                    ?>
                    <input id="address" type="text" class="uk-input " name="address" value="{{$address}}" autocomplete="address" required placeholder="例）東京都千代田区OOO１－１（全角）">
                    @error('address')
                    <div class="uk-text-danger uk-text-uppercase">
                        <p>{{ $message }}</p>
                    </div>
                    @enderror
                </div>
            </div>
            <div class="uk-margin">
                <label class="uk-form-label uk-text-left" for="busyo">
                    <span id="tantobusyo"></span>
                    <span class="uk-label uk-label-danger uk-margin-small-left">必須</span>
                </label>
                <div class="uk-form-controls">
                    <?php
                    $busyo = "";
                    if (isset($user->busyo)) {
                        $busyo = $user->busyo;
                    }
                    if (old('busyo')) {
                        $busyo = old('busyo');
                    }
                    ?>
                    <input id="busyo" type="text" class="uk-input " name="busyo" value="{{$busyo}}" autocomplete="busyo" placeholder="例)▲▲大学　●●株式会社　部門なし　等" required>
                    @error('busyo')
                    <div class="uk-text-danger uk-text-uppercase">
                        <p>{{ $message }}</p>
                    </div>
                    @enderror
                </div>
            </div>
            <div class="uk-margin" id="open_bumon_disp">
                <label class="uk-form-label uk-text-left" for="bumon">
                    <span id="tantobumon"></span>
                    <span class="uk-label uk-label-danger uk-margin-small-left">必須</span>
                </label>
                <div class="uk-form-controls">
                    <?php
                    $bumon = "";
                    if (isset($user->bumon)) {
                        $bumon = $user->bumon;
                    }
                    if (old('bumon')) {
                        $bumon = old('bumon');
                    }
                    ?>
                    <input id="bumon" type="text" class="uk-input " name="bumon" value="{{$bumon}}" autocomplete="bumon" placeholder="例)▲▲学部■■科　　〇〇部□□課　　部門なし　等">
                    @error('bumon')
                    <div class="uk-text-danger uk-text-uppercase">
                        <p>{{ $message }}</p>
                    </div>
                    @enderror
                </div>
            </div>

            @if(!isset($isedit) )
            <div class="uk-margin">
                <label class="uk-form-label uk-text-left" for="password">
                    パスワード<span class="uk-label uk-label-danger uk-margin-small-left">必須</span>
                </label>
                <div class="uk-form-controls">

                    <input id="password" type="password" class="uk-input " name="password" autocomplete="new-password" value="" required placeholder="4～16文字の半角英数字">
                    @error('password')
                    <div class="uk-text-danger uk-text-uppercase">
                        <p>{{ $message }}</p>
                    </div>
                    @enderror
                </div>
            </div>

            <div class="uk-margin">
                <label class="uk-form-label uk-text-left" for="password-confirm">
                    パスワード（再入力）<span class="uk-label uk-label-danger uk-margin-small-left">必須</span>
                </label>
                <div class="uk-form-controls">
                    <input id="password-confirm" type="password" class="uk-input " name="password_confirmation" required autocomplete="new-password" value="" placeholder="4～16文字の半角英数字">
                </div>
            </div>
            @endif
            @if(isset($isedit) && $isedit )
            @if(!isset($isprofile))
            <div class="uk-form-controls">
                <input class="uk-input" type="text" placeholder="disabled" value="********" disabled>
                <div class="uk-margin-small">
                    ※ パスワードの変更は <a href="{{ route('admin.members.update.password', $user->id) }}">こちら</a> から
                </div>
            </div>
            @endif
            @endif


            <div class="hidden" id="group_flag_disp">
                <div class="uk-margin ">
                    <label class="uk-form-label uk-text-left" for="login_id">
                        <span>協賛学会所属者の有無</span>
                        <!-- <span class="uk-label uk-label-danger uk-margin-small-left">必須</span> -->
                    </label>
                    <div class="uk-form-controls">

                        @foreach(config('pacd.user.group_flag') as $key=>$value)
                        <?php
                        $chk = "";
                        if (isset($user->group_flag) && $user->group_flag == $key) $chk = "checked";
                        if (old('group_flag') == $key) $chk = "checked";
                        ?>
                        <label><input type="radio" name="group_flag" value="{{$key}}" {{$chk}} />{{$value}}</label>
                        @endforeach
                        <div>
                            <?php
                            $kyousan = "";
                            $disabled = "disabled";
                            if (isset($user->kyousan)) $kyousan = $user->kyousan;
                            if (old('kyousan')) $kyousan = old('kyousan');
                            if (isset($user->group_flag) && $user->group_flag == 1) $disabled = "";
                            ?>
                            協賛学会名:日本分析化学会 高分子学会 日本化学会<input type="text" <?= $disabled ?> class="uk-input kyousan" name="kyousan" value="{{ $kyousan }}">
                        </div>

                    </div>
                </div>
            </div>

            <div class="uk-margin">
                <label class="uk-form-label uk-text-left" for="remarks">備考</label>
                <div class="uk-form-controls">
                    <?php
                    $remarks = "";
                    if (isset($user->remarks)) {
                        $remarks = $user->remarks;
                    }
                    if (old('remarks')) {
                        $remarks = old('remarks');
                    }
                    ?>
                    <textarea class="uk-textarea  " name="remarks" id="remarks" rows="3">{{$remarks}}</textarea>
                    @error('remarks')
                    <div class="uk-text-danger uk-text-uppercase">
                        <p>{{ $message }}</p>
                    </div>
                    @enderror
                </div>
            </div>
            {{--ダウンロード可初期値--}}
            <input type="hidden" name="is_enabled_invoice" value="1" />
            {{-- カスタムインプット項目 --}}
            @if($inputs->count())
            <div class="uk-section-xsmall">
                @foreach ($inputs as $input)
                <div class="uk-margin">
                    <label class="uk-form-label uk-text-left">
                        {{ $input->name }}
                        @if (strpos($input->validation_rules, 'required') !== false)
                        <span class="uk-label uk-label-danger uk-margin-small-left">必須</span>
                        @endif
                    </label>
                    <div class="uk-form-controls">
                        <input type="hidden" name="custom[{{ $input->id }}][form_input_id]" value="{{ $input->id }}">
                        <input type="hidden" name="custom[{{ $input->id }}][type]" value="{{ $input->type }}">
                        @if($input->type == config('pacd.form.input_type.select'))
                        {{-- プルダウン型 --}}
                        <select name="custom[{{ $input->id }}][data][]" class="uk-select">
                            @foreach ($input->values as $val)
                            <option value="{{ $val->id }}" @if(old("custom.$input->id.data.0") == $val->id) selected @endif>{{ $val->value }}</option>
                            @endforeach
                        </select>
                        @elseif($input->type == config('pacd.form.input_type.check'))
                        {{-- 複数選択型 --}}
                        <div class="uk-grid-small" uk-grid>
                            @foreach ($input->values as $val)
                            @if ($val->is_included_textarea)
                            <div class="uk-width-1-1 uk-flex uk-flex-middle">
                                <div class="uk-margin-right">
                                    <label><input name="custom[{{ $input->id }}][data][{{ $val->id }}]" class="uk-checkbox" type="checkbox" value="{{ $val->value }}" @if(old("custom.$input->id.data.$val->id")) checked @endif> {{ $val->value }}</label>
                                </div>
                                <div class="uk-width-expand">
                                    <input type="text" class="uk-input uk-form-small" name="custom[{{ $input->id }}][data_sub][{{ $val->id }}]" value='{{ old("custom.$input->id.data_sub.$val->id") }}'>
                                </div>
                            </div>
                            @else
                            <label><input name="custom[{{ $input->id }}][data][{{ $val->id }}]" class="uk-checkbox" type="checkbox" value="{{ $val->value }}" @if(old("custom.$input->id.data.$val->id")) checked @endif> {{ $val->value }}</label>
                            @endif
                            @endforeach
                        </div>
                        @else
                        {{-- テキスト型 --}}
                        @foreach ($input->values as $val)
                        <textarea class="uk-textarea" name="custom[{{ $input->id }}][data][{{ $val->id }}]" rows="1" placeholder="{{ $val->value }}">{{ old("custom.$input->id.data.$val->id") }}</textarea>
                        @endforeach
                        @endif
                        @error("custom.$input->id")
                        <div class="uk-text-danger">
                            <p>{{ $message }}</p>
                        </div>
                        @enderror
                    </div>
                </div>
                @endforeach
            </div>
            @endif



            @if (isCurrent('admin.*'))
            <div class="uk-margin">
                <label class="uk-form-label">請求書・領収書ダウンロード</label>
                <div class="uk-form-controls uk-form-controls-text">
                    <?php
                    $chk0 = "";
                    $chk1 = "";

                    if (isset($user->is_enabled_invoice) && $user->is_enabled_invoice == 0) $chk0 = "checked";


                    if (isset($user->is_enabled_invoice) && $user->is_enabled_invoice == 1) $chk1 = "checked";
                    if ((old('is_enabled_invoice') == 1)) $chk1 = "checked";
                    if (is_null(old('is_enabled_invoice'))) $chk1 = "checked";
                    ?>
                    <label><input type="radio" name="is_enabled_invoice" value="0" class="uk-radio" {{$chk0}}> ダウンロード不可</label>
                    <label><input type="radio" name="is_enabled_invoice" value="1" class="uk-radio" {{$chk1}}> ダウンロード可</label>
                    @error('is_enabled_invoice')
                    <div class="uk-text-danger">
                        <p>{{ $message }}</p>
                    </div>
                    @enderror
                </div>
            </div>
            @if(isset($user->attendees))
            <div class="uk-margin">
                <label class="uk-form-label">参加行事</label>
                <div class="uk-form-controls uk-form-controls-text">
                    @foreach ($user->attendees as $attendee)
                    {{ $attendee->event->name }}
                    @if (!$loop->last) <br> @endif
                    @endforeach
                </div>
            </div>
            @endif
            @endif

            @if(!isset($isprofile))
            @if (!isCurrent('admin.*'))
            <div class="uk-section-small uk-text-center">
                <button type="submit" class="uk-button uk-button-primary bg-green">登録</button>
                <div class="uk-margin-small-top"><a href="{{ route('login') }}" class="uk-button uk-button-text">既に登録済みの方はこちら</a></div>
            </div>
            @endif
            @endif
        </fieldset>