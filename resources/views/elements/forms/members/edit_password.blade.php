<form method="POST" action="{{ route('mypage.profile.update.password') }}" class="uk-form-horizontal" id="password_update_form">
    @csrf
    @method('put')
    <fieldset class="uk-fieldset">

        @if (session('status'))
            <div class="uk-alert-success" uk-alert>
                <a class="uk-alert-close" uk-close></a>
                <p>{{ session('status') }}</p>
            </div>
        @endif

        <div class="uk-margin">
            <label class="uk-form-label uk-text-left" for="password">
                新しいパスワード
            </label>
            <div class="uk-form-controls">
                <input id="password" type="password" class="uk-input" name="password" required autocomplete="new-password" value="" placeholder="4～16文字の半角英数字">
                @error('password')
                    <div class="uk-text-danger uk-text-uppercase">
                        <p>{{ $message }}</p>
                    </div>
                @enderror
            </div>
        </div>

        <div class="uk-margin">
            <label class="uk-form-label uk-text-left" for="password-confirm">
                新しいパスワード（再入力）
            </label>
            <div class="uk-form-controls">
                <input id="password-confirm" type="password" class="uk-input " name="password_confirmation" required autocomplete="new-password" value="" placeholder="4～16文字の半角英数字">
            </div>
        </div>

    </fieldset>
</form>