@extends('layouts.app')

@section('title', $title)

@section('content')
    <div id="page">
        <div class="header">
            <h2 class="edit-content" data-column="title">{{ $title }}</h2>
        </div>
        <div class="uk-container">
            @if (session('status'))
                <div class="uk-alert-success" uk-alert>
                    <a class="uk-alert-close" uk-close></a>
                    <p>{{ session('status') }}</p>
                </div>
            @endif

            <div class="uk-section-xsmall">
                <div class="uk-form-horizontal">
                    <fieldset class="uk-fieldset">
                        <div>
                            <div class="uk-form-label">イベント名</div>
                            <div class="uk-form-controls uk-form-controls-text">{{ $event->name }}</div>
                        </div>
                        @if($form['prefix'] != "kyosan_attendee")
                            <div>
                                <div class="uk-form-label">開催期間</div>
                                <div class="uk-form-controls uk-form-controls-text">{{ $event->date_start }}～{{ $event->date_end }}</div>
                            </div>
                            <div>
                                <div class="uk-form-label">開催場所</div>
                                <div class="uk-form-controls uk-form-controls-text">{{ $event->place }}</div>
                            </div>
                        @endif
                    </fieldset>
                </div>
            </div>

            <div class="uk-section-xsmall">
                <div class="uk-form-horizontal">
                    <fieldset class="uk-fieldset">
                        <div>
                            <div class="uk-form-label">参加者番号</div>
                            <div class="uk-form-controls uk-form-controls-text">{{ sprintf('%010d', $attendee->event_number) }}</div>
                        </div>
                        <div class="uk-clearfix">
                            <div class="uk-form-label">会員番号</div>
                            <div class="uk-form-controls uk-form-controls-text">
                            @if($user->type == 1)
                            非会員
                            @else
                            {{ $user->type_number }}
                            @endif
                            </div>
                        </div>
                        <div class="uk-clearfix">
                            <div class="uk-form-label">氏名</div>
                            <div class="uk-form-controls uk-form-controls-text">{{ $user->sei }}　{{ $user->mei }}</div>
                        </div>
                        <div class="uk-clearfix">
                            <div class="uk-form-label">氏名（ふりがな）</div>
                            <div class="uk-form-controls uk-form-controls-text">{{ $user->sei_kana }}　{{ $user->mei_kana }}</div>
                        </div>

                        @if($user->type == 3)
                        <div class="uk-clearfix">
                            <div class="uk-form-label">法人名</div>
                            <div class="uk-form-controls uk-form-controls-text">{{ $user->cp_name }}</div>
                        </div>
                        @endif
                        @if($user->type == 4)
                        <div class="uk-clearfix">
                            <div class="uk-form-label">所属</div>
                            <div class="uk-form-controls uk-form-controls-text">{{ $user->busyo }}</div>
                        </div>
                        @endif
                        <div class="uk-clearfix">
                            <div class="uk-form-label">メールアドレス</div>
                            <div class="uk-form-controls uk-form-controls-text">{{ $user->email }}</div>
                        </div>

                        <div class="uk-clearfix" >
                            <div class="uk-form-label">電話番号</div>
                            <div class="uk-form-controls uk-form-controls-text">{{ $user->tel }}</div>
                        </div>

                    </fieldset>
                </div>
            </div>

            <div class="uk-section-xsmall">
                <form method="POST" action="{{ route($form['prefix'] . ".update", [$attendee->id]) }}" class="uk-form-horizontal" id="attendee_update_form">
                    @csrf
                    @method('put')
                    <fieldset class="uk-fieldset">
                        @if($form['prefix'] === "kyosan_attendee")
                            <div class="uk-clearfix">
                                <label class="uk-form-label">振込予定日</label>
                                <div class="uk-form-controls uk-form-controls-text">
                                    <input type="date" name="paydate" class="uk-input w-50 uk-width-1-5" value="{{ $attendee->paydate }}">
                                </div>
                            </div>
                        @endif
                        {{-- カスタムインプット項目 --}}
                        <div class="custom_input_area">
                        @if($inputs->count())
                            @include('elements.forms.custom_inputs', [$inputs, 'custom_data' => $attendee->custom_form_data->keyBy('form_input_value_id')])
                        @endif
                        </div>
                        {{-- 参加費用選択 --}}
                        @if($event->sanka_explain)
                        <h3>{!! nl2br($event->sanka_explain) !!}</h3>
                        @endif
                        @php $joins = $event->event_joins->where('join_status', 1)->where('status', 1)->whereIn('pattern', [1, 2]) @endphp
                        @if ($joins->count())
                            <div class="uk-margin">
                                <label for="event_join_id" class="uk-form-label">参加料金</label>
                                <div class="uk-form-contorls uk-grid-small uk-child-width-auto uk-grid">
                                    <?php
                                        $explode = explode(",",$attendee->event_join_id_list);

                                    ?>
                                    @foreach ($joins as $join)
                                        <?php
                                            $chk = "";
                                            if(in_array($join->id,$explode)){
                                                $chk = "CHECKED";
                                            }
                                        ?>
                                        <div class="uk-width-1-1 uk-margin-small-top">
                                            <label><input class="uk-checkbox" type="checkbox" name="event_join_id_list[{{ $join->id }}]" value="{{ $join->id }}" {{$chk}}>
                                                {{ $join->join_name }}：{{ number_format($join->join_price) }}円{{ ($join->join_fee) ? "（懇親会費：" . number_format($join->join_fee) . "円）" : '' }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if ($event->discountFlag == 1)
                        <div class="uk-margin">
                            <table >
                                <tr>
                                    <td style="border:none;">
                                        <label for="event_join_id" class="uk-form-label">過去の{{str_replace("参加者情報編集","",$title)}}参加状況</label>
                                    </td>
                                    <td style="border:none;">
                                        <div class="uk-form-contorls ">
                                            <div style="padding-top:10px;">
                                                以下の{{str_replace("参加者情報編集","",$title)}}に参加された方はチェックを入れていただくと、今回の参加費が割引になります。
                                            </div>
                                            <div class="uk-grid-small uk-child-width-auto uk-grid ">
                                                <div class="uk-margin-small-top">
                                                    <?php
                                                    $checked = "";
                                                    if($attendee->discountSelectFlag == 1){
                                                        $checked = "checked";
                                                    }
                                                    ?>
                                                    <input type="checkbox" name="discountSelectFlag" id="discountSelectFlag" value="1" class="uk-checkbox"  {{$checked}} />
                                                    <label for="discountSelectFlag">{{$event->discountText}}
                                                    </label>
                                                    <br />
                                                    <br />
                                                    参加番号
                                                    <input type="text" name="discountSelectText" id="discountSelectText" value="{{$attendee->discountSelectText}}" class="uk-input"  />
                                                </div>
                                            </div>
                                        </div>

                                    </td>
                                </tr>
                            
                            </table>
                        </div>
                        @endif


                        @if($form['prefix'] == "kyosan_attendee")
                            <hr />

                            @php 
                            $joins3 = $event->event_joins->where('join_status', 1)->where('status', 1)->whereIn('pattern', [3])->first();
                            $joins4 = $event->event_joins->where('join_status', 1)->where('status', 1)->whereIn('pattern', [4])->first();
                            @endphp
                            <div>
                                @if($joins3 || $joins4)
                                    <p>展示参加者について</p>
                                    
                                    <p>{{$kyosanTitle->tenjikaiTitle}}</p>
                                    <p class="uk-margin-left">
                                        {!! nl2br($kyosanTitle->tenjikaiNote) !!}
                                    </p>
                                    <p>{{$kyosanTitle->konsinkaiTitle}}</p>
                                    <p class="uk-margin-left">
                                        {!! nl2br($kyosanTitle->konsinkaiNote) !!}
                                    </p>
                                    <br />
                                    <br />
                                @endif
                                @if($joins3)
                                    <p>{{$kyosanTitle->tenjikaiTitle}}</p>
                                    <div class="uk-grid uk-child-width-auto uk-flex-middle" uk-grid>
                                        <div>
                                            <input type="checkbox" name="tenjiSanka1Status" value="on"  @if ($attendee->tenjiSanka1Status == 'on') checked @endif />
                                        </div>
                                        <div>
                                            氏名1
                                        </div>
                                        <div class="uk-width-expand">
                                            <input type="text" name="tenjiSanka1Name" value="{{$attendee->tenjiSanka1Name}}"  class="uk-input uk-width-1-4" />
                                            <input type="hidden" name="tenjiSanka1Money" value="{{$attendee->tenjiSanka1Money}}"  />
                                            {{number_format($attendee->tenjiSanka1Money)}} 円
                                        </div>
                                    </div>
                                    <div class="uk-grid uk-child-width-auto uk-flex-middle uk-margin-remove-top" uk-grid>
                                        <div>
                                            <input type="checkbox" name="tenjiSanka2Status" value="on" @if($attendee->tenjiSanka2Status == 'on') checked @endif />
                                        </div>
                                        <div>
                                            氏名2
                                        </div>
                                        <div class="uk-width-expand">
                                            <input type="text" name="tenjiSanka2Name" value="{{$attendee->tenjiSanka2Name}}" class="uk-input uk-width-1-4" />
                                            <input type="hidden" name="tenjiSanka2Money" value="{{$attendee->tenjiSanka2Money}}"  />
                                            {{number_format($attendee->tenjiSanka2Money)}} 円
                                        </div>
                                    </div>
                                @endif
                                @if($joins4)
                                    <p>{{$kyosanTitle->konsinkaiTitle}}</p>
                                    <div class="uk-grid uk-child-width-auto uk-flex-middle" uk-grid>
                                        <div>
                                            <input type="checkbox" name="konsinkaiSanka1Status" value="on" @if($attendee->konsinkaiSanka1Status == 'on') checked @endif />
                                        </div>
                                        <div>
                                            氏名1
                                        </div>
                                        <div class="uk-width-expand">
                                            <input type="text" name="konsinkaiSanka1Name" value="{{$attendee->konsinkaiSanka1Name}}" class="uk-input uk-width-1-4" />
                                            <input type="hidden" name="konsinkaiSanka1Money" value="{{$attendee->konsinkaiSanka1Money}}"  />
                                            {{number_format($attendee->konsinkaiSanka1Money)}} 円
                                        </div>
                                    </div>
                                    <div class="uk-grid uk-child-width-auto uk-flex-middle uk-margin-remove-top" uk-grid>
                                        <div>
                                            <input type="checkbox" name="konsinkaiSanka2Status" value="on" @if($attendee->konsinkaiSanka2Status == 'on') checked @endif />
                                        </div>
                                        <div>
                                            氏名2
                                        </div>
                                        <div class="uk-width-expand">
                                            <input type="text" name="konsinkaiSanka2Name" value="{{$attendee->konsinkaiSanka2Name}}" class="uk-input uk-width-1-4" />
                                            <input type="hidden" name="konsinkaiSanka2Money" value="{{$attendee->konsinkaiSanka2Money}}"  />
                                            {{number_format($attendee->konsinkaiSanka2Money)}} 円
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif


                        @if($event->join_enable == 1)
                        <div>
                            <div class="uk-form-label">参加費支払い情報</div>
                            <div class="uk-form-controls uk-form-controls-text">{{ $attendee->is_paid ? '支払い済み' : '未払い' }}</div>
                        </div>
                        @endif
                        <div class="uk-section-small">
                            <a href="{{ route('mypage.' .$form['category_prefix']) }}" class="uk-button uk-button-secondary">戻る</a>
                            <button type="submit" class="uk-button uk-button-primary bg-green">変更</button>
                        </div>

                    </fieldset>
                </form>
            </div>
        </div>
    </div>
@endsection
