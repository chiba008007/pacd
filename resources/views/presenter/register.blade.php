@extends('layouts.app')

@section('title', $title)

@section('content')
    <div id="page">
        @include('elements.pages.contents')

        <div class="uk-container">
            @include('elements.forms.presenters.register')
        </div>
    </div>
@endsection
