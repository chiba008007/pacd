@extends('layouts.app_no_sidenav')

@section('title', $title = 'パスワード再設定')

@section('content')
    <div class="uk-section">
        <div class="uk-container uk-container-large">
            <div uk-grid class="uk-child-width-1-1@s uk-child-width-2-3@l">
                <div class="uk-width-1-1@s uk-width-1-5@l uk-width-1-3@xl"></div>
                <div class="uk-width-1-1@s uk-width-3-5@l uk-width-1-3@xl">
                    <div class="uk-card uk-card-default">
                        <div class="uk-card-body">
                            <h2 class="uk-text-center uk-text-large">{{ $title }}</h2>
                            @if (session('status'))
                                <div class="uk-alert-primary" uk-alert>
                                    <a class="uk-alert-close" uk-close></a>
                                    <p>{{ session('status') }}</p>
                                </div>
                            @endif
                            <form method="POST" action="{{ route('password.reset') }}">
                                @csrf
                                <fieldset class="uk-fieldset">
                                    <div class="uk-margin">
                                    @if (session('flash_message'))
                                        <div class="uk-alert-success" uk-alert>
                                            {{ session('flash_message') }}
                                        </div>
                                    @endif
                                        <div class="uk-position-relative">
                                            <label>ログインID</label>
                                            <input id="loginid" type="text" class="uk-input " name="loginid" value="{{ old('loginid') }}" required autocomplete="loginid" autofocus placeholder="ログインID">
                                            <br />
                                            <br />
                                            <label>メールアドレス</label>
                                            <input id="email" type="email" class="uk-input " name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="メールアドレス">
                                            <br />
                                            <br />
                                            <label>新しいパスワード</label>
                                            <input id="password" type="password" class="uk-input " name="password" value="{{ old('password') }}" required autocomplete="password" autofocus placeholder="パスワード">
                                            <br />
                                            <br />
                                            <label>確認用パスワード</label>
                                            <input id="password_confirmation" type="password" class="uk-input " name="password_confirmation" value="{{ old('password_confirmation') }}" required autocomplete="password_confirmation" autofocus placeholder="確認用パスワード">
                                            <br />
                                            <br />

                                        </div>

                                    </div>
                                    @if ($errors->any())
                                    <div class="uk-alert-danger" uk-alert>
                                    @foreach ($errors->all() as $error)
                                    {{ $error }}<br />
                                    @endforeach
                                    </div>
                                    @endif
                                    <div class="uk-margin uk-text-center">
                                        <button type="submit" class="uk-button uk-button-primary">
                                            <span class="uk-margin-small-right uk-icon" uk-icon="forward"></span>更新
                                        </button>
                                    </div>
                                </fieldset>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="uk-width-1-1@s uk-width-1-5@l uk-width-1-3@xl"></div>
            </div>
        </div>
    </div>
@endsection
