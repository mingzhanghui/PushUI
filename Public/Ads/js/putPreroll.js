/**
 * Created by Administrator on 2017-05-05.
 */
var baseurl, id, g_ids;

$(function() {
// this controller
    baseurl = document.getElementById("js_baseurl").value;

// 11 columns, 10 rows
    tablepad($('#PreRollPlayTable'), 11, 10);

    var calendarurl = document.getElementById("js_calendarimg").value;

    $('#q_date').datepicker({
        dateFormat: 'yy-mm-dd',
        showOn: "button",
        buttonImage: calendarurl,
        buttonImageOnly: true
    });

    $('#ad_startdate').datepicker({
        dateFormat: 'yy-mm-dd',
        showOn: "button",
        buttonImage: calendarurl,
        buttonImageOnly: true
    });

    $('#ad_enddate').datepicker({
        dateFormat: 'yy-mm-dd',
        showOn: "button",
        buttonImage: calendarurl,
        buttonImageOnly: true
    });

// 取消已选日期
    var clear = document.getElementById("js_cleardate");
    clear.addEventListener("click", function() {
        document.getElementById("q_date").value = '';
    });

// 修改片头广告
    $("#dialog-1").dialog({
        title: "修改片头广告",
        autoOpen: false,
        modal: true,
        width: 380,
        height: 380,
        maxheight: 420,
        create: function(event, ui) {
            // 加载所有叶子节点业务包
            $.ajax({
                type: 'GET',
                url: $('#js_indexcontroller').val() + 'loadLeafPackage',
                data: {'t':Date.now()}
            }).done(function(resp) {
                var select = document.getElementById("ad_packageid");
                select.innerHTML = '';
                Array.prototype.forEach.call(resp, function(elem, i) {
                    var option = document.createElement("option");
                    option.value = elem.id;
                    option.innerHTML = elem.packagename;
                    select.appendChild(option);
                });
            }).fail(function(jqXHR, textStatus, errorThrown) {
                alert('loadLeafPackage error: ' + errorThrown);
            })
        },
        open: function(event, ui) {
            // 加载待编辑的片头广告信息
            $.ajax({
                type: 'get',
                data: {'id':id},
                url: baseurl + 'getPrerollPut'
            }).done(function(obj) {
                Object.keys(obj).forEach(function(key, idx) {
                    $('#ad_' + key).val(obj[key]);
                });
            }).fail(function(jqXHR, textStatus, errorThrown) {
                alert('getPrerollPut ajax error: ' + errorThrown);
            });
            // workaround: handle scroll
            document.body.style.overflowX = 'hidden';
        },
        beforeClose: function(event, ui) {
            console.log("about to close...");
        },
        close: function(event, ui) {
            // restore body css
            document.body.style.overflowX = 'visible';
        },
        buttons: [
            {
                text: "确定",
                icons: {primary: "ui-icon-check"},
                click: function() {
                    // #dialog-1 > .dialog-body > form
                    var form = this.firstElementChild.firstElementChild;
                    $.ajax({
                        type: form.method,
                        data: $(form).serialize(),
                        url: form.action
                    }).done(function(resp) {
                        if (resp.code == 0) { // success
                            location.reload();
                        } else {
                            switch(resp.code) {
                                case 1: $("#ad_startdate").next().next().html(resp.msg); break;
                                case 2: $("#ad_enddate").next().next().html(resp.msg); break;
                                default: console.log(resp); alert('修改投放失败: ' + resp.msg);
                            }
                        }
                    }).fail(function(jqXHR, textStatus, errorThrown) {
                        alert('/editPut ajax error: ' + errorThrown);
                    });
                    // form.submit();
                }
            },
            {
                text: "取消",
                icons: {primary: "ui-icon-arrowreturnthick-1-w"},
                click: function() {
                    $(this).dialog("close");
                }
            }]
    });

// 出错提示
    $('#dialog-4').dialog({
        width: 320,
        height: 250,
        autoOpen: false,
        modal: true,
        buttons: [{
            text: "OK",
            icons: {primary: "ui-icon-arrowreturnthick-1-w"},
            click: function() {
                $(this).dialog("close");
            }
            // showText: false,
        }]
    });

// prompt delete
    $('#dialog-2').dialog({
        width: 320,
        height: 250,
        autoOpen: false,
        modal: true,
        open: function() {},
        close: function() {},
        buttons: [
            {
                text: "确定",
                icons: {primary: "ui-icon-trash"},
                click: function(event, ui) {
                    event.preventDefault();
                    /**
                     * 批量撤销片头广告投放
                     */
                    var form = document.createElement("form");
                    form.method = 'post';
                    form.action = baseurl + 'cancelPush';
                    document.body.appendChild(form);

                    // 片头广告投放 id string joined by ','
                    var input = document.createElement("input");
                    input.setAttribute("type", "hidden");
                    input.setAttribute("name", 'ids');
                    input.value = g_ids;
                    form.appendChild(input);

                    // 片头/暂停 广告投放表名
                    input = document.createElement("input");
                    input.setAttribute("type", "hidden");
                    input.setAttribute("name", 'table');
                    input.value = 'AdPrerolladpushstatus';
                    form.appendChild(input);

                    form.submit();
                }
            },
            {
                text: "取消",
                icons: {primary: "ui-icon-arrowreturnthick-1-w"},
                click: function() {
                    $(this).dialog("close");
                }
                // showText: false,
            }
        ]
    });
});

function editPrerollPut(obj) {
    id = obj.parentNode.parentNode.getAttribute("data-id");
    $("#dialog-1").dialog('open');
}

/**
 * 撤销投放
 * @returns {boolean}
 */
function cancelPush() {
    var checkboxes = document.getElementsByName("PutPreRollID[]");

    var v = [];
    Array.prototype.forEach.call(checkboxes, function(checkbox, i) {
        if (checkbox.checked) {
            // @table:  MBIS_Server_Ad_PreRollAdPushStatus
            // @column: ID
            v.push(checkbox.value);
        }
    });
    if (v.length === 0) {
        $('#js_error').text('未选中任何广告!');
        $('#dialog-4').dialog("open");
        return false;
    }
    g_ids = arraytostring(v);
    // prompt 确定对选中广告投放执行删除操作？  400 * 100  #dialog-2
    $('#dialog-2').dialog('open');
}
