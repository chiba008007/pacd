@extends('layouts.admin')

@section('title', $title)

@section('breadcrumb')
    @parent
    <li><span>{{ $title }}</span></li>
@endsection

@section('content')
    <div class="uk-section-small">

        <form method="POST" action="{{ route('admin.pages.banner.post') }}" class="uk-form-horizontal" enctype="multipart/form-data"  class="uk-form " uk-grid>
            @csrf
            <fieldset class="uk-fieldset">

                <div class="uk-card uk-card-body">
                    @if (session('flash_message'))
                        <div class="uk-alert-success" uk-alert>
                            {{ session('flash_message') }}
                        </div>
                    @endif
                    <div>
                        <h5>バナー画像アップロード</h5>

                        @if(isset($select->id) && $select->id)
                        <input type="file" name="bannar">
                        <input type="hidden" name="id" value="{{$select->id}}" />
                        <p>▽登録画像</p>
                        <img src="{{ asset('storage/bannar/'.$select->filename) }}" />
                        @else
                        <input type="file" name="bannar" required>
                        @endif

                        <h5>表示期間</h5>
                        <div class="uk-grid">
                            <div class="uk-width-1-2">
                                <input type="date" name="startdate" data-uk-datepicker="{format:'DD.MM.YYYY'}" class="uk-input" value="{{@$select->startdate}}">
                            </div>
                            <div class="uk-width-1-2">
                                <input type="date" name="enddate" data-uk-datepicker="{format:'DD.MM.YYYY'}" class="uk-input" value="{{@$select->enddate}}">
                            </div>
                        </div>
                        <h5>リンク先URL</h5>
                        <div class="uk-grid">
                            <div class="uk-width-1-1">
                                <input type="text" name="url" data-uk-datepicker="{format:'DD.MM.YYYY'}" class="uk-input" value="{{@$select->url}}" >
                            </div>
                        </div>
                        <br />
                        <input type="submit"  class="uk-button uk-button-primary" value="登録">
                    </div>

                </div>
            </fieldset>
        </form>
    </div>
@endsection

@section('footer')
    <script src="{{ asset('js/admin/pages.js') }}"></script>
@endsection
