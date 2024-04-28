@extends('layouts.app')

@section('title', $title)

@section('content')
    <div id="page">
        <div class="header">
            <h2 class="edit-content" data-column="title">{{ $title }}</h2>
        </div>
        <div class="uk-container uk-section-small uk-text-center">
            受付を完了しました。<br>
            登録されたメールアドレスに詳細情報をお送りいたします。<br>

            <div class="uk-section-xsmall">
                <a href="{{ $edit_url }}" class="uk-button uk-button-primary">講演者情報の確認・変更</a>
            </div>

            <div class="uk-section-xsmall">
                続けて講演申し込みを行う場合、下記より申請してください。<br>
                <div class="uk-section-xsmall">
                    <a href="{{ $create_url }}" class="uk-button uk-button-primary">講演の申し込みを行う</a>
                </div>
            </div>

            <div class="uk-section-xsmall">
                <div class="uk-section-xsmall">
                    <a href="/" class="uk-button uk-button-primary">topに戻る</a>
                </div>
            </div>
        </div>
    </div>
@endsection
