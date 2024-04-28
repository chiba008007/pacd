<!doctype html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title></title>
    <style>
        @font-face {
            font-family: ipag;
            font-style: normal;
            font-weight: normal;
            src:url('{{ storage_path(' fonts/IPAfont00303/IPAfont00303/ipag.ttf')}}');
        }

        body {
            font-family: ipag;
        }

        .area {
            width: 90%;
            margin: 0 auto;
            margin-top: 20px;
        }

        .title {
            text-align: center;
            font-size: 1.5em;
        }

        .name {
            font-size: 1.5em;
            margin-top: 1%;
            border-bottom: 2px double #000;
            width: 70%;
            padding: 1.5% 0 0 1.5%;
        }

        .info {
            width: 400px;
            float: right;
            text-align: right;
            padding-top: 0%;
            background-image: url("./storage/hanko.gif");
            background-repeat: no-repeat;

            background-position: right middle;
            padding-right: 130px;
        }

        .price {
            font-size: 2.0em;
            border-bottom: 2px double #000;
            text-align: center;
            margin-top: 4%;
        }

        .price_sub {
            text-align: center;
            margin-top: 2%;
        }

        .table {
            width: 600px;
            border-top: 1px solid #000;
            border-left: 1px solid #000;
            display: table;
        }

        .row {
            display: table-row;
        }

        .cell {
            display: table-cell;
            text-align: center;
            border-right: 1px solid #000;
            border-bottom: 1px solid #000;
            font-size: 12px;
            padding: 2px;
        }

        .rights {
            border-right: 0px;
        }

        .textleft {
            text-align: left;
        }

        .textright {
            text-align: right;
        }


        .f16 {
            font-size: 16px;
        }

        .pd10 {
            padding: 10px;
        }

        .right {
            text-align: right;
        }

        .table2 {
            width: 80%;
            display: table;
            margin-top: 5%;
        }

        .cell2 {
            display: table-cell;
        }

        .recipe {
            float: left;
            margin-top: 160px;
        }
    </style>
</head>

<body>
    <div class="area">
        <div class="title">領　収　書</div>

        <div class="right">{{$date}}</div>
        {{--法人会員（窓口担当者）の時、法人名　御中--}}
        @if($usertype == 2)
        <div class="name">{{$cp_name}} 御中</div>
        @else
        <div class="name">
            @if($usertype == 2 || $usertype == 3 )
            <p>{{$cp_name}} </p>
            @endif
            @if($usertype == 4 )
            <p>{{$busyo}} </p>
            @endif
            {{$name}} 様
        </div>
        @endif
        <div>({{$num}})</div>

        <div class="price">￥{{$price_recipe}}<small class="f16">(課税対象外請求金額)</small></div>
        <div class="price_sub">但し、
            @foreach($yearall_recipe as $key=>$value)
            {{$value->years}}
            @endforeach
            年度高分子分析研究懇談会年会費として領収致しました
        </div>
        <br clear=all />
        <div class="info">
            {!! nl2br($recipe_memo) !!}
        </div>
        <br clear=all />

        <div class="recipe">

            <div class="table">
                <div class="row">
                    <div class="cell pd10">内訳</div>
                    <div class="cell pd10">単価</div>
                    <div class="cell pd10">数量</div>
                    <div class="cell pd10">課税対象外金額</div>
                </div>
                @foreach($yearall as $key=>$value)
                <div class="row">
                    <div class="cell textleft">{{$value->years}}年度分高分子分析研究懇談会年会費(消費税0%)</div>
                    @if(isset($join) )
                    <div class="cell textright">{{$join}}円</div>
                    @else
                    <div class="cell textright">{{$pay}}円</div>
                    @endif
                    <div class="cell">1</div>
                    @if(isset($join) )
                    <div class="cell textright">{{$join}}円</div>
                    @else
                    <div class="cell textright">{{$pay}}円</div>
                    @endif
                </div>
                @endforeach

                <div class="row">
                    <div class="cell ">課税対象外金額合計</div>
                    <div class="cell"></div>
                    <div class="cell">@if(isset($fee) && $fee) 2 @else {{count($yearall)}} @endif</div>
                    <div class="cell textright">{{$price}}円</div>
                </div>

            </div>
            <br>
            <br>
            <div class="table" style="width:400px;">
                <div class="row">
                    <div class="cell ">課税対象外金額合計</div>
                    <div class="cell">消費税(0%)</div>
                    <div class="cell">合計金額</div>
                </div>
                <div class="row">
                    <div class="cell">&yen;{{$price}}</div>
                    <div class="cell ">&yen;0</div>
                    <div class="cell">&yen;{{$price}}</div>

                </div>

            </div>


        </div>

    </div>
</body>

</html>