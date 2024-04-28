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
                            @if (session('status'))
                                <div class="uk-alert-primary" uk-alert>
                                    <a class="uk-alert-close" uk-close></a>
                                    <p>{{ session('status') }}</p>
                                </div>
                            @endif
                            <form method="POST" action="{{ route('admin.password.email') }}">
                                @csrf
                                <fieldset class="uk-fieldset">
                                    <div class="uk-margin">
                                        <div class="uk-position-relative">
                                            <span class="uk-form-icon" uk-icon="mail"></span>
                                            <input id="email" type="email" class="uk-input " name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="EMail">
                                        </div>
                                        @error('email')
                                            <div class="uk-text-danger uk-text-uppercase">
                                                <p>{{ $message }}</p>
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="uk-margin uk-text-center">
                                        <button type="submit" class="uk-button uk-button-primary">
                                            <span class="uk-margin-small-right uk-icon" uk-icon="forward"></span>送信
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
