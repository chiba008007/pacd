@extends('layouts.admin')

@section('title', $title)

@section('breadcrumb')
    @parent
    <li><span href="">{{ $title }}</span></li>
@endsection

@section('content')
    <div class="uk-section-small">
        <div class="uk-container uk-container-large">
            <form method="POST" action="{{ route('admin.update.password') }}">
                @csrf
                <fieldset class="uk-fieldset">

                    @if (session('status'))
                        <div class="uk-alert-primary" uk-alert>
                            <a class="uk-alert-close" uk-close></a>
                            <p>{{ session('status') }}</p>
                        </div>
                    @endif

                    <div class="uk-margin">
                        <div class="uk-position-relative">
                            <span class="uk-form-icon" uk-icon="lock"></span>
                            <input id="password" type="password" class="uk-input " name="password" required autocomplete="new-password" placeholder="New Password">
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

                    <div class="uk-margin uk-text-right">
                        <button type="submit" class="uk-button uk-button-primary">変更</button>
                    </div>
                </fieldset>
            </form>
        </div>
    </div>
@endsection

@section('footer')
    
@endsection


