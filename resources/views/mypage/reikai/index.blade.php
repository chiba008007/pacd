@extends('layouts.app')

@section('title', $title)

@section('content')
    <div id="page">
        <div class="header">
            <h2 class="edit-content" data-column="title">{{ $title }}</h2>
        </div>
        <div class="uk-container uk-section-xsmall">
@include('elements.pages.bannar')

            @if (session('message'))
                <div class="uk-alert-danger" uk-alert>
                    <a class="uk-alert-close" uk-close></a>
                    <p>{{ session('message') }}</p>
                </div>
            @endif

            <h3 class="section__title">参加した{{ config('pacd.category.reikai.name') }}一覧</h3>
            @foreach ($attendees as $attendee)
                <h4 class="uk-width-1-1 border-bottom">{{ $attendee->event->name }}（{{ $attendee->event->date_start }}～{{ $attendee->event->date_end }}）</h4>
                <div class="uk-margin-left">
                    <div class="uk-grid-small  uk-text-center" uk-grid>
                        @if($attendee->event->attendFlag) 
                        <div class="uk-width-1-6@m"><a href="{{ route('reikai_attendee.edit', $attendee->id) }}" class="uk-button uk-button-default ui uk-background-muted uk-text-nowrap">参加者情報確認</a></div>
                        @endif
                        @if($attendee->event->presenter_flag == 1 && $attendee->event->speakerFlag == 1)
                        {{--個人会員・会員外以外--}}
                        {{--例会はグレーアウトにして、申し込みできないようにする--}}
                        {{--0329対応--}}
                        {{-- @if(!($user->type == 4 || $user->type == 1)) --}}
                        @if(!($user->type == 4 ))
                        <div class="uk-width-1-6@m"><a href="{{ route('reikai_presenter', $attendee->id) }}" class="uk-button uk-button-default ui uk-background-muted uk-text-nowrap "  >講演申し込み</a>
                        </div>
                        @endif
                        @endif

                        <div class="uk-width-1-6@m">
                        <a href="{{ route('event.webex', $attendee->event->code) }}" class="uk-button uk-button-default ui uk-background-muted uk-text-nowrap" >プログラム</a>
                        </div>

                        @if ($attendee->presenters->count() && $attendee->event->speakerMenuFlag == 1)
                            <div class="uk-width-1-6@m"><a href="" uk-toggle="target: #presenter_menu_{{ $attendee->id }}" class="uk-button uk-button-default ui uk-background-muted uk-text-nowrap" >講演者メニュー</a></div>
                        @endif
                        {{-- 参加料金を　無効にする　を選択した場合ダウンロードアイコンは見えなくする --}}
                        @if($attendee->event->join_enable == 1)
                            @if($attendee->is_enabled_invoice == 1)
                                <div class="uk-width-1-6@m" >
                                    @if($attendee->recipe_date)
                                        <button class="uk-button uk-button-default" disabled>領収書発行済</button>
                                    @else
                                        <a href="{{ route('member.join.pdf', $attendee->id."/".config('pacd.category.reikai.key')) }}" class="uk-button uk-button-default uk-background-muted" target=_blank>
                                        @if($attendee->is_paid == 1 ) 領収書
                                        @else 請求書 @endif
                                        </a>
                                    @endif
                                </div>
                            @endif
                        @endif
                        <div class="uk-width-1-6@m">
                        @if($attendee->event->date_start <= now()->format('Y-m-d') && now()->format('Y-m-d') <= $attendee->event->date_end )
                            <a href="{{ route('event.paper', $attendee->event->code) }}" class="uk-button uk-button-default ui uk-background-muted uk-text-nowrap" target=_blank>参加証</a>
                        @else
                            <button class="uk-button uk-button-default" disabled>参加証</button>
                        @endif
                        </div>

                    </div>
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
                                            <a href="{{ route('reikai_presenter.edit', $presenter->id) }}">講演者情報変更</a>
                                        </td>
                                        <td>
                                            @if (@$presenter->presentation->number)
                                                <a href="{{ route('reikai_presenter.edit.presentation', $presenter->presentation->id) }}">講演情報・資料変更</a>
                                            @else
                                                管理者準備中
                                            @endif
                                        </td>
                                        <td>
                                            {{ @$presenter->presentation->number ?? '管理者準備中' }}
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
