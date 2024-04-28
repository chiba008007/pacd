@extends('layouts.admin')

@section('title', $title)

@section('breadcrumb')
    @parent
    <li><span>{{ $title }}</span></li>
@endsection

@section('content')
    <div class="uk-section-small">
        <div class="uk-container uk-container-large">
            <a class="uk-button uk-button-primary" href="{{ route('admin.pages.banner.new') }}">新規登録</a>

        </div>

        <div class="uk-container uk-container-large">
            <div class="uk-margin-top">

                <form method="POST" action="{{ route('admin.pages.banner.set') }}"  >
                    @csrf

                    @if (session('flash_message'))
                        <div class="uk-alert-success" uk-alert>
                            {{ session('flash_message') }}
                        </div>
                    @endif
                    <div class="uk-text-right" >
                        <input type="number" name="smooth" value="{{$smooth}}" placeholder="スライド時間" class="uk-input uk-width-1-5@s" min=0 />
                        <input type="submit" name="update" value="データ更新" class="uk-button uk-button-danger" />
                    </div>

                    <table class="uk-table uk-table-small">
                        <tr>
                            <th >バナー画像</th>
                            <th>表示期間</th>
                            <th>URL<br />リンク先</th>
                            <th>並び順</th>
                            <th nowrap>ステータス</th>
                            <th>機能</th>
                        </tr>
                        @foreach( $select as $key=>$value)
                        <tr>
                            <td >

                                @if( @exif_imagetype(asset('storage/bannar/'.$value->filename)))
                                <img src="{{ asset('storage/bannar/'.$value->filename) }}" />
                                @endif
                            </td>
                            <td>
                                {{preg_replace("/-/","/",$value->startdate)}}
                                ～<br />
                                {{preg_replace("/-/","/",$value->enddate)}}
                            </td>
                            <td>

                            【{{asset('storage/bannar/'.$value->filename)}}】
                            <br />
                            【{{$value->url}}】

                            </td>
                            <td><input type="number" name="sort[{{$value->id}}]" value="{{$value->sort}}" class="uk-input uk-form-small uk-form-width-xsmall" min=0  /></td>
                            <td>
                                <select name="status[{{$value->id}}]" class="uk-select">
                                    @foreach( $status as $k=>$val)
                                        <?php
                                            $sel = "";
                                            if($value->status == $k) $sel = "SELECTED";
                                        ?>
                                        <option value="{{$k}}" <?=$sel?> >{{$val}}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <a href="{{ route('admin.pages.banner.new') }}/{{$value->id}}" class="uk-button uk-button-primary">更新</a>
                                <a href="{{ route('admin.pages.banner.del') }}/{{$value->id}}" class="uk-button uk-button-danger" onclick="if(confirm('データの削除を行います。')){return true;}else{return false;}">削除</a>
                            </td>
                        </tr>
                        @endforeach
                    </table>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('footer')
    <script src="{{ asset('js/admin/pages.js') }}"></script>
@endsection
