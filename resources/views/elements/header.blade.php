<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">


    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css">

    @if(isset($description) && $description)
    <meta name="Description" content="{{ $description }}">
    @endif
    <title>@yield('title')</title>

    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <script src="{{ asset('js/app.js') }}"></script>
    @yield('head')
    <script type="text/javascript">
        $(function() {
            $("#mypageboxbutton").click(function() {
                $("#mypagebox").show();
            });
            $("#mypageclose").click(function() {
                $("#mypagebox").hide();
            });
        });
    </script>
</head>

<body>

    {{-- top nav --}}
    <header uk-sticky class="uk-navbar-container border-bottom border-light-gray">
        <div class="uk-background-default">
            <nav uk-navbar id="topnav">
                <div class="uk-navbar-left uk-flex-nowrap">
                    <a id="sidenav_toggle" class="uk-navbar-toggle" uk-navbar-toggle-icon></a>
                    <a href="{{ route('top') }}" class="uk-navbar-item uk-logo uk-text-primary uk-text-bold " style="width: 100%">
                        <img src="{{ asset("img/logo/logo.png") }}" alt="高分子分析研究懇談会" class="logo">
                    </a>
                </div>
                <div class="uk-navbar-right">
                    @if (Auth::guard()->check())
                    <a href="{{ route('logout') }}" class="uk-button uk-button-default" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">ログアウト</a>
                    @endif
                    <button class="uk-button uk-button-danger" id="mypageboxbutton">My Page</button>
                    <ul class="uk-navbar-nav ">
                        <li>
                            {{--
                            <a href="#" class="bg-white" >
                                <div class="uk-visible@m"></div>
                                <div class="uk-hidden@m"><span class="uk-icon" uk-icon="more-vertical"></div>
                            </a>
                            --}}
                            <div class="uk-navbar-dropdown w90p pd10" id="mypagebox" style="width:100%;left:0;top:0;">
                                @if (Auth::guard()->check())

                                <div class="uk-card uk-card-default uk-card-body uk-padding-remove">
                                    <div class="uk-card uk-card-default uk-width-1-1">
                                        <div class="uk-card-header">
                                            <div class="uk-grid-small uk-flex-middle" uk-grid>


                                                <div>
                                                    <h3 class="uk-card-title uk-margin-remove-bottom">
                                                        {{--法人会員(窓口担当者)の場合は法人名を表示--}}
                                                        @if(Auth::user()->type == 2)
                                                        {{Auth::user()->cp_name}}
                                                        @else
                                                        {{Auth::user()->sei}}{{Auth::user()->mei}}
                                                        @endif
                                                        でログイン中
                                                    </h3>
                                                </div>
                                                <div class="uk-position-right uk-margin-small-top uk-margin-small-right" style="height:100px;">


                                                    <a href="{{ route('logout') }}" class="uk-button uk-button-default" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">ログアウト</a>

                                                    <a href="#" id="mypageclose" class=" uk-button uk-button-danger">閉じる</a>

                                                </div>

                                            </div>
                                        </div>
                                        <div class="uk-card-body" style="overflow: visible scroll;max-height:500px;">

                                            <table class="uk-table uk-table-justify uk-table-divider " id="memberTable">
                                                <thead>
                                                    <tr>
                                                        <th class="uk-width-small">項目</th>
                                                        <th>解説</th>
                                                        <th>機能</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>登録内容の変更確認</td>
                                                        <td>
                                                            登録内容として表示している会員情報は、事務局でお預かりしておりますデータベースを元に作成しています。登録内容を各自でご確認いただき、変更があった場合は正しい情報にご修正くださいますようお願いいたします。
                                                        </td>
                                                        <td>
                                                            <a href="{{ route('mypage.profile.edit') }}" class="uk-button uk-button-default">
                                                                会員情報変更
                                                            </a>
                                                            <br />
                                                            <br />
                                                            <a href="{{ route('mypage.profile.update.password') }}" class="uk-button uk-button-default">
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
                                                            <a href="{{ route('mypage.profile.payment') }}" class="uk-button uk-button-default">
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
                                                                <a href="{{ route('mypage.reikai') }}" class="uk-button uk-button-default">
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
                                                                <a href="{{ route('mypage.touronkai') }}" class="uk-button uk-button-default">
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
                                                                <a href="{{ route('mypage.kosyukai') }}" class="uk-button uk-button-default">
                                                                    {{ config('pacd.category.kosyukai.name') }}
                                                                </a>
                                                            </div>
                                                            @endif

                                                            <div class="box pd10 uk-margin-small-top">
                                                                <a href="{{ route('mypage.kyosan') }}" class="uk-button uk-button-default uk-text-small" style="width:100%;">
                                                                    {{ config('pacd.category.kyosan.name') }}
                                                                </a>
                                                            </div>
                                                        </td>
                                                    </tr>

                                                    <?php /*法人会員(窓口担当者)*/ ?>
                                                    @if(Auth::user()->type == 2 )
                                                    <tr>
                                                        <td>会員一覧</td>
                                                        <td>登録済みの会員一覧の確認を行えます</td>
                                                        <td>
                                                            <a href="{{ route('mypage.memberlist') }}" class="uk-button uk-button-default">
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
                                                            <a href="{{ route('eventlist') }}" class="uk-button uk-button-default">
                                                                例会・討論会・講習会 参加申し込み
                                                            </a>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>

                                        </div>

                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                            @csrf
                                        </form>
                                    </div>
                                </div>
                                @else

                                <div class="uk-card uk-card-default uk-card-body uk-padding-remove">
                                    <div class="uk-card-body pd10">

                                        <div class="box pd10 uk-text-center">
                                            <a href="#" id="mypageclose" class=" uk-button uk-button-danger">閉じる</a>
                                            <a href="{{ route('login') }}" class="uk-button uk-button-default">
                                                ログイン
                                            </a>
                                            <a href="/register" class=" uk-button uk-button-primary">新規登録</a>

                                        </div>
                                        {{--
                                            <div class="box pd10 uk-margin-small-top">
                                                <a href="{{ route('register') }}" class="uk-button uk-button-text uk-width-1-1 uk-text-left">
                                        マイページ作成
                                        </a>
                                    </div>
                                    --}}
                                </div>
                            </div>


                            @endif
                </div>
                </li>
                </ul>
        </div>
        </nav>
        </div>
    </header>
