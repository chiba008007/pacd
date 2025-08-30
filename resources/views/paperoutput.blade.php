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

        .area {
            width: 90%;
            margin: 5% auto;
        }

        body {
            font-family: ipag;
        }
        body, html {
            margin: 0;
            padding: 0;
        }
        .mt20 {
            margin-top: 20px;
        }

        .mt30 {
            margin-top: 30px;
        }

        .mt60 {
            margin-top: 60px;
        }

        .mt100 {
            margin-top: 100px;
        }

        .f18 {
            font-size: 18px;
        }

        .f22 {
            font-size: 22px;
        }

        .f30 {
            font-size: 30px;
        }

        .text-center {
            text-align: center;
        }

        .card {
            width: 300px;
            height: 270px;
            border: 1px solid #000;
            margin: 0px auto;
            padding: 0px 0px;
            position: absolute;
        }

        .bottom {
            position: absolute;
            top: 270px;
            left: auto;
            right: 0;
        }
        .bottom2 {
            position: absolute;
            bottom: 0;
            width:300px;
            left: 0px;
            right: 0;
            text-align:left;
        }
    </style>
</head>

<body>
    <div class="area text-center">
        <div class="title f30">{{$event->name}}</div>
        <div class="mt20 f22">
            参加者番号 :
            {{$event_number}}
        </div>
        @if (!($user->type == 1 || $user->type == 4))
        <div class="mt20 f22">
            {{$user->cp_name}}
        </div>
        @endif
        <div class="mt20">
            {{$user->sei}}
            {{$user->mei}}
        </div>
        <div class="mt20">
            {{$user->sei_kana}}
            {{$user->mei_kana}}
        </div>
        <div class="mt30">
            <img src="https://api.qrserver.com/v1/create-qr-code/?data={{$url}}" alt="QRコード" width=250 />
            <p>
                参加初日に受付へご提出ください
            </p>
        </div>

        <div style="page-break-after: always"></div>
        <div class="mt20">
            <div class="card text-center" style="margin-left:0px;margin-top:30px;">
                <div class="mt20 f18">
                    {{$event->name}}
                </div>

                <div class="mt20 f18">
                    @if (!($user->type == 1 || $user->type == 4))
                        {{$user->cp_name}}<br />
                    @endif
                    @if (!$join)
                        {{$user->busyo}}
                    @endif
                </div>

                <div class="mt20 f18">
                    @if($join == 1)
                        {{$attendee->tenjiSanka1Name}}
                    @elseif($join == 2)
                        {{$attendee->tenjiSanka2Name}}
                    @else
                        {{$user->sei}}
                        {{$user->mei}}
                    @endif

                </div>
                <div class="bottom">{{$event_number}}</div>
                <div class="bottom2">
                    当日の名札として使用しますので印刷して<br />お持ちください。
                </div>
            </div>
        </div>
    </div>
</body>

</html>
