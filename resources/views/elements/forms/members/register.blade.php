<div class="uk-section-xsmall">
    @if (session('status'))
        <div class="uk-alert-success" uk-alert>
            <a class="uk-alert-close" uk-close></a>
            <p>{{ session('status') }}</p>
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}" class="uk-form-horizontal" id="user_register_form">
        @csrf
        @include('elements.forms.members.common',['user'=>[]])

    </form>
</div>
