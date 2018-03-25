/**
 * Created by Administrator on 2017-03-24.
 */
$(function() {
    /* /PushUI/index.php/Content/Video */
    baseurl = $("#js-baseurl").val();

    var videoTable = document.getElementById("video-table");

    // 热点视频列表点击行显示对应信息
    var rows = $(videoTable).children("tbody").first().children("tr").not(".dumb");

    rows.each(function (i, tr) {
        tr.addEventListener("click", function() {
            if (!this.classList.contains("selected")) {
                this.classList.add("selected");
            }
            rows.not($(this)).removeClass("selected");

            // 系统命名: oid
            var oid = $(this).attr("data-oid");
            $("#system-name").val(oid);

            getVideoInfo(oid);

            // 刷新页面 记录oid, 30min
            setCookie('oid', oid, 30);

            //  用户上传附件 appendixoid, input[type="hidden"]
            $("#js-attachoid").val(oid);

            // 缩略图 / 附件列表
            var thumb = $("#js-thumb").get(0);
            var poster = $("#js-poster").get(0);
            getAppendixList(oid, thumb, poster);
        });
    });
    // 加载页面之后 自动选择
    triggerClick(rows, "oid");

    // form
    var uploader = document.getElementById("appendix_upload");
    uploader.onchange = function() {
        saveDraft();
    };
    // xhr upload appendix
    uploader.addEventListener("submit", function(event) {
        event.preventDefault();

        var formdata = new FormData(this);

        $.ajax({
            url  : baseurl + "/xhrupload",
            type : "POST",
            data : formdata,
            cache       : false,
            contentType : false,
            processData : false,
            dataType    : "html"
        })
        .done(function(data, textStatus, jqXHR) {
            console.log(data);
            var oid = document.getElementById("js-attachoid").value;
            // 缩略图 / 附件列表
            var thumb = document.getElementById("js-thumb");
            var poster = document.getElementById("js-poster");
            getAppendixList(oid, thumb, poster);

            uploader.reset();
        })
        .fail(function(jqXHR, textStatus, errorThrown) {
            alert("upload fail");
        });
    });

});

function getVideoInfo(oid) {
    var url = baseurl + "/getVideoInfo";
    $.ajax({
        type: "GET",
        url: url,
        data: {"oid":oid}
    }).done(function(data, textStatus, jqXHR) {
        $("#video-title").val(data.title);
        $("#video-bftime").val(data.bftime);
        $("#video-resource").val(data.resource);
        $("#video-info").val(data.introduction);
        // slice status
        if (data.slicestatus == 0) {
            $("#js-slicestatus").html("未备播");
        } else if(data.slicestatus == 1) {
            $("#js-slicestatus").html("<font color='green'>已备播</font>");
        } else {
            $("#js-slicestatus").html("<font color='red'>状态未知</font>");
        }
    }).fail(function( jqXHR, textStatus, errorThrown ) {
        alert(errorThrown);
    });

    getQrCode(oid, 'show', 'jsqrcode');
}

function setVideoInfo() {
    var form = document.getElementById("VideoForm");
    $.ajax({
        type: form.method,
        url: form.action,
        data: $(form).serialize()
    }).done(function(data, textStatus, jqXHR) {

        var title = document.getElementById("video-title").value;
        var target = $("#video-table").children("tbody").first().children(".selected").children(".col-2");
        target.attr("title", title).html(title);

        var fb = document.getElementById("fb_savedraft");
        fb.innerHTML = "<font color='green'>保存草稿成功!</font>"
    }).fail(function( jqXHR, textStatus, errorThrown ) {
        console.error(errorThrown);
    });

    // 保存二维码图
    saveQrCode(form.oid.value, 'show');
}

function saveDraft() {
    var fb = document.getElementById("fb_savedraft");
    fb.style.display = "inline-block";
    fb.innerHTML = "保存中...";
    try {
        // cookie.js
        var oid = $("#video-table").find("tr.selected").attr("data-oid");
        setVideoInfo();
        setCookie('oid', oid, 5);
    } catch (e) {
        fb.innerHTML = "<font color='red'>保存失败</font>";
        $(fb).fadeOut(2000);
        console.error(e);
        return false;
    }
    // feedback
    fb.innerHTML = "保存草稿...";

    $(fb).fadeOut(2000);
    return true;
}

function cutMedia() {
    var oid = getCookie("oid");
    if (oid == "") {
        oid = $("#video-table").children("tbody").first().children(".selected").attr("data-oid");
    }
    doCut(oid);
}