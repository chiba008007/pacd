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

                    @if(preg_match("/会員区別の会員番号/",$input->name) || $input->id === 14)
                        {{-- idが14(固定値)の時は振込予定日がtext形式で表示されるように修正--}}

                        @foreach ($input->values as $val)
                        <input name="custom[{{ $input->id }}][data][{{ $val->id }}]" type="hidden" value="{{ $val->value }}" checked />
                        <div class="uk-child-width-1-3 uk-margin-remove uk-padding-remove" uk-grid>
                            <div class="uk-margin-remove uk-padding-remove">
                            {{ $val->value }}
                            </div>
                            <div class="uk-margin-remove">
                                <input type="text" class="uk-input uk-form-small" name="custom[{{ $input->id }}][data_sub][{{ $val->id }}]" value='{{ old() ? old("custom.$input->id.data_sub.$val->id") : @$custom_data[$val->id]->data_sub }}'>
                            </div>
                        </div>
                        @endforeach

                    @else
                        {{-- 複数選択型 --}}
                        <div class="uk-grid-small uk-form-controls-text" uk-grid>
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
                    @endif
                @else
                    @if($input->name === '振込予定日' || $input->id === 11)
                    {{-- idが11(固定値)の時は振込予定日がプルダウン形式で表示されるように修正--}}
                        <div class="uk-grid">
                            <?php $no = 0; ?>
                            @foreach ($input->values as $val)
                                <div class="@if( $no === 0) uk-width-1-4 @else uk-width-1-5  uk-padding-remove-left @endif">
                                    <select class="uk-select uk-form-small " name="custom[{{ $input->id }}][data][{{ $val->id }}]">
                                    <option value="">{{ $val->value }}を選択</option>
                                    @if($no === 0 )
                                        <?php for ($i = 1; $i <= 12; $i++): ?>
                                        <?php
                                            $sel = "";
                                            if(isset($custom_data[$val->id]->data) && $i == $custom_data[$val->id]->data) $sel = "SELECTEd";
                                        ?>
                                        <option value="<?=$i?>" <?=$sel?>><?=$i?>月</option>
                                        <?php endfor ?>
                                    @else
                                        <?php for ($i = 1; $i <= 31; $i++): ?>
                                        <?php
                                            $sel = "";
                                            if(isset($custom_data[$val->id]->data) && $i == $custom_data[$val->id]->data) $sel = "SELECTEd";
                                        ?>
                                        <option value="<?=$i?>" <?=$sel?> ><?=$i?>日</option>
                                        <?php endfor ?>
                                    @endif
                                    </select>
                                </div>
                                <?php $no++; ?>
                            @endforeach
                        </div>
                    @elseif(preg_match("/参加券番号/",$input->name) || $input->id === 13)
                    {{-- idが13(固定値)の時はtextで表示されるように修正--}}
                        @foreach ($input->values as $val)
                            <input class="uk-input uk-form-small uk-form-width-medium" type="text" placeholder="{{ $val->value }}"  name="custom[{{ $input->id }}][data][{{ $val->id }}]" value="{{ old() ? old(custom.$input->id.data.$val->id) : @$custom_data[$val->id]->data }}">
                        @endforeach
                    @else
                        {{-- テキスト型 --}}
                        @foreach ($input->values as $val)
                            <textarea class="uk-textarea" name="custom[{{ $input->id }}][data][{{ $val->id }}]" rows="1" placeholder="{{ $val->value }}">{{ old() ? old("custom.$input->id.data.$val->id") : @$custom_data[$val->id]->data }}</textarea>
                        @endforeach
                    @endif
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
