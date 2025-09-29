@extends('layouts.app')

@section('title', 'お支払い完了')

@section('content')
<div class="uk-section-small">
    <div class="uk-container uk-container-small">
        <div class="uk-card uk-card-default uk-card-body uk-width-1-1">
            <h2 class="uk-card-title uk-text-center">お支払いが完了しました</h2>
            <p class="uk-text-center">
                この度は、お申し込みいただき誠にありがとうございました。<br>
                ご登録のメールアドレス宛に、お申し込み内容の確認メールを送信いたしましたので、ご確認ください。
            </p>
            <p class="uk-text-center">
                銀行振込振り込みを選択された方は、マイページにある、銀行振込情報をご確認ください。
            </p>
            <p class="uk-text-center">
                万が一、メールが届かない場合は、お手数ですが事務局までお問い合わせください。
            </p>
            <div class="uk-text-center uk-margin-top">
                <a href="{{ route('mypage') }}" class="uk-button uk-button-primary">マイページへ戻る</a>
            </div>
        </div>
    </div>
</div>
@endsection
