@extends('layouts.admin')

@section('title', $title)

@section('breadcrumb')
    @parent
    <li><span>{{ $title }}</span></li>
@endsection

@section('content')
    <div class="uk-section-small">
        <div class="uk-container uk-container-large">
            @if (session('status'))
                <div class="uk-alert-success" uk-alert>
                <a class="uk-alert-close" uk-close></a>
                    {{ session('status') }}
                </div>
            @endif
            <form action="{{ route("admin.$category_prefix.event.upload.add") }}" method="POST" enctype="multipart/form-data">

            @csrf
                <table class="uk-table">
                    <tr>
                        <th>{{config('pacd.CONST_FILE_UPLOAD_NAME')[1]}}</th>
                        <td><input type="file" name="upfile1" /></td>
                        <td><input type="text" name="upfile1_name" value="@if(!empty($lists['1']['dispname'])){{$lists['1']['dispname']}}@endif"  class="uk-input" placeholder="表示ファイル名" /></td>

                        <td><input type="text" name="lockkey1" value="@if(!empty($lists['1']['lockkey'])){{$lists['1']['lockkey']}}@endif"  class="uk-input" placeholder="解除キー" /></td>

                        <td class="uk-text-center">
                            @if(!empty($lists[1]['filename']) && $lists[1]['filename'])
                            <a href="/storage/{{$lists[1]['filename']}}" target=_blank class="uk-button uk-button-default" >ファイル確認</a>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>{{config('pacd.CONST_FILE_UPLOAD_NAME')[2]}}</th>
                        <td><input type="file" name="upfile2" /></td>
                        <td><input type="text" name="upfile2_name" value="@if(!empty($lists['2']['dispname'])){{$lists['2']['dispname']}}@endif" class="uk-input" placeholder="表示ファイル名" /></td>
                        <td><input type="text" name="lockkey2" value="@if(!empty($lists['2']['lockkey'])){{$lists['2']['lockkey']}}@endif"  class="uk-input" placeholder="解除キー" /></td>
                        <td class="uk-text-center">
                            @if(!empty($lists[2]['filename']) && $lists[2]['filename'] )
                            <a href="/storage/{{$lists[2]['filename']}}" target=_blank class="uk-button uk-button-default" >ファイル確認</a>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>{{config('pacd.CONST_FILE_UPLOAD_NAME')[3]}}</th>
                        <td><input type="file" name="upfile3" /></td>
                        <td><input type="text" name="upfile3_name" value="@if(!empty($lists['3']['dispname'])){{$lists['3']['dispname']}}@endif" class="uk-input" placeholder="表示ファイル名" /></td>

                        <td><input type="text" name="lockkey3" value="@if(!empty($lists['3']['lockkey'])){{$lists['3']['lockkey']}}@endif"  class="uk-input" placeholder="解除キー" /></td>

                        <td class="uk-text-center">
                            @if(!empty($lists[3]['filename']) && $lists[3]['filename'] )
                            <a href="/storage/{{$lists[3]['filename']}}" target=_blank class="uk-button uk-button-default" >ファイル確認</a>
                            @endif
                        </td>
                    </tr>

                </table>


                <input type="hidden" name="id" value="{{old('id',$id)}}" />
                <input type="submit" name="regist" id="send" value="登録" class="uk-button uk-button-primary" />
            </form>
        </div>
    </div>
@endsection

@section('footer')
    <script src="{{ asset('js/admin/event.js') }}"></script>
@endsection
