@if (session('status'))
<div class="uk-alert-success" uk-alert>
    <a class="uk-alert-close" uk-close></a>
    <p>{{ session('status') }}</p>
</div>
@endif

<form method="POST" action="{{ route('mypage.profile.update') }}" class="uk-form-horizontal uk-margin-medium-top" id="user_update_form">
    @csrf
    @method('put')
    <fieldset class="uk-fieldset">

        <div class="uk-margin">
            <label class="uk-form-label uk-text-left" for="login_id">
                ログインID<span class="uk-label uk-label-danger uk-margin-small-left">必須</span>
            </label>
            <div class="uk-form-controls">
                <input class="uk-input" type="text" placeholder="disabled" value="{{ $user->login_id }}" disabled>
            </div>
        </div>

        <div class="uk-margin">
            <label class="uk-form-label uk-text-left" for="login_id">
                会員区分<span class="uk-label uk-label-danger uk-margin-small-left">必須</span>
            </label>
            <div class="uk-form-controls">
                <input class="uk-input" type="text" placeholder="disabled" value="{{ config('pacd.user.type.' . $user->type) }}" disabled>
            </div>
        </div>

        <div class="uk-margin">
            <label class="uk-form-label uk-text-left" for="login_id">
                年会費納入状況
            </label>
            <div class="uk-form-controls uk-form-controls-text">
                {{ $user->type == 1 ? '年会費未納' : '年会費納入済' }}
            </div>
        </div>

        <div class="uk-margin">
            <label class="uk-form-label uk-text-left" for="sei">
                氏名<span class="uk-label uk-label-danger uk-margin-small-left">必須</span>
            </label>
            <div class="uk-form-controls">
                <div class="uk-grid-small" uk-grid>
                    <div class="uk-width-1-2">
                        <input id="sei" type="sei" class="uk-input " name="sei" value="{{ old('sei', $user->sei) }}" required autocomplete="sei" placeholder="姓">
                    </div>
                    <div class="uk-width-1-2">
                        <input id="mei" type="mei" class="uk-input " name="mei" value="{{ old('mei', $user->mei) }}" required autocomplete="mei" placeholder="名">
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
                氏名（ふりがな）<span class="uk-label uk-label-danger uk-margin-small-left">必須</span>
            </label>
            <div class="uk-form-controls">
                <div class="uk-grid-small" uk-grid>
                    <div class="uk-width-1-2">
                        <input id="sei_kana" type="sei_kana" class="uk-input " name="sei_kana" value="{{ old('sei_kana', $user->sei_kana) }}" required autocomplete="sei_kana" placeholder="せい">
                    </div>
                    <div class="uk-width-1-2">
                        <input id="mei_kana" type="mei_kana" class="uk-input " name="mei_kana" value="{{ old('mei_kana', $user->mei_kana) }}" required autocomplete="mei_kana" placeholder="めい">
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
                メールアドレス<span class="uk-label uk-label-danger uk-margin-small-left">必須</span>
            </label>
            <div class="uk-form-controls">
                <input id="email" type="email" class="uk-input " name="email" value="{{ old('email', $user->email) }}" required autocomplete="email">
                @error('email')
                    <div class="uk-text-danger uk-text-uppercase">
                        <p>{{ $message }}</p>
                    </div>
                @enderror
            </div>
        </div>

        <div class="uk-margin">
            <label class="uk-form-label uk-text-left" for="password">
                パスワード<span class="uk-label uk-label-danger uk-margin-small-left">必須</span>
            </label>
            <div class="uk-form-controls">
                <input class="uk-input" type="text" placeholder="disabled" value="********" disabled>
                <div class="uk-margin-small">
                    ※ パスワードの変更は <a href="{{ route('admin.members.update.password', $user->id) }}">こちら</a> から
                </div>
            </div>
        </div>

        <div class="uk-margin">
            <label class="uk-form-label uk-text-left" for="remarks">備考</label>
            <div class="uk-form-controls">
                <textarea class="uk-textarea" name="remarks" id="remarks" rows="3">{{ old() ? old('remarks') : $user->remarks }}</textarea>
                @error('remarks')
                    <div class="uk-text-danger uk-text-uppercase">
                        <p>{{ $message }}</p>
                    </div>
                @enderror
            </div>
        </div>

        {{-- カスタムインプット項目 --}}
        @if($inputs->count())
            @php $custom_data = $user->custom_form_data->keyBy('form_input_value_id') @endphp
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
                                        <option value="{{ $val->id }}" @if( (old() ? old("custom.$input->id.data.0") == $val->id : @$custom_data[$val->id]->data) == $val->value) selected @endif>{{ $val->value }}</option>
                                    @endforeach
                                </select>
                            @elseif($input->type == config('pacd.form.input_type.check'))
                                {{-- 複数選択型 --}}
                                <div class="uk-grid-small" uk-grid>
                                    @foreach ($input->values as $val)
                                        @if ($val->is_included_textarea)
                                            <div class="uk-width-1-1 uk-flex uk-flex-middle">
                                                <div class="uk-margin-right">
                                                    <label><input name="custom[{{ $input->id }}][data][{{ $val->id }}]" class="uk-checkbox" type="checkbox" value="{{ $val->value }}" @if(old() ? old("custom.$input->id.data.$val->id") : @$custom_data[$val->id]->data) checked @endif> {{ $val->value }}</label>
                                                </div>
                                                <div class="uk-width-expand">
                                                    <input type="text" class="uk-input uk-form-small" name="custom[{{ $input->id }}][data_sub][{{ $val->id }}]" value='{{ old() ? old("custom.$input->id.data_sub.$val->id") : @$custom_data[$val->id]->data_sub }}'>
                                                </div>
                                            </div>
                                        @else
                                            <label><input name="custom[{{ $input->id }}][data][{{ $val->id }}]" class="uk-checkbox" type="checkbox" value="{{ $val->value }}" @if(old() ? old("custom.$input->id.data.$val->id") : @$custom_data[$val->id]->data) checked @endif> {{ $val->value }}</label>
                                        @endif
                                    @endforeach
                                </div>
                            @else
                                {{-- テキスト型 --}}
                                @foreach ($input->values as $val)
                                    <textarea class="uk-textarea" name="custom[{{ $input->id }}][data][{{ $val->id }}]" rows="1" placeholder="{{ $val->value }}">{{ old() ? old("custom.$input->id.data.$val->id") : @$custom_data[$val->id]->data }}</textarea>
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
    </fieldset>
</form>
