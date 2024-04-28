{{-- ヘッダー --}}
@if ($route_name === 'top')
    {{-- トップページのヘッダー --}}

    {{-- スライドショー --}}
    <div class="uk-position-relative uk-visible-toggle uk-light uk-margin-small-bottom" tabindex="-1" uk-slideshow="max-height:450;autoplay: true;">
        @php
            $imgs = @scandir(@public_path('img/top_slider'));
        @endphp
        <ul class="uk-slideshow-items">
            @if ($imgs)
                @foreach (array_diff($imgs, array('..', '.')) as $img)
                    <li class="uk-text-center">
                        <img src="{{ asset("img/top_slider/$img") }}" style="width:auto;max-height:450px;">
                    </li>
                @endforeach
            @endif
        </ul>
        <a class="uk-position-center-left uk-position-small uk-hidden-hover" href="#" uk-slidenav-previous uk-slideshow-item="previous"></a>
        <a class="uk-position-center-right uk-position-small uk-hidden-hover" href="#" uk-slidenav-next uk-slideshow-item="next"></a>
        <ul class="uk-slideshow-nav uk-dotnav uk-flex-center uk-margin uk-position-bottom"></ul>
        {{--
        <div class="uk-position-top uk-position-small">
            <img src="{{ asset("img/logo/jsac1.gif") }}" alt="logo">
        </div>--}}
    </div>
@else
    {{-- トップページ以外のヘッダー --}}

    {{-- ページタイトル --}}
    <div class="header">
        <h2 class="edit-content" data-column="title">{{ $title }}</h2>
    </div>
@endif



@include('elements.pages.bannar')



{{-- コンテンツ --}}
@if ($contents)
@include('elements.pages.top_contents')
    <div class="uk-container uk-section-xsmall">

        {{--企業協賛--}}
        @if ($route_name === 'kyosan')
            @if(isset($events['coworks']) && count($events['coworks']) > 0 )
                <h3 class="section__title contents_title edit-content uk-margin-remove">協賛イベントのご案内</h3>
                @foreach($events['coworks'] as $key=>$values)
                    <div uk-alert>{{ config('pacd.category.' . array_search($key, array_column(config('pacd.category'), 'key', 'prefix')) . '.name') }}</div>

                    @foreach($values as $k=>$val)
                        @if($val->enabled)
                            <div class="uk-grid-small border-bottom border-gray uk-margin-top " uk-grid>
                                <div class="uk-width-3-4@s uk-margin-small-bottom">
                                    <a href="javascript:void(0);" class="js-modal-open" id="modal-{{$val->id}}"  >{{$val->name}}</a>
                                    <span class="uk-label uk-label-danger uk-margin-small-left">参加受付中</span>
                                    <div class="uk-width-1-4@s">&nbsp;</div>
                                </div>

                                <div class="uk-width-1-4@s uk-margin-bottom">
                                    <button class="uk-button uk-button-default nowrap js-modal-open uk-width-1-1" id="modal-{{$val->id}}" >詳細</button>
                                    @if($val->enabled)
                                    <a href="{{ route(config("pacd.categorykey.$val->category_type.prefix").'_attendee', $val->code) }}" class="uk-margin-small-top uk-button uk-button-primary uk-width-1-1" >参加申し込み</a>
                                    @endif
                                </div>
                            </div>
                            @include('elements.pages.modal')
                        @endif
                    @endforeach


                @endforeach
            @endif
        @endif

        @foreach ($contents as $content)
            @include('elements.pages.section', $content)
        @endforeach
    </div>
@endif
