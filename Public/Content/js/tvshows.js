/**
 * Created by Administrator on 2017-03-22.
 * 电视节目总集  <= edit.js
 */
$(function() {
    // controller  url
    baseurl = $("#js-baseurl").val();

    // 电视节目总集列表
    var seriesTable = document.getElementById("SeriesTable");
    padScrollTable(seriesTable, 14, 4);
    initSeriesTable(seriesTable);
    onloadClick(seriesTable);

    // 添加电视节目总集 弹出对话框
    $("#DialogAddShows").dialog({
        modal: true,
        autoOpen: false,
        width: 700,
        show: {effect: "fade", duration: 100},
        hide: {effect: "fade", duration: 100},
        buttons: [
            {
                text: "提交保存",
                icons: {
                    primary: "ui-icon-check"
                },
                click: function() {
                    var form = document.getElementById("addTVShowsInfo");
                    var formData = new FormData(form);

                    var request = new XMLHttpRequest();
                    var url = form.action;    // EditController.class.php  function newTVShows() {}
                    request.open('POST', url, true);
                    request.onreadystatechange = function() {
                        if (this.readyState === 4) {
                            if (this.status >= 200 && this.status < 400) {
                                // Success!
                                var resp = this.responseText;
                                console.log(resp);
                                try {
                                    var json = JSON.parse(resp);
                                    alert(json.msg);
                                    $("#DialogAddShows").dialog("close");
                                    // refresh TV shows title list
                                    reloadTVShowsTitle();
                                    return true;
                                } catch (e) {
                                    console.log(e);
                                    alert("添加新电视节目总集失败");
                                }
                            } else {
                                // Error :(
                                alert(":( ajax error");
                            }
                        }
                    };
                    // request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
                    request.send(formData);
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
        ],
        close: function(event, ui) {
            // 关闭对话框 时清空表单
            document.getElementById("addTVShowsInfo").reset();
        }
    });

    $("#js-addShows").button().click(function() {
        $("#DialogAddShows").dialog("open");
    });

    // 出错提示
    $("#DialogDeleteEpisodeFirst").dialog({
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

    // 上传附件对话框
    $("#DialogEditSeriesAppendix").dialog({
        modal: true,
        autoOpen:false,
        width: 300,
        open: function(event, ui) {
            var soid = getCookie("soid");
            if ("" == soid) {
                soid = $("#SeriesTable").children("tbody").children(".selected").attr("data-oid");
            }
            $("#js-soid").val(soid);
        },
        buttons: {
            "上传附件": function() {
                // フォームデータを取得
                var formdata = new FormData(document.getElementById('editRightSeriesJumpAppendixInfo'));

                // POSTでアップロード
                $.ajax({
                    url  : $("#js-baseurl").val() + "/xhrupload",
                    type : "POST",
                    data : formdata,
                    cache       : false,
                    contentType : false,
                    processData : false,
                    dataType    : "html"
                })
                .done(function(data, textStatus, jqXHR) {
                    $("#DialogEditSeriesAppendix").dialog("close");
                    // refresh
                    $("#SeriesTable").children("tbody").first().children(".selected").trigger("click");
                    document.getElementById('editRightSeriesJumpAppendixInfo').reset();
                    try {
                        var json = JSON.parse(data);
                        if (0 != json.code) {
                            alert(json.msg);
                            return false;
                        }
                    } catch (e) {
                        alert("附件上传失败！");
                        console.error(e);
                    }
                    console.log(data);
                })
                .fail(function(jqXHR, textStatus, errorThrown) {
                    alert("#DialogEditSeriesAppendix upload failed: " + errorThrown);
                });
            }
        }
    });

    // 电视节目总集 编辑操作 (查看分集 删除分集 返回 保存 添加附件)
    var wrapper = document.getElementsByClassName("buttons-wrapper")[0];
    Array.prototype.forEach.call(wrapper.childNodes, function(elem) {
        // 3 #text
        if (1 == elem.nodeType) {
            $(elem).button();
        }
    });

    // Ctrl + S 保存草稿
    document.onkeydown = function(event) {
        event = event||window.event;
        if (event.ctrlKey && event.keyCode== 83) {
            event.returnvalue = false;
            setTimeout(function() {
                EditShowsSub();
            }, 1);
            return false;
        }
    };

});

function EditShowsAppendix() {
    $("#DialogEditSeriesAppendix").dialog("open");
}

function reloadTVShowsTitle() {
    var url = baseurl + "/listTVShowsTitle";

    $.post( url, function( data ) {
        var table = document.getElementById("SeriesTable");
        var target = $(table).children("tbody").first();
        target.html("");

        for (var i = 0; i < data.length; i++) {
            var tr = $("<tr>").attr("data-oid", data[i].oid);
            target.append(tr);
            $("<td>").addClass("col-0").attr("title", "arrow").html("<div class='tr_arrow'></div>").appendTo(tr);
            $("<td>").addClass("col-1").attr("title", (i+1)).html(i+1).appendTo(tr);
            $("<td>").addClass("col-2").attr("title", data[i].title).html(data[i].title).appendTo(tr);
            var a = $("<a>").attr({
                "href":"javascript:void(0)",
                "title": "删除电视节目总集",
                "onclick": "deleteSeries(this)"
            }).html("删除");
            $("<td>").addClass("col-3").attr("title", "delete").append(a).appendTo(tr);
        }
        target.append(tr);

        initSeriesTable(table);
    });
}

/**
 * 删除电视节目总集
 * @param obj   DOM <a>删除</a>
 */
function deleteSeries(obj) {
    var tr = $(obj).parent("td").parent("tr");
    var soid = tr.attr("data-oid");
    var url = baseurl + "/countShowsEpisode";
    $.ajax({
        type: "GET",
        url: url,
        data: {"soid": soid}
    }).done(function(data, textStatus, jqXHR) {
        console.log(data);
        // 电视剧总集下面有分集
        if (data > 0) {
            $("#DialogDeleteEpisodeFirst").dialog("open");
            return false;
        }
        doDeleteSeries(soid);
        reloadTVShowsTitle(soid);

    }).fail(function(jqXHR, textStatus, errorThrown) {
        console.log("tvplayseries.js @func: deleteSeries" + errorThrown);
        return false;
    });
}

function doDeleteSeries(soid) {
    var url = baseurl + "/deleteSeries";
    $.ajax({
        type: "GET",
        url: url,
        data: {"soid": soid, "type": "tvshow"}
    }).done(function(data, textStatus, jqXHR) {
        console.log(data);

        reloadTVShowsTitle();
    });
}

// 左侧电视节目总集列表: 填充, 点击行切换右侧详情
function initSeriesTable(table) {
    if (null == table) {
        table = document.getElementById("SeriesTable");
    }
    // 填充
    padScrollTable(table, 14, 4);

    // 点击行切换右侧详情
    var url = baseurl + "/respTVShows";

    // 附件图片所在url /PushUI/resource/appendix/{oid}/xxx.jpg
    var basesrc = $("#project_root").val() + "/resource/appendix/";
    var tr = $(table).children("tbody").children("tr").not(".dumb");

    tr.each(function() {
        var that = $(this);
        that.on("click", function() {
            tr.not(that).removeClass("selected");
            that.addClass("selected");

            var soid = that.attr("data-oid");
            $.ajax({
                type: "GET",
                url: url,
                data: {"soid": soid}
            })
            .done(function (data, textStatus, jqXHR) {
                document.getElementById("EditSeriesInfo").reset();
                // 电视节目总集: fetch xhr data and set dom
                $("#EditSeriesTitle").val( data.shows.title );
                $("#EditSeriesHost").val( data.shows.host );
                $("#EditSeriesRating").val( data.shows.rating );
                $("#EditSeriesSourceFrom").val( data.shows.sourcefrom );
                $("#EditSeriesType").val( data.shows.tvshowtype );
                $("#EditSeriesLanguage").val( data.shows.languageid );
                $("#EditSeriesIntroduction").val( data.shows.introduction );
                $("#EditSeriesCountry").val(data.shows.countryid);

                // 缩略图 海报
                var poster = document.getElementById("js-poster");
                var thumb = document.getElementById("js-thumb");
                poster.src = ""; thumb.src = "";

                for (var i = 0; i < data.appendix.length; i++) {
                    var src = basesrc + data.appendix[i].url;
                    if (data.appendix[i].type == "海报") {
                        poster.src = src;
                    } else if (data.appendix[i].type == "缩略图") {
                        thumb.src = src;
                    }
                }
                setCookie("soid", soid, 30);
                // 缩略图列表  edit.js
                renderAppendixList(data.appendix);

                // 全集内容列表
                renderSeriesEpisodes(data.episodes);
            })
            .fail(function(jqXHR, textStatus, errorThrown) {
                console.error("@file: tvplayseries.js @function: initSeriesTable failed! " + errorThrown);
            });

            getQrCode(soid, 'show', 'js-qrcode');
        });
    });
}

/**
 * 载入页面自动选择左边列表点击
 * @param table: DOM nodeName: TABLE / TR
 */
function onloadClick(table) {
    if (table.nodeName != "TABLE") {
        console.warn(table);
        return false;
    }
    var tr = $(table).children("tbody").first().children("tr").not(".dumb");

    if (tr.length > 0) {
        var soid = getCookie('soid');
        if (soid == '') {
            tr.first().trigger('click');
        } else {
            var i = 0;
            for (; i < tr.length; i++) {
                if (tr[i].getAttribute('data-oid') == soid) {
                    $(tr[i]).trigger('click');
                    break;
                }
            }
            if (i == tr.length) {
                tr.first().trigger("click");
            }
        }
    }
    return true;
}

/**
 * 电视剧总集中 分集列表
 * @param data array:
 * [
 * {episodeoid: "0B4A4FDA862E6B8DD304E3C880EA6EE5", runtime: "45", title: "老乡帮老乡一亩地见分晓"},
 * {episodeoid: "287A3939C180E1F5B06A5D1B16140B85", runtime: "5", title: "抗旺药残太严重"},
 * ...
 * ]
 */
function renderSeriesEpisodes(data) {
    var target = document.getElementById("js-episodes");
    if (typeof target === "undefined" || target == null) {
        console.error("@file: tvshows.js, @func: renderSeriesEpisodes, can't find #js-episodes");
        return false;
    }
    target.innerHTML = "";

    for (var i = 0; i < data.length; i++) {
        var tr = $("<tr>").attr("data-oid", data[i].episodeoid);
        target.appendChild(tr.get(0));

        $("<td>").addClass("col-0").html("<div class='tr_arrow'></div>").appendTo(tr);
        $("<td>").addClass("col-1").attr("title", i + 1).html(i + 1).appendTo(tr);
        $("<td>").addClass("col-2").attr("title", data[i].title).html(data[i].title).appendTo(tr);
        $("<td>").addClass("col-3").attr("title", data[i].runtime).html(data[i].runtime + "分钟").appendTo(tr);
        var input = $("<input>").attr({
            "type": "radio",
            "name": "EpisodeOID",
            "title": "radio"
        });
        $("<td>").addClass("col-4").attr("title", "单选操作").append(input).appendTo(tr);
    }

    padScrollTable(target, 4, 5);

    var rows = $(target).find("tr").not(".dumb");
    rows.on("click", function() {
        var that = $(this);
        rows.not(that).removeClass("selected");
        that.addClass("selected");
        that.children("td").last().children("input[type='radio']").get(0).checked = "checked";

        setCookie("oid", this.getAttribute("data-oid"), 5);
    });
    // 默认选中一条全集内容列表
    onloadClick(target.parentNode);

    return true;
}

// 提交修改电视剧总集基本信息
function EditShowsSub() {
    var soid = $("#SeriesTable").children("tbody").first().children(".selected").attr("data-oid");
    var url = baseurl + "/editTvShowSeries";
    var data = {
        "soid" : soid,
        "title": $("#EditSeriesTitle").val(),
        "host" : $("#EditSeriesHost").val(),
        "rating" : $("#EditSeriesRating").val(),
        "sourcefrom" : $("#EditSeriesSourceFrom").val(),
        "tvshowtype" : $("#EditSeriesType").val(),
        "languageid" : $("#EditSeriesLanguage").val(),
        "countryid" : $("#EditSeriesCountry").val(),
        "introduction" : $("#EditSeriesIntroduction").val()
    };
    $.ajax({
        type: "POST",
        url: url,
        data: data
    }).done(function(data, textStatus, jqXHR) {
        // console.log(data);

        if (data.media > 0 && data.tvshow > 0) {
            // refresh 文件名称
            var td = $("#SeriesTable").find(".selected").find(".col-2");
            var title = $("#EditSeriesTitle").val();
            td.attr("title", title).html(title);

            $("#DialogSaveFeedback").dialog("open");
            setTimeout(function() {
                $("#DialogSaveFeedback").dialog("close");
            }, 2000);
        }
    }).fail(function(jqXHR, textStatus, errorThrown) {
        console.error("@file: tvshows.js @function: EditSeriesSub failed! " + errorThrown);
    });

    // 保存二维码图
    saveQrCode(soid, 'show');
}

/**
 * 全集内容列表中 删除电视节目分集
 * @returns {boolean}
 */
function deleteTvShowEpisode() {
    var selected = $("#js-episodes").children(".selected").get(0);
    if (typeof selected === "undefined" || selected == null) {
        alert("分集列表为空 或 没有选择分集!");
        return false;
    }
    var oid = selected.getAttribute("data-oid");
    if(!confirm("确定要删除分集吗?")) {
        return false;
    }

    var url = $("#js-baseurl").val() + "/delContent";
    var data = {
        "oid" : oid,
        "mediatypeid" : 3
    };
    $.ajax({
        type: "GET",
        url: url,
        data: data
    })
    .done(function () {
        unsetCookie("oid");

        var table = document.getElementById("SeriesTable");
        var soid = $("table").children("tbody").first().children(".selected").attr("data-oid");
        setCookie("soid", soid, 5);
        initSeriesTable(table);
    })
    .fail(function(jqXHR, textStatus, errorThrown) {
        alert("删除电视节目分集失败!");
        console.error("@file: tvshows.js @function: deleteTvShowEpisode failed! " + errorThrown);
    });
    return true;
}