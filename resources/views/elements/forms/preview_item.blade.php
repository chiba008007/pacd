@if(isset($preview['action']) && $preview['action'] == 'register')
    <div class="uk-form-horizontal">
        <label class="uk-form-label uk-text-left">
            {{ $preview['name'] }}
            {{--  @if (strpos($preview['validation_rules'], 'required') !== false)
                <span class="uk-label uk-label-danger uk-margin-small-left">必須</span>
            @endif  --}}
        </label>
        <div class="uk-form-controls">
            @if($preview['type'] == 2)
                {{-- プルダウン型 --}}
                <select class="uk-select">
                    @foreach ($preview['value'] as $val)
                        <option>{{ $val }}</option>
                    @endforeach
                </select>
            @elseif($preview['type'] == 3)
                {{-- 複数選択型 --}}
                <div class="uk-grid-small" uk-grid>
                    @foreach ($preview['value'] as $key => $val)
                        @if ($preview['is_included_textarea'][$key])
                            <div class="uk-width-1-1 uk-flex uk-flex-middle">
                                <div class="uk-margin-right"><label><input class="uk-checkbox" type="checkbox" value="1"> {{ $val }}</label></div>
                                <div class="uk-width-expand"><input type="text" class="uk-input uk-form-small" value=''></div>
                            </div>
                        @else
                            <label><input class="uk-checkbox" type="checkbox" value="1"> {{ $val }}</label>
                        @endif
                    @endforeach
                </div>
            @else
                {{-- テキスト型 --}}
                @foreach ($preview['value'] as $key => $val)
                    <textarea class="uk-textarea" rows="1" placeholder="{{ $val }}"></textarea>
                @endforeach
            @endif
        </div>
    </div>
@endif
