@extends('layouts.admin')

@section('title', $title = '会員情報アップロード')

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
            <div >

                <form action="{{ route('admin.members.upload') }}" method="post" class="uk-inline"  enctype="multipart/form-data">
                    @csrf
                    <div class="uk-grid">
                        <div class="uk-width-1-2">
                            <h4>会員情報一覧ファイル</h4>
                            <input type="file" name="csv_file" multiple>
                        </div>
                        <div class="uk-width-1-2">
                            <h4>添付ファイル</h4>
                            <input type="file" name="temp_file" multiple>
                        </div>
                    </div>
                    <div class="uk-margin-large-top">
                        <button class="uk-button uk-button-default" type="submit" name="upload" value="on" tabindex="-1">アップロード</button>
                       <a href="{{ route('admin.members.template') }}" class="uk-button uk-button-secondary uk-margin-large-left" >テンプレートダウンロード</a>
                    </div>
                </form>
            </div>

        </div>
    </div>
@endsection
