var _form_type_default = 0;
$(function () {
    $(".datepicker").datepicker({
        dateFormat: "yy-mm-dd",
    });
    $("#send").on("click", function () {
        if (confirm("登録を行います。よろしいですか？")) {
            return true;
        }
        return false;
    });
    $(".deletebutton").on("click", function () {
        if (confirm("イベント情報の削除を行います。よろしいですか？")) {
            return true;
        }
        return false;
    });

    //イベント選択後の日付選択
    $(this).selectEventDate();
    $(this).getNumbers($("[name='event_id']").val());
    $("[name='event_id']").on("change", function () {
        $(this).selectEventDate();
    });

    //会場・イベント・日付選択時に登録データの取得を行い起票
    /*
    $("input[name='type']").on("change",function(){
        $(this).setProgramData();});
    $("[name='event_id']").on("change",function(){
        $(this).setProgramData();});
    $("select[name='date']").on("change",function(){
        $(this).setProgramData();});
    */
    $("input[name='type']").on("change", function () {
        $(this).selectEventData();
    });
    $("[name='event_id']").on("change", function () {
        $(this).selectEventData();
    });
    $("select[name='date']").on("change", function () {
        $(this).selectEventData();
    });
    //受付中を切り替えたとき
    $(".enableswitch").on("change", function () {
        var _id = $(this).attr("id").split("-")[1];
        $(this).enableSwitch(_id);
    });

    //メールフォーム編集画面でタイトルを変更したとき
    $(this).changeMailTitle();
    $("[name='form_type']").on("change", function () {
        if (
            confirm("編集内容は破棄されるので、更新後に変更を行ってください。")
        ) {
            $(this).changeMailTitle();
        } else {
            $("[name='form_type']").val(_form_type_default);
            return false;
        }
    });

    // 発表番号を変更したとき
    $("select.presentation_id").change(function () {
        $(this).getPresentation($(this).data("index"));
    });

    $("[name='checkall']").click(function () {
        var _chk = $(this).prop("checked");
        $("input.disp_status").prop("checked", _chk);
        return true;
    });
});

$.fn.selectEventData = function () {
    var _type = $("[name='type']:checked").val();
    var _event_id = $("[name='event_id']").val();

    var _date = $("[name='date']").val();
    if (_type && _event_id && _date) {
        location.href =
            "./program?type=" +
            _type +
            "&event_id=" +
            _event_id +
            "&date=" +
            _date;
    }
    return false;
};
$.fn.changeMailTitle = function () {
    $(".hide").hide();
    var form_type = $("[name='form_type']").val();
    _form_type_default = form_type;
    if (!form_type) return false;
    let _url = location.href + "/get";
    let postData = {
        form_type: form_type,
    };
    $("[name='title']").val("");
    $("[name='note']").val("");
    $.ajax({
        type: "POST",
        url: _url,
        data: postData,
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        dataType: "json",
    })
        .then((result) => {
            if (result.form_type == "0") {
                $("tr.code-upload").show();
            } else {
                $("tr.code-" + result.form_type).show();
            }
            $("[name='title']").val(result.title);
            $("[name='note']").val(result.note);
        })
        .catch((error) => {
            console.log("データ更新エラー");
            console.log(error.status);
        });
    return false;
};

$.fn.enableSwitch = function (_id) {
    var _chk = $("#switch-" + _id).prop("checked");
    // let _url = location.href+"/enabled";
    let _url = "./list/enabled";
    let postData = {
        id: _id,
        chk: _chk,
    };
    $.ajax({
        type: "POST",
        url: _url,
        data: postData,
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        //  dataType: 'json',
    })
        .then((result) => {
            console.log(result);
        })
        .catch((error) => {
            console.log("データ更新エラー");
            console.log(error.status);
        });
};

//会場・イベント・日付選択時に登録データの取得を行い起票
$.fn.setProgramData = function () {
    //初期値設定
    $("input.number").prop("checked", false);
    $("textarea[name='explain']").val();
    $(".ampm").val("1");
    $(".start_hour").val(9);
    $(".start_minute").val(0);
    $(".end_hour").val(9);
    $(".end_minute").val(0);
    $(".presentation_id").val("");
    $(".note").val("");

    let _type = $("input[name='type']:checked").val();
    let _event_id = $("[name='event_id']").val();
    let _date = $("select[name='date']").val();
    if (_type > 0 && _event_id > 0 && _date) {
        let _form = $("#form").attr("action");
        let _url = _form + "/get";

        let postData = {
            type: _type,
            event_id: _event_id,
            date: _date,
        };
        $.ajax({
            type: "POST",
            url: _url,
            data: postData,
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            //  dataType: 'json',
        })
            .then((result) => {
                // console.log(result['list']);
                $("input#webex_url").val(result["webex_url"]);
                $("textarea[name='explain']").val(result["explain"]);
                //プログラムリストの設定
                $.each(result["list"], function (key, item) {
                    $("input#number-" + item["number"]).prop("checked", false);
                    if (item["enable"] == 1) {
                        $("input#number-" + item["number"]).prop(
                            "checked",
                            true
                        );
                    }
                    // console.log("note=>"+item['note']);
                    $("select#ampm-" + item["number"]).val(item["ampm"]);
                    $("select#start_hour-" + item["number"]).val(
                        item["start_hour"]
                    );
                    $("select#start_minute-" + item["number"]).val(
                        item["start_minute"]
                    );
                    $("select#end_hour-" + item["number"]).val(
                        item["end_hour"]
                    );
                    $("select#end_minute-" + item["number"]).val(
                        item["end_minute"]
                    );
                    $("select#presentation_id-" + item["number"]).val(
                        item["presentation_id"]
                    );
                    $("textarea#note-" + item["number"]).val(item["note"]);
                });
            })
            .catch((error) => {
                console.log("データ更新エラー");
                console.log(error.status);
            });
    }
    $(this).getNumbers(_event_id);
};

//イベント選択後の日付選択
$.fn.selectEventDate = function () {
    let _event_id = $("[name='event_id']").val();
    let _event_date = $("[name='event_date']").val();

    if (_event_id > 0) {
        let _form = $("#form").attr("action");
        let _url = _form + "/date";
        let postData = {
            event_id: _event_id,
        };
        $.ajax({
            type: "POST",
            url: _url,
            data: postData,
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            dataType: "json",
        })
            .then((result) => {
                //console.log(result);
                $("select[name='date']").children().remove();
                $("select[name='date']").append(
                    $("<option>").html("-").val("")
                );
                $.each(result["term"], function (key, item) {
                    let date = item.split("-");
                    let ymd = date[0] + "年" + date[1] + "月" + date[2] + "日";
                    $("select[name='date']").append(
                        $("<option>").html(ymd).val(item)
                    );
                });

                $("select[name='date']").val(_event_date);
            })
            .catch((error) => {
                console.log("データ更新エラー");
                console.log(error.status);
            });
    }
};

// 発表番号取得関数
$.fn.getNumbers = function (event_id) {
    if (event_id) {
        $.ajax({
            headers: { "X-CSRF-TOKEN": CSRF_TOKEN },
            type: "POST",
            timeout: 5000,
            url: GET_NUMBERS_URL + "/" + event_id,
        })
            .done(function (result) {
                // console.log(result);
                if (result) {
                    var _num = 1;
                    $("select.presentation_id").each(function (index, elem) {
                        let old = $(elem).val();
                        $(elem).html("<option></option>");
                        $.each(result, function (key, row) {
                            let _sel = $("#presentation_id-reg-" + _num).val();
                            let select = row.id == _sel ? "selected" : "";
                            $(elem).append(
                                '<option value="' +
                                    row.id +
                                    '" ' +
                                    select +
                                    ">" +
                                    row.number +
                                    "</option>"
                            );
                        });
                        _num++;
                        //if (old) $(this).getPresentation(index+1, false);
                    });
                }
            })
            .fail(function (error) {
                console.log(error.statusText);
            });
    }
};

// 講演情報取得関数
//ajaxでの動作をやめる
$.fn.getPresentation = function (key, changeNote = true) {
    let p_id = $("#presentation_id-" + key).val();

    if (p_id) {
        $.ajax({
            headers: { "X-CSRF-TOKEN": CSRF_TOKEN },
            type: "POST",
            timeout: 5000,
            url: GET_PRESENTATION_URL + "/" + p_id,
        })
            .done(function (result) {
                // console.log(result);
                if (result) {
                    if (changeNote) {
                        if (result.description) {
                            $("#note-" + key).val(result.description);
                        }
                        if (result.daimoku) {
                            $("#note-" + key).val(result.daimoku);
                        }
                    }
                    let downloads = $("#downloads-" + key);
                    downloads.html("");

                    var _code = location.href.split("/")[4];
                    if (result.proceeding) {
                        var _txt = "配布資料1";
                        if (_code == "touronkai") _txt = "講演要旨";

                        downloads.append('<div class="uk-width-1-1">');
                        downloads.append(
                            '<a class="uk-button uk-button-default uk-width-1-2" href="' +
                                OPEN_FILE_URL +
                                "/" +
                                result.number +
                                "/proceeding/" +
                                result.id +
                                '?download=true">' +
                                _txt +
                                "</a>"
                        );
                        downloads.append(
                            '<input type="checkbox" name="disp_status1[' +
                                key +
                                ']" value="1" checked class="disp_status" />有効'
                        );
                        downloads.append("</div>");
                    }
                    if (result.flash) {
                        var _txt = "配布資料2";
                        if (_code == "touronkai") _txt = "フラッシュ";
                        downloads.append('<div class="uk-width-1-1">');
                        downloads.append(
                            '<a class="uk-button uk-button-default uk-width-1-2" href="' +
                                OPEN_FILE_URL +
                                "/" +
                                result.number +
                                "/flash/" +
                                result.id +
                                '?download=true">' +
                                _txt +
                                "</a>"
                        );
                        downloads.append(
                            '<input type="checkbox" name="disp_status2[' +
                                key +
                                ']" value="1" checked class="disp_status" />有効'
                        );
                        downloads.append("</div>");
                    }

                    if (result.poster) {
                        var _txt = "配布資料3";
                        if (_code == "touronkai") _txt = "配布資料等";
                        downloads.append('<div class="uk-width-1-1">');
                        downloads.append(
                            '<a class="uk-button uk-button-default uk-width-1-2" href="' +
                                OPEN_FILE_URL +
                                "/" +
                                result.number +
                                "/poster/" +
                                result.id +
                                '?download=true">' +
                                _txt +
                                "</a>"
                        );
                        downloads.append(
                            '<input type="checkbox" name="disp_status3[' +
                                key +
                                ']" value="1" checked class="disp_status" />有効'
                        );
                        downloads.append("</div>");
                    }
                }
            })
            .fail(function (error) {
                console.log(error.statusText);
            });
    }
};
