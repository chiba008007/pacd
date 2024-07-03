@section('eventtype', $eventtype = true)
@extends('layouts.admin')

@section('title', $title = '会員ログイン')

@section('breadcrumb')
    @parent
    <li><span>{{ $title }}</span></li>
@endsection

@section('content')
    <div class="uk-container uk-container-large">
        <div class="uk-section-small">
            <form method="POST" action="{{ route('admin.'.$eventtypepath.'.password.checked') }}" class="uk-form-horizontal" >
            @csrf
                <div class="uk-margin-small-top uk-grid-column-small uk-grid-row-large uk-child-width-1-3@s "  uk-grid>
                    <input class="uk-input" type="text" name="password"  value="" placeholder="認証用パスワードを入力してください" required>
                </div>
                <div class="uk-margin-small-top uk-grid-column-small uk-grid-row-large uk-child-width-1-1@s "  uk-grid>
                    <div>
                        <button type="submit" class="uk-button uk-button-secondary">認証</button>
                    </div>
                </div>
            </form>
            <input type="hidden" id="currentPath" value="{{$currentPath}}" />

        </div>
    </div>
@endsection

@section('footer')
    <script>
       
    </script>
@endsection
