@extends('layouts.app')

@section('title', $title)

@section('content')
    <div id="page">
        <div class="header">
            <h2 class="edit-content" data-column="title">{{ $title }}</h2>
        </div>

        <div class="uk-container uk-section-xsmall">
@include('elements.pages.bannar')
            <div class="uk-grid">
                <div class="uk-width-1-2">
                    <ul uk-tab class="program">
                        @foreach($dateTerm as $key=>$value)
                            <?php $active = ($dates == $value)?"uk-active":"";?>
                            <li class="{{$active}}" ><a href="{{ route('event.webex', ['id' => $id,'date'=>$value]) }}" class="link" ><?=date("Y年m月d日",strtotime($value))?></a></li>
                        @endforeach
                    </ul>
                    <ul uk-tab class="program">
                        @foreach(config('pacd.space') as $key=>$value)
                            <?php $active = (!$key)?"uk-active":""; ?>
                            <li class="{{$active}} warning"><a href="{{$value['type']}}" class="program-list">{{$value['name']}}</a></li>
                        @endforeach
                    </ul>
                </div>
                <div class="uk-width-1-2">
                    @if( $attendee->doc_dl == 1 )
                        @if(isset($buttonFlag[1]) && $buttonFlag[1] > 0 )
                        <div class="uk-text-right uk-margin-large-right " id="typeA_downnload" >
                            <a href="{{ route('member.zip', ['event_id' => $event_id,'type'=>1, 'date'=>$dates ]) }}" class="uk-button uk-button-danger bg-blue" >【A会場】原稿一括ダウンロード</a>
                        </div>
                        @endif
                        @if(isset($buttonFlag[2]) && $buttonFlag[2] > 0 )
                        <div class="uk-text-right uk-margin-large-right uk-margin-top" id="typeB_downnload" >
                            <a href="{{ route('member.zip', ['event_id' => $event_id,'type'=>2, 'date'=>$dates ]) }}" class="uk-button uk-button-danger bg-indigo" >【B会場】原稿一括ダウンロード</a>
                        </div>
                        @endif
                        <div class="uk-margin-small-top uk-align-right">容量が大きいため多少お時間かかります。ご注意願います。</div>
                    @endif

                </div>
            </div>

            @foreach(config('pacd.space') as $key=>$value)
                <?php $active = ($key == 1)?"active":"";?>
                <div id="{{$value['type']}}" class="displayNone {{$active}}">

                    {{--発表中--}}
                    @if(isset($program[$key]->webex_url) && $program[$key]->webex_url)
                    <?php $c=$program[$key]->id;?>
                    <div class="uk-text-center uk-position-fixed uk-position-bottom-right drag" >
                        <a href="{{$program[$key]->webex_url}}" target=_blank>
                        <span uk-icon="play-circle" class="uk-text-danger"></span>
                        <br />
                        発表中<br />
                        @if(!empty($now[$c]->start_hour))
                        <?=$now[$c]->start_hour?>:<?=$now[$c]->start_minute?>～<?=$now[$c]->end_hour?>:<?=$now[$c]->end_minute?> <br />
                        @endif
                        @if(!empty($now[$c]->note))
                        {{substr($now[$c]->note,0,30)}}
                        @if(strlen($now[$c]->note) > 30)
                        ...
                        @endif
                        @endif

                        </a>
                    </div>
                    @endif
                    @if(isset($program[$key]->explain) && $program[$key]->explain)
                    <div class="uk-card uk-card-default uk-card-body uk-width-1-1@m">
                        {{$program[$key]->explain}}
                    </div>
                    @endif
                    <div class="uk-grid uk-margin-small-top ">
                        <div class="uk-width-1-1">
                            <table class="uk-table uk-table-hover uk-table-divider programTable">
                                <thead>
                                    <tr>
                                        <th class="uk-width-small">開催時刻</th>
                                        <th class="">演題名</th>
                                        <th class="uk-width-small">要旨原稿</th>
                                    </tr>
                                </thead>
                                @if(!empty($program[$key]->programlists))

                                <tbody>
                                    @foreach($program[$key]->programlists as $k=>$val)
                                        @php
                                            $presentation = $val->presentation;
                                            $user = @$presentation->presenter->attendee->user;
                                        @endphp
                                        <tr>
                                            <td>
                                                <?=sprintf("%02d",$val->start_hour)?>:
                                                <?=sprintf("%02d",$val->start_minute)?>
                                                ～
                                                <?=sprintf("%02d",$val->end_hour)?>:
                                                <?=sprintf("%02d",$val->end_minute)?>
                                            </td>
                                            <td>
                                                発表番号：{{ @$presentation->number }}<br>
                                                <?php
                                                    $h = config('pacd.honor');
                                                ?>
                                                発表者名：{{ @$user->sei }} {{ @$user->mei }} {{$h[@$val->honor]}}
                                                <p class="uk-margin-small-left">{{$val->note}}</p>
                                            </td>
                                            <td class="uk-text-center" style="width:300px;">
                                                @if(
                                                    empty($presentation->proceeding) &&
                                                    empty($presentation->flash) &&
                                                    empty($presentation->poster)
                                                )
                                                    配布資料はございません
                                                @endif
                                                @if(
                                                    (
                                                    $attendee->doc_dl == 1 &&
                                                    @$presentation->proceeding &&
                                                    $val->disp_status1 == 1
                                                    )
                                                )
                                                    @if($presentation->number)
                                                        <p class="uk-margin-small">
                                                        <a class="uk-button uk-button-primary uk-width-1-1 uk-button-small" href="{{ route('presentation.get.file', [@$presentation->number, 'proceeding',@$presentation->id, 'download' => true]) }}">
                                                            @if($category_type == 2)
                                                            配布資料1
                                                            @elseif($category_type == 4)
                                                            配布資料1
                                                            @else
                                                            講演要旨
                                                            @endif
                                                        </a>
                                                        </p>
                                                    @endif
                                                @endif

                                                @if(
                                                    (
                                                    $attendee->doc_dl == 1 &&
                                                    @$presentation->flash &&
                                                    $val->disp_status2 == 1
                                                    )
                                                )
                                                    @if($presentation->number)
                                                        <p class="uk-margin-small"><a class="uk-button uk-button-primary uk-width-1-1 uk-button-small" href="{{ route('presentation.get.file', [@$presentation->number, 'flash',@$presentation->id, 'download' => true]) }}">
                                                            @if($category_type == 2)
                                                            配布資料2
                                                            @elseif($category_type == 4)
                                                            配布資料2
                                                            @elseif($category_type == 3 &&  $event_id == 238)
                                                            プレゼンテーション資料
                                                            @else
                                                            フラッシュプレゼンテーションファイル
                                                            @endif
                                                        </a></p>
                                                    @endif

                                                @endif

                                                @if(                                                                                                        (
                                                    $attendee->doc_dl == 1 &&
                                                    @$presentation->poster &&
                                                    $val->disp_status3 == 1
                                                    )

                                                )
                                                    @if($presentation->number)
                                                        <p class="uk-margin-small"><a class="uk-button uk-button-primary uk-width-1-1 uk-button-small" href="{{ route('presentation.get.file', [@$presentation->number, 'poster',@$presentation->id, 'download' => true]) }}">
                                                            @if($category_type == 2)
                                                            配布資料3
                                                            @elseif($category_type == 4)
                                                            配布資料3
                                                            @elseif($category_type == 3 &&  $event_id == 238)
                                                            配布資料
                                                            @else
                                                            ポスター・配布資料等
                                                            @endif
                                                        </a></p>
                                                    @endif
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
