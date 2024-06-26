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
}
.area{
    width:90%;
    margin:0 auto;
    margin-top:20px;
}
.title{
    text-align:center;
    font-size:1.5em;
}
.name{
    font-size:1.5em;
    margin-top:3%;
    border-bottom:2px double #000;
    width:70%;
    padding:3% 0 0 3%;
}
.info{
    text-align:right;
    background-image:url("./storage/hanko.gif");
    background-repeat:no-repeat;
    background-position: right middle;
    padding-right:130px;
}
.price{
    font-size:2.0em;
    border-bottom:2px double #000;
    text-align:center;
    margin-top:3%;
}
.price_sub{
    text-align:center;
    margin-top:2%;
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
    font-size:12px;
    padding:2px;
}
.rights{
    border-right:0px;
}
.textleft{
    text-align:left;
}
.textright{
    text-align:right;
}
.f16{font-size:16px;}
.pd10{padding:10px;}
.right{text-align:right;}
.table2{
    width:80%;
    display:table;
    margin-top:1%;
}
.cell2{
    display:table-cell;
}
.ml{
    margin-left:30px;
}

</style>
</head>
<body>
    @for($i=0;$i<$outputtype;$i++)
    <div class="area" >
        <div class="title" >請　求　書</div>
        <div class="right" >{{$date}}</div>
        <div class="name" >
        @if($usertype == 2 || $usertype == 3  )
            <p>{{$cp_name}} </p>
        @endif
        @if( $usertype == 4 || $usertype == 1)
            <p>{{$busyo}} </p>
        @endif
        @if( $usertype == 5 || $usertype == 6)
        <p>{{$cp_name}}</p>
        @endif
        {{$name}} 様

        </div>
        @if( $usertype == 5 || $usertype == 6)
            <div>&nbsp;</div>
        @else
            <div>参加者番号 ({{$event_number}})</div>
        @endif
        <div class="info">
            {!! nl2br($invoice_address) !!}
        </div>
        <div class="price">￥
            @if($i == 1)
            {{$price2}}
            @else
            {{$price}}
            @endif
            @if($category_type ==  4 || $category_type ==  3)
            <small class="f16">(税込請求金額)</small>
            @else
            <small class="f16">(税込請求金額)</small>
            @endif
        </div>
        <div class="price_sub">下記の通り請求申し上げます。</div>

        <div class="table">
            <div class="row">
                <div class="cell pd10 "><span class="ml">内訳</span></div>
                <div class="cell pd10">単価</div>
                <div class="cell pd10">数量</div>
                <div class="cell pd10">税込金額</div>
            </div>
            @if($i == 1)
                @foreach($event_join2 as $key=>$value)
                    <div class="row">
                        <div class="cell textleft">{{$value->join_name}}_(消費税10%対象)</div>
                        <div class="cell textright">&yen;{{number_format($value->join_price)}}</div>
                        <div class="cell">1</div>
                        <div class="cell textright">&yen;{{number_format($value->join_price)}}</div>
                    </div>
                @endforeach     
            @else
                @foreach($event_join as $key=>$value)
                    <div class="row">
                        <div class="cell textleft">{{$value->join_name}}_(消費税10%対象)</div>
                        <div class="cell textright">&yen;{{number_format($value->join_price)}}</div>
                        <div class="cell">1</div>
                        <div class="cell textright">&yen;{{number_format($value->join_price)}}</div>
                    </div>
                @endforeach
            @endif
            <div class="row">
                <div class="cell ">税込金額合計</div>
                <div class="cell textright"></div>
                @if($i == 1)
                    <div class="cell">{{count($event_join2)}}</div>
                    <div class="cell textright">&yen;{{$price2}}</div>
                @else
                    <div class="cell">{{count($event_join)}}</div>
                    <div class="cell textright">&yen;{{$price}}</div>
                @endif
            </div>
        </div>
        <br />
        <br />
        <div class="table" style="width:400px;">
            <div class="row">
                <div class="cell ">税抜金額合計</div>
                <div class="cell">消費税額(10%)</div>
                <div class="cell">合計金額</div>
            </div>
            <div class="row">
                @if($i == 1)
                    <div class="cell">&yen;{{$priceNoTax2}}</div>
                    <div class="cell">&yen;{{$priceTax2}}</div>
                    <div class="cell">&yen;{{$price2}}</div>
                @else
                    <div class="cell">&yen;{{$priceNoTax}}</div>
                    <div class="cell">&yen;{{$priceTax}}</div>
                    <div class="cell">&yen;{{$price}}</div>
                @endif
            </div>
        </div>
        <div class="table2">
            <div class="row">
                <div class="cell2">振込先：</div>
                <div class="cell2">{{$bank_name}}</div>
            </div>
            <div class="row">
                <div class="cell2">口座名：</div>
                <div class="cell2">
                {{$bank_code}}
                </div>
            </div>
        </div>
        {!! nl2br($invoice_memo) !!}
    </div>
    @if($i == 0 && $outputtype == 2)
    <div style="page-break-after: always;"></div>
    @endif
    @endfor
</body>
</html>
