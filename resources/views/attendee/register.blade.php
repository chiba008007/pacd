@extends('layouts.app')

@section('title', $title)

@section('content')
    <div id="page">
        @include('elements.pages.contents')

        <div class="uk-container">
            @include('elements.forms.attendees.register')
        </div>
    </div>
@endsection
