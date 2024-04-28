@extends('layouts.admin')

@section('title', $title = '会員一覧')

@section('breadcrumb')
    @parent
    <li><a href="{{ route('admin.members.index') }}">会員管理</a></li>
    <li><span>{{ $title }}</span></li>
@endsection

@section('content')
    <div id="modal-example" uk-modal>
        <div class="uk-modal-dialog uk-modal-body">
            <h2 class="uk-modal-title">CSVダウンロード</h2>
            @foreach(config('pacd.user.type') as $key=>$value)

                <a href="{{ route('admin.members.csv.parts') }}/{{$key}}" class="uk-button uk-button-default uk-width-1-1 uk-margin-small-bottom">{{$value}}</a>

            @endforeach
            <button class="uk-button uk-button-secondary uk-modal-close" type="button">閉じる</button>
        </div>
    </div>
    <div class="uk-container uk-container-large">
        <div class="uk-section-small">

            <div class="uk-text-right uk-margin-bottom">
            <!--
                <a href="{{ route('admin.members.csv') }}" class="uk-button uk-button-default">CSVダウンロード</a>
            -->
                <a href="javascript:void(0);" class="uk-button uk-button-default" uk-toggle="target: #modal-example">CSVダウンロード</a>

                <a href="{{ route('admin.members.create') }}" class="uk-button uk-button-primary">新規登録</a>
            </div>

{{-- ページネーション --}}
            <div id="pagers">
                {{ $users->appends($params)->links() }}
            </div>

            <form method="GET" action="" class="uk-form-horizontal" >
            @csrf
                <div class="uk-grid-column-small uk-grid-row-large uk-child-width-1-2@s "  uk-grid>
                    <div>
                        <input class="uk-input" type="text" name="code"  value="{{ old('code',$code)}}" placeholder="法人・所属・ログインID・会員名・メールアドレスで検索">
                    </div>
                    <div>
                        <button type="submit" class="uk-button uk-button-secondary">検索</button>
                    </div>
                </div>
                <div class="uk-child-width-1-2@s uk-margin-small-top">
                    <div >
                        <button type="button" class="uk-button uk-button-primary" onClick="recipeStatus(1)" >
                        <small>請求書・領収書ダウンロード可に変更</small>
                        </button>

                        <button type="button" class="uk-button uk-button-danger" onClick="recipeStatus(0)">
                        <small>請求書・領収書ダウンロード不可に変更</small>
                        </button>
                    </div>
                </div>
            </form>
            <input type="hidden" id="currentPath" value="{{$currentPath}}" />
            <div class="uk-overflow-auto uk-margin-top" id="scrollheight" >
                <table id="members_table" class="uk-table uk-text-center tablesorter" style="table-layout: fixed;">
                    <thead>
                        <tr>
                            <th class="uk-width-small">詳細</th>
                            <th class="uk-width-small">年会費</th>
                            <th class="uk-width-small">法人名</th>
                            <th class="uk-width-small">所属名</th>
                            <th class="uk-width-small">ログインID</th>
                            <th class="uk-width-small">会員区分</th>
                            <th class="uk-width-small">会員名</th>
                            <th class="uk-width-small">会員名（ふりがな）</th>
                            <th class="uk-width-medium">メールアドレス</th>
                            <th class="uk-width-small">領収書ダウンロード</th>
                            <th class="uk-width-small">更新日</th>
                            <th class="uk-width-medium">備考</th>
                            @if($inputs->count())
                                @foreach ($inputs as $item)
                                    <th class="uk-width-medium">{{ $item->name }}</th>
                                @endforeach
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr>
                                <td >
                                    <a href="{{ route('admin.members.edit', $user->id) }}" class="uk-button uk-button-small uk-button-primary">編集</a>
                                    <form action="{{ route('admin.members.destroy', $user->id) }}" method="post" class="uk-inline">
                                        @csrf
                                        @method('delete')
                                        <button type="submit" class="uk-button uk-button-small uk-button-danger" onclick="return confirm('{{ $user->login_id }} を削除します。よろしいですか？')">削除</button>
                                    </form>

                                </td>

                                <td>
                                {{--法人会員（窓口担当者）個人会員のみ--}}
                                @if($user->type == 2 || $user->type == 4)
                                    <a href="{{ route('admin.members.payment', $user->id) }}" class="uk-button uk-button-small uk-button-secondary">確認</a>
                                       @else
                                    -
                                @endif
                                </td>
                                <td>{{$user->cp_name}}</td>
                                <td>{{$user->busyo}}</td>
                                <td class="uk-text-break">{{ $user->login_id }}</td>
                                <td>{{ config("pacd.user.type.$user->type", '') }}</td>
                                <td>{{ $user->sei }} {{ $user->mei }}</td>
                                <td>{{ $user->sei_kana }} {{ $user->mei_kana }}</td>
                                <td>{{ $user->email }}</td>
                                {{-- TODO: 領収書ダウンロード許可状況変更機能追加（編集ページで変更は可能） --}}
                                <td>

                                <select  id="is_enabled_invoice-{{$user->id}}" class="is_enabled_invoice uk-select uk-width-1-1" style="font-size:0.85%;">
                                    @foreach( config('pacd.is_enabled_invoice') as $key=>$val)
                                    <?php $select = ""; ?>
                                    @if ($key == $user->is_enabled_invoice)
                                    <?php $select = "selected"; ?>
                                    @endif
                                    <option value="{{$key}}" {{$select}} >{{$val}}</option>
                                    @endforeach
                                </select>
                                </td>
                                <td>{{ date_format($user->updated_at, 'Y/m/d') }}</td>

                                <td class="uk-text-left">{{ $user->remarks }}</td>
                                @if($inputs->count())
                                    @php $custom_data = $user->custom_form_data->keyBy('form_input_value_id') @endphp
                                    @foreach ($inputs as $item)
                                        <td>
                                            @foreach ($item->values as $value)
                                                @isset($custom_data[$value->id])
                                                    {{ $custom_data[$value->id]->data }}
                                                    @if ($custom_data[$value->id]->data_sub)
                                                        <br>
                                                        {{ $custom_data[$value->id]->data_sub }}
                                                    @endif
                                                    @if (!$loop->last)
                                                        <br>
                                                    @endif
                                                @endisset
                                            @endforeach
                                        </td>
                                    @endforeach
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>


        </div>
    </div>
@endsection

@section('footer')
    <script>
        function recipeStatus(status){
            var _currentPath = "/"+$("#currentPath").val();
            var postData ={};
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: _currentPath+"/recipeStatusAjax/"+status,
                type: 'GET',
                data: postData,
                timeout: 5000,
            })
            .then(result => {
                alert("データの更新をおこないました。");
                $(".is_enabled_invoice").val(status);
            })
            .catch(error => {
                console.log('データ更新エラー');
                console.log(error.status);
            });
            return false;

        }
        $('#members_table').tablesorter({
            headers: {
                0: {sorter:false},
            }
        });
        $(".is_enabled_invoice").change(function(){
        var _currentPath = "/"+$("#currentPath").val();
        var _id = $(this).attr("id").split("-")[1];
        var _val = $(this).val();
            var postData ={
                "id":_id,
                "is_enabled_invoice":_val
            };
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: _currentPath+"/invoiceDownloadAjax",
                type: 'POST',
                data: postData,
                timeout: 5000,
            })
            .then(result => {

            })
            .catch(error => {
                console.log('データ更新エラー');
                console.log(error.status);
            });
            return false;
        });
    </script>
@endsection
