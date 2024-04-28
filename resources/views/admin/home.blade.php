@extends('layouts.admin')

@section('title', $title)

@section('breadcrumb')
    @parent
    <li><span>{{ $title }}</span></li>
@endsection

@section('content')
    <div class="uk-section-small">
        <div class="uk-container uk-container-large">
            <div class="uk-alert-primary" uk-alert>
                <p>例会＆講演会イベント情報</p>
            </div>
            @if(!empty($event[2]))
            @foreach($event[2] as $key=>$value)
            <div class="uk-card uk-card-default uk-card-body uk-width-1-1@m uk-margin-top">
                <h3 class="uk-card-title">{{$value->name}}</h3>
                <p>{{$value->date_start}}～{{$value->date_end}}</p>
                <p>
                URL<br />
                <a href="{{url('/')}}/{{config('pacd.categorykey.2.prefix')}}/{{$value->code}}/attendee">{{url('/')}}/{{config('pacd.categorykey.2.prefix')}}/{{$value->code}}/attendee</a></p>
            </div>
            @endforeach
            @endif
        </div>
        <div class="uk-container uk-container-large uk-margin-top">
            <div class="uk-alert-primary" uk-alert>
                <p>高分子分析討論会</p>
            </div>
            @if(!empty($event[3]))
                @foreach($event[3] as $key=>$value)
                <div class="uk-card uk-card-default uk-card-body uk-width-1-1@m uk-margin-top">
                    <h3 class="uk-card-title">{{$value->name}}</h3>
                    <p>{{$value->date_start}}～{{$value->date_end}}</p>
                    <p>
                    URL<br />
                    <a href="{{url('/')}}/{{config('pacd.categorykey.3.prefix')}}/{{$value->code}}/attendee">{{url('/')}}/{{config('pacd.categorykey.3.prefix')}}/{{$value->code}}/attendee</a></p>
                </div>
                @endforeach
            @endif
        </div>
        <div class="uk-container uk-container-large uk-margin-top">
            <div class="uk-alert-primary" uk-alert>
                <p>企業協賛</p>
            </div>
            @if(!empty($event[5]))
                @foreach($event[5] as $key=>$value)
                <div class="uk-card uk-card-default uk-card-body uk-width-1-1@m uk-margin-top">
                    <h3 class="uk-card-title">{{$value->name}}</h3>
                    <p>
                    URL<br />
                    <a href="{{url('/')}}/{{config('pacd.categorykey.5.prefix')}}/{{$value->code}}/attendee">{{url('/')}}/{{config('pacd.categorykey.5.prefix')}}/{{$value->code}}/attendee</a></p>
                </div>
                @endforeach
            @endif
        </div>
        <div class="uk-container uk-container-large uk-margin-top">
            <div class="uk-alert-primary" uk-alert>
                <p>高分子分析技術講習会</p>
            </div>
            @if(!empty($event[4]))
                @foreach($event[4] as $key=>$value)
                <div class="uk-card uk-card-default uk-card-body uk-width-1-1@m uk-margin-top">
                    <h3 class="uk-card-title">{{$value->name}}</h3>
                    <p>{{$value->date_start}}～{{$value->date_end}}</p>
                    <p>
                    URL<br />
                    <a href="{{url('/')}}/{{config('pacd.categorykey.4.prefix')}}/{{$value->code}}/attendee">{{url('/')}}/{{config('pacd.categorykey.4.prefix')}}/{{$value->code}}/attendee</a></p>
                </div>
                @endforeach
            @endif
        </div>
    </div>
@endsection
