@extends('layouts.admin')

@section('title', $title)

@section('breadcrumb')
    @parent
    <li><span href="">{{ $title }}</span></li>
@endsection

@section('content')
    <div class="uk-section-small">
        <div class="uk-container uk-container-large">
            <form method="POST" action="{{ route('admin.update.email') }}">
                @csrf
                <fieldset class="uk-fieldset">

                    @if (session('status'))
                        <div class="uk-alert-primary" uk-alert>
                            <a class="uk-alert-close" uk-close></a>
                            <p>{{ session('status') }}</p>
                        </div>
                    @endif

                    <div class="uk-margin">
                        <p>現在のメールアドレス：{{ Auth::guard('admin')->user()->email }}</p>
                    </div>

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

                    <div class="uk-margin uk-text-right">
                        <button type="submit" class="uk-button uk-button-primary">変更</button>
                    </div>
                </fieldset>
            </form>
        </div>
    </div>
@endsection

