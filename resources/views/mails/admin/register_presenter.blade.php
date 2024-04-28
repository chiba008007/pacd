<p>{{ $form['display_name'] }}申し込みが届きました。</p>

<h3>申し込み内容</h3>

<dl>
    <dt>■参加イベント</dt>
    <dd>{{ $attendee->event->name }}</dd>
    <br>
    <dt>■会員番号</dt>
    <dd>{{ $user->login_id }}</dd>
    <br>
    <dt>■氏名</dt>
    <dd>{{ $user->sei }} {{ $user->mei }}</dd>
    <br>
    <dt>■氏名（ふりがな）</dt>
    <dd>{{ $user->sei_kana }} {{ $user->mei_kana }}</dd>
    <br>
    <dt>■メールアドレス</dt>
    <dd>{{ $user->email }}</dd>
    <br>
    @php $custom_data = $presenter->custom_form_data->keyBy('form_input_value_id') @endphp
    @foreach ($custom_inputs as $input)
        <dt>■{{ $input->name }}</dt>
        <dd>
            @foreach ($input->values as $value)
                {{ @$custom_data[$value->id]->data }}
                {{ @$custom_data[$value->id]->data_sub }}
                @if (!$loop->last)
                    <br>
                @endif
            @endforeach
        </dd>
        <br>
    @endforeach
</dl>

<br>
<p><a href="{{ route('admin.presenters.edit',[$form['category_prefix'], $presenter->id] ) }}">管理画面で確認する</a></p>
<br>
