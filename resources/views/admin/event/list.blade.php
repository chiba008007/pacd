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
            @csrf
            <div id="pagers">
                {{ $events->links() }}
            </div>
            <form method="GET" action="" class="uk-form-horizontal" >
            @csrf
                <div class="uk-grid-column-small uk-grid-row-large uk-child-width-1-3@s "  uk-grid>
                    <div>
                        <input class="uk-input" type="text" name="code"  value="{{ old('code',$code)}}" placeholder="イベント名・イベントコード検索">
                    </div>
                    <div>
                        <button type="submit" class="uk-button uk-button-secondary">検索</button>
                    </div>
                </div>
            </form>
            <table class="uk-table">
                <thead>
                    <tr>
                        <th class="uk-table-expand" style="width:350px;">詳細</th>
                        <th >イベントコード</th>
                        <th>イベント名</th>
                        <th class="uk-table-shrink">受付中</th>
                    </tr>
                </thead>
                <tbody>
                @foreach ($events as $event)
                    <tr>
                        <td>
                            @if($category_prefix != "kyosan")
                            <a href="{{ route("admin.$category_prefix.event.program.register", $event->id) }}" class="uk-button uk-button-primary uk-text-nowrap">プログラム</a>
                            @endif

                            <?php $str = "参加者";?>
                            @if(isCurrent('admin.bunseki.event'))
                            <?php $str = "参加/協賛";?>
                            @endif
                            <?php $top = ""; ?>
                            @if($category_prefix == "kyosan")
                            <?php $top = "uk-margin-small-top"; ?>
                            @endif
                            <a class="<?=$top?> uk-button uk-button-primary uk-text-nowrap" href="{{ route("admin.attendees.index", [$category_prefix, 'code' => $event->code]) }}">{{$str}}</a>
                            @if($category_prefix != "kyosan")
                            <a class="uk-button uk-button-primary uk-text-nowrap" href="{{ route("admin.presenters.index", [$category_prefix, 'code' => $event->code]) }}">講演者</a>
                            @endif
                            @if($category_prefix != "kyosan")
                            <a href="{{ route("admin.$category_prefix.event.upload", $event->id) }}" class="uk-margin-small-top uk-button uk-button-primary uk-text-nowrap">原稿</a>
                            @endif

                            <a href="{{ route("admin.$category_prefix.event.register", $event->id) }}" class="uk-margin-small-top uk-button uk-button-primary uk-text-nowrap">編集</a>

                            <a href="{{ route("admin.$category_prefix.event.delete", $event->id) }}" class="uk-margin-small-top uk-button uk-button-danger uk-text-nowrap deletebutton">削除</a>

                        </td>
                        <td>{{$event->code}}</td>
                        <td>{{$event->name}} 企業協賛</td>
                        <td>
                            <div class="switchArea">
                                <?php $checked = "";?>
                                @if($event->enabled == 1)
                                <?php $checked = "checked";?>
                                @endif
                                <input type="checkbox" id="switch-{{$event->id}}" class="enableswitch" <?=$checked?> >
                                <label for="switch-{{$event->id}}"><span></span></label>
                                <div class="swImg"></div>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

        </div>
    </div>
@endsection

@section('footer')
    <script src="{{ asset('js/admin/event.js') }}"></script>
@endsection
