<?php
$conf_reikai = "例会＆講演会";
$conf_touronkai = "高分子分析討論会";
$conf_kosyukai = "高分子分析技術講習会";
$conf_kyosan = "企業協賛";
return [
    'outputtype'=>[
        1=>"合算",
        2=>"分割",
    ],
    'pattern'=>[
        1=>"講演・講習",
        2=>"懇親会",
    ],
    'honor'=>[
        "0"=>"",
        "1"=>"先生",
        "2"=>"様",
        "3"=>"氏",
    ],
    'bank'=>[
        'name'=>"りそな銀行五反田支店（普通）0804667",
        'code'=>"（公社）日本分析化学会 高分子分析研究懇談会
ｼﾔ)ﾆﾎﾝﾌﾞﾝｾｷｶｶﾞｸｶｲ ｺｳﾌﾞﾝｼﾌﾞﾝｾｷｹﾝｷﾕｳｺﾝﾀﾞﾝｶｲ
        ",
        'invoice_address'=>"公益社団法人 日本分析化学会
高分子分析研究懇談会
運営委員長 本多 貴之
〒141-0031東京都品川区西五反田1-26-2
五反田サンハイツ304号
E-mail ： infopacd@pacd.jp",
        'invoice_memo'=>"お願い：
・振込手数料はご負担ください。
・振込名は、「参加者番号下4桁」+「会員名」でお願い致します。
「受付番号を入力できない場合や、複数人分を一度に振込む場合は、
入金日をinfopacd@pacd.jpまでお知らせください。",
        'recipe_memo'=>"公益社団法人 日本分析化学会
高分子分析研究懇談会
運営委員長 本多 貴之
〒141-0031東京都品川区西五反田1-26-2
五反田サンハイツ304号
E-mail ： infopacd@pacd.jp",

    ],
    'user' => [
        'type' => [
            1 => '非会員',
            2 => '法人会員(窓口担当者)',
            3 => '法人会員',
            4 => '個人会員',
            5 => '協賛企業（窓口担当者）',
            6 => '協賛企業枠参加者',
        ],
        'group_flag'=>[
            1=>"所属",
            0=>"未所属"
        ],
        //年会費
        'yearPrice' => [
            1 => 0,
            2 => 30000,
            3 => 0,
            4 => 2000,
            5 => 0,
            6 => 0,
        ],
    ],
    //支払い済み
    'payment'=>[
        '1'=>"支払済み",
        '0'=>"未払い"
    ],
    //支払い済み
    'recipe_status'=>[
        '1'=>"発行済",
        '0'=>"発行"
    ],
    //受付済み
    'join_status'=>[
        '1'=>"受付済",
        '0'=>"受付前"
    ],
    //フォームのキー
    "categorykey"=>[
        1=>[
            "prefix"=>"members",
            "eventlinks"=>"members",
            "name"=>"会員",
            "payments"=>"年会費"
        ],
        2=>[
            "prefix"=>"reikai",
            "eventlinks"=>"reikai",
            "name"=>"例会＆講演会",
            "payments"=>"例会＆講演会"
        ],
        3=>[
            "prefix"=>"touronkai",
            "eventlinks"=>"touronkai",
            "name"=>"高分子分析討論会",
            "payments"=>"高分子分析討論会"
        ],
        4=>[
            "prefix"=>"kosyukai",
            "eventlinks"=>"koshikai",
            "name"=>"高分子分析技術講習会",
            "payments"=>"高分子分析技術講習会"
        ],
        5=>[
            "prefix"=>"kyosan",
            "eventlinks"=>"kyosan",
            "name"=>"企業協賛",
            "payments"=>"企業協賛"
        ],

    ],
    'category' => [
        'members' => [
            'key' => 1,
            'prefix' => 'members',
            'name' => '会員',
        ],
        'reikai' => [
            'key' => 2,
            'prefix' => 'reikai',
            'name' => '例会＆講演会',
        ],
        'touronkai' => [
            'key' => 3,
            'prefix' => 'touronkai',
            'name' => '高分子分析討論会',
        ],
        'kosyukai' => [
            'key' => 4,
            'prefix' => 'kosyukai',
            'name' => '高分子分析技術講習会',
        ],
        'mailform' => [
            'key' => 999, //多分キーは使わないのでなんでもよい
            'prefix' => 'mailform',
            'name' => 'メールフォーム編集',
            'form_route_name' => 'mailform.index',
            'form_display_name' => 'メールフォーム編集',
        ],
        'kyosan' => [
            'key' => 5,
            'prefix' => 'kyosan',
            'name' => '企業協賛',
        ]
    ],
    //会場
    'space'=>[
        1=>[
            "key"=>1,
            "name"=>"A会場",
            'type'=>'typeA'
        ],
        2=>[
            "key"=>2,
            "name"=>"B会場",
            "type"=>"typeB"
        ]
    ],
    //午前午後
    'ampm'=>[
        1=>"午前",
        2=>"午後"
    ],

    // フォーム
    'form' => [
        'type' => [
            'register' => [
                'key' => 1,
                'prefix' => 'register',
                'category_prefix' => 'members',
                'display_name' => '会員登録',
                'type' => 'general',
            ],
            'reikai_attendee' => [
                'key' => 2,
                'prefix' => 'reikai_attendee',
                'category_prefix' => 'reikai',
                'display_name' => '例会＆講演会 参加者',
                'type' => 'attendee',
            ],
            'touronkai_attendee' => [
                'key' => 3,
                'prefix' => 'touronkai_attendee',
                'category_prefix' => 'touronkai',
                'display_name' => '討論会 参加者',
                'type' => 'attendee',
            ],
            'kosyukai_attendee' => [
                'key' => 4,
                'prefix' => 'kosyukai_attendee',
                'category_prefix' => 'kosyukai',
                'display_name' => '技術講習会 参加者',
                'type' => 'attendee',
            ],
            'reikai_presenter' => [
                'key' => 5,
                'prefix' => 'reikai_presenter',
                'category_prefix' => 'reikai',
                'display_name' => '例会＆講演会 講演者',
                'type' => 'presenter',
            ],
            'touronkai_presenter' => [
                'key' => 6,
                'prefix' => 'touronkai_presenter',
                'category_prefix' => 'touronkai',
                'display_name' => '討論会 講演者',
                'type' => 'presenter',
            ],
            'kosyukai_presenter' => [
                'key' => 7,
                'prefix' => 'kosyukai_presenter',
                'category_prefix' => 'kosyukai',
                'display_name' => '技術講習会 講演者',
                'type' => 'presenter',
            ],
            'kyosan_attendee' => [
                'key' => 8,
                'prefix' => 'kyosan_attendee',
                'category_prefix' => 'kyosan',
                'display_name' => '協賛企業',
                'type' => 'attendee',
            ],
        ],
        'input_type' => [
            'text' => 1,
            'select' => 2,
            'check' => 3,
        ]
    ],
    //アップロードファイル
    "CONST_FILE_UPLOAD_NAME"=>[
        1=>"開催案内",
        2=>"報告",
        3=>"要旨"
    ],
    //メールフォームテンプレート用
    //connectはテンプレートと編集用フォームの紐づけ
    "CONST_MAIL_FORM_TEMP"=>[
        "reikai"=>[
            1=>[
                'key'=>1,
                'display'=>$conf_reikai."参加登録返信メール",
                'connect'=>'2'
            ],
            2=>[
                'key'=>2,
                'display'=>$conf_reikai."講演登録返信メール",
                'connect'=>'5'
            ],
            3=>[
                'key'=>3,
                'display'=>$conf_reikai."原稿登録返信メール",
                'connect'=>'0'
            ],
        ],
        "touronkai"=>[
            4=>[
                'key'=>4,
                'display'=>$conf_touronkai."参加登録返信メール",
                'connect'=>'3'
            ],
            5=>[
                'key'=>5,
                'display'=>$conf_touronkai."講演登録返信メール",
                'connect'=>'6'
            ],
            6=>[
                'key'=>6,
                'display'=>$conf_touronkai."原稿登録返信メール",
                'connect'=>'0'
            ],
        ],

        "kosyukai"=>[
            7=>[
                'key'=>7,
                'display'=>$conf_kosyukai."参加登録返信メール",
                'connect'=>'4'
            ],
            8=>[
                'key'=>8,
                'display'=>$conf_kosyukai."講演登録返信メール",
                'connect'=>'7'
            ],
            9=>[
                'key'=>9,
                'display'=>$conf_kosyukai."原稿登録返信メール",
                'connect'=>'0'
            ],
        ],
        "members"=>[
            10=>[
                'key'=>10,
                'display'=>"非会員登録メール"
            ],
            11=>[
                'key'=>11,
                'display'=>"法人会員(窓口担当者)登録メール"
            ],
            12=>[
                'key'=>12,
                'display'=>"法人会員登録メール"
            ],
            13=>[
                'key'=>13,
                'display'=>"個人会員登録メール"
            ],
            14=>[
                'key'=>14,
                'display'=>"協賛会員(窓口担当者)登録メール"
            ],
            15=>[
                'key'=>15,
                'display'=>"協賛企業枠参加者登録メール"
            ],
            16=>[
                'key'=>16,
                'display'=>"一括会員登録メール"
            ],
        ],
        "kyosan"=>[
            17=>[
                'key'=>17,
                'display'=>$conf_kyosan."申込返信メール",
                'connect'=>'8'
            ],
        ],
    ],
    //メールフォーム置き換え
    "CONST_MAIL_REPLACE"=>[
        "member"=>[
            "login_id"=>[
                "jp"=>"ログインID",
                "replace"=>"##base1##",
            ],
            "sei"=>[
                "jp"=>"姓",
                "replace"=>"##base2##",
            ],
            "mei"=>[
                "jp"=>"名",
                "replace"=>"##base3##",
            ],
            "sei_kana"=>[
                "jp"=>"姓(ふりがな)",
                "replace"=>"##base4##"
            ],
            "mei_kana"=>[
                "jp"=>"名(ふりがな)",
                "replace"=>"##base5##",
            ],
            "email"=>[
                "jp"=>"メールアドレス",
                "replace"=>"##base6##",
                "status"=>1
            ],
            "remarks"=>[
                "jp"=>"備考",
                "replace"=>"##base7##",
            ],
            "tel"=>[
                "jp"=>"電話番号",
                "replace"=>"##base8##",
            ],
            "address"=>[
                "jp"=>"住所",
                "replace"=>"##base9##",
            ],
            "kyousan"=>[
                "jp"=>"協賛学会所属者の有無",
                "replace"=>"##base10##",
            ],
            "kyousan"=>[
                "jp"=>"協賛学会所属者の学会名",
                "replace"=>"##base10##",
            ],
            "type_number"=>[
                "jp"=>"法人会員番号",
                "replace"=>"##base11##",
            ],
            "cp_name"=>[
                "jp"=>"法人名",
                "replace"=>"##base12##",
            ],
            "busyo"=>[
                "jp"=>"所属",
                "replace"=>"##base13##",
            ],
            "bumon"=>[
                "jp"=>"部門",
                "replace"=>"##base14##",
            ],

            "passwords"=>[
                "jp"=>"パスワード",
                "replace"=>"##password##",
            ],
            "event_number"=>[
                "jp"=>"参加者番号",
                "replace"=>"##event_number##",
            ],
            "paydate"=>[
                "jp"=>"振込予定日",
                "replace"=>"##paydate##",
            ],
        ],
        "upload"=>[
            "number"=>[
                "jp"=>"発表番号",
                "replace"=>"##upload1##"
            ],
            "description"=>[
                "jp"=>"講演内容",
                "replace"=>"##upload2##"
            ],
        ],
        "join"=>[
            "joinname"=>[
                "jp"=>"参加型",
                "replace"=>"##joinname##",
            ],
            "joinprice"=>[
                "jp"=>"参加費",
                "replace"=>"##joinprice##",
            ],
            "joinfee"=>[
                "jp"=>"懇談会費",
                "replace"=>"##joinfee##",

            ],
        ]
    ],
    //form_inputsフォームタイプのキー
    "CONST_FORM_TYPE_KEY"=>[
        "members"=>[1],
        "reikai"=>[2,5],
        "touronkai"=>[3,6],
        "kosyukai"=>[4,7],
        "kyosan"=>[1],

    ],
    // 講演資料
    'presentation_file' => [
        'path' => 'private/presentation_files', // 保管場所
        'type' => [
            'proceeding',
            'flash',
            'poster',
        ]
    ],
    //メール配信対象者
    'mail_sender'=>[
        1=>"参加者",
        2=>"講演者",
        3=>"協賛企業",
        4=>"参加者のみ"
    ],
    //メール配信対象者
    'open_address_flag'=>[
        0=>"不可",
        1=>"可"
    ],
    'is_enabled_invoice'=>[
        0=>"ダウンロード不可",
        1=>"ダウンロード可"
    ]
];
