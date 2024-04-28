@extends('layouts.admin')

@section('title', $title = '年会費納入状況')

@section('breadcrumb')
    @parent
    <li><a href="{{ route('admin.members.index') }}">会員管理</a></li>
    <li><span>{{ $title }}</span></li>
@endsection

@section('content')
    <div class="uk-section-small">
        <div class="uk-container uk-container-large">
            <?php if ($message): ?>
                <div class="uk-alert-primary" uk-alert>
                    <a class="uk-alert-close" uk-close></a>
                    <p>{{ $message }}</p>
                </div>
            <?php endif; ?>
            <div class="uk-flex">
                <div><h4>【<?=$user->sei?><?=$user->mei?>】年度支払い状況</h4></div>
                <div class="uk-margin-left"><a href="#modal-example" uk-toggle>年度追加</a></div>
            </div>
            <div id="modal-example" uk-modal>
                <div class="uk-modal-dialog uk-modal-body">
                    <form action="{{ route('admin.members.update.payment.addyear',$user->id) }}" method="POST">
                        @csrf
                        <h4>年度追加を行います。</h4>
                        <input type="number" name="add_year" value="" placeholder="例) 2024" class="uk-input" /> 
                        <p class="uk-text-right">
                            <button class="uk-button uk-button-default uk-modal-close" type="button">閉じる</button>
                            <button class="uk-button uk-button-primary" type="submit">登録</button>
                        </p>
                    </form>
                </div>
            </div>
            <div class="uk-grid">
                <div class="uk-width-1">
                    <table class="uk-table uk-table-responsive uk-table-divider">
                        <tr>
                            <th>年度</th>
                            <th>支払状況</th>
                            <th>請求書ダウンロード</th>
                            <th>領収書ダウンロード</th>
                            <th>領収書状態</th>
                        </tr>
                        @foreach($payments as $key=>$value)
                            <tr>
                                <td>{{$value->years}} 年度</td>
                                <td class="uk-text-center">
                                    {{--年会費のときは法人会員（窓口担当者）と個人会員のみ--}}
                                    @if($user->type == 2 || $user->type == 4)
                                    <select class="uk-select payment_status" id="payment_status-{{$value->id}}" name="status"  >
                                    @foreach(config('pacd.payment') as $k=>$val)
                                        <?php
                                        $sel = "";
                                        if($k == $value->status) $sel = "SELECTED";
                                        ?>
                                        <option value="{{$k}}" {{$sel}} >{{$val}}</option>
                                    @endforeach
                                    </select>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="uk-text-center" >

                                    @if($user->type == 2 || $user->type == 4)
                                        <a href="{{ route('admin.members.pdf', $value->id."/".$value->uid."/invoice") }}" class="uk-button uk-button-primary" target=_blank>ダウンロード</a>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="uk-text-center" >
                                    @if($user->type == 2 || $user->type == 4)
                                        <a href="{{ route('admin.members.pdf', $value->id."/".$value->uid."/recipe") }}" class="uk-button uk-button-danger" target=_blank>ダウンロード</a>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="uk-text-center">
                                    <select class="uk-select" name="recipe_status" id="recipe_status-{{$value->id}}">
                                    @foreach(config('pacd.recipe_status') as $k=>$val)
                                        <?php
                                            $sel = "";
                                            if($k == $value->recipe_status) $sel = "selected";
                                        ?>
                                        <option value="{{$k}}" {{$sel}} >{{$val}}</option>
                                    @endforeach
                                    </select>
                                </td>
                            </tr>

                        @endforeach


                    </table>
                </div>
            </div>

        </div>
    </div>
@endsection
