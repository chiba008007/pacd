@extends('layouts.app')

@section('title', $title)

@section('content')
    <div id="page">
        <div class="header">
            <h2 class="edit-content" data-column="title">{{ $title }}</h2>
        </div>
        <div class="uk-container uk-section-small uk-text-center">
            {{$member}}のみが参加可能です。<br>
            年間会員入会方法については下記のページをご参照ください。

            <div class="uk-section-xsmall">
                <a href="{{ route('nyukai') }}">入会案内</a>
            </div>
        </div>
    </div>
@endsection
