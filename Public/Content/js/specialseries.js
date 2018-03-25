/**
 * Created by Administrator on 2017-03-31.
 * 专题节目总集
 * depends on edit.js
 */
$(function() {
    baseurl = document.getElementById("js-baseurl").value;

    // 专题节目总集名称列表
    var titleTable = document.getElementById("SeriesTable");
    padScrollTable(titleTable, 14, 4);
    bindTableEvent(titleTable);

    // 添加总集
    $("#dlg_new_specials").dialog({
        autoOpen: false,
        modal: true,
        width: 640,

        // init images upload
        create: function() {
            var appendixUploader = document.getElementsByClassName("appendix_upload");

            Array.prototype.forEach.call(appendixUploader, function(uploader, index) {
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
                    })
                    .done(function(data) {
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

                    })
                    .fail(function(jqXHR, textStatus, errorThrown) {
                        alert("upload fail" + errorThrown);
                    });
                });
                $(uploader).next().on("click", function() {
                    if (0 == index) {
                        // 缩略图
                        $("#s_thumb").trigger("click");
                    } else if (1 == index) {
                        // 海报
                        $("#s_poster").trigger("click");
                    }
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
                        // update 专题节目总集列表
                        listSpecialSeries();
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
    $("#js-addSeries").button().on("click", function() {
        $("#dlg_new_specials").dialog("open");
    });
    // 通过对话框 添加属性名称
    $("#dlg_prompt_attr").dialog({
        autoOpen: false,
        modal: true,
        width: 300,
        open: function(event, ui) {},
        close: function() {document.getElementById("prompt_attr").value = "";},
        buttons: [
            {
                text: "确认添加",
                icons: {primary: "ui-icon-check"},
                click: function() {
                    // addAttrHandler
                    (function (dlg) {
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
                        })
                        .done(function(data, textStatus, jqXHR) {
                            $(dlg).dialog("close");
                            attr.doAdd(data);
                        })
                        .fail(function(jqXHR, textStatus, errorThrown) {
                            alert(errorThrown);
                        });
                    })(this);
                }
            },
            {
                text: "取消",
                icons: {primary: "ui-icon-close"},
                click: function() {$(this).dialog("close");}
            }
        ]
    });
    // 通过专题节目总集页面 添加属性名称
    $("#js_AddAttr_fp").on("click", function() {
        attr = new Attributes("s_attr_fp");
        attr.del();
        $("#dlg_prompt_attr").dialog("open");
    });

    // 出错提示
    $("#dlg_delete_not_allowed").dialog({
        modal: true,
        autoOpen: false,
        width: 400,
        buttons: {"OK": function() {$(this).dialog("close");}}
    });

    // 修改成功提示
    $("#DialogSaveFeedback").dialog({
        modal: true,
        autoOpen: false,
        width: 300,
        buttons: {"OK": function() {$(this).dialog("close");}}
    });

    // 编辑专题节目总集 下面的按钮
    // 查看分集
    $("#button-1").button().on("click", function() {
        e.preventDefault();
        // setCookie("oid", oid, 5);
        location.href = this.href;
    });

    // 保存总集
    $("#button-2").button().on("click", function() {
        var url = baseurl + "/setSpecialsInfo";
        var soid = $("#SeriesTable").find(".selected").attr("data-soid");
        var data = {
            "soid" : soid,
            "title" : $("#EditSeriesTitle").val(),
            "genreid" : $("#EditSeriesType").val(),
            "countryid" : $("#EditSeriesCountry").val(),
            "languageid" : $("#EditSeriesLanguage").val(),
            "introduction" : $("#EditSeriesIntroduction").val(),
            "attrs" : []
        };
        $("#s_attr_fp").find("input").each(function(i, elem) {
            var attr = [elem.getAttribute("data-attrid"), elem.value];
            data.attrs.push(attr);
        });

        $.ajax({
            type: "POST",
            url: url,
            data: data
        }).done(function(data) {
            listSpecialSeries();
        }).fail(function( jqXHR, textStatus, errorThrown ) {
            alert("#button-2 saving special series error: " + errorThrown);
        });

        saveQrCode(soid, 'show');
    });
    // 返回
    $("#button-3").button();

    // 更新总集图片
    // $("#series_img_group").find("form.appendix_upload").on("change", function() {});
    pageUpdatePic();
});

/**
 * 刷新左侧 专题节目总集列表  入口函数
 */
function listSpecialSeries() {
    var table = document.getElementById("SeriesTable");
    table.innerHTML = "";
    var tbody = document.createElement("tbody");
    table.appendChild(tbody);

    if (typeof baseurl === "undefined" || baseurl == null) {
        baseurl = document.getElementById("js-baseurl").value;
    }
    var url = baseurl + "/listSpecialSeries";
    $.ajax({
        type: "GET",
        url: url,
        data: {"t":Date.now()}
    })
    .done(function(data, textStatus, jqXHR ) {
        // oid, title
        for (var i = 0; i < data.length; i++) {
            var tr = document.createElement("tr");
            tr.setAttribute("data-soid", data[i].oid);
            tbody.appendChild(tr);

            var td = [];
            for (var j = 0; j < 4; j++) {
                td.push( document.createElement("td") );
                td[j].classList.add("col-" + j);
                tr.appendChild(td[j]);
            }
            td[0].setAttribute("title", "arrow");
            td[0].innerHTML = "<div class='tr_arrow'></div>";
            td[1].setAttribute("title", i+1);
            td[1].innerHTML = i+1;
            td[2].setAttribute("title", data[i].title);
            td[2].innerHTML = data[i].title;
            td[2].style.width = "150px";   // compensate for text node: 140px => 150px
            td[3].setAttribute("title", "delete");
            td[3].innerHTML = "<a href=\"javascript:void(0)\" title=\"删除专题节目总集\" onclick=\"deleteSeries(this)\">删除</a>";
        }
        // pad
        padScrollTable(table, 14, 4);
        // add event
        bindTableEvent(table);
    })
    .fail(function(jqXHR, textStatus, errorThrown) {
        alert("listSpecialSeries ajax error: " + errorThrown);
    })
}

/**
 * /getSpecialSeriesInfo 绑定tr事件点击 取得所有总集信息
 * @param table:
 */
function bindTableEvent(table) {
    if (typeof table === "undefined" || table == null) {
        table = document.getElementById("SeriesTable");
    }
    var url = baseurl + "/getSpecialSeriesInfo";
    var rows = $(table).children("tbody").first().children("tr").not(".dumb");

    // resetEpisodeImg();

    Array.prototype.forEach.call(rows, function(tr, i) {
        tr.addEventListener("click", function() {
            this.classList.add("selected");
            $(rows).not($(tr)).removeClass("selected");

            var soid = this.getAttribute("data-soid");
            // <input type="hidden" class="oid" name="attachoid" value="">
            $("#series_img_group").find(".oid").val(soid);
            $.ajax({
                type: "GET",
                url: url,
                data: {"soid":soid}
            }).done(function(data) {
                // 名称 类型 国家 语言 简介
                $("#EditSeriesTitle").val(data.info.title);
                $("#EditSeriesType").val(data.info.genreid);
                $("#EditSeriesCountry").val(data.info.countryid);
                $("#EditSeriesLanguage").val(data.info.languageid);
                $("#EditSeriesIntroduction").val(data.info.introduction);

                // 缩略图 + 海报
                var initcss = {
                    "background-image":"",
                    "background-repeat": "no-repeat"
                };
                var epsthumb = $("#eps_thumb"), jsthumb = $("#js-thumb");
                epsthumb.attr("data-id", "");
                jsthumb.css(initcss);
                var epsposter = $("#eps_poster"), jsposter = $("#js-poster");
                epsposter.attr("data-id", "");
                jsposter.css(initcss);

                for (var index = 0; index < data.pic.length; index++) {
                    switch (data.pic[index].appendixtypeid) {
                        case "1":    // 缩略图
                            epsthumb.attr("data-id", data.pic[index].id);  // @table: appendix [col:id]
                            jsthumb.css({"background-image":"url('" + data.pic[index].url + "')"});
                            break;
                        case "2":    // 海报
                            epsposter.attr("data-id", data.pic[index].id);  // @table: appendix [col:id]
                            jsposter.css({"background-image":"url('"+ data.pic[index].url +"')"});
                            break;
                        default:
                            console.error("unexpected appendix type!");
                            console.log(data.pic[index]);
                    }
                }
                // 附加属性
                var renderAttrs = function(data) {
                    var tbody = document.getElementById("s_attr_fp");
                    tbody.innerHTML = "";
                    for (var i = 0; i < data.length; i++) {
                        var tr = $("<tr>").addClass("optional").appendTo($(tbody));

                        var id = data[i].attrid;
                        var domid = "s_attr_" + id;
                        var label = $("<label>").attr("for", domid).html(data[i].attrname + ": ");
                        $("<td>").append(label).appendTo(tr);
                        var badge = $("<a href=\"javascript:;\"><span class=\"badge\">---</span></a>");
                        var input = $("<input>").attr("id", domid).attr("name", domid).attr("data-attrid", id).attr("type", "text");
                        input.val(data[i].attrval);
                        $("<td>").append(input).append(badge).appendTo(tr);

                        badge.on("click", function() {
                            $(this).parent("td").parent("tr").remove();
                        });
                    }
                };
                renderAttrs(data.attrs);

                // 分集集内容列表
                // [
                // {episodeoid: "98EF95A6F1487B5ECACD4618F3CCEA88", episodeindex: "1", asset_name: "林宁广告", size: "4.29M"…},
                // ...
                // ]
                // <tr data-oid="xxx">
                //   <td class="col-0"><div class="tr_arrow"></div></td>
                //   <td class="col-1" title="1">1</td>
                //   <td class="col-2" title="老乡帮老乡一亩地见分晓">老乡帮老乡一亩地见分晓</td>
                //   <td class="col-3" title="4.29M">4.29M</td>
                //   <td class="col-4" title="单选操作"><input type="radio" name="EpisodeOID" title="radio"></td>
                // </tr>
                var listEpisodes = function(data) {
                    var target = document.getElementById("js-episodes");
                    target.innerHTML = "";

                    Array.prototype.forEach.call(data, function(elem, i) {
                        var tr = $("<tr>").attr("data-oid", data[i].episodeoid).appendTo($(target));

                        $("<td>").addClass("col-0").html("<div class='tr_arrow'></div>").appendTo(tr);
                        $("<td>").addClass("col-1").attr("title", i+1).html(i+1).appendTo(tr);
                        $("<td>").addClass("col-2").attr("title", data[i].asset_name).html(data[i].asset_name).appendTo(tr);
                        $("<td>").addClass("col-3").attr("title", data[i].size).html(data[i].size).appendTo(tr);
                        var input = $("<input>").attr("type", "radio").attr("name", "EpisodeOID").attr("title", "radio");
                        $("<td>").addClass("col-4").attr("title", "单选操作").append(input).appendTo(tr);
                    });
                    padScrollTable(target, 3, 5);
                    listEpisodesPic(target);
                };
                listEpisodes(data.episodes);
            }).fail(function(jqXHR, textStatus, errorThrown) {
                alert("bindTableEvent ajax error: " + errorThrown);
            });

            // 二维码
            getQrCode(soid, 'show', 'jsqrcode');

            setCookie("soid", soid);
        });  // END  addEventListener
        triggerClick(rows, "soid");
    });
}

/**
 * 点击分集列表中的行 显示对应的缩略图 + 海报
 * @param tbody #js-episodes, 分集列表
 */
function listEpisodesPic(tbody) {
    // @target: img#img_thumb, img#img_poster
    var rows = $(tbody).children("tr").not(".dumb");

    document.getElementById("img_thumb").src = "";
    document.getElementById("img_poster").src = "";

    rows.each(function(i, elem) {

        elem.onclick = function() {
            if (!elem.classList.contains("selected")) {
                elem.classList.add("selected");
            }
            var $elem = $(elem);
            $elem.children("td").last().children("input[type='radio']").get(0).checked = "checked";
            rows.not($elem).removeClass("selected");

            var oid = $elem.attr("data-oid");
            // get image by oid
            $.ajax({
                type: "GET",
                url:  baseurl + "/getAppendixList",
                data: {"oid":oid}
            }).done(function(data) {

                var thumb = document.getElementById("img_thumb");
                thumb.src = "";
                var poster = document.getElementById("img_poster");
                poster.src = "";

                var prefix = $("#project_root").val(); // + "/resource/appendix/";

                for (var i = 0; i < data.length; i++) {
                    var url = prefix + data[i].url;
                    // console.log(url);
                    if (data[i].appendixtypeid == "1") {
                        thumb.src = url;
                    } else if (data[i].appendixtypeid == "2") {
                        poster.src = url;
                    }
                }
            }).fail(function(jqXHR, textStatus, errorThrown) {
                alert("listEpisodesPic ajax error: " + errorThrown);
            })
        };
        // END elem.addEventListener
        setCookie("oid", elem.getAttribute("data-oid"));
    });  // END rows.each()
    triggerClick(rows, "oid");
}

/**
 * 删除专题节目总集
 * @param obj  DOM <a>
 */
function deleteSeries(obj) {
    var tr = $(obj).parent("td").parent("tr");
    var soid = tr.attr("data-soid");
    var s = location.href;
    var prefix = s.substr(0, s.lastIndexOf('/')+1);  // contains "/"
    var url = prefix + "countSpecialEpisode";
    $.ajax({
        type: "GET",
        url: url,
        data: {"soid":soid}
    })
    .done(function(data) {
        var n = parseInt(data);
        if (isNaN(n)) {
            alert("get episodes count failed!");
            return false;
        }
        // 下面有分集 不能删除
        if (0 < n) {
            var dlg = $("#dlg_delete_not_allowed");
            dlg.dialog("open");
            setTimeout((function() {
                dlg.dialog("close");
            }).bind(null, dlg), 3000);
            return false;
        }
        // /deleteSeries  GET: "soid": xxx, "type": "special"(@table: MBIS_Server_Special)
        $.ajax({
            type: "GET",
            url: prefix + "deleteSeries",
            data: {"soid":soid, "type":"special"}
        })
        .done(function(data) {
            var n = parseInt(data);
            if (isNaN(n) || n <= 0) {
                alert("删除专题节目总集失败!");
                return false;
            }
            listSpecialSeries();   // refresh
            return true;
        })
        .fail(function( jqXHR, textStatus, errorThrown ) {
            alert("request deleteSeries failed: " + errorThrown);
        });
        return true;
    })
    .fail(function( jqXHR, textStatus, errorThrown ) {
        alert("request countSpecialEpisode failed: " + errorThrown);
    })
}

/**
 * 专题节目总集页面更新缩略图 海报
 */
function pageUpdatePic() {
    var appendixUploader = document.getElementsByClassName("appendix_upload_fp");

    Array.prototype.forEach.call(appendixUploader, function(uploader, index) {
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
            })
            .done(function(data) {
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

            })
            .fail(function(jqXHR, textStatus, errorThrown) {
                alert("upload fail" + errorThrown);
            });
        });
        $(uploader).next().on("click", function() {
            if (0 == index) {
                // 缩略图
                $("#eps_thumb").trigger("click");
            } else if (1 == index) {
                // 海报
                $("#eps_poster").trigger("click");
            }
        });
    });
}