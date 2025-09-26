@extends('layouts.admin')

@section('title', $title)

@section('breadcrumb')
    @parent
    <li><span>{{ $title }}</span></li>
@endsection

@section('content')
    <div class="uk-section-small">
        <div class="uk-container uk-container-large">
            @if (session('status'))
                <div class="uk-alert-success" uk-alert>
                <a class="uk-alert-close" uk-close></a>
                    {{ session('status') }}
                </div>
            @endif

            <form action="{{ route("admin.$category_prefix.event.add") }}" method="POST" >
            @csrf
                <table class="uk-table">
                    <tr>
                        <th>イベントコード<br />
                        <span class="uk-label uk-label-danger">必須</span>
                        </th>
                        <td>
                            <input class="uk-input" type="text" name="code" placeholder="00001" value="{{ old('code',$code)}}" >
                            @if ($errors->any())
                                <div class="uk-text-danger">
                                    {{$errors->first('code')}}
                                </div>
                            @endif
                        </td>

                    </tr>

                    <tr>
                        <th>イベント名
                        <br />
                        <span class="uk-label uk-label-danger">必須</span>
                        </th>
                        <td><input class="uk-input" type="text" name="name" placeholder="イベント名を入力してください。" value="{{ old('name',$name)}}" >
                        @if ($errors->any())
                            <div class="uk-text-danger">
                                {{$errors->first('name')}}
                            </div>
                        @endif

                        </td>
                    </tr>
                    @if($category_prefix == "kyosan")
                    <tr>
                        <th>定員
                        </th>
                        <td><input class="uk-input uk-form-width-small" type="number" name="capacity" placeholder="半角数字" value="{{old('capacity',$capacity ?? '')}}" min="1">定員なしの場合は未入力</td>
                    </tr>
                    @endif
                    @if($category_prefix != "kyosan")
                        <tr>
                            <th>イベント概要
                            </th>
                            <td><input class="uk-input" type="text" name="event_info" placeholder="イベント概要を入力してください。" value="{{ old('event_info',$event_info)}}" >
                            @if ($errors->any())
                                <div class="uk-text-danger">
                                    {{$errors->first('event_info')}}
                                </div>
                            @endif
                            </td>
                        </tr>
                    @endif
                    @if($category_prefix != "kyosan")
                    <tr>
                        <th>主催
                        <br />
                        <span class="uk-label uk-label-danger">必須</span>
                        </th>
                        <td><input class="uk-input" type="text" name="sponser" placeholder="主催者を入力してください。" value="{{ old('sponser',$sponser)}}" >
                        @if ($errors->any())
                            <div class="uk-text-danger">
                                {{$errors->first('sponser')}}
                            </div>
                        @endif

                        </td>
                    </tr>
                    @endif
{{--
                    @if($category_prefix != "reikai")
                        <tr>
                            <th>イベント型</th>
                            <td>
                                <div class="cp_ipradio">
                                    <input type="radio" name="event_type" id="event_type1" value="1" @if(old('event_type',$event_type)=='1') checked  @endif >
                                    <label for="event_type1">通常イベント</label>
                                    <input type="radio" name="event_type" id="event_type2" value="2" @if(old('event_type',$event_type)=='2') checked  @endif >
                                    <label for="event_type2">協賛イベント</label>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>協賛</th>
                            <td><input class="uk-input" type="text" name="coworker" placeholder="協賛を入力してください。" value="{{ old('coworker',$coworker)}}" >
                            </td>
                        </tr>
                    @else
                        <input type="hidden" name="event_type"  value="1">
                    @endif
                    --}}
                    @if($category_prefix != "kyosan")
                    <input type="hidden" name="event_type"  value="1">
                    <tr>
                        <th>日時
                        </th>
                        <td>
                            <input class="uk-input datepicker uk-form-width-medium" type="text"  name="date_start" readonly placeholder="開始日を入力してください" value="{{ old('date_start',$date_start)}}"  />
                            <select name="date_start_time1" class="uk-select uk-form-width-xsmall" >
                                @for($i=9;$i<=18;$i++)
                                    <?php
                                        $i2 = sprintf("%02d",$i);
                                        $sel = "";
                                        if(old('date_start_time1',$date_start_time1) == $i2 ) $sel = "SELECTED";
                                    ?>
                                    <option value="{{$i2}}" <?=$sel?>>{{$i2}}</option>
                                @endfor
                            </select>
                            <select name="date_start_time2" class="uk-select uk-form-width-xsmall" >
                                @for($i=0;$i<=50;$i=$i+10)
                                    <?php
                                        $i2 = sprintf("%02d",$i);
                                        $sel = "";
                                        if(old('date_start_time2',$date_start_time2) == $i2 ) $sel = "SELECTED";
                                    ?>
                                    <option value="{{$i2}}" <?=$sel?> >{{$i2}}</option>
                                @endfor
                            </select>
                            ～

                            <input class="uk-input datepicker uk-form-width-medium" type="text"  name="date_end" readonly placeholder="終了日を入力してください" value="{{old('date_end',$date_end)}}" />

                            <select name="date_end_time1" class="uk-select uk-form-width-xsmall" >
                                @for($i=9;$i<=18;$i++)
                                    <?php
                                        $i2 = sprintf("%02d",$i);
                                        $sel = "";
                                        if(old('date_end_time1',$date_end_time1) == $i2 ) $sel = "SELECTED";
                                    ?>
                                    <option value="{{$i}}" <?=$sel?>>{{$i}}</option>
                                @endfor
                            </select>
                            <select name="date_end_time2" class="uk-select uk-form-width-xsmall" >
                                @for($i=0;$i<=50;$i=$i+10)
                                    <?php
                                        $i2 = sprintf("%02d",$i);
                                        $sel = "";
                                        if(old('date_end_time2',$date_end_time2) == $i2 ) $sel = "SELECTED";
                                    ?>
                                    <option value="{{$i}}" <?=$sel?> >{{$i}}</option>
                                @endfor
                            </select>

                            @if ($errors->any())
                            <div class="uk-text-danger">
                                <div>{{$errors->first('date_start')}}</div>
                                <div>{{$errors->first('date_end')}}</div>
                            </div>
                        @endif
                        </td>
                    </tr>
                    @endif
                    @if($category_prefix != "kyosan")
                    <tr>
                        <th>会場
                        </th>
                        <td><input class="uk-input" type="text" name="place" placeholder="会場を入力してください" value="{{old('place',$place)}}"></td>
                    </tr>
                    <tr>
                        <th>定員
                        </th>
                        <td><input class="uk-input uk-form-width-small" type="number" name="capacity" placeholder="半角数字" value="{{old('capacity',$capacity ?? '')}}" >定員なしの場合は未入力</td>
                    </tr>
                    @endif
                    @if($category_prefix != "kyosan")
                    <tr>
                        <th>会場住所
                        </th>
                        <td><input class="uk-input" type="text" name="event_address" placeholder="会場住所を入力してください" value="{{old('event_address',$event_address)}}" ></td>
                    </tr>
                    @endif
                    @if($category_prefix != "kyosan")
                    <tr>
                        <th>懇親会会場
                        </th>
                        <td><input class="uk-input" type="text" name="party" placeholder="場所を入力してください" value="{{old('party',$party)}}"></td>
                    </tr>
                    @endif
                    @if($category_prefix != "kyosan")
                    <tr>
                        <th>懇親会会場住所
                        </th>
                        <td><input class="uk-input" type="text" name="party_address" placeholder="住所を入力してください" value="{{old('party_address',$party_address)}}" ></td>
                    </tr>
                    @endif
                    {{--
                    <tr>
                        <th>地図表示
                        </th>
                        <td>
                        <div class="cp_ipradio">
                            <input type="radio" name="map_status" id="b_rb1" value="1" @if(old('map_status',$map_status)=='1') checked  @endif >
                            <label for="b_rb1">有効にする</label>
                            <input type="radio" name="map_status" id="b_rb2" value="0" @if(old('map_status',$map_status)=='0') checked  @endif >
                            <label for="b_rb2">無効にする</label>
                        </div>
                        </td>
                    </tr>
                    --}}
                    <input type="hidden" name="map_status" value="0" />
                    <tr>
                        <th>内容詳細
                        </th>
                        <td>
                        <div id="editor2">
                            <textarea class="uk-textarea" name="other" rows=5>{{old('other',$other)}}</textarea>
                            </div>
                        </td>
                    </tr>
                    @if($category_prefix != "kyosan")
                    <tr>
                        <th>講演者登録
                        </th>
                        <td>
                            <?php
                                $chk = "";
                                if(!$id && !old('presenter_flag')) $chk = "CHECKED";
                                if($presenter_flag == 1) $chk = "CHECKED";
                            ?>
                            <div class="uk-margin uk-grid-small uk-child-width-auto uk-grid">
                                <label><input class="uk-checkbox" name="presenter_flag" type="checkbox" {{$chk}} value="1" > 有効</label>
                            </div>
                        </td>
                    </tr>
                    @endif

                    <tr>
                        <th>請求書情報
                        </th>
                        <td>
                            請求書情報
                            <textarea class="uk-textarea" name="invoice_address" rows=7>{{old('invoice_address',$invoice_address)}}</textarea>

                            振込先
                            <textarea class="uk-textarea" name="bank_name" rows=2>{{old('bank_name',$bank_name)}}</textarea>
                            口座名
                            <textarea class="uk-textarea" name="bank_code" rows=2>{{old('bank_code',$bank_code)}}</textarea>

                            コメント
                            <textarea class="uk-textarea" name="invoice_memo" rows=7>{{old('invoice_memo',$invoice_memo)}}</textarea>

                        </td>
                    </tr>
                    <tr>
                        <th>領収書情報
                        </th>
                        <td>
                            コメント
                            <textarea class="uk-textarea" name="recipe_memo" rows=7>{{old('recipe_memo',$recipe_memo)}}</textarea>
                        </td>
                    </tr>
                </table>

                @if($category_prefix == "kyosan")
                    <h3>協賛金額</h3>
                @else
                    <h3>参加料金</h3>
                @endif
                    <div class="uk-width-1-3">
                        <div class="cp_ipradio">
                            <input type="hidden" name="join_enable"  value="0" />
                            <input type="radio" name="join_enable" id="join_enable1" value="1" @if(old('join_enable',$join_enable)=='1') checked  @endif >
                            <label for="join_enable1">有効にする</label>
                            <input type="radio" name="join_enable" id="join_enable2" value="0" @if(old('join_enable',$join_enable)=='0') checked  @endif >
                            <label for="join_enable2">無効にする</label>
                        </div>
                    </div>
                    <h3>出力形式</h3>
                    <input type="hidden" name="outputtype"  value="1" />
                    <div class="cp_ipradio" style="width:100px;">
                    @foreach(config('pacd.outputtype') as $key=>$value)

                        @php 
                            $checked = ""; 
                            if($outputtype == $key){
                                $checked = "checked";
                            }else{
                                if(old("outputtype") == $key
                                    || (!old("outputtype") && $key == 1 )
                                ){
                                    $checked = "checked";
                                }
                            }
                        @endphp
                        <input type="radio" name="outputtype" id="outputtype_{{$key}}" value="{{$key}}" {{$checked}} >
                        <label for="outputtype_{{$key}}">{{$value}}</label>
                        
                    @endforeach
                    </div>
                    
                    <p ><b>【合算】</b>講演会参加費と懇親会参加費が合算された形で、請求書及び領収書がそれぞれ1枚づつ出力されます。</p>
                    <p ><b>【分割】</b>講演会参加費と懇親会参加費が分割された形で、請求書及び領収書がそれぞれ2枚づつ出力されます。</p>
                    <h3>割引条件</h3>
                    @php
                        $checked1 = "";
                        $checked2 = "";
                        if(old("discountFlag",$discountFlag) == 1){
                            $checked1 = "checked";
                        }
                        if(old("discountFlag",$discountFlag) == 0){
                            $checked2 = "checked";
                        }
                    @endphp
                    <input type="radio" name="discountFlag" id="discountFlag_1" class="uk-radio" value=1 {{$checked1}}/>
                    <label for="discountFlag_1" >有効</label>
                    
                    <input type="radio" name="discountFlag" id="discountFlag_2" class="uk-radio uk-margin-small-left" value=0 {{$checked2}} />
                    <label for="discountFlag_2">無効</label>
                    <div id="discountArea" class="uk-margin-small-top" >
                        割引率
                        <input class="uk-input uk-form-width-medium uk-form-small" type="text" name="discountRate" value="{{old('discountRate',$discountRate)}}" >%
                        <div class="uk-margin-small-top" />
                            割引名
                            <input class="uk-input uk-form-width-medium uk-form-small" type="text" name="discountText" value="{{old('discountText',$discountText)}}" >
                        </div>
                        <div class="uk-margin-small-top" />
                            割引タイトル
                            <input class="uk-input uk-form-width uk-form-small" type="text" style="width:50%;" name="discountTitle" value="{{old('discountTitle',$discountTitle)}}" >
                        </div>
                        <div class="uk-margin-small-top" />
                            割引説明文
                            <input class="uk-input uk-form-width uk-form-small" type="text" name="discountNote" value="{{old('discountNote',$discountNote)}}" >
                        </div>
                    </div>
                    <h3>説明文</h3>
                    <textarea name="sanka_explain" class="uk-textarea">{{old('sanka_explain',$sanka_explain)}}</textarea>
                <table class="uk-table bordernone uk-margin-top">
                    <thead>
                        <tr>
                            <th class="uk-width-small">表示</th>
                            <th style="width:300px;">&nbsp;</th>
                            <th class="uk-text-left" >項目名</th>
                            <th class="uk-width-small">参加金額</th>
                        </tr>
                    </thead>
                    <tbody>
                    @for($i=0;$i<$listLoop;$i++)
                        <tr>
                            <td class="uk-text-center" >
                                <input type="hidden" name="join_status[{{$i}}]" value="0" />
                                <input type="checkbox"  class="uk-checkbox" name="join_status[{{$i}}]" value="1" {{old("join_status.$i") || $join_status[$i] ? "checked" : '' }} />
                            </td>
                            <td>
                                @if($category_prefix == "kyosan")

                                    @foreach(config('pacd.patternKyosan') as $key=>$value)
                                        <label>
                                            @php 
                                                $checked = ""; 
                                                if($pattern[$i] == $key){
                                                    $checked = "checked";
                                                }else{
                                                    if(old("pattern.$i") == $key
                                                        || (!old("pattern.$i") && $key == 1 )
                                                    ){
                                                        $checked = "checked";
                                                    }
                                                }
                                            @endphp
                                            <input type="radio" class="uk-radio" value="{{$key}}" name="pattern[{{$i}}]" {{$checked}}  />
                                            {{$value}}
                                        </label><br>
                                    @endforeach
                                @else
                                    @foreach(config('pacd.pattern') as $key=>$value)
                                        <label>
                                            @php 
                                                $checked = ""; 
                                                if($pattern[$i] == $key){
                                                    $checked = "checked";
                                                }else{
                                                    if(old("pattern.$i") == $key
                                                        || (!old("pattern.$i") && $key == 1 )
                                                    ){
                                                        $checked = "checked";
                                                    }
                                                }
                                            @endphp
                                            <input type="radio" class="uk-radio" value="{{$key}}" name="pattern[{{$i}}]" {{$checked}}  />
                                            {{$value}}
                                        </label><br>
                                    @endforeach
                                @endif
                            </td>
                            <td>
                                <input type="text" name="join_name[{{$i}}]" value="{{old("join_name.$i",$join_name[$i]) }}" class="uk-input" />
                            </td>
                            <td>
                                <input type="text" name="join_price[{{$i}}]"  value="{{old("join_price.$i",$join_price[$i])  }}" class="uk-input" />
                            </td>

                        </tr>
                    @endfor
                    </tbody>
                </table>

                <input type="hidden" name="id" value="{{old('id',$id)}}" />
                <button class="uk-button uk-button-primary" id="send"> 登録 </button>
            </form>
        </div>
    </div>
@endsection

@section('footer')
    <script src="{{ asset('js/admin/event.js') }}"></script>
    <script src="{{ asset('js/admin/pages2.js') }}"></script>
<script type="text/javascript">
    $(function(){
        $("#discountArea").hide();
        $(this).discount();
        $("[name='discountFlag']").click(function(){
            $(this).discount();
        });
    });
    $.fn.discount = function(){
        let tmp = $("[name='discountFlag']:checked").val();
        $("#discountArea").hide();
        if(tmp > 0){
            $("#discountArea").show();
        }
    }

    // 定員が0以下にならないように制御
    $('input[name="capacity"]').on('change', function() {
        if ($(this).val() && $(this).val() < 1 || $(this).val() > 10000) {
            $(this).val(''); // 1未満の場合は空にする
        }
    });
</script>
@endsection
