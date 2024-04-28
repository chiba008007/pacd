@extends('layouts.admin')

@section('title', $title)

@section('breadcrumb')
    @parent
    <li><span>{{ $title }}</span></li>
@endsection

@section('content')
    <div class="uk-section-small">
        <div class="uk-container uk-container-large">
            <table class="uk-table">
                <thead>
                    <tr>
                        <th>詳細</th>
                        <th>ページ</th>
                        <th>URL</th>
                        <th>前回編集日</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pages as $page)
                        <tr>
                            <td class="uk-text-center uk-text-nowrap">
                                <a href="{{ route('admin.pages.edit', $page->id) }}" class="uk-button uk-button-small uk-button-primary">編集</a>
                                @if(!$page->is_form)
                                    <button class="open_setting_btn uk-button-small @if($page['is_opened']) uk-button-danger @else uk-button-secondary @endif text-nowrap" type="button" uk-toggle="target: #confirm" 
                                        data-page-id="{{ $page['id'] }}"
                                        data-page-name="{{ $page['title'] }}"
                                        data-open-value="{{ (int)!$page['is_opened'] }}"
                                    >{{ $page['is_opened'] ? '非公開にする' : '公開する' }}</button>
                                @endif
                            </td>
                            <td class="uk-text-nowrap">{{ $page['title'] }}</td>
                            <td>
                                @if (Route::has($page['route_name']))
                                    @if (!$page['is_form'] || $page['route_name'] == 'register')
                                        <a href="{{ route($page['route_name']) }}" target="_blank" rel="noopener noreferrer">{{ route($page['route_name']) }} </a>
                                    @else
                                    {{ url('') . $page['uri'] }}
                                    @endif
                                @else
                                    {{ url('') . $page['uri'] }}
                                @endif
                            </td>
                            <td class="uk-text-center">{{ $page['updated_at']->format('Y/m/d') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div id="confirm" uk-modal>
                <div class="uk-modal-dialog uk-modal-body">
                    <p>
                        「<span id="page_name"></span>」を<span id="open_value_disp"></span><br>
                        よろしいですか？
                    </p>
                    <form class="uk-text-right" action="{{ route('admin.pages.open') }}" method="POST">
                        @csrf
                        <input type="hidden" id="page_id" name="id" value="">
                        <input type="hidden" id="open_value" name="is_opened" value="">
                        <button class="uk-button uk-modal-close">キャンセル</button>
                        <button class="uk-button uk-button-primary" type="submit">設定</button>
                    </form>
                </div>
            </div>

            {{-- ページネーション --}}
            <div>
                {{ $pages->links() }}
            </div>
        </div>
    </div>
@endsection

@section('footer')
    <script src="{{ asset('js/admin/pages.js') }}"></script>
@endsection
