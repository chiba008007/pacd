@extends('layouts.admin')

@section('title', $title = '会員情報編集')

@section('breadcrumb')
    @parent
    <li><a href="{{ route('admin.members.index') }}">会員管理</a></li>
    <li><span>{{ $title }}</span></li>
@endsection

@section('content')
    <div class="uk-section-small">
        <div class="uk-container uk-container-large">

            <form method="POST" action="{{ route('admin.members.update', $user->id) }}" class="uk-form-horizontal uk-margin-medium-top" >
                @csrf
                @method('put')
                @include('elements.forms.members.common',["user"=>$user,"isedit"=>true])
                <div class="uk-section-small">
                    <button type="submit" class="uk-button uk-button-primary">更新</button>
                </div>


            </form>
        </div>
    </div>
@endsection
