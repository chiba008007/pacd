@extends('layouts.admin')

@section('title', $title)

@section('breadcrumb')
    @parent
    @foreach ($breadcrumbs as $breadcrumb)
        <li><a href="{{ $breadcrumb['url'] }}">{{ $breadcrumb['title'] }}</a></li>
    @endforeach
    <li><span>編集</span></li>
@endsection

@section('content')
    <div class="uk-section-small">
        <div class="uk-container uk-container-large">

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
                        <div>
                            <div class="uk-form-label">ログインID</div>
                            <div class="uk-form-controls uk-form-controls-text">{{ $user->login_id }}</div>
                        </div>
                        <div>
                            <div class="uk-form-label">氏名</div>
                            <div class="uk-form-controls uk-form-controls-text">{{ $user->sei }}　{{ $user->mei }}</div>
                        </div>
                        <div>
                            <div class="uk-form-label">氏名（ふりがな）</div>
                            <div class="uk-form-controls uk-form-controls-text">{{ $user->sei_kana }}　{{ $user->mei_kana }}</div>
                        </div>
                        <div>
                            <div class="uk-form-label">法人名</div>
                            <div class="uk-form-controls uk-form-controls-text">{{ $user->cp_name }}</div>
                        </div>
                        <div>
                            <div class="uk-form-label">所属</div>
                            <div class="uk-form-controls uk-form-controls-text">{{ $user->busyo }}</div>
                        </div>
                        <div>
                            <div class="uk-form-label">メールアドレス</div>
                            <div class="uk-form-controls uk-form-controls-text">{{ $user->email }}</div>
                        </div>


                    </fieldset>
                </div>
            </div>

            <div class="uk-section-xsmall">
                <form method="POST" action="{{ route('admin.attendees.update', [$form['category_prefix'], $attendee->id]) }}" class="uk-form-horizontal" id="attendee_update_form">
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
                        @if($inputs->count())
                            @include('elements.forms.custom_inputs', [$inputs, 'custom_data' => $attendee->custom_form_data->keyBy('form_input_value_id')])
                        @endif

                        {{-- 参加費用選択 --}}
                        @php $joins = $event->event_joins->where('join_status', 1)->where('status', 1)->whereIn('pattern', [1, 2]) @endphp
                        @if ($joins->count())
                            <div class="uk-margin uk-width-1-1 uk-margin-small-top">
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
                                            <label><input class="uk-checkbox" type="checkbox" name="event_join_id[{{$join->id}}]" id="event_join_id_{{$join->id}}" value="{{ $join->id }}" {{$chk}} >
                                                {{ $join->join_name }}：{{ number_format($join->join_price) }}円{{ ($join->join_fee) ? "（懇親会費：" . number_format($join->join_fee) . "円）" : '' }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if ($event->discountFlag == 1)
                        <div class="uk-margin-small">
                            
                            <label class="uk-form-label">割引率({{$event->discountRate}}%)</label>
                            <div class="uk-form-controls uk-form-controls-text">
                                以下に参加された方はチェックを入れていただくと、今回の参加費が割引になります。<br />
                                過去の参加状況
                                <br />
                                @php
                                    $checked1 = "";
                                    if(old("discountSelectFlag",$attendee->discountSelectFlag) == 1){
                                        $checked1 = "checked";
                                    }
                                @endphp
                                <input type="checkbox" name="discountSelectFlag" id="discountSelectFlag" value="1" class="uk-checkbox"  {{$checked1}} />
                                <label for="discountSelectFlag" >{{$event->discountText}}</label>

                                <div class="uk-margin-small-top" />
                                    参加番号
                                    <input class="uk-input uk-form-width-medium uk-form-small" type="text" name="discountSelectText" value="{{old('discountSelectText',$attendee->discountSelectText)}}" >
                                </div>
                            </div>
                        </div>
                        @endif
                        <div class="uk-margin-small">
                            <label class="uk-form-label">参加費支払い状況</label>
                            <div class="uk-form-controls uk-form-controls-text">
                                <label><input type="radio" name="is_paid" value="0" class="uk-radio" @if(old('is_paid', $attendee->is_paid) == 0) checked @endif> 未払い</label>
                                <label><input type="radio" name="is_paid" value="1" class="uk-radio" @if(old('is_paid', $attendee->is_paid) == 1) checked @endif> 支払い済み</label>
                                @error('is_paid')
                                    <div class="uk-text-danger">
                                        <p>{{ $message }}</p>
                                    </div>
                                @enderror
                            </div>
                        </div>


                    <div class="uk-margin">
                        <label class="uk-form-label">請求書・領収書ダウンロード</label>
                        <div class="uk-form-controls uk-form-controls-text">
                            <label><input type="radio" name="is_enabled_invoice" value="0" class="uk-radio"  @if(old('is_enabled_invoice', $attendee->is_enabled_invoice) == 0) checked @endif> ダウンロード不可</label>
                            <label><input type="radio" name="is_enabled_invoice" value="1" class="uk-radio"  @if(old('is_enabled_invoice', $attendee->is_enabled_invoice) == 1) checked @endif> ダウンロード可</label>
                            @error('is_enabled_invoice')
                                <div class="uk-text-danger">
                                    <p>{{ $message }}</p>
                                </div>
                            @enderror
                        </div>
                    </div>

                    @if($form['prefix'] == "kyosan_attendee")
                    <hr />
                    <div>
                        @if ($event_joins_status[3] || $event_joins_status[4])
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
                        @if ($event_joins_status[3])
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
                                    {{number_format($attendee->tenjiSanka1Money)}}
                                    円
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
                                    {{number_format($attendee->tenjiSanka2Money)}}
                                    円
                                </div>
                            </div>
                        @endif
                        @if ($event_joins_status[4])
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
                                    {{number_format($attendee->konsinkaiSanka1Money)}}
                                    円
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
                                    {{number_format($attendee->konsinkaiSanka2Money)}}
                                    円
                                </div>
                            </div>
                        @endif
                    </div>
                    @endif

                        <div class="uk-section-small">
                            <button type="submit" class="uk-button uk-button-primary">更新</button>
                        </div>

                    </fieldset>
                </form>
            </div>
        </div>
    </div>

@endsection
