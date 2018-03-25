/**
 * Created by Administrator on 2017-03-20.
 */
$(function() {
    baseurl = document.getElementById("js-baseurl").value;

    // 电视节目——添加新电视节目总集
    $("#addSeriesDialog").dialog({
        autoOpen: false,
        modal: true,
        width: 700,
        minheight: 250,
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
                                    $("#addSeriesDialog").dialog("close");
                                    // refresh TV shows list
                                    SearchShowsByCondition();
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

    // 查找电视节目总集 弹出对话框
    $("#searchTVDialog").dialog({
        modal: true,
        autoOpen: false,
        width: 740,
        // 加载电视节目总集列表
        create: function(event, ui) {
            // console.log("#searchTVDialog created");
            return new SearchShowsByCondition();
        },
        buttons: [
            {
                text: "添加新剧集",
                icons: {
                    primary: "ui-icon-plusthick"
                },
                click: function() {
                    $("#addSeriesDialog").dialog("open");
                }
            },
            {
                text: "选择",
                icons: {
                    primary: "ui-icon-check"
                },
                click: function() {
                    var selectedtr = $("#searchTVShowTable").children("tbody").children("tr.selected");
                    var soid = selectedtr.attr("soid");
                    if (null == soid || "" == soid) {
                        alert("还没有选择总集");
                        return false;
                    }
                    $(this).dialog("close");
                    // 所属剧集 名称
                    $("#episode-tvshow").val(selectedtr.children("td.td-2").html());
                    // 所属剧集 OID "S20170314..." input[type="hidden"]
                    $("#episode-tvshowoid").val(soid);
                    if (soid != "") {
                        setCookie("soid", soid, 5);
                    }
                }
            },
            {
                text: "返回",
                icons: {
                    primary: "ui-icon-arrowreturnthick-1-w"
                },
                click: function() {
                    $(this).dialog("close");
                }
            }
        ]
    });

    // 电视节目 查找总节目集
    $("#js-searchTVShows").button().on("click", function() {
        $("#searchTVDialog").dialog("open");
    });
    // 错误提示对话框
    $("#DialogRequireTitle").dialog({
        autoOpen: false,
        modal: true,
        width: 300,
        buttons: {
            "OK": function() {$(this).dialog("close")}
        },
        close: function(event, ui) {
            document.getElementById("fb_savedraft").innerHTML = "";
        }
    });
    $("#searchTVShowsBut").button().on("click", function() {
        return new SearchShowsByCondition();
    });
    // 电视节目 总节目集详情
    $("#js-editTVShows").button().on("click", function() {
        saveDraft();
    });
    TVShowEpisodeInfo();
});

/**
 * 查找电视节目总集 加载电视节目总集列表
 * @constructor
 */
function SearchShowsByCondition() {
    if (typeof baseurl === "undefined" || baseurl == '') {
        baseurl = $("#js-baseurl").val();
    }
    var url = baseurl + "/listTVShows";
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
            var tbody = $("#searchTVShowTable").children("tbody").get(0);
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
                $("<td>").addClass("td-5").html(data[i].tvshowtype).appendTo(tr);
                $("<td>").addClass("td-6").html(data[i].sourcefrom).appendTo(tr);
                $("<td>").addClass("td-7").html(data[i].imported).appendTo(tr);
                $("<td>").addClass("td-8").html("<input type='radio' name='tvshows' title='radio' />").appendTo(tr);
            }

            // 选择电视节目总集
            var rows = target.children("tr").not(".dumb");
            rows.on("click", function () {
                $(this).addClass("selected");
                var radio = this.childNodes[this.childNodes.length-1].childNodes[0];
                radio.checked = "checked";
                rows.not($(this)).removeClass("selected");
            });
            rows.first().trigger("click");

            return true;
        })
        .fail(function( jqXHR, textStatus, errorThrown) {
            alert(errorThrown);
            console.error("@file: tvprogram.js @func: SearchShowsByCondition ajax failed");
            return false;
        })
        .always(function (jqXHR, textStatus, errorThrown) {
            var table = document.getElementById("searchTVShowTable");
            padScrollTable(table, 10, 10);
        });

}

/**
 * 取得电视节目单集信息
 */
function TVShowEpisodeInfo() {
    var url = $("#js-baseurl").val() + "/TVShowEpisodeInfo";

    var tr = $("#tvprogram-table").children("tbody").first().children("tr").not(".dumb");
    tr.each(function() {
        this.addEventListener("click", function (event) {
            if (event.stopPropagation) {
                event.stopPropagation();
            } else {
                event.cancelBubble();
            }
            var that = $(this);
            var others = tr.not(that);

            if (others.hasClass("selected")) others.removeClass("selected");
            if (!that.hasClass("selected")) that.addClass("selected");

            // 系统命名: oid
            var oid = this.getAttribute("data-oid");
            $("#system-name").val(oid);
            // 片名
            // var title = $(this).find("td").eq(2).attr("title");
            document.getElementById("fb_savedraft").innerHTML = "";

            // 所属剧集 当前集数 	总集数 分集时长
            var data = {"oid":oid};
            $.ajax({
                type: "GET",
                url: url,
                data: data
            })
                .done(function(data, textStatus, jqXHR) {
                    // console.log(data);
                    $("#episode-title").val(data.title);           // 分集名称
                    $("#episode-tvshowoid").val(data.tvshowoid);   // 总集OID
                    $("#episode-tvshow").val(data.tvshow);         // 总集名称
                    $("#episode-actor").val(data.actor);
                    $("#episode-info").val(data.introduction);
                    $("#episode-runtime").val(data.runtime);
                    $("#episode-theme").val(data.theme);

                    var slicetarget = document.getElementById("js-slicestatus");
                    if (data.slice == 0) {
                        slicetarget.innerHTML = "未备播";
                    } else if (data.slice == 1) {
                        slicetarget.innerHTML = "<font color='green'>已备播</font>";
                    } else {
                        slicetarget.innerHTML = "<font color='red'>备播状态获取失败!</font>";
                    }
                    getQrCode(oid, 'show', 'jsqrcode');

                    setCookie("soid", data.tvshowoid, 30);
                })
                .fail(function(jqXHR, textStatus, errorThrown) {
                    alert("tvprogram.js " + errorThrown);
                });


            // 刷新页面 记录oid, 30min
            setCookie('oid', oid, 30);
            //  用户上传附件 appendixoid, input[type="hidden"]
            $("#js-attachoid").val(oid);

            // 缩略图 / 附件列表
            var thumb = document.getElementById("js-thumb");
            var poster = document.getElementById("js-poster");
            getAppendixList(oid, thumb, poster);   // edit.js

        }); // END this.addEventListener
    });  // END tr.each(function() {...

    if (getCookie("oid") == "") {
        tr.first().trigger("click");
    } else {
        var oid = getCookie("oid");
        for (var i = 0; i < tr.length; i++) {
            var selected = $(tr.get(i));
            if (selected.attr("data-oid") == oid) {
                selected.trigger("click");
                break;
            }
        }
        // oid not found
        if (i == tr.length) {
            tr.first().trigger("click");
        }
    }

}

/**
 * 电视节目 单集 保存草稿 init
 * @returns {boolean}
 */
function saveDraft() {
    fb = document.getElementById("fb_savedraft");
    fb.style.display = "inline-block";
    fb.innerHTML = "保存中...";

    var title = document.getElementById("episode-title").value;
    if ("" == title.trim()) {
        $("#DialogRequireTitle").dialog("open");
        return false;
    }
    // cookie.js
    var row = $("#tvprogram-table").find(".selected");
    var oid = row.attr("data-oid");
    var tvshowoid = $("#episode-tvshowoid").val();

    // 电视节目 单集 保存草稿 => db
    var data = {
        "oid" : oid,
        "title": title,
        "tvshowoid" : tvshowoid,
        "runtime": $("#episode-runtime").val(),
        "actor" : $("#episode-actor").val(),
        "theme": $("#episode-theme").val(),
        "introduction": $("#episode-info").val()
    };
    $.ajax({
        type: "post",
        url: baseurl + "/saveTVShowEpisode",
        data: data
    }).done(function(resp, textStatus, jqXHR) {
        var selected = $("#tvprogram-table").children("tbody").first().children(".selected");
        selected.children(".col-2").attr("title", title).html(title);

        fb.innerHTML = "<font color='green'>保存成功</font>";
    }).fail(function(jqXHR, textStatus, errorThrown) {
        console.log("tvprogram.js 保存草稿失败");
        fb.innerHTML = "<font color='red'>保存失败</font>";
    }).always(function(data, textStatus, errorThrown) {
        setTimeout(function() {
            $(fb).fadeOut(2000);
        }, 5000);
    });
    setCookie('oid', oid, 5);
    // feedback
    fb.innerHTML = "<font color='green'>保存草稿...</font>";

    saveQrCode(oid, 'tv');

    return true;
}

function cutMedia() {
    var oid = getCookie("oid");
    if (oid == "") {
        oid = $("#tvprogram-table").children("tbody").first().children(".selected").attr("data-oid");
    }
    doCut(oid);
}

/**
 * 提交电视节目单集
 */
function submitEpisode() {
    var soid = document.getElementById("episode-tvshowoid").value;

    if (!soid) {
        alert('还没有设置电视节目所属剧集!');
        return false;
    }
    var tbody = document.getElementById("tvprogram-table").firstElementChild;
    var tr = tbody.querySelector('.selected');
    var oid = tr.getAttribute("data-oid");

    submitEditMedia(oid);
    return true;
}