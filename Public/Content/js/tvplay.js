/**
 * Created by Administrator on 2017-03-13.
 * depends on edit.js
 * 电视剧单集
 */
$(function() {
    // 电视剧单集页面
    // init buttons
    // 查找剧集
    $("#js-SearchTVSeries").button().on("click", function () {
        searchTVDialog.dialog("open");
        $("#searchTVSerialBut").button();
    });
    // 剧集详情
    $("#js-editTVPlay").button().on("click", function() {
        // 跳转到哪个总集
        setCookie("soid", $("#tvplay-soid").val(), 5);
    });

    $("#js-cut").button();

    // init dialogs
    var searchTVDialog = $("#searchTVDialog");
    var addSeriesDialog = $("#addSeriesDialog");

    // 查找剧集 弹出对话框
    searchTVDialog.dialog({
        modal: true,
        autoOpen: false,
        width: 740,
        buttons: {
            "添加新剧集" : function() {
                addSeriesDialog.dialog("open");
            },
            "选择": function() {
                var selectedtr = $("#searchSeriesTable").children("tbody").children("tr.selected");
                var soid = selectedtr.attr("soid");
                if (null == soid || "" == soid) {
                    alert("还没有选择总集");
                    return false;
                }
                $(this).dialog("close");
                // 所属剧集 名称
                $("#tvplay-series").val(selectedtr.children("td.td-2").html());
                // 总集数
                $("#tvplay-total").val(selectedtr.children("td.td-7").html());
                // 所属剧集 OID "S20170314..."
                $("#tvplay-soid").val(soid);
                if (soid != "") {
                    setCookie("soid", soid, 5);
                }
            },
            "返回": function() {$(this).dialog("close");}
        }
    });

    // 添加电视剧总集 弹出对话框
    addSeriesDialog.dialog({
        modal: true,
        autoOpen: false,
        width: 600,
        close: function() {
            SearchSeriesByCondition();
        },
        buttons: {
            "提交保存": function() {
                var form = document.getElementById("addSeriesInfo");

                if ($("#series-title").val().trim() == '') {
                    alert("总剧集不能为空!");
                    return false;
                }

                var data = new FormData( form );
                var url = form.action;   // newTVSeries
                $.ajax({
                    type: "post",
                    data: data,
                    url: url,
                    cache       : false,
                    contentType : false,
                    processData : false,
                    dataType    : "html"
                }).done(function(resp, textStatus, jqXHR) {
                    if (resp.code < 0) {
                        alert(resp.msg);
                        resp = null;
                        return false;
                    }
                    addSeriesDialog.dialog("close");
                    $("#addSeriesInfo").get(0).reset();

                    return true;
                }).fail(function( jqXHR, textStatus, errorThrown) {
                    alert(errorThrown);
                    return false;
                }).always(function (jqXHR, textStatus, errorThrown) {});
                return true;
            },
            "返回": function() {
                $(this).dialog("close");
            }
        }
    });

    showTVPlayInfo();

    // 弹出对话框 剧集列表
    SearchSeriesByCondition();
});

// 电视剧列表点击行显示对应信息
function showTVPlayInfo() {
    var $rows = $("#tvseries-table").children("tbody").find("tr:not(.dumb)");
    if (0 == $rows.length) {
        return false;
    }
    var url = $("#js-baseurl").val() + "/TVPlayEpisodeInfo";

    $rows.each(function (i, tr) {
        tr.addEventListener("click", function() {
            $(this).addClass("selected");
            $rows.not($(this)).removeClass("selected");

            // 系统命名: oid
            var oid = $(this).attr("data-oid");
            $("#system-name").val(oid);

            // 所属剧集 当前集数 	总集数 分集时长
            var data = {"oid":oid};
            $.ajax({
                type: "POST",
                url: url,
                data: data
            }).done(function(data, textStatus, jqXHR) {
                // console.log(data);
                $("#tvplay-episode").val(data.assetname);
                $("#tvplay-series").val(data.title);
                $("#tvplay-soid").val(data.seriesoid);
                $("#tvplay-current").val(data.episodeindex);
                $("#tvplay-total").val(data.episodes);
                $("#tvplay-length").val(data.runtime);
                $("#tvplay-info").val(data.introduction);

                var slicetarget = document.getElementById("js-slicestatus");
                if (data.slice == 0) {
                    slicetarget.innerHTML = "未备播";
                } else if (data.slice == 1) {
                    slicetarget.innerHTML = "<font color='green'>已备播</font>";
                } else {
                    slicetarget.innerHTML = "<font color='red'>备播状态获取失败!</font>";
                }
                getQrCode(oid, 'tv', 'jsqrcode');
            }).fail(function(jqXHR, textStatus, errorThrown) {
                alert("tvplay.js showTVPlayInfo() " + errorThrown);
            });

            // 刷新页面 记录oid, 30min
            setCookie('oid', oid, 30);
            //  用户上传附件 appendixoid, input[type="hidden"]
            $("#js-attachoid").val(oid);

            // 缩略图 / 附件列表
            var thumb = document.getElementById("js-thumb");
            var poster = document.getElementById("js-poster");
            getAppendixList(oid, thumb, poster);   // edit.js
        });
    });

    if (getCookie("oid") == "") {
        $($rows.get(0)).trigger("click");
    } else {
        var oid = getCookie("oid");
        for (var i = 0; i < $rows.length; i++) {
            var selected = $($rows.get(i));
            if (selected.attr("data-oid") == oid) {
                selected.trigger("click");
                break;
            }
        }
        // oid not found
        if (i == $rows.length) {
            $($rows.get(0)).trigger("click");
        }
    }

}

/**
 * 保存电视剧单集草稿
 */
function saveDraft() {
    var fb = document.getElementById("fb_savedraft");
    fb.style.display = "inline-block";
    fb.innerHTML = "保存中...";
    try {
        // save tv series episode
        var tr = $("#tvseries-table").children("tbody").children(".selected");
        var oid = tr.attr("data-oid");
        var url = $("#js-baseurl").val() + "/saveEpisodeDraft";
        var data = {
            "episodeoid" : oid,
            "seriesoid" : $("#tvplay-soid").val(),
            "episodetitle" : $("#tvplay-episode").val(),
            "seriestitle" : $("#tvplay-series").val(),
            "episodeindex" : $("#tvplay-current").val(),
            "episodes" : $("#tvplay-total").val(),
            "runtime" : $("#tvplay-length").val(),
            "introduction" : $("#tvplay-info").val()
        };
        $.ajax({
            type: "POST",
            url: url,
            data: data
        }).done(function(data, textStatus, jqXHR) {
            if (data.lastid > 0 || data.rows > 0) {
                 // 更新片名:
                 var td = $("#tvseries-table").children("tbody").first().children(".selected").children(".col-2");
                 var title = $("#tvplay-episode").val();
                 td.attr("title", title).html(title);
                 fb.innerHTML = "<font color='green'>保存草稿成功!</font>";
            }
        }).fail(function( jqXHR, textStatus, errorThrown ) {
            alert("tvplay.js saveEpisodeDraft() " + errorThrown);
        });
        // cookie.js
        setCookie('oid', oid, 5);
    } catch (e) {
        fb.innerHTML = "<font color='red'>保存失败</font>";
        $(fb).fadeOut(2000);
        console.log(e);
        return false;
    }
    // feedback
    fb.innerHTML = "<font color='green'>保存草稿...</font>";

    $(fb).fadeOut(2000);

    saveQrCode(oid, 'tv');

    return true;
}

// 根据片名查找电视剧总集
// ↓ ↓ -- template -- ↓ ↓
/*
 <table id="searchSeriesTable" class="table table-fixed">
 <tbody>
 <tr data-soid="S2017022400000000000000000000553">
 <td class="td-0"><div class="tr_arrow"></div></td>
 <td class="td-1">1</td>
 <td class="td-2">来自星星的你</td>
 <td class="td-3">韩国</td>
 <td class="td-4">中语中字</td>
 <td class="td-5">2013</td>
 <td class="td-6">爱情</td>
 <td class="td-7">21</td>
 <td class="td-8">1</td>
 <td class="td-9"><input type="radio" name="SelectedSeriesOID" title="radio"></td>
 </tr>
 </tbody>
 </table>
 */
// ↑ ↑ -- template -- ↑ ↑
function SearchSeriesByCondition() {
    var url = $("#js-baseurl").val() + "/listTVPlaySeries";
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
    }).done(function (data, textStatus, jqXHR) {
        var tbody = $("#searchSeriesTable").children("tbody").get(0);
        if (typeof tbody == "undefined") {
            return false;
        }
        tbody.innerHTML = "";
        var target = $(tbody);

        for (var i = 0; i < data.length; i++) {
            var tr = $("<tr>").attr("soid", data[i].oid);
            target.append(tr);
            $("<td>").addClass("td-0").html("<div class='tr_arrow'></div>").appendTo(tr);
            $("<td>").addClass("td-1").html(i+1).appendTo(tr);
            $("<td>").addClass("td-2").html(data[i].title).appendTo(tr);
            $("<td>").addClass("td-3").html(data[i].country).appendTo(tr);
            $("<td>").addClass("td-4").html(data[i].language).appendTo(tr);
            $("<td>").addClass("td-5").html(data[i].year).appendTo(tr);
            $("<td>").addClass("td-6").html(data[i].genre).appendTo(tr);
            $("<td>").addClass("td-7").html(data[i].episodes).appendTo(tr);
            $("<td>").addClass("td-8").html(data[i].imported).appendTo(tr);
            $("<td>").addClass("td-9").html("<input type='radio' name='SelectedSeriesOID' title='radio' />").appendTo(tr);
        }

        // 选择电视剧总集
        var rows = target.children("tr").not(".dumb");
        rows.on("click", function () {
            $(this).addClass("selected");
            var radio = this.childNodes[this.childNodes.length-1].childNodes[0];
            radio.checked = "checked";
            rows.not($(this)).removeClass("selected");
        });
        $(rows.get(0)).trigger("click");

        return true;
    }).fail(function( jqXHR, textStatus, errorThrown) {
        alert(errorThrown);
        return false;
    }).always(function (jqXHR, textStatus, errorThrown) {
        var table = document.getElementById("searchSeriesTable");
        padScrollTable(table, 10, 10);
    });
}

function cuttvplay() {
    var oid = $("#tvseries-table").find("tr.selected").attr("data-oid");
    doCut(oid);   // edit.js
}

/**
 * 提交电视剧分集
 */
function submitEpisode() {
    var soid = document.getElementById("tvplay-soid").value;

    if (soid === '' || soid === 0) {
        alert('还没有设置所属剧集!');
        return false;
    }
    var tbody = document.getElementById("tvseries-table").firstElementChild;
    var tr = tbody.querySelector('.selected');
    var oid = tr.getAttribute("data-oid");

    submitEditMedia(oid);
}