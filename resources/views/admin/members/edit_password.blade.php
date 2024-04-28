@extends('layouts.admin')

@section('title', $title = '会員パスワード変更')

@section('breadcrumb')
    @parent
    <li><a href="{{ route('admin.members.index') }}">会員管理</a></li>
    <li><span>{{ $title }}</span></li>
@endsection

@section('content')
    <div class="uk-section-small">
        <div class="uk-container uk-container-large">

            @include('elements.forms.members.edit_password')

            <div class="uk-section-small">
                <button type="submit" class="uk-button uk-button-primary" formaction="{{ route('admin.members.update.password', $user->id) }}" form="password_update_form">更新</button>
            </div>
        </div>
    </div>
@endsection
