<div class="modal js-modal " id="modal-open-{{$val->id}}" >
    <div class="modal__bg js-modal-close"></div>
    <div class="modal__content uk-panel-scrollable " style="height:70vh;">

        <h1 class="uk-heading-line"><span>{{$val->name}}</span></h1>

        <div>
            @if(
                $val->event_info ||
                $val->sponser ||
                $val->coworker
            )
            <div class="uk-card uk-card-default">
                <div class="uk-card-body">
                    @if($val->event_info)
                        <h3 class="uk-card-title">イベント概要説明</h3>
                        <p>{{$val->event_info}}</p>
                    @endif
                    @if($val->sponser)
                        <h3 class="uk-card-title">主催</h3>
                        <p>{{$val->sponser}}</p>
                    @endif
                    @if($val->coworker)
                        <h3 class="uk-card-title">協賛</h3>
                        <p>{{$val->coworker}}</p>
                    @endif

                    {{--企業協賛--}}
                    @if($val->category_type != 5)
                        <h3 class="uk-card-title">日時</h3>
                        @if(preg_match("/^2999/",$val->date_start))
                            日程未定(調整中)
                        @else
                        <?php
                            $stdate =  date_format(date_create($val->date_start),"Y年m月d日");
                            $eddate =  date_format(date_create($val->date_end),"Y年m月d日");
                            $stdatetime =  date_format(date_create($val->date_start_time),"H:i");
                            $eddatetime =  date_format(date_create($val->date_end_time),"H:i");
                        ?>
                        <p>{{$stdate}} {{$stdatetime}}～{{$eddate}} {{$eddatetime}}</p>
                        @endif
                    @endif
                </div>

            </div>
            @endif
        </div>

        @if($val->place)
        <div class="uk-margin-top">
            <div class="uk-card uk-card-default">
                <div class="uk-card-body">
                    <h3 class="uk-card-title">会場</h3>
                    <p>{{$val->place}}</p>
                    @if($val->event_address)
                    <h3 class="uk-card-title">会場住所</h3>
                    <p>{{$val->event_address}}</p>
                    @endif
                </div>
                @if($val->event_address)
                <div class="uk-card-media-bottom">
                    <iframe style="width:100%;" src="https://maps.google.co.jp/maps?output=embed&q={{$val->event_address}}"></iframe>
                </div>
                @endif
            </div>
        </div>
        @endif

        @if($val->party)
        <div class="uk-margin-top">
            <div class="uk-card uk-card-default">
                <div class="uk-card-body">
                    <h3 class="uk-card-title">懇親会会場</h3>
                    <p>{{$val->party}}</p>
                    @if($val->party_address)
                    <h3 class="uk-card-title">懇親会会場住所</h3>
                    <p>{{$val->party_address}}</p>
                    @endif
                </div>
                @if($val->party_address)
                <div class="uk-card-media-bottom">
                    <iframe style="width:100%;" src="https://maps.google.co.jp/maps?output=embed&q={{$val->party_address}}"></iframe>
                </div>
                @endif
            </div>
        </div>
        @endif

        @if($val->other)
        <div class="uk-margin-top">
            <div class="uk-card uk-card-default">
                <div class="uk-card-body">
                    <h3 class="uk-card-title">内容詳細</h3>
                    <p>{!! $val->other !!}</p>
                </div>
            </div>
        </div>
        @endif






        @if(isset( $eventsjoin[$val->id] ))
        <div class="uk-margin-top uk-width-xlarge">
            <div class="uk-card uk-card-default">
                <div class="uk-card-body">
                <h3>{{$val->sanka_explain}}</h3>
                    @foreach($eventsjoin[$val->id] as $k=>$v)
                    <div class="uk-grid-small" uk-grid>
                        <div class="uk-width-expand" >{{$v->join_name}}</div>
                        <div>{{number_format($v->join_price)}}円</div>
                    </div>
                    @endforeach

                </div>
            </div>
        </div>
        @endif

        <div class="uk-margin-top">
            <a href="#" class="js-modal-close uk-button uk-button-default nowrap">閉じる</a>
        </div>
    </div><!--modal__inner-->
</div><!--modal-->
