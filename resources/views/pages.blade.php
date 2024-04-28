@extends('layouts.app')

@section('title', $title)

@section('content')
    <div id="page">
        @include('elements.pages.contents')
        @include('elements.pages.itiran')
        @include('elements.pages.itiran_lists')

        {{-- 新規登録フォーム --}}
        @if ($route_name == 'register')
            <div class="form_contents uk-container">
                @include('elements.forms.members.register')
            </div>
        @endif
    </div>
@endsection
