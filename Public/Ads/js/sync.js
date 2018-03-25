/**
 * Created by Administrator on 2017-05-08.
 */

var baseurl = document.getElementById("js_baseurl").value;
var g_timer = null;

$(function() {
    initFeedback();
    listSync();

    $('#js_sync').on('click', function() {
        var btn = this;

        $.ajax({
            type: 'GET',
            url: baseurl + 'SendSyncSocket',
            data: {'t': Date.now()},
            contentType: 'text/plain'
        }).done(function(resp) {
            try {
                var json = JSON.parse(resp);
                if (json.code == 0) {
                    // 同步请求发送成功
                    check_sync_status();
                    g_timer = setInterval(check_sync_status, 5000);

                    btn.innerHTML = '同步中...';
                    var dlg = $("#dialog-1");
                    dlg.dialog('open');
                    setTimeout(function() {
                        dlg.dialog('close');
                    }, 3000);

                } else {
                    $('#js_fb_failed').show();
                }
            } catch(e) {
                $('#txtcontent').text(e + ': ' + resp);
                $('#js_fb_failed').show();
            }

        }).fail(function ( jqXHR, textStatus, errorThrown ) {
            console.error(textStatus);
            $('#js_fb_failed').show();
            $('#txtcontent').text('listSync error: ' + errorThrown);
        });
    });

    $("#dialog-1").dialog({
        title: "广告同步业务信息发送成功",
        autoOpen: false,
        modal: true,
        width: 400,
        maxwidth: 600,
        height: 250,
        maxheight: 600,
        resizable: false,
        closeOnEscape: false,
        show: {effect: "fade", duration: 800},
        hide: {effect: "fade", duration: 1000},
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
});

function initFeedback() {
    $('#js_fb_failed').hide();
    $('#js_fb_success').hide();
}

/**
 * 查询广告同步状态
 */
function check_sync_status() {
    $.ajax({
        type: 'GET',
        url: baseurl + 'SendQuerySocket',
        cache: false,
        contentType: 'text/plain'
    }).done(function(resp) {

        try {
            var json = JSON.parse(resp);
            if (json.code == 1) {
                btn.innerHTML = '同步中...';
            }
            // done
            else if (json.code == 0) {
                btn.innerHTML = '同步业务信息';
                $('#js_fb_failed').hide();
                $('#js_fb_success').show();
                clearInterval(g_timer);
            }
            else {
                $('#txtcontent').text(e + ': ' + json.msg);
                $('#js_fb_failed').show();
                clearInterval(g_timer);
            }
        } catch(e) {
            $('#txtcontent').text(e + ': ' + resp);
            $('#js_fb_failed').show();
            clearInterval(g_timer);
        }
        listSync();

    }).fail(function(jqXHR, textStatus, errorThrown) {
        alert('doDelPrerollAd ajax error: ' + errorThrown);
        clearInterval(g_timer);
    });
    initFeedback();
}

/**
 * refresh list
 */
function listSync() {
    $.ajax({
        type: 'get',
        url: baseurl + 'listPush',
        data: {t: Date.now()}
    }).done(function (resp) {
        var table = document.getElementById("PreRollPlayTable");
        var tbody = table.firstElementChild;
        var MAX_ROWS = 12;

        if (resp.length <= MAX_ROWS) {
            $(table).parent().css({
                "overflow-y": "auto",
                "width": "1046px"
            });
        } else {
            $(table).parent().css({
                "overflow-y": "scroll",
                "width": "1067px"
            })
        }

        tbody.innerHTML = '';
        if (resp.length === 0) {
            tbody.innerHTML = '<tr><td colspan="9">广告同步列表为空</td></tr>';
        } else {
            Array.prototype.forEach.call(resp, function(elem, i) {
                var tr = document.createElement("tr");
                tr.setAttribute("data-adid", elem.adid);
                tr.setAttribute("data-packageid", elem.packageid);
                tr.setAttribute("data-contentid", elem.contentid);

                tr.innerHTML =
                    '<td class="cp-0">'+(i+1)+'</td>' +
                    '<td class="cp-1">'+ elem.advertisename +'</td>' +
                    '<td class="cp-2">' + elem.name + '</td>' +
                    '<td class="cp-3">' + elem.adtype + '</td>' +
                    '<td class="cp-4">' + elem.packagename + '</td>' +
                    '<td class="cp-5">' + elem.startdate + '</td>' +
                    '<td class="cp-6">' + elem.enddate + '</td>' +
                    '<td class="cp-7">' + elem.status + '</td>' +
                    '<td class="cp-8"><input type="checkbox" name="SyncState[]"></td>';

                tbody.appendChild(tr);
            });
        }
        tablepad($(table), 9, MAX_ROWS);

    }).fail(function ( jqXHR, textStatus, errorThrown ) {
        console.error(textStatus);
        $('#txtcontent').text('listSync error: ' + errorThrown);
    });
}