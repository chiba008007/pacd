@extends('layouts.app')

@section('title', $title = '支払い状況')

@section('breadcrumb')
    @parent
    <li><span href="">{{ $title }}</span></li>
@endsection

@section('content')
    <div id="page">
        <div class="header">
            <h2 class="edit-content" data-column="title">{{ $title }}</h2>
        </div>
        <div class="uk-container uk-section-xsmall">
            <div uk-grid>
                <div class="uk-width-3-4@m">
                    <table class="uk-table">
                        <tr>
                            <th>年度</th>
                            <th>費目</th>
                            <th>支払い状況</th>
                            @if($payment[0]->years > 2021)
                            <th>ダウンロード</th>
                            @endif
                        </tr>
                        @foreach($payment as $key=>$value)
                            <?php $pay = config('pacd.payment')[$value->status];
                            ?>
                            <tr >
                                <td class="uk-text-center">{{$value->years}}</td>
                                <td class="uk-text-center">{{config('pacd.categorykey')[$value->type]['payments']}}</td>
                                <td class="uk-text-center">
                                @if($value->type == 1 && ( $user->type == 2 || $user->type == 4 ))
                                    {{$pay}}
                                @else - @endif
                                </td>

                               @if($value->years > 2021)
                                <td class="uk-text-center">
                                {{--年会費のときは法人会員と個人会員のみ--}}
                                @if($value->recipe_status)
                                    領収書発行済み
                                @elseif(
                                    $user->is_enabled_invoice == 1 &&
                                    $value->type == 1 && ( $user->type == 2 || $user->type == 4 ))
                                    <a href="{{ route('member.pdf', $value->id) }}" target=_blank class="uk-button uk-button-primary">
                                    @if($value->status == 0) 請求書 @else 領収書@endif
                                    </a>
                                @else
                                    -
                                @endif

                                </td>
                                @endif
                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
