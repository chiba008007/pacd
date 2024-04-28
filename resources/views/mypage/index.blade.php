@extends('layouts.app')

@section('title', $title = 'マイページ')

@section('content')
<div class="uk-section-small">
    <div class="uk-container uk-container-large">
        {{ $title }}
    </div>
    <div class="uk-container uk-container-large">
        <h3 class="uk-card-title uk-margin-remove-bottom">
        {{--法人会員(窓口担当者)の場合は法人名を表示--}}
        @if(Auth::user()->type == 2)
        {{Auth::user()->cp_name}}
        @else
        {{Auth::user()->sei}}{{Auth::user()->mei}}
        @endif
        でログイン中
        </h3>

        <div class="uk-card uk-card-default uk-card-body uk-padding-remove">
            <div class="uk-card uk-card-default uk-width-1-1">

                <table class="uk-table ">
                    <thead>
                        <tr>
                            <th class="uk-background-primary uk-text-secondary" style="width:130px;">項目</th>
                            <th class=" uk-background-primary uk-text-secondary">解説</th>
                            <th class="uk-background-primary uk-text-secondary" style="width:300px;">機能</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>登録内容の変更確認</td>
                            <td>
                                登録内容として表示している会員情報は、事務局でお預かりしておりますデータベースを元に作成しています。登録内容を各自でご確認いただき、変更があった場合は正しい情報にご修正くださいますようお願いいたします。
                            </td>
                            <td>
                                <a href="{{ route('mypage.profile.edit') }}" class="uk-button uk-button-default uk-text-small" style="width:100%;">
                                    会員情報変更
                                </a>
                                <br />
                                <br />
                                <a href="{{ route('mypage.profile.update.password') }}" class="uk-button uk-button-default uk-text-small" style="width:100%;">
                                    パスワード変更
                                </a>
                            </td>
                        </tr>
                        {{--法人会員（窓口担当者）と個人会員のみ表示--}}


                        @if(
                            Auth::user()->type == 2 ||
                            Auth::user()->type == 4
                        )
                        <tr>
                            <td>年会費納入</td>
                            <td>
                                年会費の支払い状況の確認が行えます。<br />
                                請求書・領収書の発行が行えます。
                            </td>
                            <td>
                                <a href="{{ route('mypage.profile.payment') }}" class="uk-button uk-button-default uk-text-small" style="width:100%;" >
                                    年会費納入状況
                                </a>

                            </td>
                        </tr>
                        @endif



                        <tr>
                            <td>参加状況確認</td>
                            <td>
                                各会への参加状況の確認が行えます。
                            </td>
                            <td>
                            @if(
                                Auth::user()->type == 1 ||
                                Auth::user()->type == 2 ||
                                Auth::user()->type == 3 ||
                                Auth::user()->type == 4
                            )
                                <div class="box pd10 uk-margin-small-top">
                                    <a href="{{ route('mypage.reikai') }}" class="uk-button uk-button-default uk-text-small" style="width:100%;">
                                        {{ config('pacd.category.reikai.name') }}
                                    </a>
                                </div>
                            @endif
                            @if(
                                Auth::user()->type == 1 ||
                                Auth::user()->type == 2 ||
                                Auth::user()->type == 3 ||
                                Auth::user()->type == 4 ||
                                Auth::user()->type == 5 ||
                                Auth::user()->type == 6
                            )
                                <div class="box pd10 uk-margin-small-top">
                                    <a href="{{ route('mypage.touronkai') }}" class="uk-button uk-button-default uk-text-small" style="width:100%;">
                                        {{ config('pacd.category.touronkai.name') }}
                                    </a>
                                </div>
                            @endif
                            @if(
                                Auth::user()->type == 1 ||
                                Auth::user()->type == 2 ||
                                Auth::user()->type == 3 ||
                                Auth::user()->type == 4
                            )
                                <div class="box pd10 uk-margin-small-top">
                                    <a href="{{ route('mypage.kosyukai') }}" class="uk-button uk-button-default uk-text-small" style="width:100%;">
                                        {{ config('pacd.category.kosyukai.name') }}
                                    </a>
                                </div>
                            @endif
                            @if($kyosanCount > 0 )
                                <div class="box pd10 uk-margin-small-top">
                                    <a href="{{ route('mypage.kyosan') }}" class="uk-button uk-button-default uk-text-small" style="width:100%;">
                                        {{ config('pacd.category.kyosan.name') }}
                                    </a>
                                </div>
                            @endif
                            </td>
                        </tr>

                        <?php /*法人会員(窓口担当者)*/ ?>
                        @if(Auth::user()->type == 2 )
                        <tr>
                            <td>会員一覧</td>
                            <td>登録済みの会員一覧の確認を行えます</td>
                            <td>
                                <a href="{{ route('mypage.memberlist') }}" class="uk-button uk-button-default uk-text-small" style="width:100%;">
                                    会員一覧
                                </a>
                            </td>
                        </tr>
                        @endif
                        <tr>
                            <td>参加申し込み</td>
                            <td>例会・討論会・講習会一覧ページに遷移します。<br />
                            一覧より参加申し込みを行います。
                            </td>
                            <td>
                                <a href="{{ route('eventlist') }}" class="uk-button uk-button-default uk-text-small" style="width:100%; font-size:13px;">
                                    例会・討論会・講習会 参加申し込み
                                </a>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
