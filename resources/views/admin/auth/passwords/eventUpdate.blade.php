@extends('layouts.admin')

@section('title', $title)

@section('breadcrumb')
    @parent
    <li><span href="">{{ $title }}</span></li>
@endsection

@section('content')
    <div class="uk-section-small">
        <div class="uk-container uk-container-large">
            <form method="POST" action="{{ route('admin.eventupdate.password') }}">
                @csrf
                <fieldset class="uk-fieldset">

                    @if (session('status'))
                        <div class="uk-alert-primary" uk-alert>
                            <a class="uk-alert-close" uk-close></a>
                            <p>{{ session('status') }}</p>
                        </div>
                    @endif
                    
                    @foreach($label as $key=>$value)
                    <div class="uk-margin">
                        <b>{{$eventtypeTitle[$value]}}</b>
                        <div class="uk-position-relative">
                            <span class="uk-form-icon" uk-icon="lock"></span>
                            <input id="password" type="text" class="uk-input " name="eventtype[{{$key}}]" value="{{$password[$value]}}" required autocomplete="new-password" placeholder="New Password">
                        </div>
                    </div>
                    @endforeach


                    <div class="uk-margin uk-text-right">
                        <button type="submit" class="uk-button uk-button-primary">変更</button>
                    </div>
                </fieldset>
            </form>
        </div>
    </div>
@endsection

@section('footer')
    
@endsection


