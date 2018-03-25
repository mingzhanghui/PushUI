/**
 * Created by Administrator on 2017-03-22.
 * 专题节目
 */
$(function() {
    baseurl = document.getElementById("js-baseurl").value;
    // /PushUI/resource/appendix/
    // appendixPrefix = null;

    // 专题节目 单集/总集 属性操作 object
    attr = null;

    // 专题节目总集 -- 查找
    $("#dlg_query_specials").dialog({
        autoOpen: false,
        modal: true,
        width: 570,
        create: function() {
            $("#ssbtn").on("click", function() {
                SearchSpecialsByCondition();
            });
        },
        open: function(event, ui) {
            SearchSpecialsByCondition();
        },
        buttons: [
            {
                text: "添加新节目",
                icons: {primary: "ui-icon-plusthick"},
                click: function() {
                    $("#dlg_new_specials").dialog("open");
                }
            },
            {
                text: "选择",
                icons: {primary: "ui-icon-check"},
                click: function() {
                    // 选择节目总集 取得的soid title 添加到分集信息编辑页
                    var tr = $("#ss_tbody").children(".selected");
                    var soid = tr.attr("data-soid");
                    var title = tr.children(".td-2").attr("title");
                    $("#special-soid").val(soid);
                    $("#special-series").val(title);
                    $(this).dialog("close");
                }
            },
            {
                text: "返回",
                icons: {primary: "ui-icon-arrowreturnthick-1-w"},
                click: function() {
                    $("#dlg_query_specials").dialog("close");
                }
            }
        ]
    });

    // "专题节目 -- 添加新专题节目总集"
    $("#dlg_new_specials").dialog({
        autoOpen: false,
        modal: true,
        width: 640,

        // init images upload
        create: function() {
            var appendixUploader = document.getElementsByClassName("appendix_upload");

            Array.prototype.forEach.call(appendixUploader, function(uploader) {
                // xhr upload appendix
                uploader.addEventListener("change", function(event) {
                    event.preventDefault();

                    var formData = new FormData(this);

                    $.ajax({
                        url  : baseurl + "/xhrUploadOverwrite",
                        type : "POST",
                        data : formData,
                        cache       : false,
                        contentType : false,
                        processData : false,
                        dataType    : "json"
                    }).done(function(data) {
                        console.log(data);

                        var input = $(uploader).find("input[type='file']").get(0);
                        input.setAttribute("data-id", data.id);

                        // 显示已经上传的图片
                        $.ajax({
                            type: "GET",
                            url: baseurl + "/getAppendixURLByID",
                            data: {"id":data.id}
                        })
                        .done(function(data) {
                            $(uploader).next().css({
                                "background-image": "url("+ data +")"
                            });
                        })
                        .fail(function(jqXHR, textStatus, errorThrown) {
                            alert("get appendix fail" + errorThrown);
                        });

                    }).fail(function(jqXHR, textStatus, errorThrown) {
                        alert("upload fail" + errorThrown);
                    });
                });
            });
        },

        // 打开添加新节目对话框 生成新的soid
        open: function(event, ui) {
            var baseurl = $("#js-baseurl").val();
            $.ajax({
                url: baseurl + "/newsoid",
                type: "GET",
                data: {"ts": Date.parse( new Date() ) }
            })
            .done(function(data, textStatus, jqXHR) {
                var re = new RegExp("S[0-9]{31}");
                if (re.test(data)) {
                    $("#js-newsoid").val(data);
                    // OID用于附件上传
                    $(".newsoid").val(data);
                } else {
                    alert("生成新专题节目总集OID失败!");
                }
            })
            .fail(function (jqXHR, textStatus, errorThrown) {
                alert("generate soid failed");
            });

            // 专题节目总集 添加新属性  from dialog
            attr = new Attributes("s_attr");
            attr.add("js-AddAttr");
            attr.del();
        },

        close: function(event, ui) {
            $("#s_name").val('');
            var uploadForms = document.getElementsByClassName("appendix_upload");
            Array.prototype.forEach.call(uploadForms, function(form) {
                form.reset();
            });
            console.log("closed.");

            attr = null;
        },
        buttons: [
            {
                text: "保存",
                icons: {
                    primary: "ui-icon-check"
                },
                click: function() {
                    console.log("saving special series...");

                    // 固定属性
                    var soid = $("#js-newsoid").val();
                    var title = $("#s_name").val().trim();
                    if (title === "") {
                        alert("专题节目名不能为空");
                        return false;
                    }
                    var data = {
                        "soid": soid,
                        "title": title,
                        "introduction" : $("#s_introduction").val(),
                        "countryid" : $("#s_countryid").val(),
                        "languageid" : $("#s_languageid").val(),
                        "episodes" : $("#s_episodes").val(),
                        "genreid": $("#s_genre").val(),
                        "attrs" : []
                    };
                    // 动态属性
                    var inputs = $("#s_attr").find("input");
                    for (var i = 0; i < inputs.length; i++) {
                        var input = inputs[i];
                        data.attrs.push([input.getAttribute("data-attrid"), input.value]);
                    }

                    var url = baseurl + "/newSpecialSeries";
                    $.ajax({
                        type: "POST",
                        url: url,
                        data: data
                    })
                    .done(function(data, textStatus, jqXHR) {
                        console.log(data);
                        if (data.media == -1) {
                            alert("专题节目总集名已经存在");
                            return false;
                        }
                        $("#dlg_new_specials").dialog("close");
                        SearchSpecialsByCondition();
                        return true;
                    })
                    .fail(function(jqXHR, textStatus, errorThrown) {
                        alert(errorThrown);
                    })
                }
            },
            {
                text: "取消",
                icons: {
                    primary: "ui-icon-close"
                },
                click: function() {
                    $(this).dialog("close");

                    // 专题节目 -- 添加新专题节目总集" / 删除附件
                    var thumbID = $("#s_thumb").attr("data-id");
                    var posterID = $("#s_poster").attr("data-id");

                    var remove = function(id) {
                        if (typeof id === "undefined" || id == null || id == '') {
                            console.warn("附件ID未设定, 不能删除");
                            return false;
                        }
                        $.ajax({
                            type: "GET",
                            url: baseurl + "/removeAppendix",
                            data: {"id" : id}
                        })
                        .done(function(data) {
                            console.log(data);
                        })
                        .fail(function(jqXHR, textStatus, errorThrown) {
                            alert("delete appendix: " + errorThrown);
                        })
                    };
                    remove(thumbID);
                    remove(posterID);
                }
            }
        ]
    });

    // 通过对话框 添加属性名称
    $("#dlg_prompt_attr").dialog({
        autoOpen: false,
        modal: true,
        width: 300,
        open: function(event, ui) {},
        close: function() {
            document.getElementById("prompt_attr").value = "";
        },
        buttons: [
            {
                text: "确认添加",
                icons: {primary: "ui-icon-check"},
                click: function() {
                    addAttrHandler(this);
                }
            },
            {
                text: "取消",
                icons: {primary: "ui-icon-close"},
                click: function() {
                    $(this).dialog("close");
                }
            }
        ]
    });

    // 添加专题节目总集图片
    $("#js-thumb").on("click", function() {
        var form = $(this).prev();
        form.find("input[type='file']").trigger("click");
    });
    $("#js-poster").on("click", function() {
        var form = $(this).prev();
        form.find("input[type='file']").trigger("click");
    });

    showSpecialInfo();

    // 通过专题节目单集页面 添加属性
    $("#js_AddAttr_fp").on("click", function() {
        attr = new Attributes("s_attr_fp");
        attr.del();
        $("#dlg_prompt_attr").dialog("open");
    });

    // 触发查找总节目集
    $("#js-SearchSpecialSeries").on("click", function() {
        $("#dlg_query_specials").dialog("open");
    });

    // 需要先添加总集 提示
    $("#dlg_require_series").dialog({
        autoOpen: false,
        modal: true,
        width: 300,
        open: function(event, ui) {},
        close: function() {},
        buttons: [
            {
                text: "OK",
                icons: {primary: "ui-icon-check"},
                click: function() {
                    $(this).dialog("close");
                }
            }
        ]
    });

    // 保存草稿 => 设置新增专题节目单集信息
    $("#js-save-draft").on("click", function() {
        saveDraft();
    });

    $("#js-cut").on("click", function() {
        // edit.js
        var tr = $("#special-table").children("tbody").first().children(".selected");
        var oid = tr.attr("data-oid");
        doCut(oid);
    });
});

// 保存专题节目单集草稿
function saveDraft() {
    var fb = document.getElementById("fb_savedraft");
    fb.innerHTML = "保存草稿...";

    var soid = $("#special-soid").val();
    var stitle = $("#special-series").val();
    if (soid == "" || stitle == "") {
        fb.innerHTML = "<font color='red'>保存草稿失败！</font>";
        $("#dlg_require_series").dialog("open");
        return false;
    }
    var title = $("#special-episode").val();
    if (title.trim() === "") {
        fb.innerHTML = "<font color='red'>保存草稿失败！</font>";
        alert("必须设置所选分集名称!");
        return false;
    }

    var attrs = [];
    var inputs = $("#s_attr_fp").find("input");

    Array.prototype.forEach.call(inputs, function(input) {
        var a = [];
        a[0] = input.getAttribute("data-attrid");
        a[1] = input.value;
        attrs.push(a);
    });

    var url = baseurl + "/setSpecialEpisodeInfo";
    var oid = $("#system-name").val();
    var data = {
        "oid" : oid,
        "soid" : soid,
        "title" : title,
        "introduction" : $("#special-introduction").val(),
        "episodeindex" : $("#special-episodeindex").val(),
        "attrs" : attrs
    };
    $.ajax({
        type: "POST",
        url: url,
        data: data
    }).done(function(data) {
        $("#special-table").find(".selected").children(".col-2").html(title).attr("title", title);
        fb.innerHTML = "<font color='green'>保存草稿成功！</font>";
        setTimeout(function() {
            fb.innerHTML = "";
        }, 5000);
    }).fail(function(jqXHR, textStatus, errorThrown) {
        alert(errorThrown);
    });
    saveQrCode(oid, 'show');

    return true;
}

// 对话框添加自定义属性
function addAttrHandler(dlg) {
    // dialog input
    var name = document.getElementById("prompt_attr").value;
    if (name.trim() === "") {
        alert("名称不能为空");
        return false;
    }
    // dialog form
    var div = document.getElementById("form_prompt_attr");
    $.ajax({
        type: div.getAttribute("data-method"),
        url: div.getAttribute("data-action"),
        data: {"name":name}
    }).done(function(data, textStatus, jqXHR) {
        $(dlg).dialog("close");
        attr.doAdd(data);
    }).fail(function(jqXHR, textStatus, errorThrown) {
        alert(errorThrown);
    });
}

// 专题节目列表点击行显示对应信息
function showSpecialInfo() {
    var rows = $("#special-table").children("tbody").children("tr:not(.dumb)");
    if (0 == rows.length) {
        return false;
    }
    var url = $("#js-baseurl").val() + "/getSpecialInfo";

    rows.each(function (i, tr) {
        tr.addEventListener("click", function() {
            $(this).addClass("selected");
            rows.not($(this)).removeClass("selected");

            var oid = $(this).attr("data-oid");
            $("#system-name").val(oid);
            $(".oid").val(oid);

            var data = {"oid":oid};
            $.ajax({
                type: "GET",
                url: url,
                data: data
            }).done(function(data, textStatus, jqXHR) {
                respSpecialInfo(data.info);
                respSpecialPic(data.pic);
                // 备播状态 存草稿状态
                var slicestatus = document.getElementById("js-slicestatus");
                if (data.cut == 0) {
                    slicestatus.innerHTML = "未备播";
                } else if (data.cut == 1) {
                    slicestatus.innerHTML = "<font color='green'>已备播</font>";
                }
                document.getElementById("fb_savedraft").innerHTML = "";
            }).fail(function(jqXHR, textStatus, errorThrown) {
                alert(errorThrown);
            });
            getQrCode(oid, 'show', 'jsqrcode');

            // 刷新页面 记录oid, 30min
            setCookie('oid', oid, 30);
            //  用户上传附件 appendixoid, input[type="hidden"]
            $("#js-attachoid").val(oid);
        });
    });

    if (getCookie("oid") == "") {
        $(rows.get(0)).trigger("click");
    } else {
        var oid = getCookie("oid");
        for (var i = 0; i < rows.length; i++) {
            var selected = $(rows.get(i));
            if (selected.attr("data-oid") == oid) {
                selected.trigger("click");
                break;
            }
        }
        // oid not found
        if (i == rows.length) {
            $(rows.get(0)).trigger("click");
        }
    }
}

/**
 * 显示单集页面 单集详细信息
 * @param data
 */
function respSpecialInfo(data) {
    $("#special-episode").val(data.title);
    $("#special-soid").val(data.specialoid);
    $("#special-series").val(data.stitle);
    $("#special-episodeindex").val(data.episodeindex);
    $("#special-introduction").val(data.introduction);

    var attrs = data.attrs;
    var target = $("#s_attr_fp");
    target.empty();

    Array.prototype.forEach.call(attrs, function(data, i) {
        var name = 's_attr_' + data.id;
        var tr = $("<tr>").addClass("optional").appendTo(target);

        var label = $("<label>").attr("for", name).html(data.name + ":");
        $("<td>").append(label).appendTo(tr);

        var input = $("<input>").attr("id", name).attr("name", name).attr("data-attrid", data.id).attr("type", "text");
        input.val(data.value);
        var badge = $("<a href=\"javascript:;\"><span class=\"badge\">---</span></a>");
        $('<td>').append(input).append(badge).appendTo(tr);

        badge.on("click", function() {
            tr.remove();
        });
    });
}

/**
 * 设置缩略图 海报
 * @param data  [{'id','attachoid', 'url'...},{}]
 */
function respSpecialPic(data) {
    // @param appendixPrefix, url "/PushUI/resource/appendix/"
    var setPic = function() {
        var url = "";
        var thumb = $("#js-thumb");
        var poster = $("#js-poster");

        thumb.css({"background-image":"none"});
        poster.css({"background-image":"none"});

        for (var i = 0; i < data.length; i++) {
            if (data[i].type === "缩略图") {
                document.getElementById("eps_thumb").setAttribute("data-id", data[i].id);
                thumb.css({"background-image": "url("+ data[i].url +")"});

            } else if (data[i].type === "海报") {
                document.getElementById("eps_poster").setAttribute("data-id", data[i].id);
                poster.css({"background-image": "url("+ data[i].url +")"});
            }
        }
    };
    $.ajax({
        type: "GET",
        url: baseurl + "/getAppendixURLPrefix",
        dataType: "json"
    })
    .done(function(resp) {
        var appendixPrefix = resp.path;
        setPic();
    })
    .fail(function(jqXHR, textStatus) {
        console.error("get appendix prefix failed: " + textStatus);
        var appendixPrefix = $("#project_root").val() + "/resource/appendix/";
        setPic();
    });
}

function SearchSpecialsByCondition() {
    var url = $("#js-baseurl").val() + "/listSpecialSeries";
    var search = $("#js-searchname").val();
    var q = null;
    if (typeof search == "undefined") {
        q = "";
    } else {
        q = search.trim();
    }
    var data = {"q" : q};
    $.ajax({
        type: "POST",
        url: url,
        data: data
    })
    .done(function (data, textStatus, jqXHR) {
        var tbody = document.getElementById("ss_tbody");
        if (typeof tbody == "undefined") {
            return false;
        }
        tbody.innerHTML = "";
        var target = $(tbody);

        for (var i = 0; i < data.length; i++) {
            var tr = $("<tr>").attr("data-soid", data[i].oid);
            target.append(tr);
            $("<td>").addClass("td-0").html("<div class='tr_arrow'></div>").appendTo(tr);
            $("<td>").addClass("td-1").html(i+1).appendTo(tr);
            $("<td>").addClass("td-2").attr("title", data[i].title).html(data[i].title).appendTo(tr);
            $("<td>").addClass("td-3").html(data[i].country).appendTo(tr);
            $("<td>").addClass("td-4").html(data[i].language).appendTo(tr);
            $("<td>").addClass("td-5").html(data[i].imported).appendTo(tr);
            $("<td>").addClass("td-6").html("<input type='radio' name='specials' title='radio' />").appendTo(tr);
        }

        // 选择专题节目总集
        var rows = target.children("tr").not(".dumb");
        rows.on("click", function () {
            $(this).addClass("selected");
            var radio = this.childNodes[this.childNodes.length-1].childNodes[0];
            radio.checked = "checked";
            rows.not($(this)).removeClass("selected");
        });

        var soid = getCookie("soid");
        if ("" == soid) {
            rows.first().trigger("click");
        } else {
            for (var index = 0; index < rows.length; index++) {
                if (rows.get(index).getAttribute("data-soid") == soid) {
                    $(rows.get(index)).trigger("click");
                    break;
                }
            }
            if (index == rows.length) {
                rows.first().trigger("click");
            }
        }

        return true;
    })
    .fail(function( jqXHR, textStatus, errorThrown) {
        alert(errorThrown);
        return false;
    })
    .always(function (jqXHR, textStatus, errorThrown) {
        var table = document.getElementById("searchSpecialsTable");
        padScrollTable(table, 10, 7);
    });

}

/**
 * 提交专题节目分集
 */
function submitEpisode() {
    var soid = document.getElementById("special-series").value;

    if (soid === '' || soid === 0) {
        alert('还没有设置所属剧集!');
        return false;
    }
    var oid = $("#system-name").val();

    submitEditMedia(oid);
}