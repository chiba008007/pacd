<p>
    <a href="{{ route('top') }}">{{ config('app.name') }}</a> サイトより会員の登録が行われました。<br>
    会員区分などの情報を更新する場合は、<a href="{{ route('admin.members.index') }}">管理画面</a> にログインして操作してください。
</p>

<h3>会員情報</h3>

<dl>
    <dt>■ログインID</dt>
    <dd>{{ $user->login_id }}</dd>
    <br>
    <dt>■会員区分</dt>
    <dd>非会員</dd>
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
    @php $custom_data = $user->custom_form_data->keyBy('form_input_value_id') @endphp
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
