{{-- sidenav --}}
<aside id="sidenav" class="uk-background-default">
    <ul class="uk-nav" uk-nav="multiple: true; collapsible:true">

        @if (Route::has('top'))
            <li class="@if(isCurrent('top')) active @endif"><a href="{{ route('top') }}">トップページ</a></li>
        @endif


        @if (Route::has('concept'))
            <li>
                <a class="@if(isCurrent('concept')) active @endif" href="{{ route('concept') }}">研究懇談会について</a>
                @if (isCurrent('concept') || isCurrent('reikai.history') || isCurrent('touronkai.history') || isCurrent('kosyukai.history'))
                    <ul class="uk-nav-sub indents">
                        @if (Route::has('reikai.history'))
                            <li class="@if(isCurrent('reikai.history')) active @endif"><a href="{{ route('reikai.history') }}"><span uk-icon="triangle-right"></span> 過去の例会一覧</a></li>
                        @endif
                        @if (Route::has('touronkai.history'))
                            <li class="@if(isCurrent('touronkai.history')) active @endif"><a href="{{ route('touronkai.history') }}"><span uk-icon="triangle-right"></span> 過去の討論会一覧</a></li>
                        @endif
                        @if (Route::has('kosyukai.history'))
                            <li class="@if(isCurrent('kosyukai.history')) active @endif"><a href="{{ route('kosyukai.history') }}"><span uk-icon="triangle-right"></span> 過去の講習会一覧</a></li>
                        @endif
                    </ul>
                @endif
            </li>
        @else
            <li>
                <a class="disabled uk-text-muted">研究懇談会について</a>
                <ul class="uk-nav-sub">
                    @if (Route::has('reikai.history'))
                        <li class="@if(isCurrent('reikai.history')) active @endif"><a href="{{ route('reikai.history') }}">過去の例会一覧</a></li>
                    @endif
                    @if (Route::has('touronkai.history'))
                        <li class="@if(isCurrent('touronkai.history')) active @endif"><a href="{{ route('touronkai.history') }}">過去の討論会一覧</a></li>
                    @endif
                    @if (Route::has('kosyukai.history'))
                        <li class="@if(isCurrent('kosyukai.history')) active @endif"><a href="{{ route('kosyukai.history') }}">過去の講習会一覧</a></li>
                    @endif
                </ul>
            </li>
        @endif

        @if (Route::has('schedule'))
            <li class="@if(isCurrent('schedule')) active @endif"><a href="{{ route('schedule') }}">開催行事一覧</a></li>
        @endif

        {{--例会・講習会・討論会はトップページのみ表示--}}
        <li>
            <a class="@if(isCurrent('reikai.page')) active @endif" href="{{ route('reikai.page') }}">例会＆講演会</a>
        </li>
        <li >
            <a class="@if(isCurrent('touronkai')) active @endif" href="{{ route('touronkai') }}"
            style="border-bottom:0px"
            >高分子分析討論会</a>

            <a href="{{ route('kyosan') }}" style="height:20px;line-height:0px;background-color:#fff !important;color:#000;">┗協賛企業</a>
        </li>
        <li>
            <a class="@if(isCurrent('kosyukai.page')) active @endif" href="{{ route('kosyukai.page') }}">高分子分析技術講習会</a>
        </li>

        @if (Route::has('handbook'))
            <li class="@if(isCurrent('handbook')) active @endif"><a href="{{ route('handbook') }}">高分子分析ハンドブック</a></li>
        @endif

        @if (Route::has('nyukai'))
            <li class="@if(isCurrent('nyukai')) active @endif"><a href="{{ route('nyukai') }}">入会案内</a></li>
        @endif
        @if (Route::has('mypageabout'))
            <li class="@if(isCurrent('mypageabout')) active @endif"><a href="{{ route('mypageabout') }}">MY PAGEについて</a></li>
        @endif

        @if (Route::has('iinkai'))
            <li class="@if(isCurrent('iinkai')) active @endif"><a href="{{ route('iinkai') }}">運営委員会＆企画委員会</a></li>
        @endif

        @if (Route::has('contact'))
            <li class="@if(isCurrent('contact')) active @endif"><a href="{{ route('contact') }}">本学会に関するお問い合わせ</a></li>
        @endif

        @if (Route::has('link'))
            <li class="@if(isCurrent('link')) active @endif"><a href="{{ route('link') }}">リンク</a></li>
        @endif

        @if (Route::has('kyujin'))
            <li class="@if(isCurrent('kyujin')) active @endif"><a href="{{ route('kyujin') }}">求人情報</a></li>
        @endif

    </ul>

    {{-- @if (app()->isLocal())
        <div class="uk-position-bottom-center">
            <a href="{{ route('admin.home') }}" class="uk-text-primary uk-text-border uk-padding-remove">管理画面</a>
        </div>
    @endif --}}
</aside>
<style type="text/css">

</style>
