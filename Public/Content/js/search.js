/**
 * Created by Administrator on 2017-01-22.
 */
// var baseurl = "http://localhost/PushUI/index.php/Content/Search";
function getSearchController() {
    var u = document.getElementById("js-modal").getAttribute("data-url");
    return u.substr(0, u.lastIndexOf("/"));
}
var baseurl = getSearchController();

var g_oid = null;
var g_mediatypeid = null;
var g_prefix = null;   // /PushUI/resource/appendix/

$(document).ready(function() {
    // init dialogs
    // 电影
    $("#dlg_movie").dialog({
        autoOpen: false,
        modal: true,
        width: 750,
        height: 520,
        resizable: true,
        closeOnEscape: true,
        create: function(event, ui) {
            var mediatypeid = 1;
            $("#add-appendix-" + mediatypeid).on("click", function(e) {
                e.preventDefault();

                var form = document.getElementById('appendix_upload-' + mediatypeid);
                filexhrupload(form, dlg_movie_open_handler);
            });
        },

        open: function(event, ui) {
            dlg_movie_open_handler();
        },
        beforeClose: function(event, ui) {},
        close: function(event, ui) {},
        buttons: [{
            text: "保存",
            icons: { primary: "ui-icon-arrowthickstop-1-s" },
            click: function() {
                var mediatypeid = 1;
                var form = document.getElementById("form-" + mediatypeid);
                $.ajax({
                    type: "POST",
                    url: form.action, // baseurl + "/saveMedia",
                    data: $(form).serialize()
                }).done(function(data) {
                    dlg_movie_open_handler();
                    if (0 < data.media) {
                        $("#dlg_save_done").dialog("open");
                    }
                })
            }
        }, {
            text: "返回",
            icons: { primary: "ui-icon-arrowreturnthick-1-w" },
            click: function() { $(this).dialog("close"); }
        }]
    });

    // 5 电视剧总集
    $("#dlg_tvplayseries").dialog({
        autoOpen: false,
        modal: true,
        width: 870,
        height: 540,
        closeOnEscape: true,
        create: function(event, ui) {},
        open: function(event, ui) {
            $("#EditSeriesOID").val( getoid() );
            dlg_tvplays_open_handler();
        },
        beforeClose: function(event, ui) {},
        close: function(event, ui) {},
        buttons: [{
            text: "保存修改",
            icons: { primary: "ui-icon-arrowthickstop-1-s" },
            click: function() {
                var form = document.getElementById("EditSeriesInfo");
                $.ajax({
                    type: "POST",
                    url: form.action, // baseurl + "/saveMedia",
                    data: $(form).serialize()
                }).done(function(data) {
                    dlg_tvplays_open_handler();
                    console.log(data);
                    if (0 < data.media) {
                        $("#dlg_save_done").dialog("open");
                    }
                })
            }
        }, {
            text: "添加附件",
            icons: { primary: "ui-icon-image" },
            click: function() {
                $("#dlg_upload_5").dialog("open");
            }
        }, {
            text: "查看分集",
            icons: { primary: "ui-icon-document" },
            click: function() {
                var rows = $("#js-episodes-5").children("tr.selected");
                if (0 == rows.length) {
                    alert("暂时没有分集!");
                    return false;
                }
                $("#dlg_tvplayepisode").dialog("open");
            }
        }, {
            text: "删除分集",
            icons: { primary: "ui-icon-trash" },
            click: function() {
                var tr = $("#js-episodes-5").children(".selected").first();
                var oid = tr.attr("data-oid");
                deleteEpisode(oid, 2);
            }
        }, {
            text: "返回",
            icons: { primary: "ui-icon-arrowreturnthick-1-w" },
            click: function() {
                setCookie("oid", $("#EditSeriesOID").val(), 1);
                $(this).dialog("close");
            }
        }]
    });

    // 2 电视剧 单集
    $("#dlg_tvplayepisode").dialog({
        autoOpen: false,
        modal: true,
        width: 750,
        height: 500,
        resizable: true,
        closeOnEscape: true,
        create: function(event, ui) {
            var mediatypeid = 2;
            $("#add-appendix-" + mediatypeid).on("click", function(e) {
                e.preventDefault();
                // 从总集列表中取得分级OID
                var oid = $("#js-episodes-5").children(".selected").attr("data-oid");
                $("#js-attachoid-" + mediatypeid).val(oid);
                var form = document.getElementById('appendix_upload-' + mediatypeid);
                filexhrupload(form, dlg_tvplayepisode_open_handler);
            });
        },
        open: function(event, ui) {dlg_tvplayepisode_open_handler();},
        beforeClose: function(event, ui) {},
        close: function(event, ui) {
            dlg_tvplays_open_handler();
        },
        buttons: [{
            text: "保存",
            icons: { primary: "ui-icon-arrowthickstop-1-s" },
            click: function() {
                var mediatypeid = 2;
                var form = document.getElementById("form-" + mediatypeid);
                $.ajax({
                    type: "POST",
                    url: form.action, // baseurl + "/saveMedia",
                    data: $(form).serialize()
                }).done(function(data) {
                    dlg_tvplayepisode_open_handler();
                    if (0 < data.media || 0 < data.path || 0 < data.seriesepisode) {
                        $("#dlg_save_done").dialog("open");
                    }
                })
            }
        }, {
            text: "返回",
            icons: { primary: "ui-icon-arrowreturnthick-1-w" },
            click: function() { $(this).dialog("close"); }
        }]
    });

    // 6 电视节目总集
    $("#dlg_tvshows").dialog({
        autoOpen: false,
        modal: true,
        width: 850,
        height: 500,
        closeOnEscape: true,
        create: function(event, ui) {},
        open: function(event, ui) {
            var oid = getoid();
            $("#EditSeriesOID_6").val(oid);
            dlg_tvshows_open_handler();
        },
        beforeClose: function(event, ui) {},
        close: function(event, ui) {},
        buttons: [{
            text: "保存修改",
            icons: { primary: "ui-icon-arrowthickstop-1-s" },
            click: function() {
                var mediatypeid = 6;
                var form = document.getElementById("EditSeriesInfo_" + mediatypeid);
                $.ajax({
                    type: form.method,
                    url: form.action, // baseurl + "/saveMedia",
                    data: $(form).serialize()
                }).done(function(data) {
                    dlg_tvshows_open_handler();
                    console.log(data);
                    if (0 < data.media || 0 < data.tvshow) {
                        $("#dlg_save_done").dialog("open");
                    }
                })
            }
        }, {
            text: "添加附件",
            icons: { primary: "ui-icon-image" },
            click: function() {
                $("#DialogEditSeriesAppendix").dialog("open");
            }
        }, {
            text: "查看分集",
            icons: { primary: "ui-icon-document" },
            click: function() { $("#dlg_tvshowepisode").dialog("open"); }
        }, {
            text: "删除分集",
            icons: { primary: "ui-icon-trash" },
            click: function() {
                var tr = $("#js-episodes-6").children(".selected").first();
                var oid = tr.attr("data-oid");
                deleteEpisode(oid, 3);
            }
        }, {
            text: "返回",
            icons: { primary: "ui-icon-arrowreturnthick-1-w" },
            click: function() {
                setCookie("oid", $("#EditSeriesOID_6").val(), 5);
                $(this).dialog("close");
            }
        }]
    });

    // 3 电视节目 单集
    $("#dlg_tvshowepisode").dialog({
        autoOpen: false,
        modal: true,
        width: 750,
        height: 520,
        resizable: true,
        closeOnEscape: true,
        create: function(event, ui) {
            var mediatypeid = 3;
            $("#add-appendix-" + mediatypeid).on("click", function(e) {
                e.preventDefault();
                var oid = $("#js-episodes-6").children(".selected").attr("data-oid");
                $("#js-attachoid-" + mediatypeid).val(oid);
                var form = document.getElementById('appendix_upload-' + mediatypeid);
                filexhrupload(form, dlg_tvpshowepisode_open_handler);
            });

            $("#js-searchTVShows").on("click", function() {
                $("#dlg_search_tvshows").dialog("open");
            });
        },
        open: function(event, ui) {dlg_tvpshowepisode_open_handler();},
        beforeClose: function(event, ui) {},
        close: function(event, ui) {
            dlg_tvshows_open_handler();
        },
        buttons: [{
            text: "保存",
            icons: { primary: "ui-icon-arrowthickstop-1-s" },
            click: function() {
                var mediatypeid = 3;
                var form = document.getElementById("form-" + mediatypeid);
                $.ajax({
                    type: "POST",
                    url: form.action, // baseurl + "/saveMedia",
                    data: $(form).serialize()
                }).done(function(data) {
                    dlg_tvpshowepisode_open_handler();
                    if (0 < data.media || 0 < data.path || 0 < data.seriesepisode) {
                        $("#dlg_save_done").dialog("open");
                    }
                })
            }
        }, {
            text: "返回",
            icons: { primary: "ui-icon-arrowreturnthick-1-w" },
            click: function() { $(this).dialog("close"); }
        }]
    });

    // 4 热点视频
    $("#dlg_video").dialog({
        autoOpen: false,
        modal: true,
        width: 720,
        height: 460,
        resizable: true,
        closeOnEscape: true,
        create: function(event, ui) {},
        open: function(event, ui) {
            dlg_video_open_handler();

            // 热点视频上传附件
            var mediatypeid = 4;
            $("#add-appendix-" + mediatypeid).off("click").on("click", function(e) {
                e.preventDefault();

                var form = document.getElementById('appendix_upload-' + mediatypeid);
                filexhrupload(form, dlg_video_open_handler);
            });
        },
        beforeClose: function(event, ui) {},
        close: function(event, ui) {},
        buttons: [{
            text: "保存",
            icons: { primary: "ui-icon-arrowthickstop-1-s" },
            click: function() {
                var form = document.getElementById("form-4");
                var url = form.action;
                var type = form.method;
                var data = $(form).serialize();
                $.ajax({
                    type: type,
                    url: url,
                    data: data
                }).done(function(data) {
                    if (data.media > 0 || data.path > 0 || data.video > 0) {
                        $("#dlg_save_done").dialog("open");
                    } else {
                        alert("保存热点视频信息失败");
                        console.log(data);
                    }
                    dlg_video_open_handler(); // refresh
                });
            }
        }, {
            text: "返回",
            icons: { primary: "ui-icon-arrowreturnthick-1-w" },
            click: function() { $(this).dialog("close"); }
        }]
    });

    // 7 戏曲
    $("#dlg_opera").dialog({
        autoOpen: false,
        modal: true,
        width: 750,
        height: 540,
        resizable: true,
        closeOnEscape: true,
        create: function(event, ui) {
            var mediatypeid = 7;
            $("#add-appendix-" + mediatypeid).on("click", function(e) {
                e.preventDefault();
                var form = document.getElementById("appendix_upload-" + mediatypeid);
                filexhrupload(form, dlg_opera_open_handler);
            })
        },
        open: function(event, ui) {
            $("#js-attachoid-7").val( getoid() );
            dlg_opera_open_handler();
        },
        beforeClose: function(event, ui) {},
        close: function(event, ui) {},
        buttons: [{
            text: "保存",
            icons: { primary: "ui-icon-arrowthickstop-1-s" },
            click: function() {
                var form = document.getElementById("form-7");
                var url = form.action;
                var type = form.method;
                var data = $(form).serialize();
                $.ajax({
                    type: type,
                    url: url,
                    data: data
                }).done(function(data) {
                    if (data.media > 0 || data.opera > 0) {
                        $("#dlg_save_done").dialog("open");
                    } else {
                        alert("保存戏曲信息失败");
                    }
                    dlg_opera_open_handler();
                });
            }
        }, {
            text: "返回",
            icons: { primary: "ui-icon-arrowreturnthick-1-w" },
            click: function() { $(this).dialog("close"); }
        }]
    });

    // 8 专题节目总集
    $("#dlg_specialseries").dialog({
        autoOpen: false,
        modal: true,
        width: 700,
        height: 520,
        closeOnEscape: true,
        create: function(event, ui) {
            var mediatypeid = 8;
            $("#js_addattr_" + mediatypeid).on("click", function() {
                $("#dlg_prompt_attr_" + mediatypeid).dialog("open");
            });
            $("attr_" + mediatypeid).find("a").on("click", function(e) {
                e.preventDefault();
                $(this).parent("td").parent("tr").remove();
            })
        },
        // 8 专题节目总集 信息加载
        open: function(event, ui) {
            dlg_specials_open_handler();

            // 专题节目总集图片上传
            var mediatypeid = 8;
            var oid = getoid();
            $("#js-thumb-" + mediatypeid).on("click", function() {
                $("#eps_thumb_" + mediatypeid).trigger("click");
            });
            $("#js-poster-" + mediatypeid).on("click", function() {
                $("#eps_poster_" + mediatypeid).trigger("click");
            });
            $("#js-thumboid-" + mediatypeid).val(oid);
            $("#js-posteroid-" + mediatypeid).val(oid);

            // form.action="{:U('Content/Edit/xhrUploadOverwrite')}"
            var forms = document.getElementsByClassName("appendix_upload_fp");
            for (var i = 0; i < forms.length; i++) {
                forms[i].addEventListener("change", function(e) {
                    e.preventDefault();
                    filexhrupload(this, dlg_specials_open_handler);
                });
            }
        },
        beforeClose: function(event, ui) {},
        close: function(event, ui) {},
        buttons: [
            {
                text: "删除分集",
                icons: {primary: "ui-icon-trash"},
                click: function() {
                    var oid = $("#js-episodes-8").find(".selected").first().attr("data-oid");
                    deleteEpisode(oid, 9);  // 删除专题节目分集
                }
            },
            {
                text: "查看分集",
                icons: { primary: "ui-icon-document" },
                click: function() {
                    $("#dlg_specialepisode").dialog("open");
                }
            },
            {
                text: "保存总集",
                icons: { primary: "ui-icon-arrowthickstop-1-s" },
                click: function() {
                    var attrs = [];

                    var inputs = $('#attr_8').find("input");
                    for (var i = 0; i < inputs.length; i++) {
                        var a = [inputs[i].getAttribute("data-attrid"), inputs[i].value];
                        attrs.push(a);
                    }
                    var data = {
                        'soid': getoid(),
                        'title': $("#EditSeriesTitle-8").val(),
                        'introduction': $("#EditSeriesIntroduction-8").val(),
                        'languageid': $("#EditSeriesLanguage-8").val(),
                        'countryid': $("#EditSeriesCountry-8").val(),
                        'genreid': $("#EditSeriesType-8").val(),
                        'attrs': attrs
                    };
                    $.ajax({
                        type: "POST",
                        url: getEditController() + "/setSpecialsInfo",
                        data: data
                    }).done(function(data) {
                        if (data.media > 0 || data.path > 0 || data.attr > 0) {
                            $("#dlg_save_done").dialog("open");
                        } else {
                            alert("保存专题节目总集失败");
                        }
                        console.log(data);
                    });
                }
            },
            {
                text: "返回",
                icons: { primary: "ui-icon-arrowreturnthick-1-w" },
                click: function() { $(this).dialog("close"); }
            }
        ]
    });

    // 9 专题节目单集
    $("#dlg_specialepisode").dialog({
        autoOpen: false,
        modal: true,
        width: 700,
        height: 520,
        closeOnEscape: true,
        create: function(event, ui) {
            var mediatypeid = 9;
            $("#js_addattr_" + mediatypeid).on("click", function() {
                $("#dlg_prompt_attr_" + mediatypeid).dialog("open");
            });
            $("attr_" + mediatypeid).find("a").on("click", function(e) {
                e.preventDefault();
                $(this).parent("td").parent("tr").remove();
            });
            $("#js-SearchSpecialSeries").on("click", function() {
                $("#dlg_query_specials").dialog("open");
            });
        },
        open: function(event, ui) {
            dlg_specialepisode_open_handler();

            // 专题节目总集图片上传
            var mediatypeid = 9;
            var oid = $("#js-episodes-8").find(".selected").attr("data-oid");
            $("#js-thumb-" + mediatypeid).on("click", function() {
                $("#eps_thumb_" + mediatypeid).trigger("click");
            });
            $("#js-poster-" + mediatypeid).on("click", function() {
                $("#eps_poster_" + mediatypeid).trigger("click");
            });
            $("#js-thumboid-" + mediatypeid).val(oid);
            $("#js-posteroid-" + mediatypeid).val(oid);

            var forms = document.getElementsByClassName("appendix_upload_" + mediatypeid);
            for (var i = 0; i < forms.length; i++) {
                forms[i].addEventListener("change", function(e) {
                    e.preventDefault();
                    filexhrupload(this, dlg_specialepisode_open_handler);
                });
            }
        },
        beforeClose: function(event, ui) {},
        close: function(event, ui) {
            dlg_specials_open_handler();
        },
        buttons: [{
            text: "保存单集",
            icons: { primary: "ui-icon-arrowthickstop-1-s" },
            click: function() {
                var attrs = [];

                var mediatypeid = 9;
                var inputs = $('#attr_' + mediatypeid).find("input");
                for (var i = 0; i < inputs.length; i++) {
                    var a = [inputs[i].getAttribute("data-attrid"), inputs[i].value];
                    attrs.push(a);
                }
                var oid = $("#js-episodes-8").children(".selected").attr("data-oid");
                var data = {
                    'oid': oid,
                    'soid': $("#soid-9").val(),
                    'title': $("#title-" + mediatypeid).val(),
                    'introduction': $("#introduction-" + mediatypeid).val(),
                    'episodeindex': $("#episodeindex-" + mediatypeid).val(),
                    'attrs': attrs
                };
                $.ajax({
                    type: "POST",
                    url: getEditController() + "/setSpecialEpisodeInfo",
                    data: data
                }).done(function(data) {
                    if (data.media > 0 || data.path > 0 || data.attr > 0) {
                        $("#dlg_save_done").dialog("open");
                        dlg_specialepisode_open_handler()
                    } else {
                        alert("保存专题节目单集失败: setSpecialEpisodeInfo!");
                    }
                    console.log(data);
                });
            }
        }, {
            text: "返回",
            icons: { primary: "ui-icon-arrowreturnthick-1-w" },
            click: function() { $(this).dialog("close"); }
        }]
    });

    // prompt 审核是否通过
    $("#dlg_prompt_review").dialog({
        autoOpen: false,
        modal: true,
        width: 600,
        height: 250,
        open: function(event, ui) {
            var baseurl = $("#js-baseurl").val();
            var oid = getCookie("oid");
            // alert(oid);
            $.ajax({
                type: "GET",
                data: { "oid": oid },
                url: baseurl + "/getMediaAbsPath"
            }).done(function(data) {
                $("#js-abspath").html(data.path);
            });
        },
        buttons: [{
            text: "通过",
            icons: { primary: "ui-icon-check" },
            click: function() {
                var baseurl = $("#js-baseurl").val();
                var oid = getCookie("oid");

                $.ajax({
                    type: "GET",
                    url: baseurl + "/review",
                    data: { "oid": oid }
                }).done(function(data) {
                    if (data.code > 0) {
                        $("#dlg_review_done").dialog("open");
                        $("#dlg_prompt_review").dialog("close");

                        var target = $("#tablelsw").children(".selected").find("a[title='审核']").parent();
                        target.html('审核');

                        if ($("#dlg_tvshows").dialog("isOpen")) {
                            $("#js-episodes-6").find(".selected").children(".col-4").html('已审核');
                        } else if ($("#dlg_tvplayseries").dialog("isOpen")) {
                            $("#js-episodes-5").find(".selected").children(".col-4").html('已审核');
                        } else if ($("#dlg_specialseries").dialog("isOpen")) {
                            $("#js-episodes-8").find(".selected").children(".col-4").html('已审核');
                        }

                        return true;
                    } else if (0 == data.code) {
                        alert("已审核!");
                    } else {
                        alert("还没有提交不能审核");
                    }
                    console.log(data);
                    return false;
                });

            }
        }, {
            text: "不通过",
            icons: { primary: "ui-icon-close" },
            click: function() {
                var baseurl = $("#js-baseurl").val();

                $.ajax({
                    type: "GET",
                    url: baseurl + "/reviewReject",
                    data: { "oid": getCookie("oid") }
                }).done(function(resp) {
                    console.log(resp);
                    var moduleurl = baseurl.substr(0, baseurl.lastIndexOf("/"));
                    location.href = moduleurl + '/Edit/' + resp.method;
                });
            }
        }, {
            text: "返回",
            icons: { primary: "ui-icon-arrowreturnthick-1-w" },
            click: function() {
                $(this).dialog("close");
            }
        }]
    });

    // 上传附件: 6 电视节目总集
    $("#DialogEditSeriesAppendix").dialog({
        autoOpen: false,
        modal: true,
        width: 380,
        height: 230,
        open: function(event, ui) {
            var mediatypeid = 6;
            var soid = getoid();
            $("#js-soid-" + mediatypeid).val(soid);
        },
        buttons: [{
            text: "保存",
            icons: { primary: "ui-icon-arrowthickstop-1-s" },
            click: function() {
                var form = document.getElementById('editRightSeriesJumpAppendixInfo');
                filexhrupload(form, dlg_tvshows_open_handler);
            }
        }]
    });

// 上传附件: 5 电视剧总集
    $("#dlg_upload_5").dialog({
        autoOpen: false,
        modal: true,
        width: 350,
        height: 200,
        open: function(event, ui) {
            var mediatypeid = 5;
            var soid = getoid();
            $("#js-soid-" + mediatypeid).val(soid);
        },
        buttons: [{
            text: "保存",
            icons: { primary: "ui-icon-arrowthickstop-1-s" },
            click: function() {
                var mediatypeid = 5;
                var form = document.getElementById('form_upload_' + mediatypeid);
                filexhrupload(form, dlg_tvplays_open_handler);
            }
        }]
    });

    $("#dlg_review_series").dialog({
        autoOpen: false,
        modal: true,
        width: 350,
        height: 200,
        open: function(event, ui) {},
        buttons: [{
            text: "OK",
            icons: { primary: "ui-icon-arrowreturnthick-1-w" },
            click: function() { $(this).dialog("close"); }
        }]
    });

    $("#dlg_review_done").dialog({
        autoOpen: false,
        modal: true,
        width: 350,
        height: 220,
        open: function(event, ui) {},
        buttons: [{
            text: "OK",
            icons: { primary: "ui-icon-arrowreturnthick-1-w" },
            click: function() { $(this).dialog("close"); }
        }]
    });

    $("#dlg_save_done").dialog({
        autoOpen: false,
        modal: true,
        width: 350,
        height: 200,
        open: function(event, ui) {},
        buttons: [{
            text: "OK",
            icons: { primary: "ui-icon-check" },
            click: function() { $(this).dialog("close"); }
        }]
    });

    $("#dlg_delete_series").dialog({
        autoOpen: false,
        modal: true,
        width: 350,
        height: 200,
        open: function(event, ui) {},
        buttons: [{
            text: "OK",
            icons: { primary: "ui-icon-check" },
            click: function() { $(this).dialog("close"); }
        }]
    });

    // 专题节目总集添加属性
    $("#dlg_prompt_attr_8").dialog({
        autoOpen: false,
        modal: true,
        width: 300,
        height: 150,
        open: function(event, ui) {},
        buttons: [
            {
                text: "确认添加",
                icons: { primary: "ui-icon-check" },
                click: function() {
                    var attrname = $("#prompt_attr_8").val();
                    $.ajax({
                        type: "GET",
                        url: getEditController() + "/newAttr",
                        data: {"name":attrname}
                    }).done(function(data) {
                        var id = data.id;
                        var name = "s_attr_" + id;

                        var html = "<tr class='optional'>" +
                            "<td><label for='" + name + "'>"+ attrname +": </label></td>" +
                            "<td><input id='"+ name +"' name='" + name + "' data-attrid='" + id + "' type='text'>"+
                            "<a href='javascript:;'><span class='badge'>---</span></a></td></tr>";
                        var tr = $(html);
                        $("#attr_8").append(tr);
                        tr.find("a").on("click", function() {
                            tr.remove();
                        });
                    });
                    $(this).dialog("close");
                }
            },
            {
                text: "取消",
                icons: {primary: "ui-icon-close"},
                click: function() {$(this).dialog("close");}
            }
        ]
    });

    // 专题节目分级添加属性
    $("#dlg_prompt_attr_9").dialog({
        autoOpen: false,
        modal: true,
        width: 300,
        height: 150,
        open: function(event, ui) {},
        buttons: [
            {
                text: "确认添加",
                icons: { primary: "ui-icon-check" },
                click: function() {
                    var mediatypeid = 9;
                    var attrname = $("#prompt_attr_" + mediatypeid).val();
                    $.ajax({
                        type: "GET",
                        url: getEditController() + "/newAttr",
                        data: {"name":attrname}
                    }).done(function(data) {
                        var id = data.id;
                        var name = "s_attr_" + id;

                        var html = "<tr class='optional'>" +
                            "<td><label for='" + name + "'>"+ attrname +": </label></td>" +
                            "<td><input id='"+ name +"' name='" + name + "' data-attrid='" + id + "' type='text'>"+
                            "<a href='javascript:;'><span class='badge'>---</span></a></td></tr>";
                        var tr = $(html);
                        $("#attr_" + mediatypeid).append(tr);
                        tr.find("a").on("click", function() {
                            tr.remove();
                        });
                    });
                    $(this).dialog("close");
                }
            },
            {
                text: "取消",
                icons: {primary: "ui-icon-close"},
                click: function() {$(this).dialog("close");}
            }
        ]
    });

    // 内容查询
    $("#js-search").button().on("click", function(e) {
        e.preventDefault();
        var form = document.getElementById("ContentSearch");
        form.submit();
    }).on("mousedown", function(e) {
        e.preventDefault();
    });

    // 刷新页面时 显示上次选定的分类
    var mediatypeid = getCookie("mediatypeid");
    if (typeof mediatypeid !== "undefined" && mediatypeid != "") {
        $("#search-category").val(mediatypeid);
    }
    // 刷新页面时 显示上次输入的搜索片名
    var asset_name = getCookie("asset_name");
    if (typeof asset_name !== "undefined" && asset_name != "") {
        $("#search-title").val(decodeURI(asset_name));
    }

    // 右边媒体内容简介
    var $tbody = $("#tablelsw");
    var rows = $tbody.children("tr").not(".dumb");

    var target = document.getElementById("js-mediapreview");
    var url = target.getAttribute("data-url");

    // 选择一行媒体文件 查看详情/编辑
    if (rows.length > 0) {
        Array.prototype.forEach.call(rows, function(elem, i) {
            elem.addEventListener("click", function() {

                var that = $(this);
                var input = that.children("td").last().children("input[type='radio']").get(0);
                input.checked = "checked";
                rows.not(that).removeClass("selected");
                that.addClass("selected");

                var data = {
                    "oid": this.getAttribute("data-oid"),
                    "mediatypeid": this.getAttribute("data-mediatypeid")
                };
                g_oid = data.oid;
                g_mediatypeid = data.mediatypeid;

                setCookie("oid", g_oid);

                target.innerHTML = "";
                $.ajax({
                    type: "GET",
                    url: url,
                    data: data
                }).done(function(data) {
                    target.innerHTML = data;
                }).fail(function(jqXHR, textStatus, errorThrown) {
                    alert(textStatus + " " + errorThrown);
                });
            });
        });
        triggerClick(rows, "oid");
    }

    padtable($tbody.parent().get(0), 12);

    // 显示详情/编辑
    $("#js-modal").button().on("click", function(e) {
        e.preventDefault();

        var row = $("#tablelsw").children(".selected").first();
        var oid = row.attr("data-oid");
        var mediatypeid = row.attr("data-mediatypeid");
        switch (mediatypeid) {
            case '1': // 电影
                $("#dlg_movie").dialog("open");
                break;
            case '5': // 5  电视剧总集
                $("#dlg_tvplayseries").dialog("open");
                break;
            case '6': // 6  电视节目总集
                $("#dlg_tvshows").dialog("open");
                break;
            case '4': // 4 热点视频
                $("#dlg_video").dialog("open");
                break;
            case '7': // 7 戏曲
                $("#dlg_opera").dialog("open");
                break;
            case '8': // 8 专题节目总集
                $("#dlg_specialseries").dialog("open");
                break;
            default:
        }
    });

    // 查找电视节目总集 弹出对话框
    $("#dlg_search_tvshows").dialog({
        modal: true,
        autoOpen: false,
        width: 740,
        // 加载电视节目总集列表
        create: function(event, ui) {
            $("#searchTVShowsBut").on("click", function() {
                new SearchShowsByCondition(3);
            });
            return new SearchShowsByCondition(3);   // 3 电视节目分集
        },
        buttons: [
            {
                text: "选择",
                icons: {
                    primary: "ui-icon-check"
                },
                click: function() {
                    var selectedtr = $("#searchTVShowTable").children("tbody").children("tr.selected");
                    var soid = selectedtr.attr("data-oid");
                    if (null == soid || "" == soid) {
                        alert("还没有选择总集");
                        return false;
                    }
                    $(this).dialog("close");
                    // 所属剧集 名称
                    $("#episode-tvshow").val(selectedtr.children("td.td-2").html());
                    // 所属剧集 OID "S20170314..." input[type="hidden"]
                    $("#tvshowoid-3").val(soid);
                    if (soid != "") {setCookie("soid", soid, 5);}
                }
            },
            {text: "返回", icons: {primary: "ui-icon-arrowreturnthick-1-w"}, click: function() {$(this).dialog("close");}}
        ]
    });

    // keyboard shortcut
    document.onkeydown = function(event) {
        event = event||window.event;
        if (event.ctrlKey && event.keyCode== 13) {   // Spce 32, Enter 13
            event.returnvalue = false;
            setTimeout(function() {
                $("#js-modal").trigger("click");
            }, 1);
            return false;
        }
    };

});

function getoid() {
    var tbody = document.getElementById("tablelsw");
    var row = $(tbody).children(".selected").first();
    return row.attr("data-oid");
}
function getEditController() {
    var editindex = $("#js-editcontroller").val();
    return editindex.substr(0, editindex.lastIndexOf("/"));
}

function getSearchController() {
    var searchindex = $("#js-searchcontroller").val();
    return searchindex.substr(0, searchindex.lastIndexOf("/"));
}

/**
 * 查询页面 弹出编辑对话框 附件加载
 * @param data
 * [
 * {'id':xxx,'attachoid':xx,'appendixtypeid':xxx,...},
 * {}
 * ]
 * @param mediatypeid
 */
function renderMediaAppendix(data, mediatypeid) {
    var target = document.getElementById("js-appendix-" + mediatypeid);
    target.innerHTML = "";
    var html = "";
    if (data.length == 0) {
        html = "<tr class='dumb'><td colspan='6' class='text-center'>没有上传图片附件</td></tr>";
    } else {
        Array.prototype.forEach.call(data, function(elem, i) {
            var url = elem.attachoid + "/" + elem.filename;
            var trimed = (20 < elem.filename.length) ? elem.filename.substr(0, 20) + "..." : elem.filename;

            html += "<tr data-id='" + elem.id + "' data-url='" + url + "' data-mediatypeid='"+ mediatypeid +"'>";
            html += "<td class='col-0'><div class='tr_arrow'></div></td>";
            html += "<td class='col-1'>" + (i + 1) + "</td>";
            html += "<td class='col-2' title='" + elem.filename + "'>" + trimed + "</td>";
            html += "<td class='col-3'>" + elem.size + "</td>";

            if ("1" == elem.appendixtypeid) {
                $("#js-thumb-" + mediatypeid).attr("src", elem.url);
                html += "<td class='col-4'>缩略图</td>";
            } else if ("2" == elem.appendixtypeid) {
                $("#js-poster-" + mediatypeid).attr("src", elem.url);
                html += "<td class='col-4'>海报</td>";
            } else {
                console.warn("unexpected appendixtypeid: " + elem.appendixtypeid);
                html += "<td class='col-4'>unknown</td>";
                return false;
            }
            html += "<td class='col-5'><a href=\"javascript:void(0)\" onclick=\"removeAppendix(this)\">删除</a></td></tr>";

            return true;
        });
    }
    target.innerHTML = html;
}

/**
 * 查询页面 弹出编辑对话框 总集中含有的分集列表
 * @param data: array of episodes list
 * @param mediatypeid
 */
function renderEpisodes(data, mediatypeid) {
    var target = document.getElementById("js-episodes-" + mediatypeid);
    target.innerHTML = "";
    var html = "";
    if (data.length == 0) {
        html = "<tr class='dumb'>没有分集</tr>";
    } else {
        Array.prototype.forEach.call(data, function(elem, i) {
            var review = "";
            if (typeof elem.title === "undefined") {
                elem.title = elem.asset_name;
            }
            if (typeof elem.editstatus === "undefined") {
                review = "未知";
            } else {
                if (elem.editstatus < 3) {
                    review = "<a href=\"javascript:;\" onclick=\"reviewEpisode(this)\">审核</a>";
                } else {
                    review = "已审核";
                }
            }
            html +=
                "<tr data-oid=\"" + elem.episodeoid + "\" data-mediatypeid=\"" + mediatypeid + "\">" +
                "<td class=\"col-0\"><div class=\"tr_arrow\"></div></td>" +
                "<td class=\"col-1\">" + (i + 1) + "</td>" +
                "<td class=\"col-2\">" + elem.title + "</td>";
            if (typeof elem.runtime === "undefined") {
                html += "<td class=\"col-3\">" + elem.size + "</td>";
            } else {
                html += "<td class=\"col-3\">" + elem.runtime + "分钟</td>";
            }
            html +=
                "<td class=\"col-4\">" + review + "</td>" +
                "<td class=\"col-5\"><input type=\"radio\" name=\"EpisodeOID\" title=\"radio\" /></td>" +
                "</tr>";
        });
        target.innerHTML = html;

        var rows = target.childNodes;
        // 专题节目总集可以展示 分集的缩略图和海报
        var thumb = document.getElementById("img_thumb");
        var poster = document.getElementById("img_poster");
        if (typeof thumb !== "undefined" && typeof poster !== "undefined") {
            for (var i = 0; i < rows.length; i++) {
                rows[i].addEventListener("click", function() {
                    var tdlist = this.childNodes;
                    tdlist[tdlist.length - 1].childNodes[0].checked = "checked";
                    $(rows).not($(this)).removeClass("selected");
                    if (!this.classList.contains("selected")) {
                        this.classList.add("selected");
                    }
                    var oid = this.getAttribute("data-oid");
                    $.ajax({
                        type: "GET",
                        // url: "{:U('Content/Search/listAppendixByOID')",
                        url: $("#js-baseurl").val() + '/listAppendixByOID',
                        data: { "oid": oid }
                    }).done(function(data) {
                        if (Array.isArray(data)) {
                            Array.prototype.forEach.call(data, function(item) {
                                if (item.appendixtypeid == '1') {
                                    document.getElementById("img_thumb").src = item.url;
                                } else if (item.appendixtypeid == '2') {
                                    document.getElementById("img_poster").src = item.url;
                                } else {
                                    console.warn("unexpected appendix type");
                                }
                            })
                        } else {
                            alert("listAppendixByOID error");
                            console.error(data);
                        }
                    })
                });
                $(rows[i]).css({"cursor":"pointer"});
            }
        } else {
            for (var i = 0; i < rows.length; i++) {
                rows[i].addEventListener("click", function() {
                    var tdlist = this.childNodes;
                    tdlist[tdlist.length - 1].childNodes[0].checked = "checked";
                    $(rows).not($(this)).removeClass("selected");
                    if (!this.classList.contains("selected")) {
                        this.classList.add("selected");
                    }
                });
                $(rows[i]).css({"cursor":"pointer"});
            }
        }
        if (0 < rows.length) {
            $(rows[0]).trigger("click");
        }
    }
    padtable(target.parentNode, 4);
}

/**
 * 审核总集 或者没有总集的媒体文件
 * @param obj
 * @returns {boolean}
 */
function review(obj) {
    var tr = obj.parentNode.parentNode;
    var oid = tr.getAttribute("data-oid");
    var mediatypeid = parseInt(tr.getAttribute("data-mediatypeid"));

    // 总集不能审核
    if (0 <= $.inArray(mediatypeid, [5, 6, 8])) {
        $("#dlg_review_series").dialog("open");
        return false;
    }

    g_oid = oid;
    g_mediatypeid = mediatypeid;
    $("#dlg_prompt_review").dialog("open");
}

/**
 * 审核分集
 * @param obj
 */
function reviewEpisode(obj) {
    var tr = $(obj).parent().parent();
    var oid = tr.attr("data-oid");
    $.ajax({
        type: "GET",
        url: $("#js-baseurl").val() + "/mediaPreview", //"/getMediaTypeByOID",
        data: {"oid":oid}
    }).done(function(data) {
        g_mediatypeid = data.mediatypeid;
        setCookie("oid", oid, 5);
        $("#dlg_prompt_review").dialog("open");
    });
}

/**
 * 删除附件  obj
 * @param obj <a href="javascript:void(0)" onclick="removeAppendix(this)">删除</a>
 */
function removeAppendix(obj) {
    var $tr = $(obj).parent("td").parent("tr");
    var data = {
        "id": $tr.attr("data-id"),
        "url": $tr.attr("data-url")
    };
    $.ajax({
        type: 'GET',
        url: getEditController() + '/removeAppendix',
        data: data
    }).done(function(resp, textStatus, jqXHR) {
        if (1 == resp.file) {
            // 删除附件列表一行
            var $tbody = $tr.parent();
            $tr.remove();
            if (0 == $tbody.children("tr").length) {
                $tbody.html('<tr><td colspan="6" class="text-center">没有上传图片附件</td></tr>');
            }
            var mediatypeid = $tr.attr('data-mediatypeid');
            var appendixtype = $(obj).parent("td").prev().html();
            if (appendixtype == '海报') {
                $('#js-poster-' + mediatypeid).attr('src', '');
            } else if (appendixtype == '缩略图') {
                $('#js-thumb-' + mediatypeid).attr('src', '');
            }
            return true;
        }
        alert("删除附件失败");
        return false;
    }).fail(function(jqXHR, textStatus, errorThrown) {
        alert("removeAppendix: " + textStatus + ": " + errorThrown);
    })
}

/**
 * 删除媒体文件 <tr><td><a></td></tr>
 * @param obj <a>
 * @returns {boolean}
 */
function deleteMedia(obj) {
    var tr = obj.parentNode.parentNode;
    var oid = tr.getAttribute("data-oid");
    var mediatypeid = tr.getAttribute("data-mediatypeid");

    // 总集删除 [电视剧总集,电视节目总集,专题节目总集]
    if ($.inArray(mediatypeid, ['5', '6', '8']) >= 0) {
        $.ajax({
            type: "GET",
            url: baseurl + "/countSeriesEpisodes",
            data: {'soid': oid, 'mediatypeid': mediatypeid}
        }).done(function (data) {
            if (data.data > 0) {
                $("#dlg_delete_series").dialog("open");
            } else {
                // 没有分集 可以删除
                $.ajax({
                    type: 'GET',
                    url: baseurl + "/deleteSeries",
                    data: {'soid': oid, 'mediatypeid': mediatypeid}
                }).done(function(data) {
                    location.reload();
                })
            }
        }).fail(function(jqXHR, textStatus, errorThrown) {
            alert("取得分集数xhr通信错误: " + errorThrown);
        });
    } else {
        // 删除没有总集的分类 电影 热点视频 戏曲
        $.ajax({
            type: "GET",
            data: {"oid":oid, "mediatypeid":mediatypeid},
            url: getEditController() + "/delContent"
        }).done(function(data) {
            console.log(data);
            $(tr).remove();
            location.reload();
        })
    }
}

/**
 * 电视节目/电视剧 总集对话框 分集列表中删除分集
 * @param oid
 * @param mediatypeid 2:电视剧单集  3: 电视节目单集 9: 专题节目单集
 */
function deleteEpisode(oid, mediatypeid) {
    if (typeof mediatypeid === undefined ) {
        mediatypeid = 3;
    }
    $.ajax({
        type: "GET",
        data: {"oid":oid, "mediatypeid":mediatypeid},
        url: getEditController() + "/delContent",
    }).done(function(data) {
        if (data.code) {
            // error occurred
            alert(data.msg);
        } else {
            // location.reload();
            if (mediatypeid == 2) {
                dlg_tvplays_open_handler();  // 刷新电视剧总集对话框
            } else if (mediatypeid == 3) {
                dlg_tvshows_open_handler();  // 刷新电视节目总集对话框
            } else {
                dlg_specials_open_handler();  // 刷新专题节目总集对话框
            }
        }
    });
}

function dlg_movie_open_handler() {
    var mediatypeid = 1;
    var oid = getoid();
    $.ajax({
        type: "GET",
        url: document.getElementById("js-modal").getAttribute("data-url"),  // mediaInfo
        data: { "oid": oid, "mediatypeid": mediatypeid } // 电影
    }).done(function(data) {
        $("#movie-title").val(data.title);
        $("#movie-name").val(data.oid);
        $("#movie-length").val(data.runtime);
        $("#movie-director").val(data.director);
        $("#movie-actors").val(data.actor);
        $("#movie-rate").val(data.rating);
        $("#movie-type").val(data.genreid);
        $("#movie-year").val(data.yearid);
        $("#movie-lang").val(data.languageid);
        $("#movie-tag").val(data.tagid);
        $("#movie-chan").val(data.channelid);
        $("#movie-country").val(data.countryid);
        $("#movie-info").val(data.introduction);

        renderMediaAppendix(data.appendix, mediatypeid);

        // 显示二维码图片
        $("#js-qrcode-" + mediatypeid).attr("src", data.qrcode);
    });
    $("#js-attachoid-" + mediatypeid).val(oid);
}

/**
 * 打开电视剧(来自星星的你总集)总集对话框
 */
function dlg_tvplays_open_handler() {
    var mediatypeid = 5;
    g_soid = getoid();

    $.ajax({
        type: "GET",
        url: document.getElementById("js-modal").getAttribute("data-url"),  // Content/Search/mediaInfo
        data: { "oid": g_soid, "mediatypeid": mediatypeid } // 5  电视剧总集
    }).done(function(data) {
        $("#EditSeriesTitle").val(data.title);
        $("#EditSeriesEpisodes").val(data.episodes);
        $("#EditSeriesRating").val(data.rating);
        $("#EditSeriesDirector").val(data.director);
        $("#EditSeriesActor").val(data.actor);
        $("#EditSeriesYear").val(data.yearid);
        $("#EditSeriesGenre").val(data.genreid);
        $("#EditSeriesCountry").val(data.countryid);
        $("#EditSeriesLanguage").val(data.languageid);
        $("#EditSeriesTag").val(data.tagid);
        $("#EditSeriesIntroduction").val(data.introduction);
        renderMediaAppendix(data.appendix, mediatypeid);
        renderEpisodes(data.epslist, mediatypeid);

        // 显示二维码图片
        $("#js-qrcode-" + mediatypeid).attr("src", data.qrcode);

    });
    $("#dlg_upload_" + mediatypeid).dialog("close");
}

/**
 * 打开电视节目(山东老乡节目)总集对话框
 */
function dlg_tvshows_open_handler() {
    var mediatypeid = 6;
    g_soid = getoid();
    $.ajax({
        type: "GET",
        url: document.getElementById("js-modal").getAttribute("data-url"),
        data: { "oid": g_soid, "mediatypeid": mediatypeid } // 6 电视节目总集
    }).done(function(data) {
        $("#EditSeriesTitle_" + mediatypeid).val(data.title);
        $("#EditSeriesHost_" + mediatypeid).val(data.host);
        $("#EditSeriesRating_" + mediatypeid).val(data.rating);
        $("#EditSeriesSourceFrom_" + mediatypeid).val(data.sourcefrom);
        $("#EditSeriesType_" + mediatypeid).val(data.tvshowtype);
        $("#EditSeriesLanguage_" + mediatypeid).val(data.languageid);
        $("#EditSeriesCountry_" + mediatypeid).val(data.countryid);
        $("#EditSeriesIntroduction_" + mediatypeid).val(data.introduction);
        renderMediaAppendix(data.appendix, mediatypeid);
        renderEpisodes(data.epslist, mediatypeid);

        // 显示二维码图片
        $("#js-qrcode-" + mediatypeid).attr("src", data.qrcode);

    });
    $("#DialogEditSeriesAppendix").dialog("close");
}

function dlg_tvplayepisode_open_handler() {
    var mediatypeid = 2;
    var tr = $("#js-episodes-5").children(".selected").first();
    if (0 == tr.length) {
        alert("没有选择电视剧分集");
        return false;
    }
    var oid = tr.attr("data-oid");
    $.ajax({
        type: "GET",
        url: document.getElementById("js-modal").getAttribute("data-url"),
        data: { "oid": oid, "mediatypeid": mediatypeid }
    }).done(function(data) {
        $("#title-" + mediatypeid).val(data.title);
        $("#oid-" + mediatypeid).val(data.oid);
        $("#episodeindex-" + mediatypeid).val(data.episodeindex);
        $("#episodes-" + mediatypeid).val(data.episodes);
        $("#runtime-" + mediatypeid).val(data.runtime);
        $("#introduction-" + mediatypeid).val(data.introduction);

        renderMediaAppendix(data.appendix, mediatypeid);

        // 显示二维码图片
        $("#js-qrcode-" + mediatypeid).attr("src", data.qrcode);
    });
}

function dlg_tvpshowepisode_open_handler() {
    var mediatypeid = 3;
    var tr = $("#js-episodes-6").children(".selected").first();
    if (0 == tr.length) {
        alert("没有选择电视剧分集");
        return false;
    }
    var oid = tr.attr("data-oid");
    $.ajax({
        type: "GET",
        url: document.getElementById("js-modal").getAttribute("data-url"),
        data: { "oid": oid, "mediatypeid": mediatypeid }
    }).done(function(data) {
        $("#title-" + mediatypeid).val(data.title);
        $("#oid-" + mediatypeid).val(data.oid);
        $("#tvshowoid-" + mediatypeid).val(data.tvshowoid);
        $("#runtime-" + mediatypeid).val(data.runtime);
        $("#actor-" + mediatypeid).val(data.actor);
        $("#theme-" + mediatypeid).val(data.theme);
        $("#introduction-" + mediatypeid).val(data.introduction);
        // 电视节目总集名称
        $("#episode-tvshow").val(data.st);

        renderMediaAppendix(data.appendix, mediatypeid);

        // 显示二维码图片
        $("#js-qrcode-" + mediatypeid).attr("src", data.qrcode);

    });
}

/**
 * 热点视频dialog打开
 */
function dlg_video_open_handler() {
    var mediatypeid = 4;
    $.ajax({
        type: "GET",
        url: document.getElementById("js-modal").getAttribute("data-url"),
        data: { "oid": getoid(), "mediatypeid": 4 } // 热点视频
    }).done(function(data) {
        // 二维码
        $('#js-qrcode-' + mediatypeid).attr("src", data.qrcode);

        $("#video-title").val(data.title);
        $("#video-oid").val(data.oid);
        $("#video-bftime").val(data.bftime);
        $("#video-resource").val(data.resource);
        $("#video-info").val(data.introduction);
        $("#js-attachoid-" + mediatypeid).val(data.oid);
        renderMediaAppendix(data.appendix, 4);
    });
}

function dlg_opera_open_handler() {
    var mediatypeid = 7;
    $.ajax({
        type: "GET",
        url: baseurl + "/mediaInfo",
        data: { "oid": getoid(), "mediatypeid": mediatypeid }
    }).done(function(data) {
        $("#opera-title").val(data.title);
        $("#opera-oid").val(data.oid);
        $("#opera-length").val(data.runtime);
        $("#opera-director").val(data.director);
        $("#opera-actors").val(data.actor);
        $("#opera-rate").val(data.rating);
        $("#opera-type").val(data.genreid);
        $("#opera-year").val(data.yearid);
        $("#opera-lang").val(data.languageid);
        $("#opera-resource").val(data.resource);
        $("#opera-tag").val(data.tagid);
        $("#opera-chan").val(data.channelid);
        $("#opera-country").val(data.countryid);
        $("#opera-info").val(data.introduction);
        $("#js-attachoid-" + mediatypeid).val(data.oid);
        renderMediaAppendix(data.appendix, mediatypeid);

        // 显示二维码图片
        $("#js-qrcode-" + mediatypeid).attr("src", data.qrcode);

    });
}

/**
 * 打开专题节目总集 handler
 */
function dlg_specials_open_handler() {
    var mediatypeid = 8;
    var oid = getoid();
    $.ajax({
        type: "GET",
        url: getSearchController() + "/mediaInfo",
        data: { "oid": oid, "mediatypeid": mediatypeid }
    }).done(function(data) {
        $("#EditSeriesTitle-" + mediatypeid).val(data.info.title);
        $("#EditSeriesType-" + mediatypeid).val(data.info.genreid);
        $("#EditSeriesCountry-" + mediatypeid).val(data.info.countryid);
        $("#EditSeriesLanguage-" + mediatypeid).val(data.info.languageid);
        $("#EditSeriesIntroduction-" + mediatypeid).val(data.info.introduction);

        // 加载自定义属性
        var renderAttrs = function (data, mediatypeid) {
            var tbody = document.getElementById("attr_" + mediatypeid);
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

                badge.on("click", function () {
                    $(this).parent("td").parent("tr").remove();
                });
            }
        };
        renderAttrs(data.attrs, mediatypeid);

        // 专题节目总集图片 展示
        $('#js-thumb-' + mediatypeid).css({'background-image':'none'});
        $('#js-poster-' + mediatypeid).css({'background-image':'none'});
        Array.prototype.forEach.call(data.pic, function (elem, i) {
            if (elem.appendixtypeid == '1') {
                // $("#eps_thumb_" + mediatypeid).attr("src", elem.url);
                $("#js-thumb-" + mediatypeid).css({"background-image": "url('" + elem.url + "')"});
            } else if (elem.appendixtypeid == '2') {
                // $("#eps_poster_" + mediatypeid).attr("src", elem.url);
                $("#js-poster-" + mediatypeid).css({"background-image": "url('" + elem.url + "')"});
            } else {
                console.error("unexpected appendixtypeid");
            }
        });

        // 专题节目分集列表
        renderEpisodes(data.episodes, mediatypeid);

        // 显示二维码图片
        $("#js-qrcode-" + mediatypeid).attr("src", data.qrcode);
    });
}

/**
 * 打开专题节目分集 handler
 */
function dlg_specialepisode_open_handler() {
    var mediatypeid = 9;
    var oid = $("#js-episodes-8").children(".selected").attr("data-oid");
    $("#oid-" + mediatypeid).val(oid);

    $.ajax({
        type: "GET",
        url: getSearchController() + "/mediaInfo",
        data: { "oid": oid, "mediatypeid": mediatypeid }
    }).done(function(data) {
        // 显示二维码图片
        $("#js-qrcode-" + mediatypeid).attr("src", data.qrcode);

        $("#title-" + mediatypeid).val(data.info.title);
        $("#stitle-" + mediatypeid).val(data.info.stitle);
        $("#soid-" + mediatypeid).val(data.info.specialoid);
        $("#introduction-" + mediatypeid).val(data.info.introduction);
        $("#episodeindex-" + mediatypeid).val(data.info.episodeindex);

        // 加载自定义属性
        var renderAttrs = function (data, mediatypeid) {
            var tbody = document.getElementById("attr_" + mediatypeid);
            tbody.innerHTML = "";
            for (var i = 0; i < data.length; i++) {
                var tr = $("<tr>").addClass("optional").appendTo($(tbody));

                var id = data[i].id;
                var domid = "s_attr_" + id;

                var label = $("<label>").attr("for", domid).html(data[i].name + ": ");
                $("<td>").append(label).appendTo(tr);

                var badge = $("<a href=\"javascript:;\"><span class=\"badge\">---</span></a>");
                var input = $("<input>").attr("id", domid).attr("name", domid).attr("data-attrid", id).attr("type", "text");
                input.val(data[i].value);
                $("<td>").append(input).append(badge).appendTo(tr);

                badge.on("click", function () {
                    $(this).parent("td").parent("tr").remove();
                });
            }
        };
        renderAttrs(data.info.attrs, mediatypeid);

        // 专题节目分集图片
        var renderPics = function(data) {
            Array.prototype.forEach.call(data.pic, function (elem, i) {
                if (elem.appendixtypeid == '1') {
                    $("#js-thumb-" + mediatypeid).css({"background-image": "url('" +  elem.url + "')"});
                } else if (elem.appendixtypeid == '2') {
                    $("#js-poster-" + mediatypeid).css({"background-image": "url('" +  elem.url + "')"});
                } else {
                    console.error("unexpected appendixtypeid");
                }
            });
        };
        renderPics(data);

    });
}

/**
 FormData上传文件 form指定action为处理上传文件的url
 handler为上传完了的操作
 */
function filexhrupload(form, handler) {
    var formdata = new FormData(form);

    $.ajax({
        url: form.action,
        type: "POST",
        data: formdata,
        cache: false,
        contentType: false,
        processData: false,
        dataType: "html"
    }).done(function(data, textStatus, jqXHR) {
        try {
            var json = JSON.parse(data);
            if (0 != json.code) {
                alert(json.msg);
                return false;
            }
        } catch (e) {
            alert("try parsing data failed!");
            console.error(e);
            console.log(data);
        }
        handler();
    }).fail(function( jqXHR, textStatus, errorThrown) {
        alert("附件上传 ajax 通信失败: " + errorThrown);
    });
}

/**
 * 查找电视节目总集 加载电视节目总集列表
 * mediatypeid 对应的分集类型
 * @constructor
 */
function SearchShowsByCondition(mediatypeid) {
    var url = getEditController() + "/listTVShows";
    var search = $("#js-searchname-" + mediatypeid).val();
    var q = typeof search == "undefined" ? "" : search.trim();
    $.ajax({
        type: "POST",
        url: url,
        data: {"q" : q}
    }).done(function (data, textStatus, jqXHR) {
        var tbody = $("#searchTVShowTable").children("tbody").get(0);
        if (typeof tbody == "undefined") return false;

        tbody.innerHTML = "";
        var target = $(tbody);

        for (var i = 0; i < data.length; i++) {
            var tr = $("<tr>").attr("data-oid", data[i].oid);
            target.append(tr);
            $("<td>").addClass("td-0").html("<div class='tr_arrow'></div>").appendTo(tr);
            $("<td>").addClass("td-1").html(i+1).appendTo(tr);
            $("<td>").addClass("td-2").html(data[i].title).appendTo(tr);
            $("<td>").addClass("td-3").html(data[i].country).appendTo(tr);
            $("<td>").addClass("td-4").html(data[i].language).appendTo(tr);
            $("<td>").addClass("td-5").html(data[i].tvshowtype).appendTo(tr);
            $("<td>").addClass("td-6").html(data[i].sourcefrom).appendTo(tr);
            $("<td>").addClass("td-7").html(data[i].imported).appendTo(tr);
            $("<td>").addClass("td-8").html("<input type='radio' name='series' title='radio' />").appendTo(tr);
        }

        // 选择电视节目总集
        var rows = target.children("tr").not(".dumb");
        rows.on("click", function () {
            $(this).addClass("selected");
            var radio = this.childNodes[this.childNodes.length-1].childNodes[0];
            radio.checked = "checked";
            rows.not($(this)).removeClass("selected");

            $("#soid-" + mediatypeid).val(this.getAttribute("data-oid"));
        });
        rows.first().trigger("click");

        return true;
    }).fail(function( jqXHR, textStatus, errorThrown) {
        alert(errorThrown);
        console.error("@file: search.js @func: SearchShowsByCondition ajax failed");
        return false;
    }).always(function (jqXHR, textStatus, errorThrown) {
        var table = document.getElementById("searchTVShowTable");
        padScrollTable(table, 10, 10);
    });
}

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
            text: "选择",
            icons: {primary: "ui-icon-check"},
            click: function() {
                // 选择节目总集 取得的soid title 添加到分集信息编辑页
                var tr = $("#ss_tbody").children(".selected");
                var soid = tr.attr("data-soid");
                var title = tr.children(".td-2").attr("title");
                $("#soid-9").val(soid);
                $("#stitle-9").val(title);
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

function SearchSpecialsByCondition() {
    var url = getEditController() + "/listSpecialSeries";
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
    }).fail(function( jqXHR, textStatus, errorThrown) {
        alert(errorThrown);
        return false;
    }).always(function (jqXHR, textStatus, errorThrown) {
        var table = document.getElementById("searchSpecialsTable");
        padScrollTable(table, 10, 7);
    });

}