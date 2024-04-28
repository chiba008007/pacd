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
            <img src="https://chart.apis.google.com/chart?cht=qr&chs=250x250&chl={{$url}}" alt="QRコード" />
        </div>
        <div class="mt30">
            参加費：{{$ispaid}}
        </div>
        <!-- 第28回高分子分析討論会でのみ表示 -->
        @if($event->id == 238 && $attendee->event_join_id_list == 2375)
        懇親会
        @endif
        <div style="page-break-after: always"></div>
        <div class="mt20">
            <p>
                当日は名札入れをお配りしますので、<br>
                御名刺を入れていただくか、<br>
                こちらの枠線で切り取ってお使いください。
            </p>
            <div class="card text-center">
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
                    <!-- 第28回高分子分析討論会でのみ表示 -->
                    @if($event->id == 238 && $attendee->event_join_id_list == 2375)
                    <div style="text-align:right;">懇親会</div>
                    @endif
                </div>
                <div class="bottom">{{$event_number}}</div>
            </div>
        </div>
    </div>
</body>

</html>