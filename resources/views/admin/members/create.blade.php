@extends('layouts.admin')

@section('title', $title = '会員登録')

@section('breadcrumb')
    @parent
    <li><a href="{{ route('admin.members.index') }}">会員管理</a></li>
    <li><span>{{ $title }}</span></li>
@endsection

@section('content')
    <div class="uk-section-xsmall">
        <div class="uk-container uk-container-large">

            @include('elements.forms.members.register')

            <div class="uk-form-horizontal">

{{--
                <div class="uk-margin">
                    <label class="uk-form-label">請求書・領収書ダウンロード</label>
                    <div class="uk-form-controls uk-form-controls-text">
                        <label><input type="radio" name="is_enabled_invoice" value="0" class="uk-radio"  @if(old('is_enabled_invoice') == 0) checked @endif  form="user_register_form"> ダウンロード不可</label>
                        <label><input type="radio" name="is_enabled_invoice" value="1" class="uk-radio"  @if(old('is_enabled_invoice') == 1) checked @endif  form="user_register_form"> ダウンロード可</label>
                        @error('is_enabled_invoice')
                            <div class="uk-text-danger">
                                <p>{{ $message }}</p>
                            </div>
                        @enderror
                    </div>
                </div>
--}}
                <div class="uk-margin">
                    <label class="uk-form-label uk-text-left">会員登録メール送信</label>
                    <div class="uk-form-controls">
                        <label><input name="send_mail" class="uk-checkbox" type="checkbox" value="1" @if(old("send_mail")) checked @endif  form="user_register_form"> 送信する</label>
                        @error('send_mail')
                            <div class="uk-text-danger">
                                <p>不正な値が入力されました。</p>
                            </div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="uk-section-small">
                <button type="submit" class="uk-button uk-button-primary" formaction="{{ route('admin.members.store') }}" form="user_register_form">登録</button>
            </div>
        </div>
    </div>
@endsection
