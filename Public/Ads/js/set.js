/**
 * Created by Administrator on 2017-02-16.
 */
var baseurl = '';

window.onload = function () {
    baseurl = $('#js_baseurl').val();
    // 读取广告2个 路径
    getPath();

    // feedback
    $("#dialog-1").dialog({
        autoOpen: false,
        modal: true,
        width: 300,
        maxwidth: 600,
        height: 250,
        maxheight: 600,
        resizable: false,
        closeOnEscape: false,
        show: {effect: "fade", duration: 100},
        hide: {effect: "fade", duration: 100},
        create: function(event, ui) {},
        open: function(event, ui) {},
        beforeClose: function(event, ui) {},
        close: function(event, ui) {},
        buttons: [{
            text: "确定",
            icons: {
                primary: "ui-icon-check"
            },
            click: function() {
                $(this).dialog("close");
            }
        }]
    });

    // 表单 提交修改
    $('#js_setadvpath').on('submit', function(e) {
        e.preventDefault();

        $.ajax({
            type: 'POST',
            url: this.action,
            data: $(this).serialize()
        }).done(function(data, textStatus, errorThrown) {
            $('#js_feedback').html(data.msg);
            if (data.code == 0) {
                $('#dialog-1').dialog('open');
            }
            $('#js_submit').text('提交修改');
        }).fail(function(jqXHR, textStatus, errorThrown) {
            $('#js_feedback').html(errorThrown);
            $('#js_submit').text('提交修改');
        });
        $('#js_submit').text('正在提交...');
    });

};

/**
 * ロード  同步日志路径 + 广告存储路径
 */
function getPath() {
    //  get 同步日志路径p
    var log = document.getElementById('Adlog_path');
    var urlLog = document.getElementById('url-log').value;
    $.get(urlLog, function (data) {
        if (data == null || typeof data.stringvalue == 'undefined') {
            log.value = '同步日志路径取得失败';
            return false;
        }
        log.value = data.stringvalue;
    });
//    get 广告存储路径
    var adv = document.getElementById('Adv_path');
    var urlAd = document.getElementById('url-ad').value;
    $.get(urlAd, function (data) {
        if (data == null || typeof data.stringvalue == 'undefined') {
            adv.value = '广告存储路径取得失败';
            return false;
        }
        adv.value = data.stringvalue;
    });

    // reset feedback information
    var fb3 = document.getElementById('fb-3');
    fb3.classList = ['help-block'];
    log.onfocus = function () {
        fb3.innerHTML = '';
    };
    adv.onfocus = function () {
        fb3.innerHTML = '';
    };

    // check path
    function xhrcheckpath(obj, target, url) {
        $.ajax({
            type: 'post',
            url: url,
            data: {'path' : obj.value}
        }).done(function (resp) {
            target.innerHTML = resp;
        });
    }
    var url = baseurl + 'checkPath';

    var fb1 = document.getElementById('fb-1');
    $(log).blur(function () {
        return xhrcheckpath(this, fb1, url);
    }).focus(function () {
        // fb1.innerHTML = '';
    });

    var fb2 = document.getElementById('fb-2');
    $(adv).blur(function () {
        return xhrcheckpath(this, fb2, url);
    }).focus(function () {
        // fb2.innerHTML = '';
    });
}
