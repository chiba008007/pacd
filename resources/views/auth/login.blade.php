@extends('layouts.app_no_sidenav')

@section('title', $title = 'ログイン')

@section('content')
    <div class="uk-section">
        <div class="uk-container uk-container-large">
            <div uk-grid class="uk-child-width-1-1@s uk-child-width-2-3@l">
                <div class="uk-width-1-1@s uk-width-1-5@l uk-width-1-3@xl"></div>
                <div class="uk-width-1-1@s uk-width-3-5@l uk-width-1-3@xl">
                    <div class="uk-card uk-card-default">
                        <div class="uk-card-header">{{ config('app.name', '高分子分析研究懇談会') }}</div>
                        <div class="uk-card-body">
                            @if (session('status'))
                                <div class="uk-alert-primary" uk-alert>
                                    <a class="uk-alert-close" uk-close></a>
                                    <p>{{ session()->pull('status') }}</p>
                                </div>
                            @endif

                            <h2 class="uk-text-center uk-text-large">{{ $title }}</h2>
                            <form method="POST" action="{{ route('login') }}">
                                @csrf
                                <fieldset class="uk-fieldset">
                                    <div class="uk-margin">
                                        <div class="uk-position-relative">
                                            <span class="uk-form-icon" uk-icon="user"></span>
                                            <input id="login_id" type="login_id" class="uk-input " name="login_id" value="{{ old('login_id') }}" required autocomplete="login_id" placeholder="ログインID">
                                        </div>
                                        @error('login_id')
                                            <div class="uk-text-danger uk-text-uppercase">
                                                <p>{{ $message }}</p>
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="uk-margin">
                                        <div class="uk-position-relative">
                                            <span class="uk-form-icon" uk-icon="lock"></span>
                                            <input id="password" type="password" class="uk-input " name="password" required autocomplete="current-password" placeholder="パスワード">
                                        </div>
                                        @error('password')
                                            <div class="uk-text-danger uk-text-uppercase">
                                                <p>{{ $message }}</p>
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="uk-margin uk-text-center">
                                        <button type="submit" class="uk-button uk-button-primary">
                                            <span class="uk-margin-small-right uk-icon" uk-icon="sign-in"></span>ログイン
                                        </button>
                                    </div>

                                    <div class="uk-margin-medium-top uk-text-center">
                                        <a href="{{ route('password.request') }}">パスワードを忘れた場合</a>
                                    </div>
                                </fieldset>
                            </form>
                        </div>
                        <div class="uk-card-footer uk-text-center">
                            @if( request()->input("key") == 2)
                            「法人会員の方でマイページをお持ちでない方は<a href="{{ route('register') }}/?key=2">こちら</a>からマイページの作成をお願い致します。<br />マイページ作成後、参加登録が可能となります。」

                            @elseif( request()->input("key") == 4)
                            「マイページをお持ちでない方は<a href="{{ route('register') }}/?key=4">こちら</a>からマイページの作成をお願い致します。<br />マイページ作成後、参加登録が可能となります。」
                            @elseif( request()->input("key") == 3)
                            「マイページをお持ちでない方は<a href="{{ route('register') }}/?key=3">こちら</a>からマイページの作成をお願い致します。<br />マイページ作成後、参加登録が可能となります。」
                            @else
                            <a href="{{ route('register') }}" class="uk-button uk-button-text">MY PAGE登録はこちら</a>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="uk-width-1-1@s uk-width-1-5@l uk-width-1-3@xl"></div>
            </div>
        </div>
    </div>
@endsection
