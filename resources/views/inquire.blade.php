@extends('layouts.app')

@section('title', $title)

@section('content')
<script src="https://www.google.com/recaptcha/api.js?render=6LcMjrUdAAAAAAyNr15HDGz84sxpjIqoLQEzkK31"></script>
  <script>
      grecaptcha.ready(function () {
        grecaptcha.execute("6LcMjrUdAAAAAAyNr15HDGz84sxpjIqoLQEzkK31", {action: "sent"}).then(function(token) {
          var recaptchaResponse = document.getElementById("recaptchaResponse");
          recaptchaResponse.value = token;
        });
      });
  </script>

    <div id="page" class="uk-padding" >
        <p>
        お問い合わせについては、該当する問い合わせ項目を選択し、下記フォームに内容を記載して送信をクリックしてください
        </p>
        @if (session('flash_message'))
            <div class="uk-alert-primary" uk-alert>
                {{ session('flash_message') }}
            </div>
        @endif
        <p class="validation">
        @if ($errors->first('name')) <div class="uk-text-danger">※{{$errors->first('name')}}</div> @endif
        @if ($errors->first('kana')) <div class="uk-text-danger">※{{$errors->first('kana')}}</div> @endif
        @if ($errors->first('mail')) <div class="uk-text-danger">※{{$errors->first('mail')}}</div> @endif
        @if ($errors->first('mailconf')) <div class="uk-text-danger">※{{$errors->first('mailconf')}}</div> @endif
        @if ($errors->first('note')) <div class="uk-text-danger">※{{$errors->first('note')}}</div> @endif
        </p>
        <form method="POST" action="{{route('inquire')}}" >
            @csrf
            <table class="uk-table">
                <tr>
                    <td class="uk-width-1-5">問合せ項目</td>
                    <td>
                        <select class="uk-select" name="from">
                        @foreach($inquireSetting as $key=>$value)
                        @php $sel = '' @endphp
                        @if(old('from') == $value->email)
                        @php $sel = 'selected' @endphp
                        @endif
                        <option value="{{$value->email}}" {{$sel}} >{{$value->name}}({{$value->email}})</option>
                        @endforeach
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="uk-width-1-5">お名前<span class="uk-text-danger">[必須]</span></td>
                    <td>
                        <input class="uk-input" type="text" name="name" placeholder="問合せ名を入力してください" value="{{ old('name') }}" />
                    </td>
                </tr>
                <tr>
                    <td class="uk-width-1-5">ふりがな<span class="uk-text-danger">[必須]</span></td>
                    <td>
                        <input class="uk-input" type="text" name="kana" placeholder="問合せ名ふりがなを入力してください" value="{{ old('kana') }}" />
                    </td>
                </tr>
                <tr>
                    <td class="uk-width-1-5">所属・勤務先</td>
                    <td>
                        <input class="uk-input" type="text" name="company" placeholder="所属・勤務先を入力してください" value="{{ old('company') }}" />
                    </td>
                </tr>
                <tr>
                    <td class="uk-width-1-5">連絡先電話番号</td>
                    <td>
                        <input class="uk-input uk-width-3-5" type="text" name="tel" placeholder="連絡先電話番号を入力してください" value="{{ old('tel') }}" />
                    </td>
                </tr>
                <tr>
                    <td class="uk-width-1-5">メールアドレス<span class="uk-text-danger">[必須]</span></td>
                    <td>
                        <input class="uk-input" type="text" name="mail" placeholder="メールアドレスを入力してください" value="{{ old('mail') }}"  />
                    </td>
                </tr>
                <tr>
                    <td class="uk-width-1-5">確認用メールアドレス<span class="uk-text-danger">[必須]</span></td>
                    <td>
                        <input class="uk-input" type="text" name="mailconf" placeholder="メールアドレスを入力してください" value="{{ old('mailconf') }}"  />
                    </td>
                </tr>
                <tr>
                    <td class="uk-width-1-5">会員番号(個人会員・法人会員の方)</td>
                    <td>
                        <input class="uk-input uk-width-3-5" type="text" name="number" placeholder="会員番号を入力してください" value="{{ old('number') }}"  />
                    </td>
                </tr>
                <tr>
                    <td class="uk-width-1-5">お問い合わせ内容<span class="uk-text-danger">[必須]</span></td>
                    <td>
                        <textarea name="note" class="uk-textarea" rows=4>{{ old('note') }}</textarea>
                    </td>
                </tr>
            </table>

            <input type="hidden" name="recaptchaResponse" id="recaptchaResponse">
            <a href="{{route('inquire')}}" class="uk-button uk-button-danger uk-button-large" >リセット</a>
            <input type="submit" value="送信"  class="uk-button uk-button-default uk-button-large" >
        </form>
    </div>
@endsection
