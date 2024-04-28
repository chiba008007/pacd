@extends('layouts.admin')

@section('title', 'タイトルを入力')

@section('breadcrumb')
    @parent
    <li><a href="#">ホーム以下のパンくずを入力</a></li>
    <li><span>現在のページ</span></li>
@endsection

@section('content')
    <div class="uk-section-small">
        <div class="uk-container uk-container-large">
            メインコンテンツ
        </div>
    </div>
@endsection


