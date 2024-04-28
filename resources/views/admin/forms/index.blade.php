@extends('layouts.admin')

@section('title', $title)

@section('breadcrumb')
    @parent
    @foreach ($breadcrumbs as $breadcrumb)
        <li><a href="{{ $breadcrumb['url'] }}">{{ $breadcrumb['title'] }}</a></li>
    @endforeach
@endsection

@section('content')
    <div class="uk-container uk-container-large">
        <div class="uk-section-small">
            <div class="uk-text-right">
                <a href="{{ route('admin.form.preview', [$form['category_prefix'], $form['prefix']]) }}" class="uk-button uk-button-secondary">プレビュー</a>
                <a href="{{ route('admin.form.create', [$form['category_prefix'], $form['prefix']]) }}" class="uk-button uk-button-primary">新規登録</a>
            </div>

            <table id="members_table" class="uk-table uk-text-center">
                <thead>
                    <tr>
                        <th>詳細</th>
                        <th>イベント</th>
                        <th>項目名</th>
                        <th class="uk-text-nowrap">エラーチェック</th>
                        <th class="uk-text-nowrap">公開画面に表示</th>
                        <th class="uk-text-nowrap">会員一覧に表示</th>
                        <th class="uk-text-nowrap">フォーマット</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($inputs as $input)
                        <tr>
                            <td class="uk-text-nowrap">
                                <a href="{{ route('admin.form.edit', [$form['category_prefix'], $form['prefix'], $input->id]) }}" class="uk-button uk-button-small uk-button-primary">編集</a>
                                <form action="{{ route('admin.form.destroy', [$form['category_prefix'], $form['prefix'], $input->id]) }}" method="post" class="uk-inline">
                                    @csrf
                                    @method('delete')
                                    <button type="submit" class="uk-button uk-button-small uk-button-danger" onclick="return confirm('{{ $input->name }} を削除します。よろしいですか？')">削除</button>
                                </form>
                            </td>
                            <td class="uk-text-wrap">
                            @if($input->event_id == 0) 共通
                            @else　{{$input->event_name}}
                            @endif
                            </td>
                            <td class="uk-text-wrap">{{ $input->name }}</td>
                            <td class="uk-text-wrap">{{ implode(', ', $input->validation_rules_display) ?: 'なし' }}</td>
                            <td>{{ ($input->is_display_published) ? '表示する' : '表示しない' }}</td>
                            <td>{{ ($input->is_display_user_list) ? '表示する' : '表示しない' }}</td>
                            <td>
                                @if ($input->type == config('pacd.form.input_type.text'))
                                    テキスト型
                                @elseif ($input->type == config('pacd.form.input_type.select'))
                                    プルダウン型
                                @elseif ($input->type == config('pacd.form.input_type.check'))
                                    複数選択型
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection


