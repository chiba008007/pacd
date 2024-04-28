@extends('layouts.admin')

@section('title', $title)

@section('breadcrumb')
    @parent
    <li><span>{{ $title }}</span></li>
@endsection

@section('content')
    <div class="uk-section-small">
        <form action="{{ route("admin.$category_prefix.edit",$key) }}" method="POST" >
        @csrf
            <div class="uk-container uk-container-large">
                @if (session('status'))
                    <div class="uk-alert-success" uk-alert>
                    <a class="uk-alert-close" uk-close></a>
                        {{ session('status') }}
                    </div>
                @endif

                <div class="uk-grid-column-small uk-grid-row-large uk-child-width-1-2@s " uk-grid>
                    <div>
                        <div class="uk-card uk-card-default uk-card-body">
                            <select class="uk-select" name="form_type">
                                @foreach($CONST_MAIL_FORM_TEMP as $value)
                                <option value="{{$value['key']}}">{{$value['display']}}</option>
                                @endforeach
                            </select>
                            <div class="uk-margin-top">
                                <label >メールタイトル</label>
                                <input class="uk-input" type="text" name="title" placeholder="メールのタイトルを入力してください">
                            </div>
                            <div class="uk-margin-top">
                                <textarea class="uk-textarea" name="note" rows="15" placeholder="メール内容入力"></textarea>
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class="uk-card uk-card-default uk-card-body">
                            <p>置き換え</p>
                            {{--会員登録--}}
                            <table class="uk-table">
                            @foreach($CONST_MAIL_REPLACE as $key=>$value)
                                <tr>
                                    <th>{{$value['jp']}}</th>
                                    <td>{{$value[ 'replace' ]}}</td>
                                </tr>
                            @endforeach
                            {{--各登録フォーム--}}
                            @foreach($form_type as $key=>$value)
                                <tr class="code-<?=$value->form_type?> hide">
                                    <th>{{$value['name']}}</th>
                                    <td>##mem{{$value[ 'id' ]}}##</td>
                                </tr>
                            @endforeach
                            {{--原稿登録用--}}
                            @foreach($CONST_MAIL_REPLACE_UPLOAD as $key=>$value)
                                <tr class="code-upload hide">
                                    <th>{{$value['jp']}}</th>
                                    <td>{{$value[ 'replace' ]}}</td>
                                </tr>
                            @endforeach
                            {{--参加費--}}
                            @foreach($CONST_MAIL_REPLACE_JOIN as $key=>$value)
                                <tr class="code-join hide">
                                    <th>{{$value['jp']}}</th>
                                    <td>{{$value[ 'replace' ]}}</td>
                                </tr>
                            @endforeach

                            </table>
                        </div>
                    </div>
                </div>
                <div class="uk-margin-top">
                    <button type="submit" class="uk-button uk-button-primary" id="send"> 更新 </button>
                </div>
            </div>

        </form>
    </div>
@endsection

@section('footer')
    <script src="{{ asset('js/admin/event.js') }}"></script>
@endsection
