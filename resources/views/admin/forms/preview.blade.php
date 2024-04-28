@extends('layouts.admin')

@section('title', $title)

@section('breadcrumb')
    @parent
    @foreach ($breadcrumbs as $breadcrumb)
        <li><a href="{{ $breadcrumb['url'] }}">{{ $breadcrumb['title'] }}</a></li>
    @endforeach
    <li><span>プレビュー</span></li>
@endsection

@section('content')
    <div class="uk-section-xsmall uk-container">
        <div id="page" class="uk-background-default uk-padding">
            @include('elements.pages.contents', $page)

            {{-- 会員登録・イベント参加申込フォーム --}}
            @isset($page['inputs'])
                <div class="form_contents">
                    @if ($page['route_name'] == 'register')
                        @include('elements.forms.members.register', ['inputs' => $page['inputs']])
                    @elseif (strpos($page['route_name'], 'presenter') !== false)
                        @include('elements.forms.presenters.register', ['inputs' => $page['inputs']])
                    @else
                        @include('elements.forms.attendees.register', ['inputs' => $page['inputs']])
                    @endif
                </div>

                {{-- 編集中フォーム項目のプレビュー --}}
                @include('elements.forms.preview_item')

            @endisset
        </div>
    </div>

    <div class="uk-section-xsmall uk-container">
        <form action="{{ route('admin.form.back',[ $form['category_prefix'], $form['prefix']]) }}" method="post" id="preview_form">
            @csrf
            @if(!empty($preview))
                <input type="hidden" name="back_url" value="{{ ($preview['action'] == 'register') ? route('admin.form.create',[ $form['category_prefix'], $form['prefix']]) : route('admin.form.edit', [$form['category_prefix'], $form['prefix'], $preview['form_input_id']]) }}">
                @foreach ($preview as $name => $data)
                    @if ($name != '_token' && $name != '_method')
                        @if (is_array($data))
                            @foreach ($data as $key => $val)
                                <input type="hidden" name="{{ $name }}[{{ $key }}]" value="{{ $val }}">
                            @endforeach
                        @else
                            <input type="hidden" name="{{ $name }}" value="{{ $data }}">
                        @endif
                    @endif
                @endforeach
                @if($preview['action'] == 'update')
                    <button type="submit" class="uk-button uk-button-primary" formaction="{{ route('admin.form.update', [$form['category_prefix'], $form['prefix'], $preview['form_input_id']]) }}" name="action" value="update">更新</button>
                @else
                    <button type="submit" class="uk-button uk-button-primary" formaction="{{ route('admin.form.store', [$form['category_prefix'], $form['prefix']]) }}" name="action" value="register">登録</button>
                @endif
            @endif
            <button type="submit" class="uk-button uk-button-secondary" formaction="{{ route('admin.form.back', [$form['category_prefix'], $form['prefix']]) }}">戻る</button>
        </form>
    </div>
@endsection

@section('footer')
    <script>
        $(".edit-content a, .edit-content [type='submit'], .form_contents [type='submit'], .form_contents a").click(function(){
            UIkit.notification("管理画面からは移動できません", {status: 'danger'});
            return false;
        });
        $("button[value='update']").click(function() {
            $("#preview_form").append('<input type="hidden" name="_method" value="put">');
        });
    </script>
@endsection
