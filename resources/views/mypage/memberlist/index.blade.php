@extends('layouts.app')

@section('title', $title)

@section('content')
    <div id="page">
        <div class="header">
            <h2 class="edit-content" data-column="title">{{ $title }}</h2>
        </div>

        <div class="uk-container uk-section-xsmall">
            <form action="{{ route('mypage.memberlist')}}" method="post">
            @csrf
            <div class="uk-column-1-4">
                <p>
                    <input type="text" name="name" class="uk-input" value="{{$name}}" placeholder="検索したい文字列を入力" />
                </p>
                <p>
                    <input type="submit" name="search" value="検索" class="uk-button uk-button-primary" />
                </p>
            </div>
            </form>
            <br clear=all />
            {{$member->appends($params)->links()}}
            <table class="uk-table">
                <tr class="uk-background-muted">
                    <th >会員名</th>
                    <th >メールアドレス</th>
                    <th >電話番号</th>
                </tr>
                @foreach ($member as $mem)
                <tr>
                    <td>{{$mem->sei}}{{$mem->mei}}</td>
                    <td>{{$mem->email}}</td>
                    <td>{{$mem->tel}}</td>
                </tr>
                @endforeach
            </table>
        </div>
    </div>
@endsection
