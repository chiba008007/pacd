@extends('layouts.admin')

@section('title', $title)

@section('breadcrumb')
    @parent
    @foreach ($breadcrumbs as $breadcrumb)
        <li><a href="{{ $breadcrumb['url'] }}">{{ $breadcrumb['title'] }}</a></li>
    @endforeach
    <li><span>編集</span></li>
@endsection

@section('content')
    <div class="uk-container uk-container-large">
        <div class="uk-section-small">
            <form method="POST" action="{{ route("admin.form.update", [$form['category_prefix'], $form['prefix'], $input->id]) }}">
                @csrf
                @method('put')
                <input type="hidden" name="form_type" value="{{ $form['key'] }}">
                <table class="uk-table">
                    <tbody>
                        <tr>
                            <th>
                                イベント名<br>
                                <span class="uk-label uk-label-danger">必須</span>
                            </th>
                            <td class="uk-form-controls">
                                <select name="event_id" class="uk-select" >
                                <option value="0">共通</option>
                                @foreach($eventlists as $key=>$value)
                                    <?php
                                    $sel = "";
                                    if($value->id == $input->event_id) $sel = "selected";
                                    ?>
                                    <option value="{{$value->id}}" {{$sel}} >{{$value->name}}</option>
                                @endforeach
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                項目名<br>
                                <span class="uk-label uk-label-danger">必須</span>
                            </th>
                            <td class="uk-form-controls">
                                <input type="text" class='uk-input' name="name" value="{{ (old()) ? old("name") : $input->name }}">
                                @error("name")
                                    <div class="uk-text-danger">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </td>
                        </tr>

                        <tr>
                            <th>エラーチェック</th>
                            <td class="uk-form-controls">
                                <div class="uk-grid-small" uk-grid>
                                    <input type="hidden" name="validation_required" value="0">
                                    <input type="hidden" name="validation_numeric" value="0">
                                    <input type="hidden" name="validation_alpha" value="0">
                                    <label><input name="validation_required" class="uk-checkbox" type="checkbox" value="1" @if((old()) ? old('validation_required') : in_array('必須', $input->validation_rules_display)) checked @endif> 必須チェック</label>
                                    <label><input name="validation_numeric" class="uk-checkbox" type="checkbox" value="1" @if((old()) ? old('validation_numeric') : in_array('数値', $input->validation_rules_display)) checked @endif> 数値チェック</label>
                                    <label><input name="validation_alpha" class="uk-checkbox" type="checkbox" value="1" @if((old()) ? old('validation_alpha') : in_array('半角英字', $input->validation_rules_display)) checked @endif> 半角英字チェック</label>
                                </div>
                                @if($errors->has('validation_required') || $errors->has('validation_numeric') || $errors->has('validation_alpha'))
                                    <div class="uk-text-danger">
                                        <p>不正な値が入力されました。</p>
                                    </div>
                                @endif
                            </td>
                        </tr>

                        <tr>
                            <th>エラーメッセージ</th>
                            <td class="uk-form-controls">
                                <input type="text" class="uk-input" name="validation.message" value="{{ (old()) ? old("validation_message") : $input->validation_message }}">
                                @error("validation_message")
                                    <div class="uk-text-danger">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </td>
                        </tr>

                        <tr>
                            <th>表示</th>
                            <td class="uk-form-controls">
                                <div class="uk-grid-small" uk-grid>
                                    <input type="hidden" name="is_display_published" value="0">
                                    <input type="hidden" name="is_display_user_list" value="0">
                                    <label><input name="is_display_published" class="uk-checkbox" type="checkbox" value="1" @if( (old()) ? old('is_display_published') : $input->is_display_published) checked @endif> 公開画面へ表示</label>
                                    <label><input name="is_display_user_list" class="uk-checkbox" type="checkbox" value="1" @if( (old()) ? old('is_display_user_list') : $input->is_display_user_list) checked @endif> 会員一覧へ表示</label>
                                </div>
                                @if($errors->has('is_display_published') || $errors->has('is_display_user_list'))
                                    <div class="uk-text-danger">
                                        <p>不正な値が入力されました。</p>
                                    </div>
                                @endif
                            </td>
                        </tr>

                        <tr>
                            <th>参加者CSV</th>
                            <td class="uk-form-controls">
                                <div class="uk-grid-small" uk-grid>
                                    <input type="hidden" name="csvflag" value="0">
                                    <label><input name="csvflag" class="uk-checkbox" type="checkbox" value="1" @if(old('csvflag', $input->csvflag)) checked @endif> 出力</label>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <th>CSV出力箇所</th>
                            <td class="uk-form-controls">
                                <!-- <select name="csvtag" class="uk-select">
                                    @foreach (Config::get('csv.clum_pos') as $key => $val)
                                        <option name="csvtag" value="{{ $key }}" @if($key == old('csvtag', $input->csvtag)) selected @endif>{{ $val }} @if($key !== 0) の後へ @endif</option>
                                    @endforeach
                                </select> -->
                                <input type="text" name="csvtag" class="uk-input" value="{{ (old()) ? old("csvtag") : $input->csvtag }}">
                                @error("csvtag")
                                    <div class="uk-text-danger">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </td>

                        </tr>

                        <tr>
                            <th>入力フォーマット選択</th>
                            <td class="uk-form-controls">
                                <ul id="types" class="uk-subnav uk-subnav-pill uk-margin-remove">
                                    <li data-type="1" class="uk-padding-remove-left @if(old('type', $input->type) == config('pacd.form.input_type.text')) uk-active @endif"><a>テキスト型</a></li>
                                    <li data-type="2" class="@if(old('type', $input->type) == config('pacd.form.input_type.select')) uk-active @endif"><a>プルダウン型</a></li>
                                    <li data-type="3" class="@if(old('type', $input->type) == config('pacd.form.input_type.check')) uk-active @endif"><a>複数選択型</a></li>
                                </ul>

                                <div class="uk-padding-small">
                                    <input type="hidden" name="type" value="{{ old('type', $input->type) }}">
                                    <div uk-grid class="uk-grid-small uk-margin-small">
                                        <p id="count_title">{{ (old('type', $input->type) == config('pacd.form.input_type.text')) ? '入力枠数：' : '選択項目数：' }}</p>
                                        <select name="count">
                                            @for ($i = 1; $i <= 10; $i++)
                                                <option @if(old('count', count($input->values)) == $i) selected @endif value="{{ $i }}">{{ $i }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                    <div class="uk-margin-small">
                                        <p id="value_title">{{ (old('type', $input->type) == config('pacd.form.input_type.text')) ? '入力例：' : '選択項目：' }}</p>
                                        <div id="values">
                                            @foreach (old('value', $input->values) as $key => $val)
                                                <div class="value_group">
                                                    <div class="uk-flex uk-flex-middle uk-margin-small">
                                                        <div style="width: 30px">{{ $key+1 }}</div>
                                                        <input type="text" name="value[{{ $key }}]" class="uk-input" value="{{ (old()) ? old("value.$key") : $val->value }}">
                                                    </div>
                                                    <input type="hidden" name="is_included_textarea[{{ $key }}]" value="0">
                                                    <label @if(old('type', $input->type) != config('pacd.form.input_type.check')) hidden @endif style="margin-left:30px">
                                                        <input name="is_included_textarea[{{ $key }}]" value="1" class="uk-checkbox" type="checkbox" @if( (old()) ? old("is_included_textarea.$key") : $val->is_included_textarea) checked @endif> テキストエリア付
                                                    </label>
                                                    @error("value.$key")
                                                        <div class="uk-text-danger">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="uk-section-xsmall">
                    <button class="uk-button uk-button-secondary" type="submit" value="preview" name="action">プレビュー</button>
                    <button class="uk-button uk-button-primary" type="submit" value="update" name="action">更新</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('footer')
    <script src="{{ asset('js/admin/forms.js') }}"></script>
@endsection
