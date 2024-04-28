@extends('layouts.admin')

@section('title', $title)

@section('breadcrumb')
    @parent
    <li><span>{{ $title }}</span></li>
@endsection

@section('content')
    <div class="uk-section-small">
        <div class="uk-container uk-container-large">
             <div class="uk-alert-warning" uk-alert>
                会場・イベント・日程の選択を行ってください。<br />
                選択後、プログラム入力画面が表示されます。
             </div>
            @if (session('status'))
                <div class="uk-alert-success" uk-alert>
                <a class="uk-alert-close" uk-close></a>
                    {{ session('status') }}
                </div>
            @endif
            <form action="{{ route("admin.$category_prefix.event.program.add") }}" method="POST" id="form" >
            @csrf
                <div class="uk-grid">
                    <div class="uk-width-1-4">会場を選択</div>
                    <div class="uk-width-3-4">
                        <div class="cp_ipradio w30">
                            @foreach (config('pacd.space') as $key=>$space)
                            <?php
                            $sel = "";
                            if($request->get( 'type' ) == $key) $sel = "checked";
                            ?>
                            <input type="radio" name="type" id="sp_{{$key}}" value="{{$key}}" <?=$sel?> >
                            <label for="sp_{{$key}}" class="pd5">{{$space['name']}}</label>
                            @endforeach
                        </div>
                        @if ($errors->any())
                            <div class="uk-text-danger">
                                <div>{{$errors->first('type')}}</div>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="uk-grid uk-margin">
                    <div class="uk-width-1-4">イベント選択</div>
                    <div class="uk-width-3-4">
                        @if($id > 0 )
                        <input type="hidden" name="event_id" value="{{$id}}" />
                        <select disabled class="uk-select uk-width-1-1">
                        @else
                        <select name="event_id"  class="uk-select uk-width-1-1">
                        @endif

                            <option value="" >イベントを選択してください。</option>
                            @foreach($event as $key=>$val)
                            @if(old('event_id') == $val->id || $val->id == $id || $request->get('event_id') == $val->id)
                            <option value="{{$val->id}}" selected >{{$val->name}}</option>
                            @else
                            <option value="{{$val->id}}">{{$val->name}}</option>
                            @endif
                            @endforeach
                        </select>
                        @if ($errors->any())
                            <div class="uk-text-danger">
                                <div>{{$errors->first('event_id')}}</div>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="uk-grid uk-margin">
                    <div class="uk-width-1-4">日程選択</div>
                    <input type="hidden" name="event_date" value="<?=$request->get( 'date' )?>" />
                    <div class="uk-width-3-4">
                        <select name="date" class="uk-select uk-width-1-1">
                            <option value="" >日程を選択してください。(イベント選択後日程が選択可能になります。)</option>
                        </select>
                        @if ($errors->any())
                            <div class="uk-text-danger">
                                <div>{{$errors->first('date')}}</div>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="uk-grid uk-margin">
                    <div class="uk-width-1-4">webexURL</div>
                    <div class="uk-width-3-4">
                        <input type="text" name="webex_url" id="webex_url" value="{{@$program->webex_url}}" class="uk-input" />
                    </div>
                </div>
                <div class="uk-grid uk-margin">
                    <div class="uk-width-1-4">説明</div>
                    <div class="uk-width-3-4">
                        <textarea name="explain" class="uk-textarea" rows="5">{{@$program->explain}}</textarea>
                    </div>
                </div>
                @if($request->get('event_id'))
                <div class="uk-grid uk-margin">
                    <div class="uk-width-1-4">資料一括ダウンロード</div>
                    <div class="uk-width-3-4">
                        @if(isset($filecount) && $filecount > 0)
                        <a href="{{ route("admin.zipdownload",[
                            $request->get('event_id'),
                            $request->get('type'),
                            $request->get('date')
                            ])}}" class="btn" >一括ダウンロード</a>
                        @else
                            ダウンロードできるファイルがありません。
                        @endif
                    </div>
                </div>
                @endif

                <?php
                    if(
                        $request->get('type') &&
                        $request->get('event_id') &&
                        $request->get('date')
                    ):
                ?>
                <button class="uk-button uk-button-primary uk-width-1-1 botton-sticky" id="send" > 登録 </button>

                <table class="uk-table uk-table-small uk-table-divider">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>午前/午後</th>
                            <th style="width:240px;">時刻</th>
                            <th>発表番号</th>
                            <th>講演内容
                            <br />
                            <select id="honor" class='uk-select uk-width-1-2' >
                                @foreach(config('pacd.honor') as $key=>$honor)
                                    <option value='{{$key}}' {{$sel}} >{{$honor}}</option>
                                @endforeach
                            </select>
                            </th>
                            <th style="width:240px;">
                                原稿ダウンロード
                                <input type="checkbox" name="checkall" value="on" />
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @for($i=1;$i<=35;$i++)
                        <?php
                            $num=$i-1;
                        ?>
                        <tr>
                            <td class="uk-text-center">
                                <input type="hidden" name="number[{{$i}}]" value="0" />
                                <input class="uk-checkbox number" type="checkbox" name="number[{{$i}}]" id="number-{{$i}}"  value="1" @if(@$programlist[$num]->enable == 1) checked @endif>
                            </td>
                            <td>

                                <select name="ampm[{{$i}}]" id="ampm-{{$i}}" class="uk-select uk-width-1-1 ampm">
                                    @foreach (config('pacd.ampm') as $k=>$val)

                                        <option value="{{$k}}" @if(@$programlist[$num]->ampm == $k) selected @endif>{{$val}}</option>
                                    @endforeach
                                </select>
                            </td>

                            <td class="uk-width-1-6">
                                <select name="start_hour[{{$i}}]" id="start_hour-{{$i}}"  class="uk-select uk-width-1-3 start_hour">
                                    @for($h=9;$h<=23;$h++)
                                        <option value="<?=$h?>" @if(@$programlist[$num]->start_hour == $h) selected @endif><?=$h?></option>
                                    @endfor
                                </select>:
                                <select name="start_minute[{{$i}}]" id="start_minute-{{$i}}"  class="uk-select uk-width-1-2 start_minute">
                                    @for($m=0;$m<=60;$m=$m+5)
                                        <option value="<?=$m?>" @if(@$programlist[$num]->start_minute == $m) selected @endif><?=$m?></option>
                                    @endfor
                                </select>
                                <select name="end_hour[{{$i}}]"  class="uk-select uk-width-1-3 end_hour" id="end_hour-{{$i}}" >
                                    @for($h=9;$h<=23;$h++)
                                        <option value="<?=$h?>" @if(@$programlist[$num]->end_hour== $h) selected @endif><?=$h?></option>
                                    @endfor
                                </select>:
                                <select name="end_minute[{{$i}}]"  class="uk-select uk-width-1-2 end_minute" id="end_minute-{{$i}}">
                                    @for($m=0;$m<=60;$m=$m+5)
                                    <option value="<?=$m?>" @if(@$programlist[$num]->end_minute == $m) selected @endif><?=$m?></option>
                                    @endfor
                                </select>
                            </td>
                            <td>
                                <input type="hidden" id="presentation_id-reg-{{$i}}" value="{{@$programlist[$num]->presentation_id}}" />
                                <select name="presentation_id[{{$i}}]" class="presentation_id uk-select uk-width-1-1" id="presentation_id-{{$i}}" data-index="{{ $i }}">
                                    <option value="{{ old("presentation_id.$i") }}"></option>
                                </select>
                            </td>
                            <td>
                                <textarea name="note[{{$i}}]" class="note uk-textarea" rows="5"  id="note-{{$i}}">{{@$programlist[$num]->note}}</textarea>
                                <br />
                                <select name="honor[{{$i}}]" class='uk-select uk-width-1-2 honor' >
                                    @foreach(config('pacd.honor') as $key=>$honor)
                                        <?php
                                            $sel = "";
                                            if(!empty($programlist[$num]->honor)
                                            && $programlist[$num]->honor == $key) $sel = "SELECTED";
                                        ?>
                                        <option value='{{$key}}' {{$sel}} >{{$honor}}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td id="downloads-{{$i}}">
                                <?php

                                    $txt1 = "配布資料1";
                                    $txt2 = "配布資料2";
                                    $txt3 = "配布資料3";
                                    if($category_prefix == "touronkai") $txt1 = "講演要旨";
                                    if($category_prefix == "touronkai") $txt2 = "フラッシュ";
                                    if($category_prefix == "touronkai") $txt3 = "配布資料等";
                                ?>
                                <?php if(@$programlist[$num]->number): ?>
                                    <div class="uk-width-1-1">
                                        <a class="uk-button uk-button-default uk-width-1-2" href="{{ route('presentation.get.file', ['','']) }}/{{@$programlist[$num]->number}}/proceeding/{{@$programlist[$num]->presentation_id}}?download=true"><?=$txt1?></a>
                                        <label>
                                        <?php
                                            $chk = "";
                                            if($programlist[$num]->disp_status1 == 1) $chk = "CHECKED";
                                        ?>
                                        <input type="checkbox" name="disp_status1[{{$i}}]" value="1" <?=$chk?> class="disp_status"  />有効

                                        </label>
                                    </div>
                                    <div class="uk-width-1-1">
                                        <a class="uk-button uk-button-default uk-width-1-2" href="{{ route('presentation.get.file', ['','']) }}/{{@$programlist[$num]->number}}/flash/{{@$programlist[$num]->presentation_id}}?download=true"><?=$txt2?></a>
                                        <?php
                                            $chk = "";
                                            if($programlist[$num]->disp_status2 == 1) $chk = "CHECKED";
                                        ?>
                                        <label><input type="checkbox" name="disp_status2[{{$i}}]" value="1" <?=$chk?> class="disp_status" />有効</label>
                                    </div>
                                    <div class="uk-width-1-1">
                                        <a class="uk-button uk-button-default uk-width-1-2" href="{{ route('presentation.get.file', ['','']) }}/{{@$programlist[$num]->number}}/poster/{{@$programlist[$num]->presentation_id}}?download=true"><?=$txt3?></a>
                                        <?php
                                            $chk = "";
                                            if($programlist[$num]->disp_status3 == 1) $chk = "CHECKED";
                                        ?>
                                        <label><input type="checkbox" name="disp_status3[{{$i}}]" value="1" <?=$chk?> class="disp_status"  />有効</label>
                                    </div>
                                <?php endif; ?>


                            </td>
                        </tr>
                        @endfor
                    </tbody>
                </table>
                <?php
                    endif;
                ?>

                <input type="hidden" name="id" value="" />


            </form>
        </div>
    </div>
@endsection

@section('footer')
    <script>
        const GET_NUMBERS_URL = "{{ route('admin.presentations.get.numbers', '') }}";
        const GET_PRESENTATION_URL = "{{ route('admin.presentations.get', '') }}";
        const OPEN_FILE_URL = "{{ route('presentation.get.file', ['','']) }}";
        const CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');


        $("#honor").change(function(){
            $(".honor").val($(this).val());
        });
    </script>
    <script src="{{ asset('js/admin/event.js') }}"></script>
@endsection
