@extends('layouts.app')

@section('title', $title)

@section('content')
    <div id="page">
        <div class="header">
            <h2 class="edit-content" data-column="title">{{ $title }}</h2>
        </div>
        <div class="uk-container uk-section-small">

            @if (session('status'))
                <div class="uk-alert-success" uk-alert>
                    <a class="uk-alert-close" uk-close></a>
                    <p>{{ session('status') }}</p>
                </div>
            @endif

            <form action="{{ route($form['prefix'] . '.update.presentation', $presentation->id) }}" method="post" class="uk-form-horizontal" enctype="multipart/form-data">
                @csrf
                @method('put')
                <fieldset class="uk-fieldset">
                    <div class="uk-margin">
                        <label class="uk-form-label uk-text-left" for="description">
                            発表番号
                        </label>
                        <div class="uk-form-controls uk-form-controls-text">
                            {{ $presentation->number }}
                        </div>
                    </div>

                    <div class="uk-margin">
                        <label class="uk-form-label uk-text-left" for="description">
                            講演内容
                        </label>
                        <div class="uk-form-controls">
                            <textarea name="description" class="uk-textarea" rows="5">{{ old() ? old('description') : $presentation->description }}</textarea>
                            @error('description')
                                <div class="uk-text-danger uk-text-uppercase">
                                    <p>{{ $message }}</p>
                                </div>
                            @enderror
                        </div>
                    </div>

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
                                @if ($presentation->proceeding)
                                    <div><a href="{{ route('presentation.get.file', [$presentation->number, 'proceeding',$presentation->id]) }}" target="_blank">登録ファイル確認</a></div>
                                    <div>
                                        <label><input name="delete[proceeding]" class="uk-checkbox" type="checkbox" value="1" @if(old("proceeding_delete")) checked @endif> 削除する</label>
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
                            @elseif( $form[ 'category_prefix' ] == "touronkai" && $attendee->event_id == 238)
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
                                @if ($presentation->flash)
                                    <div><a href="{{ route('presentation.get.file', [$presentation->number, 'flash',$presentation->id]) }}" target="_blank">登録ファイル確認</a></div>
                                    <div>
                                        <label><input name="delete[flash]" class="uk-checkbox" type="checkbox" value="1" @if(old("flash_delete")) checked @endif> 削除する</label>
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
                            @elseif( $form[ 'category_prefix' ] == "touronkai" && $attendee->event_id == 238)
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
                                @if ($presentation->poster)
                                    <div><a href="{{ route('presentation.get.file', [$presentation->number, 'poster',$presentation->id]) }}" target="_blank">登録ファイル確認</a></div>
                                    <div>
                                        <label><input name="delete[poster]" class="uk-checkbox" type="checkbox" value="1" @if(old("poster_delete")) checked @endif> 削除する</label>
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
                        <a href="{{ route('mypage.' .$form['category_prefix']) }}" class="uk-button uk-button-secondary">戻る</a>
                        <button type="submit" class="uk-button uk-button-primary">変更</button>
                    </div>
                </fieldset>
            </form>
        </div>
    </div>
@endsection
