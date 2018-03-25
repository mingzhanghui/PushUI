/**
 * Created by Administrator on 2017-05-11.
 */
var timer = null, g_controller;

$(function() {
    g_controller = $('#js_controller').val();

    var feedbacks = document.getElementById("js_fb_wrapper").children;
    Array.prototype.forEach.call(feedbacks, function(fb, i) {
        fb.style.display = 'none';
    });

    // 同步请求已发出
    $('#dialog-1').dialog({
        width: 300,
        height: 240,
        autoOpen: false,
        modal: true,
        open: function() {
            timer = setInterval(checkStatus, 3000);
        },
        // close: function() {},
        buttons: {
            'OK': function() {
                $(this).dialog('close');
            }
        }
    });

    // 同步请求发出结果
    $('#dialog-2').dialog({
        width: 320,
        height: 200,
        autoOpen: false,
        modal: true,
        buttons: {
            'OK': function() {
                $(this).dialog('close');
            }
        }
    });

    /**
     * 用户机顶盒信息同步
     */
    $('#js-btn').click(function () {
        var btn = this;
        var $dlg = $("#dialog-1");
        var $fb = $('#js_fb_wrapper');

        $.ajax({
            type: 'GET',
            url: g_controller + '/syncUser',
            dataType: 'html'
        }).done(function(data) {
            try {
                var json = JSON.parse(data);
                if (json.code == 0) {
                    $dlg.dialog("open");
                    setTimeout(function() {
                        $dlg.dialog("close");
                    }, 3000);
                    $fb.children('.ui-state-highlight').show();
                } else {
                    $('#js_feedback').html(json.msg);
                    $('#dialog-2').dialog('open');
                }
            } catch(e) {
                $('#js_feedback').html(data + e);
                $('#dialog-2').dialog('open');
            }
            $(btn).val('用户信息同步');
        });
        $(this).val('请求发送中...');
        // hide all feedback
        $fb.children().hide();
    });
// close modal
    $('.btn-dismiss').button().click(function () {
        $(this).parent().parent().dialog('close');
    });

});

function checkStatus() {
    $.ajax({
        type: 'GET',
        url: 'syncStatus',
        dataType: 'html',
        cache: false
    }).done(function(data) {
        var json, status;
        try {
            json = JSON.parse(data);
        } catch(e) {
            $('#js_feedback').html(data + e);
            $('#dialog-2').dialog('open');
            clearInterval(timer);  // 返回json格式错误
            return false;
        }
        status = json.status;
        var feedbacks = document.getElementById("js_fb_wrapper").children;
        Array.prototype.forEach.call(feedbacks, function(fb, i) {
            fb.style.display = 'none';
        });
        feedbacks[status].style.display = 'block';
        if (status == '2') {
            clearInterval(timer);  // 同步完成
            $('#dialog-1').dialog("close");
        } else if (status == '0') {
            clearInterval(timer);   // 0 同步失败
        } else if (status == '1') {
            $('#js_fb_wrapper').children('.ui-state-highlight').show();
        }
    })
}
