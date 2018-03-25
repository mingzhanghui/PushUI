/**
 * Created by Administrator on 2017-02-04.
 * 业务管理页面  depends on package.js
 */
var g_url, g_linkid;

$(document).ready(function() {
    // 加载业务包列表
    loadPackageTree(function() {
        var obj = document.getElementById("js-package-tree");
        var aa = $(obj).find("a");   // 所有业务包<a>数组
        Array.prototype.forEach.call(aa, function(a) {
            a.addEventListener("click", function () {
                var that = $(this);
                // fold/unfold tree
                var ul = that.next('ul');
                var icon = that.prev();

                if (0 < icon.length) {
                    if (icon.hasClass('ui-icon-plusthick')) {
                        icon.removeClass('ui-icon-plusthick').addClass('ui-icon-minusthick');
                        ul.show();
                    } else if (icon.hasClass('ui-icon-minusthick')) {
                        icon.removeClass('ui-icon-minusthick').addClass('ui-icon-plusthick');
                        ul.hide();
                    }
                }

                // 选中的业务包
                that.addClass("selected");
                aa.not(that).removeClass("selected");

                var li = that.parent("li");
                var pkgid = li.attr("data-id");

                // show每个业务包只读信息
                load_package_info(pkgid);

                // 清空所含内容列表
                $("#this_mission").empty();

                // 所含业务期
                document.getElementById("js-missioninfo").reset();
                list_package_mission(pkgid);
                // 修改业务期
                document.getElementById("m_pid").value = pkgid;
            });
        });

        if (aa.length > 0) {
            aa.first().trigger("click");
        }
    });

    // 填充所含业务期列表
    padScrollTable(document.getElementById("js-PackageMission"), 7, 5);

    // 添加新节目dialog  本期内容列表 => 新增内容 => 添加新的节目
    $("#dlg_add_media").dialog({
        title: "添加新的节目",
        autoOpen: false,
        modal: true,
        width: 560,
        minheight: 300,
        resizable: true,
        closeOnEscape: true,
        create: function(event, ui) {
            list_mediatype();

            // 查找
            var form = document.getElementById("js-formsearch");
            form.addEventListener("submit", function(e) {
               e.preventDefault();
            });

            $("#js-searchcontent").on("click", content_search);
        //    选择播发日期
        },
        open: function(event, ui) {
            // 设置新节目类型
            var templateid = document.getElementById("pkg_packagetemplateid").value;
            if (templateid == "0") {
                var dlg = this;
                console.error("业务包模板未知!");
                setTimeout(function() {
                    $(dlg).dialog("close");
                }, 100);
                alert("没有选择业务包");
                return false;
            }
            var mediatypeidlist = [];
            switch (templateid) {
                case "1": // 新闻类
                    mediatypeidlist = [4];   // 热点推送视频
                    break;
                case "2":  // 电影戏曲类
                    mediatypeidlist = [1, 7];  // 电影 + 戏曲
                    break;
                case "3": // 电视剧类
                    mediatypeidlist = [2];   // 电视剧分集
                    break;
                case "4": // 综艺节目类
                    mediatypeidlist = [3];     // 电视节目分集
                    break;
                case "5":  // 专题节目类
                case "6":   // 教育类
                    mediatypeidlist = [9];     // 专题节目分集
                    break;
                default:
                    console.error("unexpected templateid!");
            }
            if (mediatypeidlist.length == 0) {
                alert("Unexpected empty mediatypelist");
                return false;
            }
            var select = document.getElementById("js-mediatypeid");
            console.log(mediatypeidlist);

            if (mediatypeidlist.length == 1) {
                select.value = mediatypeidlist[0];
                select.setAttribute("disabled", true);
                select.style.cursor = "not-allowed";
            } else {
                // 业务包为 电影戏曲类
                var options = [];
                Array.prototype.forEach.call(select.children, function(elem, i) {
                    var v = Number(elem.value);
                    // mediatypelist contains v
                    if (mediatypeidlist.indexOf(v) >= 0) {
                        options.push(elem);
                    }
                });
                select.hasAttribute("disabled") && select.removeAttribute("disabled");
                select.style.cursor = "default";

                // <option value="1">电影</option>
                // <option value="7">戏曲</option>
                select.innerHTML = "";
                options.forEach(function(option) {
                   select.appendChild(option);
                });
            }
            // 可以添加的节目 文件名 类型查找
            content_search();
        },
        beforeClose: function(event, ui) {
            var tbody = document.getElementById("snc_tbody");
            tbody.innerHTML = "";
        },
        close: function(event, ui) {
            list_mediatype();   // 业务包 电影戏曲类 select > option 有删除
        },
        buttons: [
            {
                text: "提交保存",
                icons: {
                    primary: "ui-icon-check"
                },
                click: function() {
                    var form = document.getElementById("form_broadcast");
                    var a = form.action;
                    var url = a.substr(0, a.lastIndexOf('/')) + "/addContent";
                    // data: array('missionid', 'mediaoid', 'date');

                    var oidlist = [];
                    Array.prototype.forEach.call(document.getElementsByName("MediaOID[]"), function(elem) {
                        if (elem.checked) {
                            oidlist.push(elem.value);
                        }
                    });
                    var missionid = getmissionid();
                    var data = {
                        "mediaoidlist" : oidlist,
                        "date": document.getElementById("c_date").value,
                        "round": document.getElementById("c_round").value,
                        "missionid": missionid
                    };
                    var dlg = this;

                    $.ajax({
                        type: "POST",
                        url: url,
                        data: data
                    }).done(function(data) {
                        if (data.code < 0) {
                            alert(data.error);   // エーラーが発生しました
                            return;
                        }
                        // refresh 本期内容列表
                        list_mission_link_media(missionid);

                        $(dlg).dialog("close");

                    }).error(function(jqXHR, textStatus, errorThrown) {
                        alert("xhr " + url + "error: " + errorThrown)
                    });
                }
            },
            {
                text: "取消",
                icons: {
                    primary: "ui-icon-close"
                },
                click: function() {
                    $(this).dialog("close");
                }
            }
        ]
    });

    // 只有叶子节点才能创建业务期!
    $("#DialogErrorMission").dialog({
        autoOpen: false,
        width: 300,
        modal: true,
        close: function() {
            document.getElementById("js-addmission").innerHTML = "添加业务期";
        },
        buttons: [{
            text: 'OK',
            click: function() { $(this).dialog("close"); }
        }]
    });

    // dialog => button: "添加业务期"
    $('#dlg_add_mission').dialog({
        autoOpen: false,
        width: 580,
        modal: true,
        create: function() {
            // 清空添加业务期表单
            var form = document.getElementById("addMissionInfo");
            form.reset();
            $("#addPackageID").attr("disabled", "disabled").css({"cursor":"not-allowed"});

            // 设定业务包名dropdown 所有叶子节点的业务包名
            var url = document.getElementById("js-subscribe-url").value + "/listNodePackages";

            $.ajax({
                type: "GET",
                url: url,
                data: {"t":Date.now()}
            }).done(function(data, textStatus, jqXHR) {
                var select = document.getElementById("addPackageID");
                select.innerHTML = "";   // init

                Array.prototype.forEach.call(data, function(elem, i) {
                    var option = document.createElement("option");
                    option.value = elem.id;
                    option.innerHTML = elem.packagename;
                    select.appendChild(option);
                });

            }).fail(function(jqXHR, textStatus) {
                alert("Request listNodePackages failed: " + textStatus);
            })
        },

        open: function() {
            var a = $("#js-package-tree").find(".selected").parent();
            var packageid = a.attr("data-id");
            $("#addPackageID").val(packageid);

            document.getElementById("js-help-start").innerHTML = "";
            document.getElementById("js-help-end").innerHTML = "";
        },
        close: function() {document.getElementById("addMissionInfo").reset();},
        buttons: [
            {
                text: '提交保存',
                icons: {primary: "ui-icon-check"},
                click: function() {    // 添加新的业务期处理函数
                    // check form data
                    var datecheck = new Datecheck();
                    if (!datecheck.handle()) {
                        console.log("unexpected date");
                        return false;
                    }

                    // enable PackageID  => PackagelinkMission
                    $("#addPackageID").removeAttr("disabled");
                    var form = document.getElementById("addMissionInfo");
                    var data = $(form).serialize();

                    // => Mission
                    var url = $("#js-subscribe-url").val() + "/addMission";

                    var dlg = this;
                    $.ajax({
                        type: "POST",
                        url: url,
                        data: data
                    }).done(function(data, textStatus, jqXHR) {
                        // written to db!
                        if (data.missionid > 0 && data.linkid > 0) {
                            // reload 所含业务期列表 from package.js
                            $(dlg).dialog("close");
                            list_package_mission(data.packageid);

                            form.reset();
                            // 检查日期范围
                            Datecheck.prototype.loadRange();

                            // add mission done
                            document.getElementById("js-addmission").innerHTML = "添加业务期";

                        } else {
                            alert("添加业务期失败: " + textStatus);
                        }
                    }).fail(function(jqXHR, textStatus, errorThrown) {
                        alert("addMission error: " + textStatus + ". " + errorThrown);
                    });
                    $("#js-addmission").html('添加业务期中...');
                }
            },
            {
                text:'取消',
                icons: {primary: "ui-icon-close"},
                click: function() {
                    $(this).dialog('close');
                    $("#js-addmission").val("添加业务期");
                }
            }
        ]
    });

    // 确定要删除这个播发计划?
    $("#dlg_confirm_delete").dialog({
        autoOpen: false,
        modal: true,
        width: 300,
        height: 250,
        closeOnEscape: true,
        open: function() {},
        close: function() {},
        buttons: [{
            text:"确定",
            icons: {primary: "ui-icon-trash"},
            click: function() {
                var data = {"id":g_linkid};
                $.get(g_url, data, function(resp) {
                    if (resp.code) {
                        if (resp.msg) {
                            alert(resp.msg);
                        } else {
                            alert("删除本期内容失败!");
                        }
                    } else {
                        // alert("删除本期内容成功!");
                        var missionid = getmissionid();
                        list_mission_link_media(missionid);
                    }
                });
                $(this).dialog("close");
            }
        },{
            text: "取消",
            icons: {primary: "ui-icon-arrowreturnthick-1-w"},
            click: function () {
                $(this).dialog("close");
            }
        }]
    });

    // 确定要批量删除这些内容?
    $("#dlg_confirm_bulkDelete").dialog({
        autoOpen: false,
        modal: true,
        width: 300,
        height: 250,
        closeOnEscape: true,
        open: function() {},
        close: function() {},
        buttons: [{
            text:"确定",
            icons: {primary: "ui-icon-trash"},
            click: function() {
                var checkboxes = document.getElementsByName("iscont");
                var v = [];
                Array.prototype.forEach.call(checkboxes, function(checkbox) {
                    if (checkbox.checked) {
                        v.push(checkbox.value);
                    }
                });
                var jsonString = JSON.stringify(v);  // string: ["870","871","872"]
                $.ajax({
                    type: "POST",
                    url: g_url,
                    data: {"data": jsonString},
                    cache: false,
                    success: function(resp) {
                        alert("删除了" + resp + "个内容!");
                        var missionid = getmissionid();
                        list_mission_link_media(missionid);
                    }
                });
                $(this).dialog("close");
            }
        },{
            text: "取消",
            icons: {primary: "ui-icon-arrowreturnthick-1-w"},
            click: function () {
                $(this).dialog("close");
            }
        }]
    });

    // 添加业务期 弹出对话框
    $("#js-addmission").on("click", function() {
        var li = $("#js-package-tree").find(".selected").parent("li");

        if (typeof li.get(0) === "undefined") {
            alert("还没有选择业务包!");
            return false;
        }

        // 只有叶子节点 才能创建业务期
        if (li.hasClass("directory")) {
            var DialogErrorMission = $("#DialogErrorMission");
            DialogErrorMission.dialog("open");
            setTimeout((function() {
                DialogErrorMission.dialog("close");
            }).bind(null, DialogErrorMission), 3000);

        } else if (li.hasClass("file")) {
            // listNodePackage("addPackageID");
            // do add mission
            $("#dlg_add_mission").dialog("open");
        }
    });

    // 删除业务期
    $("#js-delmission").on("click", function() {
        var btn = this;
        var btn_text = btn.innerHTML;

        var mid = $("#js-PackageMission").children(".selected").attr("data-mid");
        if (typeof mid === "undefined") {
            alert("还没有选择业务期!");
            return false;
        }

        var url = document.getElementById("js-subscribe-url").value + "/delMission";
        $.ajax({
            type: "GET",
            url: url,
            data: { "mid": mid }
        }).done(function(data) {   // data, textStatus, jqXHR
            if (data.code < 0) {
                $("#DialogCannotDeleteMission").dialog("open");
                btn.innerHTML = btn_text;  // recover button text
                return false;
            }
            // 重新加载 所含业务期 列表
            var pkgid = $("#js-package-tree").find(".selected").parent("li").attr("data-id");
            list_package_mission(pkgid);

            Datecheck.prototype.loadRange();

            // 清空业务期详情 表单
            var form = document.getElementById("js-missioninfo");
            form.reset();

            btn.innerHTML = btn_text;  // recover button text

        }).fail(function(jqXHR, textStatus) {
            alert("Request delMission failed: " + textStatus);
        });
        btn.innerHTML = "正在删除...";
    });

    // 修改业务期
    $("#js-missioninfo").on("submit", function(e) {
        e.preventDefault();

        var form = this;
        var mid = $(form).find("input[name='id']").val();
        if ("" == mid || typeof mid === "undefined" || null == mid) {
            alert("还没有选择业务期");
            return false;
        }

        $.ajax({
            type: "POST",
            url: form.action,   // /PushUI/index.php/Subscribe/Subscribe/editMission
            data: $(form).serialize()
        }).done(function(data, textStatus, jqXHR) {
            if (data.code < 0) {
                alert(data.msg);
                console.log(data);
                return false;
            }
            // 所含业务期 refresh
            var packageid = $("#m_pid").val();
            list_package_mission(packageid);
            var success = $("#js-mission-success");
            success.show();
            setTimeout((function() {
                success.fadeOut();
            }).bind(null, success), 3000);
            $("#btn-submit").html('修改').css({"opacity":"1"});

            return true;

        }).error(function(jqXHR, textStatus, errorThrown) {
            alert("edit mission xhr fail: " + errorThrown);
        });
        $("#btn-submit").html('提交业务期...').css({"opacity":".8"});
    });
    // 取消修改业务期
    $("#btn-cancel").on("click", function() {
        $("#m_missionname").val('');
        $("#m_startdate").val('');
        $("#m_enddate").val('');
        $("#m_missiondescription").val('');
    });

    // show error/success dialogs
    (function () {
        var elemidlist = ['DialogDateCompare', 'DialogCannotDeleteMission',
            'dlg_cannot_add_content', 'dlg_cannot_edit_content', 'dlg_illegal_date', 'dialog_edit_success'];
        elemidlist.forEach(function(elemid) {
            $("#" + elemid).dialog({
                autoOpen: false,
                width: 320,
                modal: true,
                buttons: [{
                    text: "确定",
                    click: function() { $(this).dialog("close"); }
                }]
            });
        })
    }).call(this);

    // datepicker
    (function () {
        var imgpath = document.getElementById("js-imgpath").value + "/calendar.svg";
        var elementIdList = ['m_startdate', 'm_enddate', 'c_date', 'ed_date', 'datepicker-2', 'datepicker-3'];
        elementIdList.forEach(function(elemid) {
            $("#" + elemid).datepicker({
                dateFormat: 'yy-mm-dd',
                showOn: "button",
                buttonImage: imgpath,
                buttonImageOnly: true
            });
        });
    }).call(this);

    // 本期内容列表 新增内容
    $("#btn-issue").on("click", function() {
        // 检查播发结束日期
        var form = document.getElementById("form_broadcast");
        var url = form.action;
        var missionid = getmissionid();

        if (missionid === undefined || Number(missionid) === 0) {
            alert("业务期没有选择!");
            return false;
        }

        url = url.substr(0, url.lastIndexOf('/')) + '/getMissionInfo?mid=' + missionid;
        $.getJSON(url, function(data) {
            // data.enddate
            var today = gettoday();
            if (today > data.enddate) {
                var dlg = $("#dlg_cannot_add_content");
                dlg.dialog("open");
                setTimeout(function() {
                    dlg.dialog("close");
                }, 3000);
            } else {
                $("#dlg_add_media").dialog("open");
            }
        });
    });

    // 编辑内容 重新设置播发日期 / 轮播次数 <a>修改</a>
    $("#dlg_edit_content").dialog({
        title: "修改本期内容",
        autoOpen: false,
        modal: true,
        width: 560,
        height: 200,
        resizable: true,
        closeOnEscape: true,
        // show: {effect: "fade", duration: 100},
        // hide: {effect: "fade", duration: 100},
        create: function(event, ui) {
            var form = document.getElementById("FormEditContent");
            form.addEventListener("submit", function(e) {
               e.preventDefault();
            });
        },
        open: function(event, ui) {
            document.getElementById("ed_missionid").value = getmissionid();
        },
        beforeClose: function(event, ui) {},
        close: function(event, ui) {},
        buttons: [
        {
            text: "保存",
            icons: {
                primary: "ui-icon-check"
            },
            click: function() {
                var form = document.getElementById("FormEditContent");
                var dlg = this;

                $.ajax({
                    type: "POST",
                    url: form.action,
                    data: $(form).serialize()
                }).done(function(resp) {
                    if (resp.code === 1) {
                        var missionid = getmissionid();
                        list_mission_link_media(missionid);
                        $(dlg).dialog("close");

                        var success = $("#dialog_edit_success");
                        success.dialog("open");
                        setTimeout(function() {
                            success.dialog("close");
                        }, 3000);
                    } else if (resp.code === -1) {
                        var err = $("#dlg_illegal_date");
                        err.dialog("open");
                        setTimeout(function() {
                            err.dialog("close");
                        }, 3000);
                    } else {
                        alert("修改失败!");
                    }
                });
                return true;
            }
        },
        {
            text:"取消",
            icons: {primary: "ui-icon-close"},
            click: function() {
                $(this).dialog("close");
            }
        }]
    });
});

// 添加新的业务期 开始日期 结束日期 检查
function Datecheck() {
    this.start = document.getElementById("datepicker-2");
    this.end = document.getElementById("datepicker-3");
    // reset event bindings
    $(this.start).unbind("change");
    $(this.end).unbind("change");
    this.event();
}

Datecheck.prototype = {
    range: [],

    // 加载已存在业务期的日期范围
    // var range = [{
    //   start: '2017-03-01',
    //   end: '2017-03-06'
    // }, {
    //   start: '2017-04-01',
    //   end: '2017-04-02'
    // }];
    loadRange: function() {
        // 所含业务期表格
        Datecheck.prototype.range = [];
        $("#js-PackageMission").find("td.col-1").each(function() {
            var obj = {
                start: $(this).html(),
                end: $(this).next().html()
            };
            Datecheck.prototype.range.push(obj);
        });
    },

    /* date overlap? */
    dateOverlap: function(start, end) {
        Datecheck.prototype.loadRange();

        for (var i = 0; i < Datecheck.prototype.range.length; i++) {
            if ((start <= Datecheck.prototype.range[i].start && end >= Datecheck.prototype.range[i].start) || (start <= Datecheck.prototype.range[i].end && end >= Datecheck.prototype.range[i].end)) {
                return true;
            }
        }
        return false;
    },

    /* date in range? */
    dateInRange: function(date) {
        Datecheck.prototype.loadRange();

        for (var i = 0; i < Datecheck.prototype.range.length; i++) {
            if (Datecheck.prototype.range[i].start <= date &&
                date <= Datecheck.prototype.range[i].end) {
                console.log({
                    "start": Datecheck.prototype.range[i].start,
                    "date": date,
                    "end": Datecheck.prototype.range[i].end
                });
                return true;
            }
        }
        return false;
    },

    checkDateRange: function() {
        var start = $("#datepicker-2").val();
        var end = $("#datepicker-3").val();
        if (start != '' && end != '') {
            if (Datecheck.prototype.dateOverlap(start, end)) {
                $("#DialogDateCompare").dialog("open");
                return null;
            } else {
                return { 'start': start, 'end': end };
            }
        }
        return null;
    },

    // 检测日期合法
    // 1. 2017-03-01    -> 2017-03-06
    // 2. 2017-04-01    -> 2017-04-02
    // 合法: 2017-03-07  -> 2017-03-31
    checkStartDate: function() {
        var date = $("#datepicker-2").val();
        var helpStart = $("#js-help-start");

        if (date != '') {
            if (Datecheck.prototype.dateInRange(date)) {
                helpStart.html("<span class='text-danger'>开始日期在已有业务期范围内</span>");
                return "";
            } else if (date < gettoday()) {
                helpStart.html("<span class='text-danger'>开始日期不能小于今天</span>");
            } else {
                helpStart.html("<span class='text-success'>日期合法</span>");
            }
        } else {
            helpStart.html("<span class='text-danger'>开始日期不能为空</span>");
            return "";
        }
        var endDate = $("#datepicker-3").val();
        if (endDate != '') {
            // 开始日期 <= 结束日期
            if (date <= endDate) {
                return date;
            } else {
                // $("#DialogDateCompare").dialog("open");
                helpStart.html("<span class='text-danger'>开始日期不能大于结束日期</span>");
                return "";
            }
        }
        return date;
    },

    checkEndDate: function() {
        var date = $("#datepicker-3").val();
        var helpEnd = $("#js-help-end");

        if (date != '') {
            if (Datecheck.prototype.dateInRange(date)) {
                helpEnd.html("<span class='text-danger'>结束日期在已有业务期范围内</span>");
                return "";
            } else if (date < gettoday()) {
                helpEnd.html("<span class='text-danger'>开始日期不能小于今天</span>");
            } else {
                helpEnd.html("<span class='text-success'>日期合法</span>");
            }
        } else {
            helpEnd.html("<span class='text-danger'>结束日期不能为空</span>");
            return "";
        }
        var startDate = $("#datepicker-2").val();
        if (startDate != '') {
            // 开始日期 <= 结束日期
            if (startDate <= date) {
                return date;
            } else {
                // $("#DialogDateCompare").dialog("open");
                helpEnd.html("<span class='text-danger'>开始日期不能大于结束日期</span>");
                return "";
            }
        }
        return date;
    },

    /* 检查所有日期 */
    handle: function() {
        // init
        var checkStart = Datecheck.prototype.checkStartDate();
        var checkEnd = Datecheck.prototype.checkEndDate();
        var checkRange = Datecheck.prototype.checkDateRange();

        return checkStart && checkEnd && checkRange;
    },

    /* datepicker onblur */
    event: function() {
        // change event
        this.start.onblur = function() {
            Datecheck.prototype.checkStartDate();
        };
        this.end.onblur = function() {
            Datecheck.prototype.checkEndDate();
        };
    }
};

function load_package_info(packageid) {
    $.ajax({
        type: "GET",
        url: getindexcontroller() + "/getPackageInfo",
        data: {"id": packageid}
    }).done(function(data) {
        var fields = ['packagename','updatecycletypeid','packagetemplateid', 'packagetypeid', 'price','chargetypeid'];
        Array.prototype.forEach.call(fields, function(field, i) {
            $("#pkg_" + field).val(data[field]);
        });
    }).fail(function() {
        alert("load_package_info[" + packageid + "] failed!");
    });
}

/**
 * 加载所含业务期列表
 * @param packageid   subscribe package ID   eg: 49
 * @returns {boolean}
 */
function list_package_mission(packageid) {

    var subscribe = document.getElementById("js-subscribe-url");

    $.ajax({
        type: "GET",
        url: subscribe.value + "/listPackageMission",
        data: { "packageid": packageid }
    }).done(function(data) {

        var target = document.getElementById("js-PackageMission");
        if (typeof target === "undefined" || target == null) {
            return false;
        }
        // 加载 所含业务期列表
        target.innerHTML = ""; // init
        var tr;

        if (data.length <= 0) {
            // 这个业务包下没有业务期, 清空业务期详情列表
            $('#mis_missionname').val('');
            $('#mis_startdate').val('');
            $('#mis_enddate').val('');
            $('#mis_missiondescription').val('');

            var td = $('<td>').attr('colspan', 6).css({'display':'block'}).html('这个业务包下暂时没有业务期~');
            tr = $('<tr>').append(td);
            $(target).append(tr);

            // 没有业务期 业务期下的内容也要清空
            var content = document.getElementById("this_mission");
            content.innerHTML = "";
            padScrollTable(content, 10, 6);
        } else {
            for (var i = 0; i < data.length; i++) {
                tr = $("<tr>").attr("data-mid", data[i].mid);
                $(target).append(tr);
                // 业务期名, 起始日期, 结束日期, 状态, 选择
                $("<td>").addClass("col-0").html('<div class="tr_arrow"></div>').appendTo(tr);
                $("<td>").addClass("col-1").html(data[i].missionname).attr("title", data[i].missionname).appendTo(tr);
                $("<td>").addClass("col-2").html(data[i].startdate).attr("title", data[i].startdate).appendTo(tr);
                $("<td>").addClass("col-3").html(data[i].enddate).attr("title", data[i].enddate).appendTo(tr);
                var status = (data[i].status > 0) ? "<font class='text-success'>同步成功</font>" : "<font class='text-danger'>未同步</font>";
                $("<td>").addClass("col-4").html(status).attr("title", status).appendTo(tr);
                $("<td>").addClass("col-5").attr("title", "选择").append("<input type='radio' name='period'>").appendTo(tr);
            }
            // load 所含业务期 DONE
            select_mission();
        }

        // 点击tr选择input[type="radio"]
        padScrollTable(target, 7, 5);

    }).fail(function(jqXHR, textStatus) {
        alert("Request listPackageMission failed: " + textStatus);
    });
    return true;
}

/**
 * 所含业务期列表绑定事件
 */
function select_mission() {
    var tbody = document.getElementById("js-PackageMission");
    var rows = $(tbody).children("tr").not(".dumb");

    if (rows.length==0) {
        return false;
    }
    var url = document.getElementById("js-subscribe-url").value;

    Array.prototype.forEach.call(rows, function(tr, i) {
        tr.addEventListener("click", function() {
            // 单击tr行选择radio圆圈
            var that = $(this);
            that.find("input[type='radio']").get(0).checked = "checked";
            rows.not($(this)).removeClass("selected");
            that.addClass("selected");

            // 1. XHR 加载业务期详情
            var mid = this.getAttribute("data-mid");
            var data = {"mid":mid};

            $.ajax({
                type: "GET",
                url: url +  "/getMissionInfo",
                data: data
            }).done(function(resp) {  // data, textStatus, jqXHR
                Object.keys(resp).forEach(function(key) {  // params: key, idx
                    $("#m_" + key).val(resp[key]);
                });
            });

            // 2. XHR 加载本期内容列表
            list_mission_link_media(mid);
        });
    });
    if (rows.length > 0) {
        rows.first().trigger("click");
    }

    return true;
}

// 除了总集之外的媒体类型 #js-mediatypeid
function list_mediatype() {
    var s = document.getElementById("js-subscribe-url").value;
    var url = s + "/listMediaType";

    $.ajax({
        type: "GET",
        url: url
    }).done(function(resp) {
        var target = document.getElementById("js-mediatypeid");
        target.innerHTML = "";
        // <option value="3">电视节目分集</option>
        Array.prototype.forEach.call(resp, function(elem, i) {
            var option = document.createElement("option");
            option.setAttribute("value", elem.id);
            option.innerHTML = elem.mediatype;
            target.appendChild(option);
        });
        target.onchange = function() {
            content_search();
        };
    });
}

// 新增内容 => 添加新的节目 => 查找
function content_search() {
    var data = {
        "asset_name" : $("#js-searchname").val(),
        "mediatypeid": $("#js-mediatypeid").val()
    };

    var form = document.getElementById("js-formsearch");   // listMissionContent
    var tbody = document.getElementById("snc_tbody");

    $.ajax({
        type: form.method,
        url: form.action,
        data: data
    }).done(function(data) {
        tbody.innerHTML = "";
        var $tbody = $(tbody);

        if (0 === data.length) {
            var td = $("<td>").attr("colspan", 6).html('节目为空').css({"text-align":"center"});
            $("<tr>").append(td).appendTo($tbody);

        } else {

            $.each(data, function(i, val) {
                var tr = $("<tr>").attr("data-oid", val.oid);
                $tbody.append(tr);
                $("<td>").addClass("td-0").html("<div class='tr_arrow'></div>").appendTo(tr);
                $("<td>").addClass("td-1").html(i+1).appendTo(tr);

                var name = "";
                if (val.asset_name.length > 12) {
                    name = val.asset_name.substr(0, 12) + "...";
                } else {
                    name = val.asset_name;
                }
                $("<td>").addClass("td-2").attr("title", val.asset_name).html(name).appendTo(tr);

                var mediatypeid = Number(val.mediatypeid);
                var mediatype = getmediatype(mediatypeid);
                $("<td>").addClass("td-3").html(mediatype).appendTo(tr);

                $("<td>").addClass("td-4").html(val.size).appendTo(tr);

                var $input = $("<input>").attr("type","checkbox").attr("name", "MediaOID[]").attr("title", "checkbox");
                $input.val(val.oid);
                $("<td>").addClass("td-5").append($input).appendTo(tr);
            });

            // 点击行 选择input[type="checkbox"]
            Array.prototype.forEach.call(tbody.children, function(tr) {
                var input = tr.lastChild.children[0];

                for (var i = 0; i < tr.children.length-1; i++) {
                    tr.children[i].addEventListener("click", function() {
                        input.checked = !input.checked;
                    });
                }
            });

            // shift multiple select
            shift_multiple_select('MediaOID[]');
        }
        padScrollTable(tbody, 10, 6);
    });
}

function getmediatype(id) {
    var a = [1, 2, 3, 4, 7, 9];
    var b = ["电影", "电视剧", "电视节目", "热点视频", "戏曲", "专题节目分集"];
    var index = a.indexOf(id);
    return b[index];
}

function getmissionid() {
    var tbody = document.getElementById("js-PackageMission");
    var rows = tbody.children;

    var index = 0;
    for (var i = 0; i < rows.length; i++) {
        if (rows[i].classList.contains("selected")) {
            index = i;
            break;
        }
    }
    return rows[index].getAttribute("data-mid");
}

function checkDate(obj) {
    var missionid = getmissionid();
    var date = obj.value;
    var help = obj.nextElementSibling.nextElementSibling;
    help.innerHTML = "";

    // date format YYYY-mm-dd
    var pat = new RegExp("(([0-9]{3}[1-9]|[0-9]{2}[1-9][0-9]{1}|[0-9]{1}[1-9][0-9]{2}|[1-9][0-9]{3})-(((0[13578]|1[02])-(0[1-9]|[12][0-9]|3[01]))|((0[469]|11)-(0[1-9]|[12][0-9]|30))|(02-(0[1-9]|[1][0-9]|2[0-8]))))|((([0-9]{2})(0[48]|[2468][048]|[13579][26])|((0[48]|[2468][048]|[3579][26])00))-02-29)");
    if (!pat.test(date)) {
        help.innerHTML = "<span class='text-danger'>日期格式:YYYY-mm-dd</span>";
        return false;
    }

    var form = obj.parentNode.parentNode.parentNode;
    var url = form.action;
    url = url.substr(0, url.lastIndexOf('/')) + '/getMissionInfo?mid='+missionid;
    $.getJSON(url, function(data) {
        if (date < data.startdate || date > data.enddate) {
            // help-inline
            help.innerHTML = "<span class='text-danger'>日期非法</span>";
        } else {
            help.innerHTML = "<span class='text-success'>日期合法</span>";
        }
    });
}

function shift_multiple_select(name) {
    window.lastChecked = null;
    var $chkboxes = $("input[name='" + name + "']");

    $chkboxes.on("click", function(e) {
        if(!window.lastChecked) {
            window.lastChecked = this;
            return;
        }
        if(e.shiftKey) {
            var start = $chkboxes.index(this);
            var end = $chkboxes.index(window.lastChecked);

            $chkboxes.slice(Math.min(start,end), Math.max(start,end)+ 1)
                .prop('checked', window.lastChecked.checked);
        }
        window.lastChecked = this;
    });
}

// 本期内容列表 => <a>修改</a>
function edmissionlinkmedia(id) {
    if (id === undefined || id === null) {
        alert("id is not set for edmissionlinkmedia");
        return false;
    }
    var url = document.getElementById("js-subscribe-url").value + "/getMissionLinkMediaByID?id=" + id;
    $.getJSON(url, function(resp) {
        var date = resp.date;
        var today = gettoday();
        if (date < today) {
            var dlg = $("#dlg_cannot_edit_content");
            dlg.dialog("open");
            setmyTime(function() {
                dlg.dialog("close");
            }, 3000);
            return;
        }

        $("#ed_id").val(id);
        $("#ed_date").val(resp.date);
        $("#ed_round").val(resp.round);

        $("#dlg_edit_content").dialog("open");
    });
    return true;
}

// 本期内容列表 => <a>删除</a>
function rmmissionlinkmedia(id) {
    // TODO: 删除媒体确认  同时删除所有同名媒体 + 强制下线
    g_url = document.getElementById("js-subscribe-url").value + "/delContent";
    g_linkid = id;

    $("#dlg_confirm_delete").dialog("open");
}

// 批量删除内容
function bulk_delete_media(obj) {
    g_url = obj.getAttribute("data-url");   // bulkDelContent
    $("#dlg_confirm_bulkDelete").dialog("open");
}

// load 本期内容列表
function list_mission_link_media(missionid) {
    var url = document.getElementById("js-subscribe-url").value;
    var tbody = $("#this_mission");

    $.ajax({
        type: "GET",
        url: url + "/listMissionLinkMedia",
        data: {"missionid":missionid}
    }).done(function(resp) {
        tbody.empty();

        resp.forEach(function(entry, i) {
            var tr = $("<tr>").attr("data-id", entry.id).attr("data-missionid", entry.missionid).attr("data-mediaoid", entry.mediaoid);
            // tr.attr("title", entry.filename);
            tbody.append(tr);

            // @func: mb_substr ~/Public/Common/js/function.js
            var td = [
                "<td class='td-0'><div class='tr_arrow'></div></td>",
                "<td class='td-1'><input type='checkbox' name='iscont' title='checkbox' value='"+ entry.id +"'/></td>",
                "<td class='td-2'><span>"+ (i+1) +"</span></td>",
                "<td class='td-3' title='"+entry.filename+"'><span>"+ entry.filename +"</span></td>",
                "<td class='td-4'><span>" + entry.date + "</span></td>",
                "<td class='td-5'><span>" + entry.round + "</span></td>",
                "<td class='td-6'>",
                "<a href='javascript:;' onclick='edmissionlinkmedia("+ entry.id+")'>修改</a>",
                "<a href='javascript:;' onclick='rmmissionlinkmedia("+entry.id+")'>删除</a>",
                "</td>"
            ].join('');

            tr.html(td);
        });

        // shift multiple select
        shift_multiple_select('iscont');
        // pad: tbody, row, col
        padScrollTable(tbody.get(0), 8, 8);

        // resize
        var colElement, colWidth, originalSize;
        var $ths = $("#tableCaption").find("th");
        for (var i = 0; i < $ths.length; i++) {
            (function(i) {
                $($ths[i]).resizable({
                    handles: "e",

                    // set correct COL element and original size
                    start: function(event, ui) {
                        var table = $(this).parent("tr").parent().parent();
                        var colIndex = ui.helper.index() + 1;
                        colElement = table.find("colgroup > col:nth-child(" +
                            colIndex + ")");

                        // get col width (faster than .width() on IE)
                        colWidth = parseInt(colElement.get(0).style.width, 10);
                        originalSize = ui.size.width;
                    },

                    // set COL width
                    resize: function(event, ui) {
                        var resizeDelta = ui.size.width - originalSize;

                        var newColWidth = colWidth + resizeDelta;
                        colElement.width(newColWidth);

                        var $td = tbody.find(".td-" + i);
                        $td.css({"width":newColWidth});
                        $td.children("span").css({
                            "width":newColWidth,
                            "overflow-wrap": "break-word",
                            "white-space": "nowrap",
                            "overflow": "hidden",
                            "text-overflow": "ellipsis",
                            "display": "inline-block"
                        });

                        // height must be set in order to prevent IE9 to set wrong height
                        $(this).css("height", "auto");
                    }
                });
            })(i);
        }
    })
}

