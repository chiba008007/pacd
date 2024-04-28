@extends('layouts.app')

@section('title', $title."イベント")

<style type="text/css">


</style>
@section('content')
    <div id="page">
        @include('elements.pages.contents')

        <div class="uk-padding" >
            <h1 class="uk-heading-line"><span>{{$event->name}}</span></h1>

            <div class="uk-card uk-card-default">
                <div class="uk-card-body">
                    @if($event->event_info)
                        <h3 class="uk-card-title">イベント概要説明</h3>
                        <p>{{$event->event_info}}</p>
                    @endif
                    @if($event->sponser)
                        <h3 class="uk-card-title">主催</h3>
                        <p>{{$event->sponser}}</p>
                    @endif
                    @if($event->coworker)
                        <h3 class="uk-card-title">協賛</h3>
                        <p>{{$event->coworker}}</p>
                    @endif

                    <h3 class="uk-card-title">日時</h3>
                    <?php
                        $stdate =  date_format(date_create($event->date_start),"Y年m月d日");
                        $eddate =  date_format(date_create($event->date_end),"Y年m月d日");
                        $stdatetime =  date_format(date_create($event->date_start_time),"H:i");
                        $eddatetime =  date_format(date_create($event->date_end_time),"H:i");
                    ?>
                    @if(preg_match("/^2999/",$stdate))
                    日程未定(調整中)
                    @else
                    <p>{{$stdate}} {{$stdatetime}}～{{$eddate}} {{$eddatetime}}</p>
                    @endif
                </div>

            </div>


            @if($event->place)
                <div class="uk-margin-top">
                    <div class="uk-card uk-card-default">
                        <div class="uk-card-body">
                            <h3 class="uk-card-title">会場</h3>
                            <p>{{$event->place}}</p>
                            @if($event->event_address)
                            <h3 class="uk-card-title">会場住所</h3>
                            <p>{{$event->event_address}}</p>
                            @endif
                        </div>
                        @if($event->event_address)
                        <div class="uk-card-media-bottom">
                            <iframe style="width:100%;" src="https://maps.google.co.jp/maps?output=embed&q={{$event->event_address}}"></iframe>
                        </div>
                        @endif
                    </div>
                </div>
            @endif


            @if($event->party)
                <div class="uk-margin-top">
                    <div class="uk-card uk-card-default">
                        <div class="uk-card-body">
                            <h3 class="uk-card-title">懇親会会場</h3>
                            <p>{{$event->party}}</p>
                            @if($event->party_address)
                            <h3 class="uk-card-title">懇親会会場住所</h3>
                            <p>{{$event->party_address}}</p>
                            @endif
                        </div>
                        @if($event->party_address)
                        <div class="uk-card-media-bottom">
                            <iframe style="width:100%;" src="https://maps.google.co.jp/maps?output=embed&q={{$event->party_address}}"></iframe>
                        </div>
                        @endif
                    </div>
                </div>
            @endif

            @if($event->other)
                <div class="uk-margin-top">
                    <div class="uk-card uk-card-default">
                        <div class="uk-card-body">
                            <h3 class="uk-card-title">内容詳細</h3>
                            <p>{!! $event->other !!}</p>
                        </div>
                    </div>
                </div>
            @endif

            @if(isset( $eventsjoin[$event->id] ))
                <div class="uk-margin-top uk-width-xlarge">
                    <div class="uk-card uk-card-default">
                        <div class="uk-card-body">
                        <h3>{!! nl2br($event->sanka_explain) !!}</h3>
                            @foreach($eventsjoin[$event->id] as $k=>$v)
                            <div class="uk-grid-small" uk-grid>
                                <div class="uk-width-expand" uk-leader="fill: -">{{$v->join_name}}</div>
                                <div>{{number_format($v->join_price)}}円</div>
                            </div>
                            @endforeach

                        </div>
                    </div>
                </div>
            @endif

        </div>

    </div>
@endsection
