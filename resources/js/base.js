$(function(){

    /******************
     * モーダル表示
     */
    $('.js-modal-open').on('click',function(){
        var _id = $(this).attr("id").split("-")[1];
        $('#modal-open-'+_id).fadeIn();
        return false;
    });
    $('.js-modal-close').on('click',function(){
        $('.js-modal').fadeOut();
        return false;
    });
    /******************
     * プログラム一覧表示切り替え
     */
    $(".type_downnload").hide();
    $("#typeA_downnload").show();
    $(".program-list").on("click",function(){
        $(".type_downnload").hide();
        $(".displayNone").hide();
        var _id = $(this).attr("href");
        $("#"+_id).show();
        if(_id == "typeA") $("#typeA_downnload").show();
        if(_id == "typeB") $("#typeB_downnload").show();

    });

    /************
     * 発表中エリアドラッグ
     */
    $(".drag").draggable({
        containment: 'body',
    });
    /*************
     * 日付タブクリック
     */
    $("a.link").on("click",function(){
        let _href = $(this).attr("href");
        location.href=_href;
        return false;
    });
    //会員区分を変更し表示フォームの変更を行う
    $(this).typeNumber();
    $("#type_number_select").on("change",function(){
        $(this).typeNumber();
    });
    $("[name='group_flag']").on("click",function(){
        var _disabled = $(this).val();
        $("[name='kyousan']").prop("disabled",true);
        if(_disabled == 1) $("[name='kyousan']").prop("disabled",false);
    });


    //支払い状況変更
    $(".payment_status").on("change",function(){
        $(this).changePaymentStatus();
    });
    //領収書再発行
    $("[name='recipe_status']").on("change",function(){
        $(this).changeInvoiceStatus();
    });



    //スクロールえりあ可変
    $(this).scrollarea();
    $(window).resize(function() {
      //リサイズされたときの処理
        $(this).scrollarea();
    });
    //領収書日付
    $("[name='recipe_date']").on("change",function(){
        $(this).changeRecipeDate();
    });
});

$.fn.scrollarea=function(){
    var _wh = $(window).height()/1.4;
    $("#scrollheight").height(_wh);

};

$.fn.changeRecipeDate = function(){
    var _recipe_date = $(this).val();
    var _id = $(this).attr("id").split("-")[1];
    var postData = {
        "recipe_date":_recipe_date,
        "id":_id
    };

    var _url = location.pathname+"/recipedateAjax";
    _url = _url.replace("//recipedateAjax","/recipedateAjax");

    $.ajax({
        type:'POST',
        url:_url,
        data: postData,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
     //   dataType: 'json',
    })
    .then(result => {
        console.log(result);

    })
    .catch(error => {
        console.log('データ更新エラー');
        console.log(error.status);
    });
    return true;
};

$.fn.changeInvoiceStatus = function(){
    var _recipe_status = $(this).val();
    var _id = $(this).attr("id").split("-")[1];
    var postData = {
        "recipe_status":_recipe_status,
        "id":_id
    };
    var _url = location.pathname+"/paymentupdate";
    _url = _url.replace("//paymentupdate","/paymentupdate");
    $.ajax({
        type:'POST',
        url:_url,
        data: postData,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
     //   dataType: 'json',
    })
    .then(result => {
        console.log(result);

    })
    .catch(error => {
        console.log('データ更新エラー');
        console.log(error.status);
    });
    return true;
};

$.fn.changePaymentStatus = function(){
    var _payment_status = $(this).val();
    var _id = $(this).attr("id").split("-")[1];
    var postData = {
        "payment_status":_payment_status,
        "id":_id
    };
  //  var _url = $(location).attr('pathname')+"/paymentupdate";
    var _url = location.pathname+"/paymentupdate";
    _url = _url.replace("//paymentupdate","/paymentupdate");

    $.ajax({
        type:'POST',
        url:_url,
        data: postData,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
     //   dataType: 'json',
    })
    .then(result => {
        console.log(result);

    })
    .catch(error => {
        console.log('データ更新エラー');
        console.log(error.status);
    });
    return true;
};

$.fn.typeNumber = function(){
    $(".hidden").hide();
    var _val = $("#type_number_select").val();
    $("#type_number").prop('disabled', false);
    $("#cp_name").prop('disabled', false);
    $("#houjinnumber").hide();
    $("#open_bumon_disp").hide();

    //法人会員（代表者）
    if(_val == 2){
        $("#type_number").prop('disabled', false);
        $("#cp_name").prop('disabled', false);
        $("#open_address_flag_disp").show();
        $("#type_number_disp").show();
        $("#cp_name_disp").show();
        $(".houjin").show();
        $("#tantobumon").show();
        $("#tanto_type_number").text("法人会員番号");
        $("#tantoname").text("担当者氏名");
        $("#tantonamekana").text("担当者氏名(ふりがな)");
        $("#tantomail").text("担当者メールアドレス");
        $("#tantobusyo").text("担当者所属部署");
        $("#tantotel").text("担当者電話番号");
        $("#tantoaddress").text("法人住所");
    }
    //法人会員
    if(_val == 3){
        $("#type_number").prop('disabled', false);
        $("#cp_name").prop('disabled', false);
        $("#houjinnumber").show();

        $("#type_number_disp").show();
        $("#cp_name_disp").show();
        $(".houjin").show();
        $("#tanto_type_number").text("法人会員番号");
        $("#tantoname").text("氏名");
        $("#tantonamekana").text("氏名(ふりがな)");
        $("#tantomail").text("メールアドレス");
        $("#tantobusyo").text("所属部署");
        $("#tantotel").text("電話番号");
        $("#tantoaddress").text("住所");
        $("#group_flag_disp").show();

    }
    //協賛企業
    if(_val == 5){
        $("#type_number").prop('disabled', true);
        $("#cp_name").prop('disabled', false);
        $("#cp_name_disp").show();
        $(".houjin").show();
        $("#tantoname").text("担当者氏名");
        $("#tantonamekana").text("担当者氏名(ふりがな)");
        $("#tantomail").text("担当者メールアドレス");
        $("#tantobusyo").text("担当者所属部署");
        $("#tantotel").text("担当者電話番号");
        $("#tantoaddress").text("法人住所");
        $("#group_flag_disp").show();

    }
    //協賛企業
    if(_val == 6){
        $("#type_number").prop('disabled', true);
        $("#cp_name").prop('disabled', false);

        $("#cp_name_disp").show();
        $(".houjin").show();
        $("#tantoname").text("氏名");
        $("#tantonamekana").text("氏名(ふりがな)");
        $("#tantomail").text("メールアドレス");
        $("#tantobusyo").text("所属部署");
        $("#tantotel").text("電話番号");
        $("#tantoaddress").text("住所");
        $("#group_flag_disp").show();

    }

    //個人会員
    if(_val == 4){
        $("#open_bumon_disp").show();
        $("#type_number").prop('disabled', false);
        $("#cp_name").prop('disabled', true);
        $("#open_address_flag_disp").show();

        $("#type_number_disp").show();
        $(".houjin").show();
        $("#tanto_type_number").text("個人会員番号");
        $("#tantoname").text("氏名");
        $("#tantonamekana").text("氏名(ふりがな)");
        $("#tantomail").text("メールアドレス");
        $("#tantobusyo").text("所属");
        $("#tantobumon").text("部門");
        $("#tantotel").text("電話番号");
        $("#tantoaddress").text("所属住所");
    }

    //非会員
    if(_val == 1 || !_val){
        $("#open_bumon_disp").show();
        $("#type_number").prop('disabled', true);
        $("#cp_name").prop('disabled', true);

        $("#group_flag_disp").show();
        $("#tantoname").text("氏名");
        $("#tantonamekana").text("氏名(ふりがな)");
        $("#tantomail").text("メールアドレス");
        $("#tantobusyo").text("所属");
        $("#tantobumon").text("部門");
        $("#tantotel").text("電話番号");
        $("#tantoaddress").text("所属住所");
    }

};
