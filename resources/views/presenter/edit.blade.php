@extends('layouts.app')

@section('title', $title)

@section('content')
    <div id="page">
        <div class="header">
            <h2 class="edit-content" data-column="title">{{ $title }}</h2>
        </div>
        <div class="uk-container">
            @if (session('status'))
                <div class="uk-alert-success" uk-alert>
                    <a class="uk-alert-close" uk-close></a>
                    <p>{{ session('status') }}</p>
                </div>
            @endif

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
                            <div class="uk-form-controls uk-form-controls-text">{{ $user->login_id }}</div>
                        </div>
                        <div>
                            <div class="uk-form-label">氏名</div>
                            <div class="uk-form-controls uk-form-controls-text">{{ $user->sei }}　{{ $user->mei }}</div>
                        </div>
                        <div>
                            <div class="uk-form-label">氏名（ふりがな）</div>
                            <div class="uk-form-controls uk-form-controls-text">{{ $user->sei_kana }}　{{ $user->mei_kana }}</div>
                        </div>
                        <div>
                            <div class="uk-form-label">メールアドレス</div>
                            <div class="uk-form-controls uk-form-controls-text">{{ $user->email }}</div>
                        </div>
                    </fieldset>
                </div>
            </div>

            <div class="uk-section-xsmall">
                <form method="POST" action="{{ route( $form['prefix'] . ".update", [$presenter->id]) }}" class="uk-form-horizontal" id="presenter_update_form">
                    @csrf
                    @method('put')
                    <fieldset class="uk-fieldset">
                        @if($form['category_prefix'] == "touronkai")
                            <div>
                                <div class="uk-form-label">題目</div>
                                <div class="uk-form-controls uk-form-controls-text">
                                    <textarea name="daimoku" class="uk-textarea" rows="3">{{ $presentation->daimoku }}</textarea>
                                </div>
                            </div>
                            <div>
                                <div class="uk-form-label">発表演者</div>
                                <div class="uk-form-controls uk-form-controls-text">
                                    <textarea name="enjya" class="uk-textarea" rows="3" placeholder="記入例）〇山田 一郎,　鈴木次郎　（発表者に〇印　区切りはカンマ「,」" >{{ $presentation->enjya }}</textarea>
                                </div>
                            </div>
                            <div>
                                <div class="uk-form-label">所属</div>
                                <div class="uk-form-controls uk-form-controls-text">
                                    <textarea name="syozoku" class="uk-textarea2" rows="3" placeholder="記入例）□□大,　△△大院工　等 　略称で記入
法人格は株式会社・有限会社・財団法人・社団法人・独立行政法人・国立大学法人等は省略
（区切りはカンマ「,」　発表者所属に○印）" >{{ $presentation->syozoku }}</textarea>
                                </div>
                            </div>
                            <div>
                                <div class="uk-form-label">発表概要</div>
                                <div class="uk-form-controls uk-form-controls-text">
                                    <textarea name="gaiyo" class="uk-textarea" rows="3">{{ $presentation->gaiyo }}</textarea>
                                </div>
                            </div>
                        @else
                            <div>
                                <div class="uk-form-label">講演内容</div>
                                <div class="uk-form-controls uk-form-controls-text">
                                    <textarea class="uk-textarea" name="description" rows="3" >{{ $presentation->description }}</textarea>
                                </div>
                            </div>
                        @endif
                        {{-- カスタムインプット項目 --}}
                        @if($inputs->count())
                            @include('elements.forms.custom_inputs', [$inputs, 'custom_data' => $presenter->custom_form_data->keyBy('form_input_value_id')])
                        @endif

                        <div class="uk-section-small">
                            <a href="{{ route('mypage.' .$form['category_prefix']) }}" class="uk-button uk-button-secondary">戻る</a>
                            <button type="submit" class="uk-button uk-button-primary bg-green">変更</button>
                        </div>
                    </fieldset>
                </form>
            </div>

        </div>
    </div>
@endsection
