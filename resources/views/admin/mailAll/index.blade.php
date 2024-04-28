@extends('layouts.admin')

@section('title', $title)

@section('breadcrumb')
    @parent
    <li><span>{{ $title }}</span></li>
@endsection

@section('content')
    <div class="uk-section-small">
        <form action="{{ route("admin.$category_prefix.mail.send",[$category_prefix]) }}" method="POST" enctype='multipart/form-data' >
        @csrf
            <div class="uk-container uk-container-large">
                @if (session('status'))
                    <div class="uk-alert-success" uk-alert>
                    <a class="uk-alert-close" uk-close></a>
                        {{ session('status') }}
                    </div>
                @endif

                <div class="uk-grid-column-small uk-grid-row-large uk-child-width-1-1@s " uk-grid>
                    <div class="uk-margin">
                        配信対象イベント
                        <select class="uk-select" name="event_id">
                        <option value="">配信対象イベントを選択してください。</option>
                        @foreach($event as $key=>$value)
                            <?php
                                $sel = "";
                                if( old('event_id')){
                                    if(old('event_id') == $value->id) $sel = "SELECTED";
                                }else{
                                    if(isset($data->event_id) && $data->event_id == $value->id) $sel = "SELECTED";
                                }
                            ?>
                            <option value="{{$value->id}}" {{$sel}} >
                                @if($category_prefix != "kyosan")
                                {{date("Y年m月d日",strtotime($value->date_start))}}～
                                {{date("Y年m月d日",strtotime($value->date_end))}}
                                @endif
                                {{$value->name}}
                            </option>
                        @endforeach
                        </select>
                        <span class="uk-text-danger">{{$errors->first('event_id')}}</span>
                    </div>

                    <div class="uk-margin">
                        配信対象者<br />
                        @if($category_prefix == "kyosan")
                            <label><input type="radio" name="sender_type" value="3" checked >{{config('pacd.mail_sender')['3']}}</label>
                        @else
                            @foreach(config('pacd.mail_sender') as $key=>$value)
                                <?php if($key <= 2): ?>
                                <?php
                                    $sel = "";
                                    if(old('sender_type')){
                                        if(old('sender_type') == $key) $sel = "checked";
                                    }else{
                                        if(isset($data->sender_type) && $data->sender_type == $key) $sel = "checked";
                                    }
                                ?>
                                <label><input type="radio" name="sender_type" value="{{$key}}" {{$sel}} >{{$value}}</label>
                                <?php endif; ?>
                            @endforeach<br />
                            <span class="uk-text-danger">{{$errors->first('sender_type')}}</span>
                        @endif

                    </div>
                    <div class="uk-margin-small-top">
                        タイトル
                        <?php
                            $subject = "";
                            if(old('subject')){$subject = old('subject');
                            }else{
                                if(isset($data->subject))$subject=$data->subject;
                            }
                        ?>
                        <input type="text" name="subject" value="{{$subject}}"  class="uk-input" placeholder="メールのタイトルを入力してください。" />
                        <span class="uk-text-danger">{{$errors->first('subject')}}</span>

                    </div>
                    <div class="uk-margin-small-top">
                        本文
                        <?php
                            $body = "";
                            if(old('body')){$body = old('body');
                            }else{
                                if(isset($data->body))$body=$data->body;
                            }
                        ?>
                        <textarea name="body" class="uk-textarea" rows=5 >
@if($body)
{{$body}}
@else
ログインID ##ID##
##NAME##様@endif</textarea>
<span class="uk-text-danger">{{$errors->first('body')}}</span>
                    </div>


                    <div class="uk-margin-small-top">
                        <p>##ID##はログインID / ##NAME##は会員名に置き換えて送信されます。</p>
                    </div>
                </div>
                <div class="js-upload uk-margin-top">
                    添付ファイルアップロード
                    <br />
                    <input type="file" name="upload" >

                </div>

                <div class="uk-margin-top">
                    <button type="submit" class="uk-button uk-button-primary" id="send"> 登録 </button>
                </div>
                <p>こちらからは登録のみを行います。一覧画面より送信処理を行ってください。</p>
            </div>
            <input type="hidden" name="id" value="{{$id}}" />
        </form>
    </div>
@endsection

@section('footer')
    <script src="{{ asset('js/admin/event.js') }}"></script>
@endsection
