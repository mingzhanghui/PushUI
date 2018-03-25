/**
 * Created by Administrator on 2017-02-22.
 */
$(document).ready(function() {
    /* /PushUI/index.php/Content/Edit */
    baseurl = $("#js-baseurl").val();

    var movieTable = document.getElementById("movie-table");
    if (typeof movieTable == "undefined") {
        console.log("#movie-table is undefined");
        return false;
    }

    // 电影列表点击行显示对应信息
    var $rows = $(movieTable).children("tbody").find("tr:not(.dumb)");

    $rows.each(function (i, tr) {
        tr.addEventListener("click", function() {
            $(this).addClass("selected");
            $rows.not($(this)).removeClass("selected");

            // 系统命名: oid
            var oid = $(this).attr("data-oid");
            $("#movie-name").val(oid);

            // 刷新页面 记录oid, 30min
            setCookie('oid', oid, 30);
            // 片名:
            var assetname = $(this).find("td").eq(1).attr("title");
            $("#movie-title").val(assetname);
            // 导演, 主要演员, 片长, 编辑状态(备播)
            getMovie(oid);
            getQrCode(oid, "movie", "jsqrcode");

            //  用户上传附件 appendixoid, input[type="hidden"]
            $("#js-attachoid").val(oid);

            // 缩略图 / 附件列表
            var thumb = $("#js-thumb").get(0);
            var poster = $("#js-poster").get(0);
            getAppendixList(oid, thumb, poster);

        });
    });

    // 加载页面之后 选择哪个电影
    if ($rows.length > 0) {
        var cookieoid = getCookie('oid');

        if (cookieoid == '') {
            $rows.first().trigger('click');
        } else {
            for (var i = 0; i < $rows.length; i++) {
                if ($rows[i].getAttribute('data-oid') == cookieoid) {
                    $($rows[i]).trigger('click');
                    break;
                }
            }
            if (i == $rows.length) {
                $rows.first().trigger('click');
            }
        }
        console.log(cookieoid);
    }

    var uploader = document.getElementById("appendix_upload");
    uploader.onchange = function() {
        saveDraft();
    };

    // 上传图片
    var form = document.getElementById("appendix_upload");
    form.onsubmit = function(e) {
        e.preventDefault();
        var oid = $("#js-attachoid").val();
        filexhrupload(this, function() {
            // 缩略图 / 附件列表
            var thumb = $("#js-thumb").get(0);
            var poster = $("#js-poster").get(0);
            getAppendixList(oid, thumb, poster);
        });
    };
});
/**
 * 保存草稿 电影
 */
function saveDraft() {
    var fb = document.getElementById("fb_savedraft");
    fb.style.display = "inline-block";
    fb.innerHTML = "保存中...";

    // 1. 片名, 更新编辑状态
    var title = $.trim( $("#movie-title").val() );
    var tr = $("#movie-table").find(".selected");
    var oid = tr.attr("data-oid");
    var data = {
        'oid':oid,
        'title':title,
        'runtime': $("#movie-length").val(),
        'director':$("#movie-director").val(),
        'actor': $("#movie-actors").val(),
        'introduction' : $("#movie-info").val(),
        'rating' : $("#movie-rate").val(),
        'languageid' : $("#movie-lang").val(),
        'channelid' : $("#movie-chan").val(),
        'countryid': $('#movie-country').val(),

        // MBIS_Server_MedialinkType  类型
        'genreid': $("#movie-genre").val(),
        // MBIS_Server_MedialinkTag
        'tagid' : $("#movie-tag").val(),
        // MBIS_Server_MedialinkYear
        'yearid' : $("#movie-year").val()
    };

    try {
        $.ajax({
            type: 'POST',
            url: baseurl + '/saveMovie',
            data: data
        }).done(function (resp, textStatus, jqXHR) {
            if (resp.media == 1) {
                tr.children().eq(2).html(title);
                $("#fb_savedraft").html('保存完成');
            } else {
                $("#fb_savedraft").html('保存失败');
            }
            setCookie('oid', oid, 5);
        }).fail(function(jqXHR, textStatus, errorThrown) {
            alert(textStatus + ': ' + errorThrown);
        });

    } catch (e) {
        fb.innerHTML = "<font color='red'>保存失败</font>";
        return false;
    }
    saveQrCode(oid, 'movie');

    return true;
}

/**
 * medialinkgenre, media, medialinktag, medialinkyear
 * @param oid
 */
function getMovie(oid) {
    // target
    $.ajax({
        type: 'GET',
        url: baseurl + '/getMovie',
        data: {'oid': oid}
    }).done(function(resp, textStatus, jqXHR) {

        $("#movie-title").val(resp.title);
        $("#movie-info").val(resp.introduction);
        $("#movie-rate").val(resp.rating);
        $("#movie-lang").val(resp.languageid);
        $("#movie-chan").val(resp.channelid);
        // medialinkxxx...
        $("#movie-genre").val(resp.genreid);
        $("#movie-tag").val(resp.tagid);
        $("#movie-year").val(resp.yearid);
        $("#movie-country").val(resp.countryid);

        $("#movie-actors").val(resp.actor);
        $("#movie-director").val(resp.director);
        $("#movie-length").val(resp.runtime);

        // 已经备播?
        $slice = $("#js-slicestatus");
        if (resp.slicestatus == 0) {
            $slice.html("未备播");
        } else {
            $slice.html("<font color='green'>已备播</font>");
        }
    }).fail(function(jqXHR, textStatus, errorThrown) {
        alert(textStatus + ": " + errorThrown);
    });
}

function cutMovie() {
    var oid = $("#movie-table").find("tr.selected").attr("data-oid");
    doCut(oid);   // edit.js
}