@extends('layouts.admin')

@section('title', $title)

@section('breadcrumb')
    @parent
    <li><span>{{ $title }}</span></li>
@endsection

@section('content')
    <div class="uk-section-small">
        <div class="uk-container uk-container-large">
            @if (session('status'))
                <div class="uk-alert-success" uk-alert>
                <a class="uk-alert-close" uk-close></a>
                    {{ session('status') }}
                </div>
            @endif
            @csrf

            <div class='uk-text-right'>
                <a href="{{ route('admin.'.$category_prefix.'.mail', [$category_prefix])}}" class="uk-button uk-button-primary">登録</a>
            </div>
            <table class="uk-table">
                <thead>
                    <tr>
                        <th class="uk-table-expand" style="width:350px;">詳細</th>
                        <th >配信対象イベント</th>
                        @if($category_prefix != 'kyosan')
                        <th >配信対象者</th>
                        @endif
                        <th>配信日</th>
                        <th >メールタイトル</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($list as $key=>$value)
                    <tr>
                        <td>
                            <a href="{{ route('admin.'.$category_prefix.'.mail.mailsend', [$category_prefix,$value->id])}}" class="uk-button uk-button-secondary sendbutton">送信</a>
                            <a href="{{ route('admin.'.$category_prefix.'.mail', [$category_prefix,$value->id])}}" class="uk-button uk-button-primary">編集</a>
                            <a href="{{ route('admin.'.$category_prefix.'.mail.delete', [$category_prefix,$value->id])}}" class="uk-button uk-button-danger deletebuttonSend">削除</a>
                        </td>
                        <td>{{$value->name}}</td>
                        @if($category_prefix != 'kyosan')
                        <td>{{config('pacd.mail_sender.'.$value->sender_type)}}</td>
                        @endif
                        <td>{{$value->senddate}}</td>
                        <td>{{$value->subject}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {{--$events->links()--}}

        </div>
    </div>
@endsection

@section('footer')
    <script src="{{ asset('js/admin/event.js') }}"></script>
@endsection
