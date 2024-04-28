@extends('layouts.admin')

@section('title', $title)

@section('breadcrumb')
    @parent
    <li><span>{{ $title }}</span></li>
@endsection

@section('content')
    <div class="uk-section-small">
        <div class="uk-container uk-container-large">
            <form action="{{ route('admin.pages.inquire') }}" method="POST" >
                @csrf
                <label>メールアドレス</label>
                <input type="text" name="email" value="" class="uk-input" required />
                <br />
                <br />
                <label>問合せ先名</label>
                <input type="text" name="name" value="" class="uk-input" required />
                <div class="uk-padding-small">
                    <input type="submit" name="regist" value="登録" class="uk-button uk-button-primary" />
                </div>
            </form>
            <table class="uk-table">
                <thead>
                    <tr>
                        <th>メールアドレス</th>
                        <th>問合せ先名</th>
                        <th>&nbsp;</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($inquireSetting as $key=>$value)
                        <tr>
                            <td>{{ $value[ 'email' ] }}</td>
                            <td>{{ $value[ 'name' ] }}</td>
                            <td><a href="{{ route('admin.pages.inquire.delete', $value->id) }}" class="uk-button uk-button-danger" >削除</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>


        </div>
    </div>
@endsection

@section('footer')
    <script src="{{ asset('js/admin/pages.js') }}"></script>
@endsection
