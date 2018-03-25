/**
 * Created by Administrator on 2017-02-07.
 */
var g_timer = null;
var g_counter = 0;  // 检查同步结果 次数
var syncBtn = document.getElementById("js_btn_sync");  // 同步业务信息按钮
var g_missions = [];   // 需要同步的业务期
var g_size_synced = 0;    // 内容同步已经同步文件大小
var g_size_total = 0;     // 内容同步总文件大小

$(document).ready(function() {
    // error dialog
    $('#dialog-1').dialog({
        title: "同步业务信息出错",
        autoOpen: false,
        modal: true,
        width: 300,
        height: 250,
        resizable: true,
        closeOnEscape: true,
        // show: {effect: "fade", duration: 100},
        hide: {effect: "fade", duration: 100},
        create: function(event, ui) {},
        open: function(event, ui) {},
        beforeClose: function(event, ui) {},
        close: function(event, ui) {},
        buttons: [{
            text: "OK",
            icons: {
                primary: "ui-icon-check"
            },
            click: function() {
                $(this).dialog("close");
            }
        }]
    });
    // 正在同步业务信息
    $('#dialog-2').dialog({
        dialogClass: 'no-close',
        autoOpen: false,
        modal: true,
        width: 260,
        height: 200,
        resizable: true,
        closeOnEscape: false,
        open: function() {
            g_size_synced = g_size_total = 0;
        }
    });

    // 内容同步左边表格
    listContentSync();

    // 内容同步右边表格
    listMission();

    // 同步业务信息
    var fbgrp = document.getElementById("js_feedback").children;

    syncBtn.addEventListener("click", function() {
        var btn = this;
        btn.style.cursor = 'not-allowed';

        // mission id array to sync, for update last sync status
        $.ajax({
            type: 'GET',
            url: 'missionsToSync'
        }).done(function(data) {
            // g_missions = [];
            while(g_missions.length > 0) {
                g_missions.pop();
            }
            Array.prototype.forEach.call(data, function(id) {
                g_missions.push(id);
            });
        });

        // 发送同步请求
        $.ajax({
            type: 'GET',
            url: btn.getAttribute("data-url"),  // sendSyncSocket
            data: {'t': Date.now()}
        }).done(function(data) {
            hideAllFb(fbgrp);
            if (data.code != 0) {
                fbgrp[2].style.display = 'block';  // send failed
                btn.style.cursor = 'pointer';
                setTimeout(function() {
                    fbgrp[2].style.display = 'none';
                }, 5000);
                return;
            }
            fbgrp[0].style.display = 'block';  // send success, 正在同步中...
            // 检查同步结果
            g_timer = setInterval(checkSyncStatus, 3000);
        }).fail(function() {
            $("#dialog-2").dialog("close");
        });
        $("#dialog-2").dialog("open");
    });

    // 查看处理日志
    var loglist = $("#js-list");
    var url = loglist.attr("data-url"); // getLogContent

    loglist.find('a').on('click', function() {
        var data = {'name': this.innerHTML};

        var content = document.getElementById('js-content');
        content.innerHTML = "<p class='text-primary'>加载文件...</p>";
        $.post(url, data, function (resp) {
            content.innerHTML = resp;
        });

        idx = $(this).index('#js-list a');
        var al = $('#js-list').find('a');
        al.eq(idx).addClass('active');
        al.not(al.eq(idx)).removeClass('active');

    }).first().trigger('click');

});

// 隐藏所有同步反馈信息
function hideAllFb(fbgrp) {
    if (typeof fbgrp === 'undefined' || fbgrp==null) {
        fbgrp = document.getElementById("js_feedback").children;
    }
    for (var i = 0; i < fbgrp.length; i++) {
        fbgrp[i].style.display = 'none';
    }
}

// 内容同步左边表格
function listContentSync() {
    var table = document.getElementById("sync-1");
    var tbody = table.children[1];
    var url = tbody.getAttribute("data-url");
    $.ajax({
        type: "GET",
        data: {'t': Date.now()},  // do not cache
        url: url   // Subscribe/Sync/listContentSync
    }).done(function(resp) {
        tbody.innerHTML = '';

        if (resp.length == 0) {
            var tr = document.createElement("tr");
            tbody.appendChild(tr);
            tr.innerHTML = '';
            var empty = document.createElement('td');
            empty.setAttribute('colspan', 6);
            empty.style.padding = '0 0 0 160px';
            empty.innerText = '暂时没有内容同步';
            tr.appendChild(empty);
        } else {
            resp.forEach(function(elem, i) {
                var tr = document.createElement("tr");
                tbody.appendChild(tr);
                tr.setAttribute("data-oid", elem.oid);

                var bgColor = '';
                if (elem.status == 100) {
                    bgColor = '#7cb67d';
                } else if (elem.status > 60) {
                    bgColor = '#f9dd8e';
                } else {
                    bgColor = '#fddfdf';
                }
                var title = (elem.title === null)?elem.filename:elem.title;
                tr.innerHTML = [
                    '<td class="col-0" data-oid="'+elem.oid+'">'+ (i+1) +'</td>',
                    '<td class="col-1" title="'+elem.filename+'">'+ title +'</td>',
                    '<td class="col-2">' + elem.size + '</td>',
                    '<td class="col-3">￥'+ parseFloat(elem.price/100).toFixed(2)+'</td>',
                    '<td class="col-4">'+ elem.mediatype +'</td>',
                    '<td class="col-5">' + html_progress(elem.status) + '</td>'
                ].join('');
                tr.setAttribute("data-linkid", elem.missionlinkmediaid);

                var size = size2k(elem.size);
                g_size_total += size;
                g_size_synced += size * elem.status;
            });
            var progress = g_size_synced / g_size_total;

            $('#js_progress').html( html_progress(progress) );
        }

        padScrollTable(table, 14, 6);
    }).fail(function(jqXHR, textStatus, errorThrown) {
        alert(errorThrown);
    })
}

// 内容同步右边表格
function listMission() {
    var table = document.getElementById("sync-2");
    var tbody = table.children[1];
    var url = tbody.getAttribute("data-url");

    $.ajax({
        type: "GET",
        data: {'t': Date.now()},
        url: url   // /index.php/Subscribe/Sync/listMission.html
    }).done(function(resp) {
        tbody.innerHTML = '';

        if (resp.length > 0) {
            resp.forEach(function(elem, i) {
                var tr = document.createElement("tr");
                tr.setAttribute("data-packageid", elem.packageid);
                tr.setAttribute("data-missionid", elem.missionid);
                tbody.appendChild(tr);

                var state = elem.state > 0 ?
                    '<span class="text-success">已同步</span>' :
                    '<span class="text-danger">未同步</span>';

                var price = elem.price ? '￥'+(elem.price/100).toFixed(2) : '免费';

                tr.innerHTML = '<td class="col-0">'+(i+1)+'</td>' +
                    '<td class="col-1" title="'+elem.packagename+'">'+elem.packagename +'</td>' +
                    '<td class="col-2" title="'+price+'">' + price + '</td>' +
                    '<td class="col-3" title="'+elem.missionname+'">'+elem.missionname+'</td>' +
                    '<td class="col-4">'+elem.versionid+'</td>' +
                    '<td class="col-5">'+elem.synversionid+'</td>' +
                    '<td class="col-6">'+state+'</td>';
            });
        } else {
            var tr = document.createElement("tr");
            tbody.appendChild(tr);
            tr.innerHTML = '';
            var empty = document.createElement('td');
            empty.setAttribute('colspan', 7);
            empty.style.padding = '0 0 0 200px';
            empty.innerText = '暂时没有有效的业务期';
            tr.appendChild(empty);
        }
        padScrollTable(table, 14, 7);

    }).fail(function(jqXHR, textStatus, errorThrown) {
        alert(errorThrown);
    });
}

function checkSyncStatus() {
    var feedback = document.getElementById("js_feedback");
    var url = feedback.getAttribute("data-url");   // sendQuerySocket
    // 正在同步中...
    var $loading = $('#dialog-2');

    $.ajax({
        type: 'GET',
        cache: false,
        url: url,
        contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
        dataType: 'json'
    }).done(function(data) {
        hideAllFb(feedback.children);
        var ret = parseInt(data.code);
        // case 0: $msg = 'sync done'; break;
        // case 1: $msg = 'sync ongoing'; break;
        // case 2: $msg = 'sync error'; break;
        switch (ret) {
            case 0:
                // 确认内容同步左边列表的进度显示
                var tb = document.getElementById("sync-1");
                var bars = tb.querySelectorAll(".ui-progressbar");
                // var isSyncDone = false;
                for (var i = 0; i < bars.length; i++) {
                    var p = bars[i].getAttribute("aria-valuenow");
                    if (parseInt(p) < 100) {
                        break;
                    }
                }

                // if (i == bars.length) {isSyncDone = true;}
                // 同步完了
                // if (isSyncDone) {
                    feedback.children[1].style.display = 'block';
                    syncBtn.style.cursor = 'pointer';
                    clearInterval(g_timer);  // 同步完成 清除定时器
                    setLastSyncState(1);

                    // 3s后 内容同步左边表格 100%;
                    setTimeout(listContentSync, 3000);
                    // 1s后 内容同步右边表格
                    setTimeout(listMission, 1000);

                    $loading.dialog('close');
                // } else {
//                    listContentSync();
//                }

                break;
            case 1:
                feedback.children[0].style.display = 'block';
                syncBtn.style.cursor = 'not-allowed';  // 同步中
                // 0.1s后 更新内容同步左边表格 进度变化
                // setTimeout(listContentSync, 100);
                listContentSync();
                break;
            case 2:
                feedback.children[3].style.display = 'block';
                syncBtn.style.cursor = 'pointer';   // 同步失败
                setLastSyncState(-1);
                $loading.dialog('close');

                clearInterval(g_timer);   // 同步失败 清除定时器
                break;
            default:
                console.error('unexpected return code');
                $loading.dialog('close');
        }

    }).fail(function(jqXHR, textStatus, errorThrown) {
        console.error("checkSyncStatus: " + errorThrown);
    });
    g_counter += 1;
    if (g_counter > 200) {
        console.warn("超过最大轮询次数");
        clearInterval(g_timer);
        console.log(g_timer);
    }
}

/**
 * 更新右边表格 业务期 上次同步状态
 * @param state
 */
function setLastSyncState(state) {
    console.log(g_missions);
    if (0 === g_missions.length) {
        return false;
    }
    var data =  {
        "missions" : JSON.stringify(g_missions),
        "state": state
    };
    $.ajax({
        type:"POST",
        url: "setLastSyncState",
        data: data
    }).done(function(data) {
        console.log(data);
        setTimeout(listMission, 3000);
    })
}

/**
 * 文件大小转化为K, 1.88G => 1925.12M
 */
function size2k(s) {
    var size = parseFloat(s.substr(0, s.length-1));
    var unit = s.substr(s.length-1);
    switch (unit) {
        case "B":
            size = 0;
            break;
        case "K":
            break;
        case "M":
            size *= 1024;
            break;
        case "G":
            size *= 1048576;
            break;
        default:
            console.error("Unexpected file size: " + s);
    }
    return size;
}

/**
 * 通过进度百分比取得进度条颜色
 * @returns {*}
 */
function get_bgcolor_by_status(status) {
    var bgcolor;

    if (status == 100) {
        bgcolor = '#7cb67d';
    } else if (status > 60) {
        bgcolor = '#f9dd8e';
    } else {
        bgcolor = '#ffafaf';  // '#fddfdf';
    }
    return bgcolor;
}

/**
 * 生成进度条
 * @param value  0-100
 * @returns {string}
 */
function html_progress(value) {
    var progress = parseInt( value );
    return [
        '<div class="progress"><div role="progressbar" aria-valuemin="0" class="ui-progressbar ui-corner-all ui-widget ui-widget-content" aria-valuemax="100"',
        'aria-valuenow="' + progress + '">',
        '<div class="ui-progressbar-value ui-corner-left ui-widget-header" ',
        'style="width:'+ progress +'%; background:'+ get_bgcolor_by_status(progress) +'"></div>',
        '<span>' + progress + '%</span></div></div>'
    ].join('');
}
