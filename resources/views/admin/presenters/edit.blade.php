@extends('layouts.admin')

@section('title', $title)

@section('breadcrumb')
    @parent
    @foreach ($breadcrumbs as $breadcrumb)
        <li><a href="{{ $breadcrumb['url'] }}">{{ $breadcrumb['title'] }}</a></li>
    @endforeach
    <li><span>編集</span></li>
@endsection

@section('content')
    <div class="uk-section-small">
        <div class="uk-container uk-container-large">

            <div class="uk-section-xsmall">
                <div class="uk-form-horizontal">
                    <fieldset class="uk-fieldset">
                        <div>
                            <div class="uk-form-label">イベント名</div>
                            <div class="uk-form-controls uk-form-controls-text">{{ $event->name }}</div>
                        </div>
                        <div>
                            <div class="uk-form-label">開催期間</div>
                            <div class="uk-form-controls uk-form-controls-text">{{ $event->date_start }}～{{ $event->date_end }}</div>
                        </div>
                        <div>
                            <div class="uk-form-label">開催場所</div>
                            <div class="uk-form-controls uk-form-controls-text">{{ $event->place }}</div>
                        </div>
                    </fieldset>
                </div>
            </div>

            <div class="uk-section-xsmall">
                <div class="uk-form-horizontal">
                    <fieldset class="uk-fieldset">
                        <div>
                            <div class="uk-form-label">講演者番号</div>
                            <div class="uk-form-controls uk-form-controls-text">{{ sprintf('%010d', $presenter->id) }}</div>
                        </div>
                        <div>
                            <div class="uk-form-label">参加者番号</div>
                            <div class="uk-form-controls uk-form-controls-text">{{ sprintf('%010d', $attendee->event_number) }}</div>
                        </div>
                        <div>
                            <div class="uk-form-label">ログインID</div>
                            <div class="uk-form-controls uk-form-controls-text">
                            @if(isset($user->login_id))
                            {{ $user->login_id }}
                            @else
                            &nbsp;
                            @endif
                            </div>
                        </div>
                        <div>
                            <div class="uk-form-label">氏名</div>
                            <div class="uk-form-controls uk-form-controls-text">
                            @if(isset($user->sei))
                            {{ $user->sei }}　{{ $user->mei }}
                            @else
                            &nbsp;
                            @endif
                            </div>
                        </div>
                        <div>
                            <div class="uk-form-label">氏名（ふりがな）</div>
                            <div class="uk-form-controls uk-form-controls-text">
                            @if(isset($user->sei_kana))
                            {{ $user->sei_kana }}　{{ $user->mei_kana }}
                            @else
                            &nbsp;
                            @endif
                            </div>
                        </div>
                        <div>
                            <div class="uk-form-label">メールアドレス</div>
                            <div class="uk-form-controls uk-form-controls-text">
                            @if(isset($user->email))
                            {{ $user->email }}
                            @else
                            &nbsp;
                            @endif
                            </div>
                        </div>
                    </fieldset>
                </div>
            </div>

            <div class="uk-section-xsmall">
                <form method="POST" action="{{ route('admin.presenters.update', [$form['category_prefix'], $presenter->id]) }}" class="uk-form-horizontal" id="presenter_update_form"  enctype="multipart/form-data">
                    @csrf
                    @method('put')
                    <fieldset class="uk-fieldset">
                        {{-- カスタムインプット項目 --}}
                        @if($inputs->count())
                            @include('elements.forms.custom_inputs', [$inputs, 'custom_data' => $presenter->custom_form_data->keyBy('form_input_value_id')])
                        @endif

                        <div class="uk-margin">
                            <label class="uk-form-label uk-text-left" for="number">
                                発表番号
                            </label>
                            <div class="uk-form-controls">
                                <input id="number" type="text" class="uk-input " name="number" value="{{ old('number', @$presentation->number ) }}" />
                                @error('number')
                                    <div class="uk-text-danger uk-text-uppercase">
                                        <p>{{ $message }}</p>
                                    </div>
                                @enderror
                            </div>
                        </div>
                        @if($form['category_prefix'] == "touronkai")
                            <div class="uk-margin">
                                <label class="uk-form-label uk-text-left" for="description">
                                    題目
                                </label>
                                <div class="uk-form-controls">
                                    <textarea name="daimoku" class="uk-textarea" rows="3">{{ old() ? old('description') : @$presentation->daimoku }}</textarea>
                                    @error('daimoku')
                                        <div class="uk-text-danger uk-text-uppercase">
                                            <p>{{ $message }}</p>
                                        </div>
                                    @enderror
                                </div>
                            </div>


                            <div class="uk-margin">
                                <label class="uk-form-label uk-text-left">
                                    発表者/所属
                                </label>
                                <div class="uk-form-controls">
                                <p>ポスター発表者は発表者1、所属1へ記入。所属1は先頭に「○」をつけること（記入例）○高分子大学</p>
                                    <table class="uk-table" >
                                        <tr>
                                            <th colspan=2>発表者</th>
                                            <th colspan=2>所属</th>
                                        </tr>
                                        @for($i=1;$i<=6;$i++)
                                        <tr>
                                            <td>発表者{{$i}}</td>
                                            <td>
                                                <input type="text" class="uk-input " name="enjya{{$i}}" value="{{ $presentation->{'enjya' . $i} }}" >
                                            </td>
                                            <td>所属{{$i}}</td>
                                            <td>
                                                <input type="text" class="uk-input " name="syozoku{{$i}}" value="{{ $presentation->{'syozoku' . $i} }}" >
                                            </td>
                                        </tr>
                                        @endfor
                                    </table>
                                <p>発表者、所属が書ききれない場合は、こちらに記入ください。氏名、所属の順で記入。</p>
                                <textarea name="enjya_other" class="uk-textarea" rows="3" placeholder="記入例）氏名、所属の順で記入。">{{ old() ? old('enjya_other') : @$presentation->enjya_other }}</textarea>
                                </div>
                            </div>


                            {{--
                            <div class="uk-margin">
                                <label class="uk-form-label uk-text-left" for="description">
                                    発表演者
                                </label>
                                <div class="uk-form-controls">
                                    <textarea name="enjya" class="uk-textarea" rows="3" placeholder="記入例）〇山田 一郎,　鈴木次郎　（発表者に〇印　区切りはカンマ「,」">{{ old() ? old('enjya') : @$presentation->enjya }}</textarea>
                                    @error('enjya')
                                        <div class="uk-text-danger uk-text-uppercase">
                                            <p>{{ $message }}</p>
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="uk-margin">
                                <label class="uk-form-label uk-text-left" for="description">
                                    所属
                                </label>
                                <div class="uk-form-controls">
                                    <textarea name="syozoku" class="uk-textarea2" rows="3" placeholder="記入例）□□大,　△△大院工　等 　略称で記入
法人格は株式会社・有限会社・財団法人・社団法人・独立行政法人・国立大学法人等は省略
（区切りはカンマ「,」　発表者所属に○印）
">{{ old() ? old('syozoku') : @$presentation->syozoku }}</textarea>
                                    @error('syozoku')
                                        <div class="uk-text-danger uk-text-uppercase">
                                            <p>{{ $message }}</p>
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            --}}
                            <div class="uk-margin">
                                <label class="uk-form-label uk-text-left" for="description">
                                    発表概要
                                </label>
                                <div class="uk-form-controls">
                                    <textarea name="gaiyo" class="uk-textarea" rows="3">{{ old() ? old('gaiyo') : @$presentation->gaiyo }}</textarea>
                                    @error('gaiyo')
                                        <div class="uk-text-danger uk-text-uppercase">
                                            <p>{{ $message }}</p>
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        @else
                            <div class="uk-margin">
                                <label class="uk-form-label uk-text-left" for="description">
                                    講演内容
                                </label>
                                <div class="uk-form-controls">
                                    <textarea name="description" class="uk-textarea" rows="5">{{ old() ? old('description') : @$presentation->description }}</textarea>
                                    @error('description')
                                        <div class="uk-text-danger uk-text-uppercase">
                                            <p>{{ $message }}</p>
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        @endif
                        <div class="uk-margin">
                            <label class="uk-form-label uk-text-left" for="proceeding">
                                @if($form['category_prefix'] == "reikai")
                                    配布資料1
                                @elseif($form['category_prefix'] == "kosyukai")
                                    配布資料1
                                @else
                                    講演要旨
                                @endif
                            </label>
                            <div class="uk-form-controls">
                                <div class="uk-grid-small uk-flex-middle" uk-grid>
                                    <div uk-form-custom="target: true">
                                        <input type="file" name="file[proceeding]">
                                        <input class="uk-input" type="text" placeholder="ファイルを選択" disabled>
                                    </div>
                                    @if (@$presentation->proceeding && $presentation->number)
                                        <div><a href="{{ route('presentation.get.file', [@$presentation->number, 'proceeding',$presentation->id]) }}" target="_blank">登録ファイル確認</a></div>
                                        <div>
                                            <label><input name="delete[proceeding]" class="uk-checkbox" type="checkbox" value="1" @if(old("delete.proceeding")) checked @endif> 削除する</label>
                                        </div>
                                    @endif
                                </div>
                                @error('file.proceeding')
                                    <div class="uk-text-danger uk-text-uppercase">
                                        <p>{{ $message }}</p>
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <div class="uk-margin">
                            <label class="uk-form-label uk-text-left" for="flash">
                                @if($form['category_prefix'] == "reikai")
                                    配布資料2
                                @elseif($form['category_prefix'] == "kosyukai")
                                    配布資料2
                                @elseif($event_id == 238 && $form['category_prefix'] == "touronkai")
                                    {{--第28回高分子分析討論会のみ対応--}}
                                    プレゼンテーション資料
                                @else
                                    フラッシュプレゼンテーションファイル
                                @endif

                            </label>
                            <div class="uk-form-controls">
                                <div class="uk-grid-small uk-flex-middle" uk-grid>
                                    <div uk-form-custom="target: true">
                                        <input type="file" name="file[flash]">
                                        <input class="uk-input" type="text" placeholder="ファイルを選択" disabled>
                                    </div>
                                    @if (@$presentation->flash && $presentation->number)
                                        <div><a href="{{ route('presentation.get.file', [@$presentation->number, 'flash',$presentation->id]) }}" target="_blank">登録ファイル確認</a></div>
                                        <div>
                                            <label><input name="delete[flash]" class="uk-checkbox" type="checkbox" value="1" @if(old("delete.flash")) checked @endif> 削除する</label>
                                        </div>
                                    @endif
                                </div>
                                @error('file.flash')
                                    <div class="uk-text-danger uk-text-uppercase">
                                        <p>{{ $message }}</p>
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <div class="uk-margin">
                            <label class="uk-form-label uk-text-left" for="poster">
                                @if($form['category_prefix'] == "reikai")
                                    配布資料3
                                @elseif($form['category_prefix'] == "kosyukai")
                                    配布資料3
                                @elseif($event_id == 238 && $form['category_prefix'] == "touronkai")
                                    {{--第28回高分子分析討論会のみ対応--}}
                                    配布資料
                                @else
                                    ポスター・配布資料等
                                @endif

                            </label>
                            <div class="uk-form-controls">
                                <div class="uk-grid-small uk-flex-middle" uk-grid>
                                    <div uk-form-custom="target: true">
                                        <input type="file" name="file[poster]">
                                        <input class="uk-input" type="text" placeholder="ファイルを選択" disabled>
                                    </div>
                                    @if (@$presentation->poster && $presentation->number)
                                        <div><a href="{{ route('presentation.get.file', [@$presentation->number, 'poster',$presentation->id]) }}" target="_blank">登録ファイル確認</a></div>
                                        <div>
                                            <label><input name="delete[poster]" class="uk-checkbox" type="checkbox" value="1" @if(old("delete.poster")) checked @endif> 削除する</label>
                                        </div>
                                    @endif
                                </div>
                                @error('file.poster')
                                    <div class="uk-text-danger uk-text-uppercase">
                                        <p>{{ $message }}</p>
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <div class="uk-section-small">
                            <button type="submit" class="uk-button uk-button-primary">更新</button>
                        </div>

                    </fieldset>
                </form>
            </div>
        </div>
    </div>

@endsection
