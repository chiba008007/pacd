@extends('layouts.admin')

@section('title', $title)

@section('breadcrumb')
    @parent
    <li><span>{{ $title }}</span></li>
@endsection

@section('content')
    <div class="uk-section-small">
        <div class="uk-container uk-container-large">
            <form action="{{ route('admin.pages.url.setting') }}" method="POST" enctype="multipart/form-data" >
                @csrf

                <label>ファイルアップロード</label><br />
                <input type="file" name="files">
                <br />
                <br />
                <label>説明文</label>
                <input type="text" name="memo" value="" class="uk-input" />
                <div class="uk-padding-small">
                    <input type="submit" name="regist" value="登録" class="uk-button uk-button-primary" />
                </div>
            </form>
            <table class="uk-table">
                <thead>
                    <tr>
                        <th>作成日</th>
                        <th>URL</th>
                        <th>説明</th>
                        <th>&nbsp;</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($urls as $key=>$url)
                        <tr>
                            <td>{{ $url->updated_at }}</td>
                            <td>{{ route('urldownload', $url->id ) }}</td>
                            <td>{{ $url[ 'memo' ] }}</td>
                            <td><a href="{{ route('admin.pages.url.delete', $url->id) }}" class="uk-button uk-button-danger" >削除</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>


        </div>
    </div>
@endsection

@section('footer')
    <script src="{{ asset('js/admin/pages.js') }}"></script>
@endsection
