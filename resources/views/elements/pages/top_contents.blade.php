

@if ($route_name == 'top' || $route_name == 'eventlist')
    {{-- イベント情報 --}}

    <div class="uk-container uk-section-xsmall">
        <section>
        @if ($route_name == 'top' )
            <h4 class="uk-text-success">
        本懇談会では高分子分析の発展を目的に各種行事の開催を行い，お互いの情報共有・技術共有を促進しています。また，これらをまとめた書籍出版や分析討論会での講演等の活動を行っています
    </h4>
        @endif

            <h3 id="content3" class="section__title contents_title uk-margin-remove" style="font-size:12pt;">次回のイベント情報</h3>
            <div class="uk-padding">
                {{-- 通常イベント --}}
                @foreach($events['events'] as $key=>$values)

                    <div uk-alert>{{ config('pacd.category.' . array_search($key, array_column(config('pacd.category'), 'key', 'prefix')) . '.name') }}</div>

                    @foreach($values as $k=>$val)
                    <div class="uk-grid" uk-grid>
                        <div class="uk-width-1-6@s">
                            @if(preg_match("/^2999/",$val->date_start))
                            日程未定(調整中)
                            @else
                            {{date('Y年m月d日',strtotime($val->date_start))}}～
                            {{date('m月d日',strtotime($val->date_end))}}
                            @endif
                        </div>
                        <div class="uk-width-5-6@s uk-margin-small-bottom">
                            <div class="uk-grid">
                                <div class="uk-width-2-3@s">
                                    <a href="javascript:void(0);" class="js-modal-open" id="title-{{$val->id}}">{{$val->name}}</a>

                                    @if($val->enabled)

                                    <span class="uk-label uk-label-danger uk-margin-small-left">参加受付中</span>

                                    @else
                                    {{-- <span class="uk-label bg-dark-gray uk-margin-small-left">募集締切</span> --}}
                                    @endif

                                </div>
                                <div class="uk-width-1-3@s">
                                    <button class="uk-button uk-button-default nowrap js-modal-open uk-width-1-1" id="modal-{{$val->id}}" >詳細</button>
                                    @if($val->enabled)
                                    <a href="{{ route(config("pacd.categorykey.$val->category_type.prefix").'_attendee', $val->code) }}" class="uk-margin-small-top uk-button uk-button-primary uk-width-1-1" >参加申し込み</a>
                                    @endif
                                </div>

                            </div>
                        </div>

                    </div>

                    @include('elements.pages.modal')
                    @endforeach
                @endforeach
            </div>


            {{--  協賛イベント  --}}

            @if(count($events['coworks']) > 0 )
            <h3 class="section__title contents_title edit-content uk-margin-remove">協賛イベントのご案内</h3>
            <div class="uk-padding">

                @foreach($events['coworks'] as $key=>$values)
                    <div uk-alert>{{ config('pacd.category.' . array_search($key, array_column(config('pacd.category'), 'key', 'prefix')) . '.name') }}</div>
                    @foreach($values as $k=>$val)
                    <div class="uk-grid-small border-bottom border-gray" uk-grid>

                        <div class="uk-width-3-4@s uk-margin-small-bottom">
                            <a href="javascript:void(0);" class="js-modal-open" id="modal-{{$val->id}}"  >{{$val->name}}</a>

                            @if($val->enabled)
                            <span class="uk-label uk-label-danger uk-margin-small-left">参加受付中</span>
                            @else
                            <span class="uk-label bg-dark-gray uk-margin-small-left">募集締切</span>
                            @endif

                        </div>
                        <div class="uk-width-1-4@s uk-margin-bottom">
                            <button class="uk-button uk-button-default nowrap js-modal-open uk-width-1-1" id="modal-{{$val->id}}" >詳細</button>
                            @if($val->enabled)
                            <a href="{{ route(config("pacd.categorykey.$val->category_type.prefix").'_attendee', $val->code) }}" class="uk-margin-small-top uk-button uk-button-primary uk-width-1-1" >参加申し込み</a>
                            @endif
                        </div>
                    </div>

                    @include('elements.pages.modal')
                    @endforeach
                @endforeach
            </div>
            @endif
        </section>
    </div>
@endif
