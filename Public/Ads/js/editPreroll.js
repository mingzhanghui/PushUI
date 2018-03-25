/**
 * Created by Administrator on 2017-02-14.
 * 广告编辑 / 片头广告
 */
var contentid = null;
var adid = null;
var baseurl = document.getElementById("js_baseurl").value;

$(function() {
    // fill table: table, cols, rows
    var table = document.getElementById("PreRollTable");
    var rows = table.firstElementChild.children;
    var UpdatePreRollForm = document.getElementById("UpdatePreRollForm");

    Array.prototype.forEach.call(rows, function(tr, i) {
        tr.addEventListener("click", function() {
            var s = table.firstElementChild.querySelector('.selected');
            s && s.classList.remove("selected");
            this.classList.add("selected");
            // show aside
            contentid = this.getAttribute("data-contentid");
            adid = this.getAttribute('data-adid');
            getPrerollAdMedia(adid);

            UpdatePreRollForm.firstElementChild.value = adid;
        });
    });

    // 取消片头广告修改
    var cancel = document.getElementById("js_cancel");
    cancel.onclick = function() {
        getPrerollAdMedia(adid);
    };

    tablepad($(table), 8, 10);

    // 投放广告
    $('#dialog-2').dialog({
        width: 360,
        height: 380,
        maxheight: 420,
        autoOpen: false,
        modal: true,
        create: function(event, ui) {
            // 加载所有叶子节点业务包
            $.ajax({
                type: 'GET',
                url: baseurl + 'loadLeafPackage',
                data: {'t':Date.now()}
            }).done(function(resp) {
                var select = document.getElementById("put_packageid");
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
            document.body.style.overflow = 'hidden';
            // 填充 广告ID & 广告名称 & 文件名称
            var table = document.getElementById("PreRollTable");
            var rows = table.children[0].children;
            var name, advertisename;
            for (var i = 0; i < rows.length; i++) {
                if (rows[i].getAttribute("data-contentid") === contentid) {
                    name = rows[i].querySelector(".td-1").getAttribute("title");
                    advertisename = rows[i].querySelector(".td-2").getAttribute("title");
                }
            }
            document.getElementById("put_advertisename").value = advertisename;
            document.getElementById("put_name").value = name;
        },
        beforeClose: function(event, ui) {},
        close: function(event, ui) {
            var form = this.children[0].children[0];
            form.reset();
            $(this).find('.help-block').html('');
            document.body.style.overflow = 'visible';
        },
        buttons: [{
            text: "确定",
            icons: {primary: "ui-icon-check"},
            click: function() {
                // submit form
                var dlg = this;
                var form = this.children[0].children[0];
                var input = form.firstElementChild;
                input.value = adid;  // AdPrerolladmedia  id, AdPrerollAdpushstatus adid

                var data = $(form).serialize();
                $.ajax({
                    type: "POST",
                    url: baseurl + 'putAd',
                    data: data
                }).done(function (resp) {
                    if (resp.code == 0) { // success
                        $(dlg).dialog("close");
                        $("#dialog-3").dialog("open");
                    } else {
                        switch(resp.code) {
                            case 1: $("#put_startdate").next().next().html(resp.msg); break;
                            case 2: $("#put_enddate").next().next().html(resp.msg); break;
                            default: console.log(resp); alert('投放失败: ' + resp.msg);
                        }
                    }
                }).fail(function(jqXHR, textStatus, errorThrown) {
                    alert('putAd failed: ' + errorThrown);
                });
            }
        }, {
            text: "取消",
            icons: {primary: "ui-icon-arrowreturnthick-1-w"},
            click: function() {
                $(this).dialog("close");
            }
        }]
    });

    // 投放广告成功
    $('#dialog-3').dialog({
        width: 300,
        height: 200,
        autoOpen: false,
        modal: true,
        buttons: [{
            text: "确定",
            icons: {primary: "ui-icon-check"},
            click: function() {
                $(this).dialog("close");
            }
            // showText: false,
        }]
    });

    // 删除广告
    var del = document.getElementById("js_btn_2");
    del.onclick = function() {
        var ads = document.getElementsByName("PreRollID[]");
        var v = [];
        Array.prototype.forEach.call(ads, function(ad, i) {
           if (ad.checked) {
               v.push(ad.value);
           }
        });
        if (v.length === 0) {
            $('#js_error').text('未选中任何广告!');
            $('#dialog-4').dialog("open");
            return false;
        }
        // 广告是否投放过了
        delPrerollAd(v);

        return true;
    };

    // 出错提示
    $('#dialog-4').dialog({
        width: 300,
        height: 200,
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
});

/**
 * 检查preroll广告是否投放
 * @param v
 */
function delPrerollAd(v) {
    var s = arraytostring(v);

    $.ajax({
        type: 'POST',
        data: {'s':s},
        url: baseurl + 'hasPrerollAdPushStatus',
        cache: 'false'
    }).done(function(resp) {
        if (resp.hasPush) {
            $('#js_error').text(resp.name +'有'+ resp.count + '条未投放完成的广告');
            $("#dialog-4").dialog("open");
            return;
        }
        doDelPrerollAd(s);
    }).fail(function(jqXHR, textStatus, errorThrown) {
        alert('delPrerollAd ajax error: ' + errorThrown);
    });
}

/**
 * 批量删除片头广告
 * @param s  id1,id2,...idn
 */
function doDelPrerollAd(s) {
    var form = document.createElement("form");
    form.method = 'post';
    form.action = baseurl + 'delPrerollAd';
    document.body.appendChild(form);

    var input = document.createElement("input");
    input.setAttribute("type", "hidden");
    input.setAttribute("name", 's');
    input.value = s;
    form.appendChild(input);

    form.submit();
}

function putad(obj) {
    var tr = obj.parentNode.parentNode;
    adid = tr.getAttribute("data-adid");
    contentid = tr.getAttribute("data-contentid");
    $("#dialog-2").dialog("open");
}

function getPrerollAdMedia(adid) {
    $.ajax({
        type: 'GET',
        url: baseurl + 'getPrerollAdMedia',
        data: {'id':adid}
    }).done(function(obj) {
        Object.keys(obj).forEach(function(key, idx) {
            $("#up_" + key).val(obj[key]).attr("title", obj[key]);
        });
        // $("#up_url").attr("title", obj['url']);  // url input 长度不够
    }).fail(function(jqXHR, textStatus, errorThrown) {
        console.error('getPrerollAdMedia ajax error[]'+textStatus+': ' + errorThrown);
    });
}
