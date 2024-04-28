require('tinymce');
require('tinymce/themes/silver');
require('tinymce/icons/default');
require('tinymce-i18n/langs5/ja');

// plugins
require('tinymce/plugins/link');
require('tinymce/plugins/paste');
require('tinymce/plugins/code');




$(function() {

// 一覧ページ
// ==================================

    $(".open_setting_btn").click(function(){
        let data = $(this).data();
        let dispVal = '非公開にします。';
        console.log(data.openValue);
        if (data.openValue) {
            dispVal = '公開します。';
        }
        $("#page_name").html(data.pageName);
        $("#open_value_disp").html(dispVal);
        $("#open_value").val(data.openValue);
        $("#page_id").val(data.pageId);
    });





// 編集ページ
// ==================================

    // tiny初期化
    tinymce.init({
        selector: "#editor2 textarea",
        height: 300,
        width:650,
        skin: 'oxide-dark',
        menubar: false,
        plugins: "link paste code",
        toolbar: 'undo redo | fontsizeselect bold italic superscript subscript | backcolor forecolor | link unlink | code | removeformat',
        statusbar: false,
        language: 'ja',
        forced_root_block : '',
        force_p_newlines : false,
        force_br_newlines : true,
        content_css : '/css/admin.css',
        oninit : "setPlainText",
        // inline: true,
    });




    let editor = $("#editor");
    let select;
    let newContentType = '';

    // 新しい項目を追加の戻るボタン
    $("#add_reset").click(function(){
        $(".add_reset_toggle").attr('hidden', true);
    });
    // 編集可能箇所
    $(".edit-content").click(function(e) {
        select = $(this);
        if (select.data('contentId') != 'new' && select.data('subContentId') != 'new') {
            $("iframe").contents().find("#tinymce").html($(this).html());
        } else {
            $("iframe").contents().find("#tinymce").html("");
        }
        editor.attr("hidden", false);
    });

    // エディターの登録ボタン
    $("#update").click(function(){
        let updateContent = $("iframe").contents().find("#tinymce").html();

        if (updateContent == '<br data-mce-bogus="1">') {
            // エディタ内を空欄にしても <br data-mce-bogus="1"> が消えてくれないので暫定的に削除
            // FIXME: tinymceのinline editorに設定を変更すると綺麗に空になる。要スタイル調整。
            // 参照：https://www.tiny.cloud/docs/demo/inline/#
            updateContent = "";
        }
        let postData = {
            'content_id': select.data('contentId'),
            'sub_content_id': select.data('subContentId'),
            'column': select.data('column'),
            'content': updateContent,
            'content_type': select.data('contentType'),
            'column_count': select.data('columnCount'),
        }

        // データ送信
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: $(this).data('action'),
            type: 'POST',
            data: postData,
            timeout: 5000,
        })
        .then(result => {
            // console.log(result);

            if (select.data('contentId') == 'new' || select.data('subContentId') == 'new') {
                // 項目追加の場合リロード
                location.reload();
            } else {
                // 編集の場合表示更新
                select.html(updateContent);
                disabledLink();
            }
        })
        .catch(error => {
            console.log('データ更新エラー');
            console.log(error.status);
        });
    });

    disabledLink();

    // リンク無効
    function disabledLink() {
        $(".edit-content a, .edit-content [type='submit'], .form_contents [type='submit'], .form_contents a").click(function(){
            UIkit.notification("管理画面からは移動できません", {status: 'danger'});
            return false;
        });
    }
});
