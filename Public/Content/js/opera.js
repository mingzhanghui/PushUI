/**
 * Created by Administrator on 2017-03-24.
 */
$(function() {
    /* /PushUI/index.php/Content/Edit */
    baseurl = $("#js-baseurl").val();

    var operaTable = document.getElementById("opera-table");

    // 戏曲列表点击行显示对应信息
    var rows = $(operaTable).children("tbody").first().children("tr").not(".dumb");

    rows.each(function (i, tr) {
        tr.addEventListener("click", function() {
            if (!this.classList.contains("selected")) {
                this.classList.add("selected");
            }
            rows.not($(this)).removeClass("selected");

            // 系统命名: oid
            var oid = $(this).attr("data-oid");
            $("#system-name").val(oid);

            // 右边基本信息
            getOperaInfo(oid);

            // 刷新页面 记录oid, 30min
            setCookie('oid', oid, 30);

            //  用户上传附件 appendixoid, input[type="hidden"]
            $("#js-attachoid").val(oid);

            // 取得 缩略图 / 附件列表
            var thumb = $("#js-thumb").get(0);
            var poster = $("#js-poster").get(0);
            getAppendixList(oid, thumb, poster);
        });
    });
    // 加载页面之后 自动选择
    triggerClick(rows, "oid");

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
            .done(function(resp, textStatus, jqXHR) {
                var oid = document.getElementById("js-attachoid").value;
                // 缩略图 / 附件列表
                var thumb = document.getElementById("js-thumb");
                var poster = document.getElementById("js-poster");
                getAppendixList(oid, thumb, poster);

                console.log(resp);
                try {
                    var data = JSON.parse(resp);
                    if (data.code != "0") {
                        alert(data.msg);
                        return false;
                    }
                } catch(e) {
                    alert(e);
                    return false;
                }
                uploader.reset();
                return true;
            })
            .fail(function(jqXHR, textStatus, errorThrown) {
                alert("upload fail");
            });
    });
});

function getOperaInfo(oid) {
    var url = baseurl + "/getOperaInfo";
    $.ajax({
        type: "GET",
        url: url,
        data: {"oid":oid}
    })
        .done(function(data, textStatus, jqXHR) {
            // console.log(data);

            $("#opera-title").val(data.title);
            $("#opera-length").val(data.runtime);
            $("#opera-director").val(data.director);
            $("#opera-actors").val(data.actor);
            $("#opera-rate").val(data.rating);
            $("#opera-type").val(data.genreid);
            $("#opera-year").val(data.yearid);
            $("#opera-lang").val(data.languageid);
            $("#opera-tag").val(data.tagid);
            $("#opera-chan").val(data.channelid);
            $("#opera-info").val(data.introduction);

            // slice status
            if (data.slicestatus == 0) {
                $("#js-slicestatus").html("未备播");
            } else if(data.slicestatus == 1) {
                $("#js-slicestatus").html("<font color='green'>已备播</font>");
            } else {
                $("#js-slicestatus").html("<font color='red'>状态未知</font>");
            }
        })
        .fail(function( jqXHR, textStatus, errorThrown ) {
            alert(errorThrown);
        });

    getQrCode(oid, 'show', 'jsqrcode');
}

// /pushui/index.php/Content/Edit/setOperaInfo
function setOperaInfo() {
    var form = document.getElementById("OperaForm");
    var fb = document.getElementById("fb_savedraft");

    $.ajax({
        type: form.method,   // setOperaInfo
        url: form.action,
        data: $(form).serialize()
    }).done(function(data, textStatus, jqXHR) {
        var table = document.getElementById("opera-table");
        var title = form.title.value;
        var td = table.querySelector(".selected").children[2];
        td.innerHTML = title;
        td.title = title;

        fb.innerHTML = "<font color='green'>保存草稿完了</font>";
    }).fail(function( jqXHR, textStatus, errorThrown ) {
        console.error(errorThrown);
    });
    // feedback
    fb.innerHTML = "<font color='yellow'>保存草稿...</font>";

    saveQrCode($("#system-name").val(), 'show');
}

function saveDraft() {
    var fb = document.getElementById("fb_savedraft");
    fb.style.display = "inline-block";
    fb.innerHTML = "保存中...";
    try {
        // cookie.js
        var table = document.getElementById("opera-table");
        var oid = $(table).children("tbody").find(".selected").attr("data-oid");
        setOperaInfo();
        setCookie('oid', oid, 5);
    } catch (e) {
        fb.innerHTML = "<font color='red'>保存失败</font>";
        $(fb).fadeOut(2000);
        console.error(e);
        return false;
    }

    return true;
}

function cutMedia() {
    var oid = getCookie("oid");
    if (oid == "") {
        oid = $("#opera-table").children("tbody").first().children(".selected").attr("data-oid");
    }
    doCut(oid);
}