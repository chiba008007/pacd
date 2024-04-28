@extends('layouts.app')

@section('title', $title = 'パスワード変更')

@section('breadcrumb')
    @parent
    <li><span href="">{{ $title }}</span></li>
@endsection

@section('content')
    <div id="page">
        <div class="header">
            <h2 class="edit-content" data-column="title">{{ $title }}</h2>
        </div>
        <div class="uk-container uk-section-xsmall">

            @include('elements.forms.members.edit_password')

            <div class="uk-section-small uk-text-center">
                <button type="submit" class="uk-button uk-button-primary bg-green" form="password_update_form">パスワード変更</button>
            </div>
        </div>
    </div>
@endsection
