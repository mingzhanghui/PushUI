/**
 * Created by Administrator on 2017-05-09.
 */
var baseurl = '';

window.onload = function() {
    baseurl = $('#js-baseurl').val();

    // ダイアログ機能を適用
    $('#dialog-1').dialog({
        width: 250,
        height: 180,
        autoOpen: false,
        modal: true
    });
    $('#dialog-2').dialog({
        width: 250,
        height: 180,
        autoOpen: false,
        modal: true,
        close: function() {
            location.reload();
        },
        buttons: {
            "确定": function() {$(this).dialog('close');}
        }
    });
    $('#dialog-3').dialog({
        width: 250,
        height: 180,
        autoOpen: false,
        modal: true,
        close: function() {
            $("#js-newstb").val('提交并开通');
        },
        buttons: {
            "确定": function() {
                $(this).dialog('close');
            }
        }
    });

    // 手动导入
    $('#newcustomer-2').on('submit', function(e) {
        e.preventDefault();
        var form = this;

        $.ajax({
            type: 'GET',
            url: form.action,
            data: $(form).serialize()
        }).done(function(data) {
            if (data.code > 0) {
                $('#fb-' + data.code).html(data.msg);
            } else {
                $('#js_manual_feedback').html(data.msg);
                $('#dialog-3').dialog('open');
                form.reset();
            }
            $("#js-newstb").val('提交并开通');
        }).fail(function(jqXHR, textStatus, errorThrown) {
            $('#js_manual_feedback').html(errorThrown);
            $('#dialog-3').dialog('open');
            $("#js-newstb").val('提交并开通');
        });
        $("#js-newstb").val('提交...');
        for (var i = 1; i < 4; i++) {
            $('#fb-' + i).html('');
        }
    });

    // 批量导入提交表单之前先显示 loading.gif
    /*
    $('#newcustomer-1').on('submit', function(e) {
        e.preventDefault();
        $('#dialog-1').dialog('open');
        this.submit();
    });
    */
    var form = document.getElementById("newcustomer-1");
    form.excel.addEventListener('change', function() {
        form.submit();
        $('#dialog-1').dialog('open');
    });

    // 批量导入: iframe upload
    $("#uploadTrg").on("load", function() {
        // iframeのwindowオブジェクトを取得
        var ifrm = this.contentWindow;
        // 外部サイトにメッセージを投げる
        var full = location.protocol+'//'+location.hostname+(location.port ? ':'+location.port: '');
        var url = full + baseurl + 'bulkImport.html';
        ifrm.postMessage("HELLO!", url);
    });

    // メッセージ受信イベント iframe
    window.addEventListener('message', function(event) {
        // alert(event.data); //'WORLD!!!'
        $('#js_batch_feedback').html(event.data);
        $('#dialog-1').dialog('close');
        $('#dialog-2').dialog('open');
    }, false);

    // 上传按钮点击效果
    (function() {
        $("#upload_excel").parent().on("mouseup", function() {
            $(this).css({"transform":"translateX(-1px) translateY(-1px)"});
        }).on("mousedown", function() {
            $(this).css({"transform":"translateX(0) translateY(0)"});
        });
    }).apply();
};



