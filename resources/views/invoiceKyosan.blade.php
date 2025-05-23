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
    margin-top:1.2%;
    border-bottom:2px double #000;
    width:70%;
    padding:2% 0 0 2%;
}
.info{
    text-align:right;
    padding-top:5%;
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
    margin-top:5%;
}
.cell2{
    display:table-cell;
}


</style>
</head>
<body>
    <div class="area">
        <div class="title" >請　求　書</div>
        <div class="right" >{{$date}}</div>
        <div class="name" >{{$cp_name}} 様</div>
        <div>参加者番号 ({{$event_number}})</div>
        <div class="info">
            {!! nl2br($invoice_address) !!}
        </div>
        

        <div class="table2">
            <div class="row">
                <div class="cell2">振込先：</div>
                <div class="cell2"> {!! nl2br($bank_name) !!} </div>
            </div>
            <div class="row">
                <div class="cell2">口座名：</div>
                <div class="cell2">{!! nl2br($bank_code) !!}</div>
            </div>
        </div>
        <br />

        <div class="price">￥
            {{$price}}
            <small class="f16">(税込請求金額)</small>
        </div>

        <div class="price_sub">下記の通り請求申し上げます。</div>

        <div class="table">
            <div class="row">
                <div class="cell pd10 "><span class="ml">内訳</span></div>
                <div class="cell pd10">単価</div>
                <div class="cell pd10">数量</div>
                <div class="cell pd10">税込金額</div>
            </div>

            <div class="row">
                <div class="cell textleft">{{$string}}_(消費税10%対象)</div>
                <div class="cell textright">&yen;{{$price}}</div>
                <div class="cell">1</div>
                <div class="cell textright">&yen;{{$price}}</div>
            </div>

            <div class="row">
                <div class="cell ">税込金額合計</div>
                <div class="cell textright"></div>

                <div class="cell">1</div>
                <div class="cell textright">&yen;{{$price}}</div>
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
                <div class="cell">&yen;{{$priceNoTax}}</div>
                <div class="cell">&yen;{{$priceTax}}</div>
                <div class="cell">&yen;{{$price}}</div>
            </div>
        </div>
        <br />
        <br />
        {!! nl2br($invoice_memo) !!}


    </div>
</body>
</html>
