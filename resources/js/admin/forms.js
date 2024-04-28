$(function(){
    let count = parseInt($('select[name="count"]').val());

    // 枠数、項目数セレクトボックス選択時
    $('select[name="count"]').on({
        'change': function(){
            addValueInputs(parseInt($(this).val()) - count);
            count = parseInt($(this).val());
            if ($("#types li.uk-active").data('type') == 3) {
                $("#values label").attr('hidden', false);
            }
        },
        'focus': function(){
            count = parseInt($(this).val());
        }
    });

    // 入力フォーマット選択時
    $("#types li").click(function(){
        $("#types li.uk-active").removeClass('uk-active');
        $(this).addClass('uk-active');
        $('input[name="type"]').val($(this).data('type'));
        if ($(this).data('type') == 3) {
            $("#values label").attr('hidden', false);
        } else {
            $("#values label").attr('hidden', true);
        }
        if ($(this).data('type') == 1) {
            $("#count_title").text('入力枠数：');
            $("#value_title").text('入力例：');
        } else {
            $("#count_title").text('選択項目数：');
            $("#value_title").text('選択項目：');
        }
    });

    // 入力例、選択項目input生成・削除
    function addValueInputs(add_count) {
        if (add_count > 0) {
            for (let i=count; i<count+add_count; i++) {
                $("#values").append(
                    '<div class="value_group">' +
                        '<div class="uk-flex uk-flex-middle uk-margin-small">' +
                            '<div style="width: 30px">' + (i+1) + '</div>' +
                            '<input type="text" name="value[' + i + ']" class="uk-input" value="">' +
                        '</div>' +
                        '<input type="hidden" name="is_included_textarea[' + i + ']" value="0">' +
                        '<label hidden style="margin-left:30px">' + 
                            '<input name="is_included_textarea[' + i + ']" value="1" class="uk-checkbox" type="checkbox"> テキストエリア付'+
                        '</label>' +
                    '</div>'
                );
            }
        } else if (add_count < 0) {
            for (let i=add_count; i<0; i++) {
                $(".value_group:last").remove();
            }
        }
    }
});
