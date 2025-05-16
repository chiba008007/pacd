@extends('layouts.admin')

@section('title', $title)

@section('breadcrumb')
    @parent
    <li><span>{{ $title }}</span></li>
@endsection

@section('content')
    <div class="uk-section-small">
        <div class="uk-container uk-container-large">
            <form action="{{ route('admin.pages.kyosan.post') }}" method="POST" enctype="multipart/form-data" >
                @csrf
                <p>展示参加者について</p>
                タイトル<br />
                <input type="text"  name="tenjikaiTitle" class="uk-input uk-form-width uk-width-3-4" value="{{$data->tenjikaiTitle}}" />
                <br />
                内容<br />
                <textarea class="uk-textarea uk-form-width uk-width-3-4" name="tenjikaiNote" rows=6 >{{$data->tenjikaiNote}}</textarea>
                <br />
                <p>懇親会参加者について</p>
                タイトル<br />
                <input type="text"  name="konsinkaiTitle" class="uk-input uk-form-width uk-width-3-4" value="{{$data->konsinkaiTitle}}" />
                <br />
                内容<br />
                <textarea class="uk-textarea uk-form-width uk-width-3-4" name="konsinkaiNote" rows=6 >{{$data->konsinkaiNote}}</textarea>
                <br />
                <input type="submit" name="regist" value="登録" class="uk-button uk-button-primary uk-margin-small-top"  />
            </form>
        </div>
    </div>
@endsection

@section('footer')
    <script src="{{ asset('js/admin/pages.js') }}"></script>
@endsection
