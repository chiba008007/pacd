@extends('layouts.app')

@section('title', $title)

@section('content')
    <div id="page">
        <div class="header">
            <h2 class="edit-content" data-column="title">{{ $title }}</h2>
        </div>
        <div class="uk-container uk-section-xsmall">

            @if (session('message'))
                <div class="uk-alert-danger" uk-alert>
                    <a class="uk-alert-close" uk-close></a>
                    <p>{{ session('message') }}</p>
                </div>
            @endif
@include('elements.pages.bannar')

            <h3 class="section__title">参加した{{ config('pacd.category.kyosan.name') }}一覧</h3>
            @foreach ($attendees as $attendee)
                <h4 class="uk-width-1-1 border-bottom">{{ $attendee->event->name }}</h4>
                <div class="uk-margin-left">
                    <div class="uk-grid-small  uk-text-center" uk-grid>
                        @if($attendee->event->attendFlag)
                        <div class="uk-width-1-6@m"><a href="{{ route('kyosan_attendee.edit', $attendee->id) }}" class="uk-button uk-button-default ui uk-background-muted uk-text-nowrap">参加者情報変更</a></div>
                        @endif
                        @if($attendee->event->presenter_flag == 1)
                        <div class="uk-width-1-6@m"><a href="{{ route('kyosan_presenter', $attendee->id) }}" class="uk-button uk-button-default ui uk-background-muted uk-text-nowrap">講演申し込み</a></div>
                        @endif

{{--
                        <div class="uk-width-1-6@m"><a href="{{ route('event.webex', $attendee->event->code) }}" class="uk-button uk-button-default ui uk-background-muted uk-text-nowrap" >プログラム</a></div>
                        @if ($attendee->presenters->count())
                            <div class="uk-width-1-6@m"><a href="" uk-toggle="target: #presenter_menu_{{ $attendee->id }}" class="uk-button uk-button-default ui uk-background-muted uk-text-nowrap">講演者メニュー</a></div>
                        @endif
--}}
                        {{-- 参加料金を　無効にする　を選択した場合ダウンロードアイコンは見えなくする --}}
                        @if($attendee->event->join_enable == 1)
                            @if($attendee->is_enabled_invoice == 1)
                                <div class="uk-width-1-6@m" >
                                    @if($attendee->recipe_date)
                                        <button class="uk-button uk-button-default" disabled>領収書発行済</button>
                                    @else
                                        <a href="{{ route('member.join.pdf', $attendee->id."/".config('pacd.category.kyosan.key')) }}" class="uk-button uk-button-default uk-background-muted" target=_blank>
                                        @if($attendee->is_paid == 1) 領収書
                                        @else 請求書 @endif
                                        </a>
                                    @endif
                                </div>
                            @endif
                        @endif


                        <div class="uk-width-1-6@m">
                            <a href="{{ route('event.paper', $attendee->event->code) }}" class="uk-button uk-button-default ui uk-background-muted uk-text-nowrap" target=_blank>参加証</a>
                        </div>
                    </div>

                    @if(
                        $attendee->tenjiSanka1Status ||
                        $attendee->tenjiSanka2Status ||
                        $attendee->konsinkaiSanka1Status ||
                        $attendee->konsinkaiSanka2Status
                    )
                    <div class="uk-grid-small " uk-grid>
                        <div class="uk-width-1-2@m" >
                            <div>▼ 参加者1</div>
                            @php
                            $disabled1 = false;
                            $disabled2 = false;
                            @endphp
                            @if ($attendee->is_paid == 0 )
                            @php
                            $disabled1 = true;
                            $disabled2 = false;
                            @endphp
                            @else
                            @php
                            $disabled1 = false;
                            $disabled2 = true;
                            @endphp
                            @endif
                            @if($attendee->tenjiSanka1Status)
                                @if($disabled1)
                                    <a href="{{ route('member.kyosan.invoice', ['type' => 'invoice', 'filecode' => $attendee->id, 'no'=>1]) }}" class="uk-button uk-button-default uk-background-muted" target="_blank" >参加費請求書</a>
                                @endif
                                @if($disabled2)
                                    <a href="{{ route('member.kyosan.invoice', ['type' => 'recipe', 'filecode' => $attendee->id, 'no'=>1]) }}" class="uk-button uk-button-default" target="_blank" >参加費領収書</a>
                                @endif
                            @endif
                            @if($attendee->konsinkaiSanka1Status)
                                @if($disabled1)
                                    <a href="{{ route('member.kyosan.invoice', ['type' => 'konshinkaiInvoice', 'filecode' => $attendee->id, 'no'=>1]) }}" class="uk-button uk-button-default uk-background-muted" target="_blank">懇親会請求書</a>
                                @endif
                                @if($disabled2)
                                    <a href="{{ route('member.kyosan.invoice', ['type' => 'konshinkaiRecipe', 'filecode' => $attendee->id, 'no'=>1]) }}" class="uk-button uk-button-default uk-background-muted" target="_blank">懇親会領収書</a>
                                @endif
                            @endif
                            @if($attendee->tenjiSanka1Status)
                                <a href="{{ route('event.paper', ['id' => $attendee->event->code, 'join' => 1]) }}" class="uk-button uk-button-default ui uk-background-muted uk-text-nowrap" target="_blank">
                                参加証</a>
                            @endif
                        </div>
                        <div class="uk-width-1-2@m" >
                            <div>▼ 参加者2</div>
                            @if($attendee->tenjiSanka2Status)
                                @if($disabled1)
                                    <a href="{{ route('member.kyosan.invoice', ['type' => 'invoice', 'filecode' => $attendee->id, 'no'=>2]) }}" class="uk-button uk-button-default uk-background-muted" target="_blank">参加費請求書</a>
                                @endif
                                @if($disabled2)
                                    <a href="{{ route('member.kyosan.invoice', ['type' => 'recipe', 'filecode' => $attendee->id, 'no'=>1]) }}" class="uk-button uk-button-default" target="_blank">参加費領収書</a>
                                @endif
                            @endif
                            @if($attendee->konsinkaiSanka2Status)
                                @if($disabled1)
                                    <a href="{{ route('member.kyosan.invoice', ['type' => 'konshinkaiInvoice', 'filecode' => $attendee->id, 'no'=>2]) }}" class="uk-button uk-button-default uk-background-muted" target="_blank">懇親会請求書</a>
                                @endif
                                @if($disabled2)
                                    <a href="{{ route('member.kyosan.invoice', ['type' => 'konshinkaiRecipe', 'filecode' => $attendee->id, 'no'=>2]) }}" class="uk-button uk-button-default uk-background-muted" target="_blank">懇親会領収書</a>
                                @endif
                            @endif
                            @if($attendee->tenjiSanka2Status)
                                <a href="{{ route('event.paper', ['id' => $attendee->event->code, 'join' => 2]) }}" class="uk-button uk-button-default ui uk-background-muted uk-text-nowrap" target="_blank">
                                参加証</a>
                            @endif
                        </div>
                    </div>
                    @endif

                    @if ($attendee->presenters->count())
                        <table class="uk-table uk-table-responsive uk-table-small uk-text-center" id="presenter_menu_{{ $attendee->id }}" hidden>
                            <thead>
                                <tr>
                                    <th>講演者番号</th>
                                    <th>変更メニュー</th>
                                    <th>講演管理</th>
                                    <th>発表番号</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($attendee->presenters as $presenter)
                                    <tr>
                                        <th>{{ sprintf('%010d', $presenter->id) }}</th>
                                        <td>
                                            <a href="{{ route('kyosan_presenter.edit', $presenter->id) }}">講演者情報変更</a>
                                        </td>
                                        <td>
                                            @if (@$presenter->presentation->number)
                                                <a href="{{ route('kyosan_presenter.edit.presentation', $presenter->presentation->id) }}">講演情報・資料変更</a>
                                            @else
                                                管理者準備中
                                            @endif
                                        </td>
                                        <td>
                                            {{ @$presenter->presentation->number ?? '運営管理者確認中' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
@endsection
