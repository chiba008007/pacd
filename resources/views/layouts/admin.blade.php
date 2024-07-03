<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title')</title>

    <link href="{{ asset('css/admin.css') }}" rel="stylesheet">

    <script src="{{ asset('js/app.js') }}"></script>

    @yield('head')
    <style type="text/css">
        @media screen and (max-width: 460px){
        .sp {display:none}
        }

textarea::placeholder {
  color: red;
}

/* IE */
input:-ms-input-placeholder {
  color: red;
}

/* Edge */
textarea::-ms-input-placeholder {
  color: red;
}
.uk-textarea2{
    height:80px;
    max-width: 97%;
    width: 97%;
    border: 0 none;
    padding: 4px 10px;
    background: #fff;
    color: #666;
    border: 1px solid #e5e5e5;
    transition: 0.2s ease-in-out;
    transition-property: color, background-color, border;
}
    </style>
</head>
<body>

    {{-- top nav --}}
    <header uk-sticky class="uk-navbar-container">
        <div class="uk-background-primary">
            <nav id="topnav" uk-navbar>
                <div class="uk-navbar-left">
                    @if (Auth::guard('admin')->check())
                        <a id="sidenav_toggle" class="uk-navbar-toggle text-light" uk-navbar-toggle-icon ></a>
                    @endif
                    <a href="{{ route('admin.home') }}" class="uk-navbar-item uk-logo text-light">
                        管理画面
                    </a>
                </div>

                <div class="uk-navbar-right uk-light">
                    <ul class="uk-navbar-nav">
                        @if (Auth::guard('admin')->check())
                            <li class="uk-active sp"><a href="{{ route('admin.members.index') }}">会員管理</a></li>
                            <li class="uk-active sp"><a href="{{ route('admin.reikai.event.list') }}">{{config('pacd.category.reikai.name')}}</a></li>
                            <li class="uk-active sp"><a href="{{ route('admin.touronkai.event.list') }}">{{config('pacd.category.touronkai.name')}}</a></li>
                            <li class="uk-active sp"><a href="{{ route('admin.kyosan.event.list') }}">{{config('pacd.category.kyosan.name')}}</a></li>
                            <li class="uk-active sp"><a href="{{ route('admin.kosyukai.event.list') }}">{{config('pacd.category.kosyukai.name')}}</a></li>
                            <li class="uk-active sp"><a href="{{ route('admin.pages.index') }}">公開ページ管理</a></li>

                            <li class="uk-active">
                                <a href="#"><span class="uk-margin-small-right uk-margin-left uk-icon" uk-icon="user"></span>管理者&nbsp; <span class="uk-margin-small-right uk-icon" uk-icon="chevron-down"></span></a>
                                <div uk-dropdown="pos: bottom-right; mode: click; offset: -1;">
                                    <ul class="uk-nav uk-navbar-dropdown-nav">
                                        <li class="uk-text-center uk-text-bold">{{ Auth::guard('admin')->user()->login_id }}</li>
                                        <li class="uk-text-center uk-margin-bottom">{{ Auth::guard('admin')->user()->email }}</li>
                                        <li class="uk-nav-divider"></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.update.email') }}">メールアドレス変更</a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.update.password') }}">パスワード変更</a></li>
                                        @if(Auth::guard('admin')->user()->login_id === 'main' )
                                        <li><a class="dropdown-item" href="{{ route('admin.event.password') }}">各イベントパスワード</a></li>
                                        @endif
                                        <li class="uk-nav-divider"></li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('admin.logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">ログアウト</a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                            <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        @else
                            <li class="{{ (isCurrent('admin.login')) ? 'uk-active' : '' }}"><a class="text-nowrap" href="{{ route('admin.login') }}">ログイン</a></li>
                            <li class="{{ (isCurrent('admin.register')) ? 'uk-active' : '' }}"><a class="text-nowrap" href="{{ route('admin.register') }}">新規登録</a></li>
                        @endif
                    </ul>
                </div>
            </nav>
        </div>
    </header>

    {{-- side nav --}}

    @if (Auth::guard('admin')->check() && !isset($eventtype) )
        <aside id="sidenav" class="uk-background-default">
            <ul class="uk-nav uk-nav-default">
                @if (isCurrent('/' . config('admin.uri') . '/members/*'))
                    {{-- 会員管理メニュー --}}
                    <li class="uk-nav-header">会員管理メニュー</li>
                    <li class="uk-margin-left"><a href="{{ route('admin.members.index') }}"><span class="uk-margin-small-right uk-icon" uk-icon="users"></span>会員一覧</a></li>
                    <li class="uk-margin-left"><a href="{{ route('admin.members.create') }}"><span class="uk-margin-small-right uk-icon" uk-icon="plus"></span>会員登録</a></li>
                    <li class="uk-margin-left"><a href="{{ route('admin.form.index', ['members', 'register']) }}"><span class="uk-margin-small-right uk-icon" uk-icon="pencil"></span>会員登録フォーム編集</a></li>
                    <li class="uk-margin-left"><a href="{{ route('admin.mailform.index', ['members', 'member' ])}}"><span class="uk-margin-small-right uk-icon" uk-icon="mail"></span>会員登録メール</a></li>
                    <li class="uk-margin-left"><a href="{{ route('admin.members.upload') }}"><span class="uk-margin-small-right uk-icon" uk-icon="upload"></span>会員一括アップロード</a></li>
                    <li class="uk-margin-left"><a href="{{ route('admin.members.yearPayment') }}"><span class="uk-margin-small-right uk-icon" uk-icon="file-text"></span>年会費情報</a></li>
                    <li class="uk-margin-left"><a href="{{ route('admin.members.storages') }}"><span class="uk-margin-small-right uk-icon" uk-icon="file-text"></span>請求書・領収書PDF一覧</a></li>
                @elseif (isCurrent('admin.pages.*'))
                    {{-- 公開ページ管理メニュー --}}
                    <li class="uk-nav-header">公開ページ管理メニュー</li>
                    <li class="uk-margin-left"><a href="{{ route('top') }}" target="_blank"><span class="uk-margin-small-right uk-icon" uk-icon="forward"></span>公開サイトへ</a></li>
                    <li class="uk-margin-left"><a href="{{ route('admin.pages.index') }}"><span class="uk-margin-small-right uk-icon" uk-icon="list"></span>公開ページ一覧</a></li>
                    <li class="uk-margin-left"><a href="{{ route('admin.pages.banner') }}"><span class="uk-margin-small-right uk-icon" uk-icon="move"></span>バナー管理</a></li>
                    <li class="uk-margin-left"><a href="{{ route('admin.pages.inquire') }}"><span class="uk-margin-small-right uk-icon" uk-icon="mail"></span>問合せ先アドレス</a></li>
                    <li class="uk-margin-left"><a href="{{ route('admin.pages.url') }}"><span class="uk-margin-small-right uk-icon" uk-icon="rss"></span>URL発行</a></li>
                    <li class="uk-nav-divider"></li>
                    <ul class="uk-margin-left uk-nav-sub">
                        @foreach (App\Models\Page::get()->toArray() as $page)
                            <li class=""><a href="{{ route('admin.pages.edit', [$page['id']]) }}">{{ $page['title'] }}</a></li>
                        @endforeach
                    </ul>
                @elseif (isCurrent('/' . config('admin.uri') . '/reikai/*'))
                    {{-- 例会＆講演会メニュー --}}
                    <li class="uk-nav-header">例会＆講演会管理メニュー</li>
                    <li class="uk-margin-left"><a href="{{ route('admin.reikai.event.list') }}" ><span class="uk-margin-small-right uk-icon" uk-icon="list"></span>イベント一覧</a></li>
                    <li class="uk-margin-left"><a href="{{ route('admin.reikai.event.register') }}" ><span class="uk-margin-small-right uk-icon" uk-icon="plus"></span>イベント登録</a></li>
                    <li class="uk-margin-left"><a href="{{ route('admin.reikai.event.program.register') }}" ><span class="uk-margin-small-right uk-icon" uk-icon="settings"></span>プログラム設定</a></li>
                    <li class="uk-margin-left"><a href="{{ route('admin.attendees.index', ['reikai']) }}"><span class="uk-margin-small-right uk-icon" uk-icon="users"></span>参加者一覧</a></li>
                    <li class="uk-margin-left"><a href="{{ route('admin.attendees.create', ['reikai']) }}"><span class="uk-margin-small-right uk-icon" uk-icon="plus"></span>参加登録</a></li>
                    <li class="uk-margin-left"><a href="{{ route('admin.form.index', ['reikai', 'reikai_attendee']) }}"><span class="uk-margin-small-right uk-icon" uk-icon="pencil"></span>参加申込フォーム編集</a></li>
                    <li class="uk-margin-left"><a href="{{ route('admin.presenters.index', ['reikai']) }}"><span class="uk-margin-small-right uk-icon" uk-icon="users"></span>講演者一覧</a></li>
                    <li class="uk-margin-left"><a href="{{ route('admin.presenters.create', ['reikai']) }}"><span class="uk-margin-small-right uk-icon" uk-icon="plus"></span>講演登録</a></li>
                    <li class="uk-margin-left"><a href="{{ route('admin.form.index', ['reikai', 'reikai_presenter']) }}"><span class="uk-margin-small-right uk-icon" uk-icon="pencil"></span>講演者申込フォーム編集</a></li>
                    <li class="uk-margin-left"><a href="{{ route('admin.mailform.index', ['reikai', 'member' ])}}"><span class="uk-margin-small-right uk-icon" uk-icon="mail"></span>登録メール</a></li>
                    <li class="uk-margin-left"><a href="{{ route('admin.reikai.mail.list', ['reikai' ])}}"><span class="uk-margin-small-right uk-icon" uk-icon="mail"></span>一括メール配信</a></li>

                @elseif (isCurrent('/' . config('admin.uri') . '/touronkai/*'))
                    {{-- 討論会メニュー --}}
                    <li class="uk-nav-header">高分子分析討論会管理メニュー</li>
                    <li class="uk-margin-left"><a href="{{ route('admin.touronkai.event.list') }}" ><span class="uk-margin-small-right uk-icon" uk-icon="list"></span>イベント一覧</a></li>
                    <li class="uk-margin-left"><a href="{{ route('admin.touronkai.event.register') }}" ><span class="uk-margin-small-right uk-icon" uk-icon="plus"></span>イベント登録</a></li>
                    <li class="uk-margin-left"><a href="{{ route('admin.touronkai.event.program.register') }}" ><span class="uk-margin-small-right uk-icon" uk-icon="settings"></span>プログラム設定</a></li>
                    <li class="uk-margin-left"><a href="{{ route('admin.attendees.index', ['touronkai']) }}"><span class="uk-margin-small-right uk-icon" uk-icon="users"></span>参加者一覧</a></li>
                    <li class="uk-margin-left"><a href="{{ route('admin.attendees.create', ['touronkai']) }}"><span class="uk-margin-small-right uk-icon" uk-icon="plus"></span>参加登録</a></li>
                    <li class="uk-margin-left"><a href="{{ route('admin.form.index', ['touronkai', 'touronkai_attendee']) }}"><span class="uk-margin-small-right uk-icon" uk-icon="pencil"></span>参加申込フォーム編集</a></li>
                    <li class="uk-margin-left"><a href="{{ route('admin.presenters.index', ['touronkai']) }}"><span class="uk-margin-small-right uk-icon" uk-icon="users"></span>講演者一覧</a></li>
                    <li class="uk-margin-left"><a href="{{ route('admin.presenters.create', ['touronkai']) }}"><span class="uk-margin-small-right uk-icon" uk-icon="plus"></span>講演登録</a></li>
                    <li class="uk-margin-left"><a href="{{ route('admin.form.index', ['touronkai', 'touronkai_presenter']) }}"><span class="uk-margin-small-right uk-icon" uk-icon="pencil"></span>講演者申込フォーム編集</a></li>
                    <li class="uk-margin-left"><a href="{{ route('admin.mailform.index', ['touronkai', 'member' ])}}"><span class="uk-margin-small-right uk-icon" uk-icon="mail"></span>登録メール</a></li>
                    <li class="uk-margin-left"><a href="{{ route('admin.touronkai.mail.list', ['touronkai' ])}}"><span class="uk-margin-small-right uk-icon" uk-icon="mail"></span>一括メール配信</a></li>
                @elseif (isCurrent('/' . config('admin.uri') . '/kyosan/*'))
                    {{-- 企業協賛メニュー --}}
                    <li class="uk-nav-header">企業協賛管理メニュー</li>
                    <li class="uk-margin-left"><a href="{{ route('admin.kyosan.event.list') }}" ><span class="uk-margin-small-right uk-icon" uk-icon="list"></span>イベント一覧</a></li>
                    <li class="uk-margin-left"><a href="{{ route('admin.kyosan.event.register') }}" ><span class="uk-margin-small-right uk-icon" uk-icon="plus"></span>イベント登録</a></li>
                    <li class="uk-margin-left"><a href="{{ route('admin.attendees.index', ['kyosan']) }}"><span class="uk-margin-small-right uk-icon" uk-icon="users"></span>協賛企業一覧</a></li>
                    <li class="uk-margin-left"><a href="{{ route('admin.attendees.create', ['kyosan']) }}"><span class="uk-margin-small-right uk-icon" uk-icon="plus"></span>協賛企業申込</a></li>
                    <li class="uk-margin-left"><a href="{{ route('admin.form.index', ['kyosan', 'kyosan_attendee']) }}"><span class="uk-margin-small-right uk-icon" uk-icon="pencil"></span>協賛申込フォーム編集</a></li>


                    <li class="uk-margin-left"><a href="{{ route('admin.mailform.index', ['kyosan', 'member' ])}}"><span class="uk-margin-small-right uk-icon" uk-icon="mail"></span>登録メール</a></li>
                    <li class="uk-margin-left"><a href="{{ route('admin.kyosan.mail.list', ['kyosan' ])}}"><span class="uk-margin-small-right uk-icon" uk-icon="mail"></span>一括メール配信</a></li>


                @elseif (isCurrent('/' . config('admin.uri') . '/kosyukai/*'))
                    {{-- 技術講習会 --}}
                    <li class="uk-nav-header">高分子分析技術講習会管理メニュー</li>
                    <li class="uk-margin-left"><a href="{{ route('admin.kosyukai.event.list') }}" ><span class="uk-margin-small-right uk-icon" uk-icon="list"></span>イベント一覧</a></li>
                    <li class="uk-margin-left"><a href="{{ route('admin.kosyukai.event.register') }}" ><span class="uk-margin-small-right uk-icon" uk-icon="plus"></span>イベント登録</a></li>
                    <li class="uk-margin-left"><a href="{{ route('admin.kosyukai.event.program.register') }}" ><span class="uk-margin-small-right uk-icon" uk-icon="settings"></span>プログラム設定</a></li>
                    <li class="uk-margin-left"><a href="{{ route('admin.attendees.index', ['kosyukai']) }}"><span class="uk-margin-small-right uk-icon" uk-icon="users"></span>参加者一覧</a></li>
                    <li class="uk-margin-left"><a href="{{ route('admin.attendees.create', ['kosyukai']) }}"><span class="uk-margin-small-right uk-icon" uk-icon="plus"></span>参加登録</a></li>
                    <li class="uk-margin-left"><a href="{{ route('admin.form.index', ['kosyukai', 'kosyukai_attendee']) }}"><span class="uk-margin-small-right uk-icon" uk-icon="pencil"></span>参加申込フォーム編集</a></li>
                    <li class="uk-margin-left"><a href="{{ route('admin.presenters.index', ['kosyukai']) }}"><span class="uk-margin-small-right uk-icon" uk-icon="users"></span>講演者一覧</a></li>
                    <li class="uk-margin-left"><a href="{{ route('admin.presenters.create', ['kosyukai']) }}"><span class="uk-margin-small-right uk-icon" uk-icon="plus"></span>講演登録</a></li>
                    <li class="uk-margin-left"><a href="{{ route('admin.form.index', ['kosyukai', 'kosyukai_presenter']) }}"><span class="uk-margin-small-right uk-icon" uk-icon="pencil"></span>講演者申込フォーム編集</a></li>
                    <li class="uk-margin-left"><a href="{{ route('admin.mailform.index', ['kosyukai', 'member' ])}}"><span class="uk-margin-small-right uk-icon" uk-icon="mail"></span>登録メール</a></li>
                    <li class="uk-margin-left"><a href="{{ route('admin.kosyukai.mail.list', ['kosyukai' ])}}"><span class="uk-margin-small-right uk-icon" uk-icon="mail"></span>一括メール配信</a></li>

                @else
                    {{-- デフォルトメニュー --}}
                    <li class="uk-nav-header ">メニュー</li>
                    <li class="uk-margin-left sp"><a href="{{ route('admin.members.index') }}"><span class="uk-margin-small-right uk-icon " uk-icon="users"></span>会員管理</a></li>
                    <li class="uk-margin-left sp"><a href="{{ route('admin.reikai.event.list') }}"><span class="uk-margin-small-right uk-icon" uk-icon="microphone"></span>{{config('pacd.category.reikai.name')}}</a></li>
                    <li class="uk-margin-left sp"><a href="{{ route('admin.touronkai.event.list') }}"><span class="uk-margin-small-right uk-icon" uk-icon="comments"></span>{{config('pacd.category.touronkai.name')}}</a></li>
                    <li class="uk-margin-left sp"><a href="{{ route('admin.kyosan.event.list') }}"><span class="uk-margin-small-right uk-icon" uk-icon="location"></span>{{config('pacd.category.kyosan.name')}}</a></li>
                    <li class="uk-margin-left sp"><a href="{{ route('admin.kosyukai.event.list') }}"><span class="uk-margin-small-right uk-icon" uk-icon="file-edit"></span>{{config('pacd.category.kosyukai.name')}}</a></li>
                    <li class="uk-margin-left sp"><a href="{{ route('admin.pages.index') }}"><span class="uk-margin-small-right uk-icon" uk-icon="world"></span>公開ページ管理</a></li>
                    <li class="uk-margin-left"><a href="{{ route('admin.qrhome') }}" target=_blank><span class="uk-margin-small-right uk-icon" uk-icon="camera"></span>QRコードリーダ</a></li>
                @endif
            </ul>
        </aside>
    @endif

    {{-- contents --}}
    <main class="content @if (Auth::guard('admin')->check()) content-padder @endif uk-background-muted">
        @if (Auth::guard('admin')->check())
            <div class="header uk-background-default">
                <div class="uk-grid-small uk-padding-small" uk-grid>
                    {{-- ダッシュボードタイトル --}}
                    <h2>{{ $title }}</h2>
                    {{-- パンくず --}}
                    @section('breadcrumb')
                    <ul class="uk-breadcrumb uk-navbar-right">
                        <li><a href="{{ route('admin.home') }}"><span class="ion-speedometer uk-margin-small-right"></span>Home</a></li>
                        @show
                    </ul>
                </div>
            </div>
        @endif
        @yield('content')
    </main>

    <script src="{{ asset('js/admin/admin.js') }}"></script>
    @include('elements.flash')

    @yield('footer')
</body>
</html>
