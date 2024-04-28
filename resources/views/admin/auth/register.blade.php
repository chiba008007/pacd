@extends('layouts.admin')

@section('title', $title)

@section('content')
    <div class="uk-section">
        <div class="uk-container uk-container-large">
            <div uk-grid class="uk-child-width-1-1@s uk-child-width-2-3@l">
                <div class="uk-width-1-1@s uk-width-1-5@l uk-width-1-3@xl"></div>
                <div class="uk-width-1-1@s uk-width-3-5@l uk-width-1-3@xl">
                    <div class="uk-card uk-card-default">
                        <div class="uk-card-header">{{ config('app.name', '高分子分析研究懇談会') }} 管理画面</div>
                        <div class="uk-card-body">
                            <h2 class="uk-text-center uk-text-large">{{ $title }}</h2>
                            <form method="POST" action="{{ route('admin.register') }}">
                                @csrf
                                <fieldset class="uk-fieldset">

                                    <div class="uk-margin">
                                        <div class="uk-position-relative">
                                            <span class="uk-form-icon" uk-icon="user"></span>
                                            <input id="login_id" type="text" class="uk-input " name="login_id" value="{{ old('login_id') }}" required autocomplete="login_id" autofocus placeholder="Login ID">
                                        </div>
                                        @error('login_id')
                                            <div class="uk-text-danger uk-text-uppercase">
                                                <p>{{ $message }}</p>
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="uk-margin">
                                        <div class="uk-position-relative">
                                            <span class="uk-form-icon" uk-icon="mail"></span>
                                            <input id="email" type="email" class="uk-input " name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="EMail">
                                        </div>
                                        @error('email')
                                            <div class="uk-text-danger uk-text-uppercase">
                                                <p>{{ $message }}</p>
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="uk-margin">
                                        <div class="uk-position-relative">
                                            <span class="uk-form-icon" uk-icon="lock"></span>
                                            <input id="password" type="password" class="uk-input " name="password" required autocomplete="new-password" placeholder="Password">
                                        </div>
                                        @error('password')
                                            <div class="uk-text-danger uk-text-uppercase">
                                                <p>{{ $message }}</p>
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="uk-margin">
                                        <div class="uk-position-relative">
                                            <span class="uk-form-icon" uk-icon="lock"></span>
                                            <input id="password-confirm" type="password" class="uk-input " name="password_confirmation" required autocomplete="new-password" placeholder="Confirm Password">
                                        </div>
                                    </div>

                                    <div class="uk-margin uk-text-center">
                                        <button type="submit" class="uk-button uk-button-primary">登録</button>
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
