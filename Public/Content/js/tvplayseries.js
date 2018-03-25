/**
 * Created by Administrator on 2017-03-15.
 * 电视剧总集
 */
$(function() {
    baseurl = $("#js-baseurl").val();

// 电视剧总集页面
    var table = document.getElementById("SeriesTable");
    initSeriesTable(table);

    padScrollTable(document.getElementById("js-episodes"), 4, 5);

    // 添加电视剧总集 弹出对话框
    $("#addSeriesDialog").dialog({
        modal: true,
        autoOpen: false,
        width: 600,
        buttons: {
            "提交保存": function() {
                var form = document.getElementById("addSeriesInfo");

                if ($("#series-title").val().trim() == '') {
                    alert("总剧集不能为空!");
                    return false;
                }

                // upload file using FormData
                var data = new FormData( form );
                var url = form.action;
                $.ajax({
                    type: "post",
                    data: data,
                    url: url,
                    cache       : false,
                    contentType : false,
                    processData : false,
                    dataType    : "html"
                })
                .done(function(resp, textStatus, jqXHR) {
                    if (resp.code < 0) {
                        alert(resp.msg);
                        resp = null;
                        return false;
                    }
                    $("#addSeriesDialog").dialog("close");
                    $("#addSeriesInfo").get(0).reset();
                    reloadSeriesList();

                    return true;
                })
                .fail(function( jqXHR, textStatus, errorThrown) {
                    alert("@file: tvplayseries.js. xhr post addSeriesInfo " + errorThrown);
                    return false;
                })
                .always(function (jqXHR, textStatus, errorThrown) {});
                return true;
            },
            "返回": function() {
                $(this).dialog("close");
            }
        }
    });

    $("#js-addSeries").button().click(function() {
        $("#addSeriesDialog").dialog("open");
    });

    // 错误提示对话框
    $("#DialogDeleteEpisodeFirst").dialog({
        modal: true,
        autoOpen: false,
        width: 400,
        buttons: {"OK": function() {$(this).dialog("close");}}
    });

    // 上传附件对话框
    $("#DialogEditSeriesAppendix").dialog({
        modal: true,
        autoOpen:false,
        width: 300,
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
                }).done(function(data, textStatus, jqXHR) {
                    $("#DialogEditSeriesAppendix").dialog("close");
                    // refresh
                    $("#SeriesTable").children("tbody").first().children(".selected").trigger("click");
                    document.getElementById('editRightSeriesJumpAppendixInfo').reset();
                    if (data.code != 0) {
                        alert(data.msg);
                    }
                }).fail(function(jqXHR, textStatus, errorThrown) {
                    alert("#DialogEditSeriesAppendix upload failed: " + errorThrown);
                });
            }
        }
    });

    // jquery-ui buttons
    (function() {
        var wrapper = document.getElementsByClassName("buttons-wrapper")[0];
        var buttons = [];
        for (var i = 0; i < wrapper.children.length; i++) {
            $(wrapper.children[i]).button();
        }

        // 保存总集Ctrl+S
        document.onkeydown = function(event) {
            event = event || window.event;
            if (event.ctrlKey && event.keyCode ==83) {
                event.returnvalue = false;
                setTimeout(function() {
                    EditSeriesSub();
                }, 1);
                return false;
            }
        }

    })()

    // 保存电视剧总集feedback
    $('#dialog-1').dialog({
        modal: true,
        autoOpen:false,
        width: 300,
        buttons: {
            "确定": function() {
                $(this).dialog('close');
            }
        }
    });

});

//  左侧电视剧总集列表: 填充, 点击行切换右侧详情
function initSeriesTable(table) {
    if (null == table) {
        table = document.getElementById("SeriesTable");
    }
    // 填充
    padScrollTable(table, 14, 4);

    // 点击行切换右侧详情
    var url = baseurl + "/respTVSeries";
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
                type: "get",
                url: url,
                data: {"soid": soid}
            })
            .done(function (data, textStatus, jqXHR) {
                document.getElementById("EditSeriesInfo").reset();
                // fetch xhr data and set dom
                $("#EditSeriesTitle").val( data.series.title );
                $("#EditSeriesEpisodes").val( data.series.episodes );
                $("#EditSeriesRating").val( data.series.rating );
                $("#EditSeriesDirector").val( data.series.director );
                $("#EditSeriesActor").val( data.series.actor );
                $("#EditSeriesYear").val( data.series.yearid );
                $("#EditSeriesGenre").val( data.series.genreid );
                $("#EditSeriesCountry").val( data.series.countryid );
                $("#EditSeriesLanguage").val( data.series.languageid );
                $("#EditSeriesTag").val( data.series.tagid );
                $("#EditSeriesIntroduction").val( data.series.introduction );

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
                // 缩略图列表
                renderAppendixList(data.appendix);

                // 全集内容列表
                renderSeriesEpisodes(data.episodes);
            })
            .fail(function(jqXHR, textStatus, errorThrown) {
                console.log("@file: tvplayseries.js @function: initSeriesTable failed! " + errorThrown);
            });

            getQrCode(soid, 'tv', 'js-qrcode');
        });
    });

    var soid = getCookie('soid');
    // alert(soid);
    if (soid == '') {
        tr.first().trigger('click');
    } else {
        for (i = 0; i < tr.length; i++) {
            if (tr[i].getAttribute('data-oid') == soid) {
                $(tr[i]).trigger('click');
            }
        }
    }
}

/**
 * target #js-episodes
 * @param data
 * [
 * {'episodeoid':'734CCD42DC810E9AD0012B1FE6836D36', 'title':'Stars.E01.ts', 'runtime':'40', 'episodeindex':1},
 * {...},
 * {...},
 * ...
 * ]
 */
function renderSeriesEpisodes(data) {
    // console.log(data);
    var target = $("#js-episodes");
    if (target.length === 0) {
        console.log("@file: tvplayseries.js, @func: renderSeriesEpisodes, can't find #js-episodes");
        return false;
    }
    target.html("");

    for (var i = 0; i < data.length; i++) {
        var tr = $("<tr>").attr("data-oid", data[i].episodeoid);
        tr.appendTo(target);
        $("<td>").addClass("col-0").html("<div class='tr_arrow'></div>").appendTo(tr);
        $("<td>").addClass("col-1").attr("title", i+1).html(i+1).appendTo(tr);
        $("<td>").addClass("col-2").attr("title", data[i].title).html(data[i].title).appendTo(tr);
        $("<td>").addClass("col-3").attr("title", data[i].runtime).html(data[i].runtime + "分钟").appendTo(tr);
        var input = $("<input>").attr({
            "type" : "radio",
            "name" : "EpisodeOID",
            "title": "radio"
        });
        $("<td>").addClass("col-4").attr("title", "单选操作").append(input).appendTo(tr);
    }

    padScrollTable(target.get(0), 4, 5);

    var rows = target.find("tr").not(".dumb");
    rows.on("click", function() {
        var that = $(this);
        rows.not(that).removeClass("selected");
        that.addClass("selected");
        that.children("td").last().children("input[type='radio']").get(0).checked = "checked";

        setCookie("oid", this.getAttribute("data-oid"), 5);
    });

    return true;
}

/**
 * 删除电视节目总集
 * @param obj
 */
function deleteSeries(obj) {
    var tr = $(obj).parent("td").parent("tr");
    var soid = tr.attr("data-oid");
    var url = baseurl + "/countSeriesEpisode";
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
        data: {"soid": soid, "type": "series"}
    }).done(function(data, textStatus, jqXHR) {
        console.log(data);

        reloadSeriesList();
    });
}

// 重新加载电视剧总集名列表
function reloadSeriesList() {
    var url = baseurl + "/listTVPlaySeriesTitle";

    $.post( url, function( data ) {
        console.log(data);

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
                "title": "删除电视剧总集",
                "onclick": "deleteSeries(this)"
            }).html("删除");
            $("<td>").addClass("col-3").attr("title", "delete").append(a).appendTo(tr);
        }
        target.append(tr);

        initSeriesTable(table);
    });
}

// 电视剧总集 添加 缩略图 / 海报
function EditSeriesAppendix() {
    var tr = $("#SeriesTable").children("tbody").children(".selected");
    var soid = tr.attr("data-oid");
    setCookie("soid", soid, 5);
    // open dialog
    $("#js-soid").val( soid );
    $("#DialogEditSeriesAppendix").dialog("open");
}

// 提交修改电视剧总集基本信息
function EditSeriesSub() {
    var soid = $("#SeriesTable").children("tbody").first().children(".selected").attr("data-oid");
    var url = baseurl + "/editTvPlaySeries";
    var data = {
        "soid" : soid,
        "title": $("#EditSeriesTitle").val(),
        "episodes": $("#EditSeriesEpisodes").val(),
        "rating" : $("#EditSeriesRating").val(),
        "director" : $("#EditSeriesDirector").val(),
        "actor" : $("#EditSeriesActor").val(),
        "yearid" : $("#EditSeriesYear").val(),
        "genreid" : $("#EditSeriesGenre").val(),
        "countryid" : $("#EditSeriesCountry").val(),
        "languageid" : $("#EditSeriesLanguage").val(),
        "tagid" : $("#EditSeriesTag").val(),
        "introduction" : $("#EditSeriesIntroduction").val()
    };

    $.ajax({
        type: "GET",
        url: url,
        data: data
    }).done(function(data, textStatus, jqXHR) {
        if (data.media > 0 && data.series > 0) {
            $('#js_feedback').html('电视剧总集保存成功!');
            $('#dialog-1').dialog('open');

            // refresh 文件名称
            var td = $("#SeriesTable").find(".selected").find(".col-2");
            var title = $("#EditSeriesTitle").val();
            td.attr("title", title).html(title);
        } else {
            $('#js_feedback').html('电视剧总集保存失败!');
            $('#dialog-1').dialog('open');
        }
    }).fail(function(jqXHR, textStatus, errorThrown) {
        console.error("@file: tvplayseries.js @function: EditSeriesSub failed! " + errorThrown);
    });

    saveQrCode(soid, 'tv');
}

/**
 * 删除电视剧分集 mediatypeid 2
 * @constructor
 */
function deleteSeriesEpisode() {
    var tr = $("#js-episodes").children(".selected");
    if (tr.length == 0) {
        alert("你还未选择要删除的分集!");
        return;
    }
    var oid = tr.attr("data-oid");
    $.ajax({
        type: "GET",
        url: baseurl + "/delContent",
        data: {"oid" : oid, "mediatypeid": 2}
    })
    .done(function(data, textStatus, jqXHR) {
        console.log(data);
        // reload
        var table = document.getElementById("SeriesTable");
        var soid = $("table").children("tbody").first().children(".selected").attr("data-oid");
        setCookie("soid", soid, 5);
        unsetCookie("oid");
        initSeriesTable(table);
    })
    .fail(function(jqXHR, textStatus, errorThrown) {
        console.error("@file: tvplayseries.js @function: deleteSeriesEpisode fail: " + errorThrown);
    })

}