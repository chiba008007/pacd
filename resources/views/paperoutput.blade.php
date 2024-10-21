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
            width: 91mm;
            height: 55mm;
            border: 1px solid #000;
            margin: 0 auto;
            padding: 20px 10px;
            position: relative;
        }

        .bottom {
            position: absolute;
            bottom: 0;
            left: auto;
            right: 0;
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
        <div class="mt20 f22">
            {{$user->cp_name}}
        </div>
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
        </div>

        <div style="page-break-after: always"></div>
        <div class="mt20">
            <p style="text-align:left;display:block;width:360px;margin:0 auto;">
                当日は名札入れをお配りしますので、<br>
                こちらの枠線で切り取って名札としてお使いください。
            </p>
            <div class="card text-center" style="margin-top:30px;">
                <div class="mt20 f18">
                    {{$event->name}}
                </div>
                <div class="mt20 f18">
                    {{$user->cp_name}}<br />
                    {{$user->busyo}}
                </div>
                <div class="mt20 f18">
                    {{$user->sei}}
                    {{$user->mei}}

                </div>
                <div class="bottom">{{$event_number}}</div>
            </div>
        </div>
    </div>
</body>

</html>