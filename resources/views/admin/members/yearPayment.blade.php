@extends('layouts.admin')

@section('title', $title = '年会費(請求書・領収書)情報編集')

@section('breadcrumb')
    @parent
    <li><a href="{{ route('admin.members.index') }}">会員管理</a></li>
    <li><span>{{ $title }}</span></li>
@endsection

@section('content')
    <div class="uk-section-small">
        <div class="uk-container uk-container-large">
            <?php if ($message): ?>
            <div class="uk-alert-primary" uk-alert>
                <a class="uk-alert-close" uk-close></a>
                <p>{{ $message }}</p>
            </div>
            <?php endif; ?>
            <div >

                <form name="form1" action="{{ route('admin.members.yearPaymentRegist') }}" method="post" class="uk-inline"  >
                    @csrf
                    <div class="uk-grid">
                        <div class="uk-width-1-4">
                            <h4>年度選択</h4>
                        </div>
                        <div class="uk-width-3-4">
                            <select name="year" class="uk-select" id="selectyear" onChange="yearChange(this.value)">
                            @for($year = date('Y');$year<=date('Y')+3;$year++)
                                <?php $sel = "" ?>
                                @if($year == $selectyear)
                                <?php $sel = "selected"; ?>
                                @endif
                                <option value="{{$year}}" {{$sel}} >{{$year}}年</option>
                            @endfor
                            </select>
                        </div>

                        <div class="uk-width-1-4 uk-margin-top">
                            <h4>請求書情報</h4>
                        </div>
                        <div class="uk-width-3-4 uk-margin-top">
                            請求書情報
                            <textarea class="uk-textarea" name="invoice_address" rows=7>{{old('invoice_address',$invoice_address)}}</textarea>
                            <br />
                            振込先
                            <textarea class="uk-textarea" name="bank_name" rows=2>{{old('bank_name',$bank_name)}}</textarea>
                            <br />
                            口座名
                            <textarea class="uk-textarea" name="bank_code" rows=2>{{old('bank_code',$bank_code)}}</textarea>
                            <br />
                            コメント
                            <textarea class="uk-textarea" name="invoice_memo" rows=7>{{old('invoice_memo',$invoice_memo)}}</textarea>
                        </div>
                        <div class="uk-width-1-4 uk-margin-top">
                            <h4>領収書情報</h4>
                        </div>
                        <div class="uk-width-3-4 uk-margin-top">
                            コメント
                            <textarea class="uk-textarea" name="recipe_memo" rows=7>{{old('recipe_memo',$recipe_memo)}}</textarea>
                        </div>
                    </div>
                    <div class="uk-margin-large-top">
                        <button class="uk-button uk-button-default" type="submit" name="upload" value="on" tabindex="-1">更新</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
@endsection

<script type="text/javascript">
<!--
    function yearChange(value){
        document.location = "?year="+value;
    }
//-->
</script>
