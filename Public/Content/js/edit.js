/**
 * Created by Administrator on 2017-01-20.
 * lib: 内容管理 / 内容部编辑
 */
$(document).ready(function() {
    // 填充内容编辑滚动列表空白行
    $('.table-edit').each(function () {
        padScrollTable(this, 16, 5);
    });

    // init jquery ui buttons
    $("#js-cut").button();
    $("#js-submit").button();
    $("#js-save-draft").button();
    $("#add-appendix").button().on("mouseleave", function() {

    }).on("focus", function() {

    });

    // initialize dialogs
    $("#loadingCut").dialog({
        modal: true,
        autoOpen: false,
        width: 250,
        height: 100
    });
    $("#loadingSubmit").dialog({
        modal: true,
        autoOpen: false,
        width: 300
    });
    $("#requireContent").dialog({
        modal: true,
        autoOpen: false,
        width: 350
    });
    $("#requireCut").dialog({
        modal: true,
        autoOpen: false,
        width: 350,
        buttons: {"确定" : function () {$(this).dialog("close");}}
    });
    $("#alreadyCut").dialog({
        modal: true,
        autoOpen: false,
        width: 350,
        buttons: {"确定" : function () {$(this).dialog("close");}}
    });
    $("#DialogSubmitSuccess").dialog({
        modal: true,
        autoOpen: false,
        width: 300,
        buttons: {
            // 提交备播之后的媒体
            "确定" : function() {
                unsetCookie("oid");
                window.location.reload();
            }
        }
    });

    // 分页跳转
    var jump = document.getElementById('js-jumppage');
    if (typeof jump !== 'undefined' && jump != null) {
        jump.addEventListener('click', function() {
            var input = this.previousSibling;
            // TEXT_NODE
            if (input.nodeType == 3) {
                input = input.previousSibling;
            }
            // @QueryString <= function.js
            // document.baseURI = "http://localhost/PushUI/index.php/Content/Edit/index.html?p=1";
            QueryString.p = input.value;
            var obj = document.getElementById('js-totalpage');
            var totalpage = 1;
            if (typeof obj !== 'undefined') {
                totalpage = obj.innerHTML;
            } else {
                console.log('#js-totalpage is undefined');
            }
            if (QueryString.p > totalpage) {
                QueryString.p = totalpage;
                console.log('page number is out of range');
            } else if (QueryString.p < 1) {
                QueryString.p = 1;
            }

            var baseurl = document.baseURI.split('?')[0];
            var getstr = "";
            for (var key in QueryString) {
                if (QueryString.hasOwnProperty(key)) {
                    getstr += key + '=' + QueryString[key] + '&';
                }
            }
            // trim last '&'
            getstr = getstr.slice(0, -1);
            location.href = baseurl + '?' + getstr;
        });
    }

    // Ctrl + S 保存草稿
    document.onkeydown = function(event) {
        event = event||window.event;
        if (event.ctrlKey && event.keyCode== 83) {
            event.returnvalue = false;
            setTimeout(function() {
                saveDraft();
            }, 1);
            return false;
        }
    };
});

/**
 * pad table [table] to [row] rows and [col] cols
 * @param table   DOM <table> or: <tbody>
 * @param row
 * @param col
 * @returns {boolean}
 */
function padScrollTable(table, row, col) {
    if (table === undefined) {
        return false;
    }
    var tbody = null;
    if (table.tagName === "TABLE") {
        tbody = $(table).children('tbody').first();
    } else if (table.tagName === "TBODY") {
        tbody = $(table);
    } else {
        console.log("expect tagName TABLE or: TBODY");
        return false;
    }
    var trs = tbody.children('tr');
    if (trs.length > row) {
        return true;
    }
    var n = row - trs.length;
    while (n--) {
        var tr = $('<tr></tr>').addClass('dumb');
        var td = $('<td></td>').attr('colspan', col);
        tr.append(td).appendTo(tbody);
    }
}

/**
 * 内容编辑 左边列表删除
 * @param obj  <a href="javascript:;"></a>
 * @param mediatypeid int
 */
function delContentClick(obj, mediatypeid) {
    var oid = $(obj).parent().parent().attr("data-oid");
    if(!confirm("确定要删除吗")) {
        return false;
    }
    var baseurl = $("#js-baseurl").val();
    if (baseurl == '' || baseurl == null) {
        baseurl = getCookie("baseurl");
        baseurl = baseurl.replace(/%2F/g, "/");
    }
    var url = baseurl + "/delContent";
    var data = {
        "oid" : oid,
        "mediatypeid" : mediatypeid
    };
    $.ajax({
        type: "GET",
        url: url,
        data: data
    }).done(function () {
        unsetCookie("oid");
        window.location.reload();
    });
}

/**
 * 加载附件列表 <tbody id="js-appendix" ...>
 * @param oid
 * @param targetThumb  type: DOM   <img src="xxx" />
 * @param targetPoster  type: DOM  <img src="xxx" />
 */
function getAppendixList(oid, targetThumb, targetPoster) {
    if (typeof baseurl == 'undefined') {
        var baseurl = $("#js-baseurl").val();
    }
    $.ajax({
        type : 'post',
        url : baseurl + '/getAppendixList',
        data : {'oid': oid},
        cache: false
    }).done(function (resp) {
        if (!Array.isArray(resp)) {
            alert('get appendix failed!');
            return false;
        }

        // 附件图片src初始化
        targetThumb.src = "";
        targetPoster.src = "";

        //  /PushUI
        var root = $("#project_root").get(0).value;
        var appendixurl = '';

        // 加载附件列表
        var html = "";
        Array.prototype.forEach.call(resp, function (elem, i) {
            if (elem.type == "缩略图") {
                targetThumb.src = appendixurl + elem.url;
            } else if (elem.type == "海报") {
                targetPoster.src = appendixurl + elem.url;
            } else {
                console.error("Unexpected appendix type!");
            }
            var name = elem.filename;
            if (name.length > 20) {
                name = name.substring(0,20) + "...";
            }
            html += "<tr data-id='"+elem.id+"' data-url='"+elem.url+"'>";
            html +=
                "<td class='col-0'><div class=\"tr_arrow\"></div></td>" +
                "<td class='col-1'>" + parseInt(i+1) + "</td>" +
                "<td class='col-2' title='"+ elem.filename +"'>"+ name +"</td>" +
                "<td class='col-3'>"+ elem.size +"</td>" +
                "<td class='col-4'>"+ elem.type +"</td>" +
                "<td class='col-5'><a href=\"javascript:void(0)\" onclick='removeAppendix(this)'>删除</a></td>";
            html += "</tr>";
        });
        $("#js-appendix").html(html);

    })
    .fail(function(jqXHR, textStatus, errorThrown) {
        alert(textStatus + ": " + errorThrown);
    });
}

/**
 * 删除附件  obj
 * @param obj <a href="javascript:void(0)" onclick="removeAppendix(this)">删除</a>
 */
function removeAppendix(obj) {
    if (typeof baseurl == "undefined") {
        var baseurl = $("#js-baseurl").val();
    }
    var $tr = $(obj).parent("td").parent("tr");
    var data = {
        "id"  : $tr.attr("data-id"),
        "url" : $tr.attr("data-url")
    };
    $.ajax({
        type: 'POST',
        url : baseurl + '/removeAppendix',
        data : data
    }).done(function(resp, textStatus, jqXHR) {
        console.log(resp);

        if (1 == resp.file) {
            // 删除附件列表一行
            $(obj).parent().parent().remove();
            // 削除缩略图 / 海报
            var type = $(obj).parent().prev().html();
            if (type == "缩略图") {
                document.getElementById("js-thumb").src = "";
            } else if (type == "海报") {
                document.getElementById("js-poster").src = "";
            }
            return true;
        }
        alert("删除附件失败");
        return false;
    })
    .fail(function(jqXHR, textStatus, errorThrown) {
        alert("removeAppendix: " + textStatus + ": " + errorThrown);
    })
}

/**
 * 备播
 * @param oid
 */
function doCut(oid) {
    var baseurl = $("#js-baseurl").val();

    $.ajax({
        type: 'GET',
        url: baseurl + '/cut',
        data: {'oid' : oid}
    }).done(function (resp) {
        // 已经备播?
        var slice = document.getElementById("js-slicestatus");
        var path = resp.path;
        var part = resp.part;

        if (resp.code != 0) {
            $("#loadingCut").dialog("close");
            slice.innerHTML = "<font color='red'>备播失败!</font>";
            $('#js_cut_feedback').html(resp.msg);
            $('#dialog-cutfailed').dialog('open');
            setTimeout(function() {
                slice.innerHTML = "未备播";
            }, 5000);
        } else if (resp.code == 2) {
            // 'code'=>2, 'msg'=>'已经备播 不再切分
            $('#js_cut_feedback').html(resp.msg);
            $('#dialog-cutfailed').dialog('open');
            setTimeout(function() {
                slice.innerHTML = "已备播";
            }, 5000);
        } else {
            // 最多轮询30次
            var maxCheck = 30;
            // 查询备播状态
            var i = 0;
            var checkSliceStatus = function() {
                if (i < maxCheck) {
                    var data = {
                        "oid":oid,
                        "path":path,
                        "part":part
                    };
                    $.ajax({
                        type: "POST",
                        data: data,
                        url: baseurl + '/cutStatus'
                    }).done(function(res) {
                        if (res.code == 1) {
                            // close loading dialog
                            $("#loadingCut").dialog("close");
                            // open success information dialog
                            var $s = $('#alreadyCut');
                            $s.dialog("open");
                            setTimeout(function() {
                                $s.dialog("close");
                            }, 3000);
                            // update cut status on page
                            slice.innerHTML = "<font color='green'>已备播</font>";
                        } else if (res.code == -1) {
                            $("#loadingCut").dialog("close");
                            $('#js_cut_feedback').html('备播失败');
                            $('#dialog-cutfailed').dialog('open');
                            setTimeout(function() {
                                slice.innerHTML = "未备播";
                            }, 5000);
                        } else {  // 0,2
                            // 没有备播完 wait...
                            i++;
                            setTimeout(checkSliceStatus, 3000);  // loop
                        }
                    });
                } else {
                    $('#loadingCut').dialog("close");
                    $('#js_cut_feedback').html("备播超时!");
                    $('#dialog-cutfailed').dialog('open');
                }
                return 0;
            };
            checkSliceStatus();
        }

    }).fail(function(jqXHR, textStatus, errorThrown) {
        $("#loadingCut").dialog("close");
        alert(textStatus + ": " + errorThrown);
    });
    $("#loadingCut").dialog("open");
    var slice = document.getElementById("js-slicestatus");
    slice.innerHTML = "备播中...";
}

/**
 * 提交编辑过的媒体内容, (MBIS_Server_EditStatus editstatus=2)
 */
function submitEditMedia() {
    // 先保存草稿
    var oid = getCookie("oid");

    if (oid == "") {
        alert("页面已过期 请重新载入");
        return false;
    }

    if (!saveDraft()) {
        alert("保存草稿失败!");
        return false;
    }

    // 检查 是否备播
    var baseurl = $("#js-baseurl").val();
    var url = baseurl + "/getSliceStatus";
    var data = {"oid" : oid};

    $.get(url, data, function(resp) {
        if (resp != 1) {
            var dialog = $("#requireCut");
            dialog.dialog("open");
            setTimeout((function() {dialog.dialog("close");}).bind(null, dialog), 3000);
            return false;
        }
        // 已备播
        var dialogLoading = $("#loadingSubmit");
        dialogLoading.dialog("open");

        // MBIS_Server_EditStatus editstatus改为2
        $.ajax({
            type: "post",
            url: baseurl + "/submitMedia",
            data: {"oid":oid}
        }).done(function(resp, textStatus, jqXHR) {
            dialogLoading.dialog("close");
            if (resp > 0) {
                var dialog = $("#DialogSubmitSuccess");
                dialog.dialog("open");
                return true;
            } else {
                alert("提交失败");
                return false;
            }
        }).fail(function(jqXHR, textStatus, errorThrown) {
            alert("checkSliceStatus: " + textStatus + ": " + errorThrown);
        });
        // end $.ajax
        return true;
    });   // end $.get

    return false;
}

/**
 * 总集页面 加载附件内容列表
 * target: #js-appendix
 * @param data:
 * [
 * {'id':724,'attachoid':'xxx', 'appendixtypeid'...},
 * {'id':725,'attachoid':'xxx', 'appendixtypeid'...}
 * ]
 */
function renderAppendixList(data) {
    var target = document.getElementById("js-appendix");
    if (typeof target === "undefined" || target === null) {
        console.log("target #js-appendix is undefined or null");
        return false;
    }
    target.innerHTML = "";

    var html = "";
    Array.prototype.forEach.call(data, function(elem, i) {
        html += "<tr data-id='"+elem.id+"' data-url='"+elem.url+"'>";
        html +=
            "<td class='col-0'><div class=\"tr_arrow\"></div></td>" +
            "<td class='col-1'>" + parseInt(i+1) + "</td>" +
            "<td class='col-2' title='"+ elem.filename +"'>"+ elem.filename +"</td>" +
            "<td class='col-3'>"+ elem.size +"</td>" +
            "<td class='col-4'>"+ elem.type +"</td>" +
            "<td class='col-5'><a href=\"javascript:void(0)\" onclick='removeAppendix(this)'>删除</a></td>";
        html += "</tr>";
    });
    target.innerHTML = html;

    var table = document.getElementById("EditSeriesAppendixList");
    padScrollTable(table, 4, 6);

    return true;
}

/**
 * 刷新页面 点击左边列表
 * @param rows  jQuery obj  <tr>
 * @param name string "oid"/"soid"
 */
function triggerClick(rows, name) {
    if (null == name || '' == name) {name = "oid";} // default
    if (rows.length > 0) {
        var cookieoid = getCookie(name);

        if (cookieoid == '') {
            rows.first().trigger('click');
        } else {
            for (var i = 0; i < rows.length; i++) {
                var oid = rows[i].getAttribute("data-" + name);
                if (null == oid) {
                    oid = rows[i].getAttribute(name);
                }
                if (oid == cookieoid) {
                    $(rows[i]).trigger('click');
                    break;
                }
            }
            if (i == rows.length) {rows.first().trigger('click');}
        }
        console.log(cookieoid);
    }
}


/**
 * 专题节目 单集/总集 属性操作
 * @param tbodyid   输出位置tbody dom id
 * @constructor
 */
function Attributes (tbodyid) {
    var tbody = $("#" + tbodyid);
    var dlg = document.getElementById("dlg_prompt_attr");

    // 点击删除badge圆圈删除这个属性
    this.del = function () {
        var target = tbody.find('.badge');
        target.on("click", function () {
            $(this).parent('a').parent('td').parent('tr').remove();
        });
    };

    // 添加属性
    this.add = function(triggerid) {
        $('#' + triggerid).on("click", function() {
            // prompt input attr name
            $(dlg).dialog("open");
        });
    };

    this.doAdd = function(data) {
        console.log(data);

        var title = data.name;
        var name = 's_attr_' + data.id;
        // target
        var tr = $("<tr>").addClass("optional").appendTo(tbody);

        var label = $("<label>").attr("for", name).html(title + ":");
        $("<td>").append(label).appendTo(tr);

        var input = $("<input>").attr("id", name).attr("name", name).attr("data-attrid", data.id).attr("type", "text");
        var badge = $("<a href=\"javascript:;\"><span class=\"badge\">---</span></a>");
        $('<td>').append(input).append(badge).appendTo(tr);

        badge.on("click", function() {
            tr.remove();
        });
    };

}

function issoid(oid) {
    var pat = new RegExp("S[0-9]{31}");
    return pat.test(oid);
}

/**
 FormData上传文件 form指定action为处理上传文件的url
 handler为上传完了的操作
 */
function filexhrupload(form, handler) {
    var formdata = new FormData(form);

    $.ajax({
        url: form.action,
        type: "POST",
        data: formdata,
        cache: false,
        contentType: false,
        processData: false,
        dataType: "html"
    }).done(function(data, textStatus, jqXHR) {
        try {
            var json = JSON.parse(data);
            if (0 != json.code) {
                alert(json.msg);
                return false;
            }
        } catch (e) {
            alert("try parsing data failed!");
            console.error(e);
            console.log(data);
        }
        handler();
    }).fail(function( jqXHR, textStatus, errorThrown) {
        alert("附件上传 ajax 通信失败: " + errorThrown);
    });
}

/**
 * 取得二维码
 * @param oid
 * @param mediatype
 * @param domid
 */
function getQrCode(oid, mediatype, domid) {
    /*
    var o = {
        'oid':oid,
        'type':mediatype  // ['movie', 'tv', 'show']
    };
    var prefix = location.href.substr(0, location.href.lastIndexOf("/"));
    document.getElementById(domid).src = prefix + '/mediaQRCode?' + $.param(o);
    */
    $.get('getQrCode', {'oid':oid, 'type':mediatype}, function(data) {
        document.getElementById(domid).src = data.url;
    });
}

/**
 * 保存二维码
 * @param oid
 * @param mediatype
 */
function saveQrCode(oid, mediatype) {
    var data = {
        'oid':oid,
        'type':mediatype  // ['movie', 'tv', 'show']
    };
    $.ajax({
        type: 'GET',
        url: 'saveQrCode',
        data: data
    }).done(function(data) {

    });
}