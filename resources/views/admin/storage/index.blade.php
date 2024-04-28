@extends('layouts.admin')

@section('title', $title = '請求書/領収書一覧')

@section('breadcrumb')
    @parent
    <li><a href="{{ route('admin.members.index') }}">会員管理</a></li>
    <li><span>{{ $title }}</span></li>
@endsection

@section('content')

    <div class="uk-container uk-container-large">

        @if (session('flash_message'))
            <div uk-alert class="uk-alert-primary uk-margin-small-top">
                {{ session('flash_message') }}
            </div>
        @endif

        <div class="uk-section-small">
            <form action="{{ route('admin.members.storages.post') }}" method="post">
                 @csrf
                <div class="uk-flex">
                    <div class="uk-width-1-3">
                        <p>作成日期間検索</p>
                        <input type="date" name="fromDate" value="{{ $request->session()->get('fromDate') }}" class="uk-padding-small" />～
                        <input type="date" name="toDate" value="{{ $request->session()->get('toDate') }}" class="uk-padding-small" />
                    </div>
                    <div class="uk-width-1-3">
                        <p>ファイル名検索</p>
                        <input type="text" name="filename" value="{{ $request->session()->get('filename') }}" class="uk-padding-small" />
                        <input type="submit" name="search" value="検索" class="uk-button uk-button-primary" />
                    </div>
                    <div class="uk-width-1-3 ">
                        
                        <div class="uk-text-right">
                        <a href="{{ route('admin.members.storages.down') }}" class="uk-button uk-button-primary " onClick="return confirm('表示されている(ページ移動含む)請求書・領収書データがZIPファイルとしてダウンロードされます')">PDF一括ダウンロード</a>
                        
                        </div>
                    </div>
                </div>
            </form>
            {{-- ページネーション --}}
            <ul class="uk-pagination">
                @for($i=0;$i<$total;$i++)
                <li  class="uk-active"><a href="{{route('admin.members.storages',$i)}}">{{$i+1}}</a></li>                
                @endfor
            </ul>
            <div class="uk-overflow-auto uk-margin-top" id="scrollheight" >
                <table id="members_table" class="uk-table uk-text-center ">
                    <thead>
                        <tr>
                            <th class="">ファイル名</th>
                            <th class="">作成日</th>
                            <th class="uk-width-1-5">機能</th>
                        </tr>
                    </thead>
                    <tbody >
                        @foreach ($lists as $list)
                        <tr>
                            <td class="uk-text-meta uk-text-left">{{$list->filename}}</td>
                            <td class="uk-text-meta uk-text-center">{{str_replace('-', '/',$list->create_date)}}</td>
                            <td>
                                [<a href="{{route('admin.members.storagesDelete',['id'=>$list->id])}}" >削除</a>]
                                [<a href="{{route('admin.members.storagesDownload',['id'=>$list->id])}}" >ダウンロード</a>]
                                
                            </td>
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
        
    </script>
@endsection
