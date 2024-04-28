<!doctype html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<title></title>
<style>
@font-face{
    font-family: ipag;
    font-style: normal;
    font-weight: normal;
    src:url('{{ storage_path('fonts/IPAfont00303/IPAfont00303/ipag.ttf')}}');
}
body {
    font-family: ipag;
    font-size:14px;
}
.area{
    width:90%;
    margin:0 auto;
    margin-top:10px;
}
.title{
    text-align:center;
    font-size:1.5em;
}
p{
    padding:0 !important;
    margin:0 !important;
}
.name{
    font-size:1.5em;
    margin-top:20px;
    border-bottom:2px double #000;
    width:70%;
    padding:1.5% 0 0 1.5%;
}
.info{
    text-align:right;
    padding-top:5%;
    background-image:url("./storage/hanko.gif");
    background-repeat:no-repeat;
    background-position: right top;
}
.price{
    font-size:2.0em;
    border-bottom:2px double #000;
    text-align:center;
    margin-top:1.5%;
}
.price_sub{
    text-align:center;
    margin-top:20px;
    margin-bottom:10px;
}
.table{
    width:100%;
    border-top:1px solid #000;
    border-left:1px solid #000;
    display:table;
}
.row{
    display:table-row;
}
.cell{
    display:table-cell;
    text-align:center;
    border-right:1px solid #000;
    border-bottom:1px solid #000;
}
.f16{font-size:16px;}
.pd10{padding:10px;}
.right{text-align:right;}
.table2{
    width:80%;
    display:table;
    margin-top:5%;
}
.cell2{
    display:table-cell;
}


</style>
</head>
<body>
    <div class="area">
        <div class="title" >領　収　書</div>

        <div class="right" >{{$date}}</div>
        <div class="name" >
        @if($usertype == 2 || $usertype == 3 )
            <p>{{$cp_name}} </p>
        @endif
        @if($usertype == 4 || $usertype == 1)
            <p>{{$busyo}} </p>
        @endif
        {{$name}} 様
        </div>
        <div>参加者番号 ({{$event_number}})</div>

        <div class="price">￥{{$price}}
            @if($category_type ==  4 || $category_type ==  3)
            <small class="f16">(課税対象)</small>
            @else
            <small class="f16">(課税対象外)</small>
            @endif
        </div>
        <div class="price_sub">但し、下記の内訳の通り領収致しました。</div>

        <div class="table">
            <div class="row">
                <div class="cell pd10">内訳</div>
                <div class="cell pd10">単価</div>
                <div class="cell pd10">数量</div>
                <div class="cell pd10">金額</div>
            </div>
            @foreach($event_join as $key=>$value)
                <div class="row">
                    <div class="cell">{{$value->join_name}}</div>
                    <div class="cell"></div>
                    <div class="cell">1</div>
                    <div class="cell">￥{{number_format($value->join_price)}}</div>
                </div>
            @endforeach

            <div class="row">
                <div class="cell">&nbsp;</div>
                <div class="cell">&nbsp;</div>
                <div class="cell">&nbsp;</div>
                <div class="cell">以下余白</div>
            </div>
            <div class="row">
                <div class="cell">&nbsp;</div>
                <div class="cell">&nbsp;</div>
                <div class="cell">&nbsp;</div>
                <div class="cell">&nbsp;</div>
            </div>
            <div class="row">
                <div class="cell">合計</div>
                <div class="cell"></div>
                <div class="cell">{{count($event_join)}}</div>
                <div class="cell">￥{{$price}}</div>
            </div>

        </div>

        <div class="info">
            {!! nl2br($recipe_memo) !!}
        </div>
    </div>
</body>
</html>
