/**
 * Created by Administrator on 2017-04-13.
 * 业务管理 - 业务编辑
 */
var g_id;   // 当前业务包ID

$(function () {
    // load 业务包列表
    loadPackageTree(respPackage);

    initButtons();

    initDialogs();

    toggleCharge();
});

function initButtons() {
    var btns = $('.panel-footer').find('a.ui-button');
    btns.button();

    $("#pkg-pic").change(function() {
        $("#js-imgname").val(this.value);
    });
}

function initDialogs() {
    /**
     * 添加业务包dialog
     */
    $('#DialogAddPackage').dialog({
        autoOpen: false,
        width: 530,
        modal: true,
        open: function() {
            // 检查业务包层次
            var dlg = this;
            $.ajax({
                type: 'GET',
                url: 'getPackageDepth',
                data: {'id':g_id}
            }).done(function(data) {
                if (data.depth >= 5) {
                    $(dlg).dialog("close");
                    $("#js_feedback").html("业务包层次最多5层!");
                    $('#DialogAddFailed').dialog("open");
                    return false;
                }
            });
        },
        buttons: {
            '提交保存': function () {
                var form = document.getElementById("js-newpkg");
                doAddPackage(form);
            },
            '取消': function () {$(this).dialog('close');}
        }
    });

    $("#DialogRequireID").dialog({
        autoOpen: false,
        width: 300,
        height: 100,
        modal: true,
        buttons: {
            'OK' : function() {
                $(this).dialog('close');
            }
        }
    });

    $("#DialogEditSuccess").dialog({
        autoOpen: false,
        width: 300,
        height: 100,
        modal: true,
        buttons: {
            'OK' : function() {
                $(this).dialog('close');
            }
        }
    });

    $("#DialogEditFailed").dialog({
        autoOpen: false,
        width: 300,
        height: 100,
        modal: true,
        buttons: {
            'OK' : function() {
                $(this).dialog('close');
            }
        }
    });

    $("#DialogDelPackage").dialog({
        autoOpen: false,
        width: 300,
        height: 100,
        modal: true,
        buttons: {
            '确定删除': function() {
                var url = getindexcontroller() + '/delPackage';

                var $id = $("#js-package-id").val();
                if (null == $id || '' == $id) {
                    var dialog = $("#DialogRequireID");
                    dialog.dialog('open');
                    setTimeout((function() {
                        dialog.dialog("close");
                    }).bind(null, dialog), 3000);
                    return false;
                }
                var data = {'id' : $id};
                var dlg = this;

                $.get(url, data, function(resp) {
                    if (resp.code == -1) {
                        alert("业务包下有业务期, 先删除业务期");
                        return false;
                    }
                    $(dlg).dialog("close");
                    loadPackageTree(respPackage);   // refresh packages...
                    document.getElementById("js-edit-package").reset();   // reset form
                    document.getElementById("pkg-thumb").src = "";  // reset thumb
                    return true;
                });
            },
            '取消' : function() {
                $(this).dialog('close');
            }
        }
    });

    $('#btn-add-pkg').click(function () {
        $('#DialogAddPackage').dialog('open');
        // 叶子节点不能再创建子业务包
        /*
        var prompt = $("#js-prompt-node");
        if (1 == $("#js-isnode").val()) {
            prompt.hide();
        } else {
            prompt.show();
        }
        */
    });

    /**
     * 业务包添加失败
     */
    $("#DialogAddFailed").dialog({
        autoOpen: false,
        width: 300,
        height: 100,
        modal: true,
        buttons: {
            'OK' : function() {
                $(this).dialog('close');
            }
        }
    });
}

/**
 * 点击业务包树形图中业务包<a></a>显示对应业务包详细信息
 */
function respPackage() {
    var obj = document.getElementById("js-package-tree");
    var aa = $(obj).find("a");   // 所有业务包<a>数组
    Array.prototype.forEach.call(aa, function(a) {
        a.addEventListener("click", function() {
            g_id = this.parentNode.getAttribute("data-id");  // 当前业务包ID

            var that = $(this);
            // fold/unfold tree
            var ul = that.next('ul');
            var icon = that.prev();

            if (0 < icon.length) {
                if (icon.hasClass('ui-icon-plusthick')) {
                    icon.removeClass('ui-icon-plusthick').addClass('ui-icon-minusthick');
                    ul.show();
                } else if (icon.hasClass('ui-icon-minusthick')) {
                    icon.removeClass('ui-icon-minusthick').addClass('ui-icon-plusthick');
                    ul.hide();
                }
            }

            // 选中的业务包
            that.addClass("selected");
            aa.not(that).removeClass("selected");

            var li = that.parent("li");
            var pkgid = li.attr("data-id");

            // input[type="hidden"] package
            $("#js-package-id").val(pkgid);
            $("#js-id").val( pkgid );   // save selected id

            // 业务包详情
            var pkgThumb = document.getElementById("pkg-thumb");
            // 业务编辑页 有缩略图
            if (typeof pkgThumb !== "undefined" && pkgThumb != null) {
                // save selected parent id
                $("#js-pid").val(li.attr("data-pid"));
                // save selected node type (isnode?)
                var isnode = li.hasClass("file") ? 1 : 0;
                // $("#js-isnode").val(isnode);
            }

            pkgThumb.src = '';

            $.ajax({
                url: getindexcontroller() + "/getPackageInfo",
                // url: $("#js-packageinfo-url").val(),
                data: {'id': that.parent().attr("data-id")}
            }).done(function(resp) {
                // 业务包名称
                $("#pkg-name").val(resp.packagename);
                $("#js-confirm-name").text(resp.packagename);

                // 订阅周期
                $("#pkg-period").val(resp.updatecycletypeid);
                // 业务包模板
                $("#pkg-tpl").val(resp.packagetemplateid);
                // 业务包类型
                $("#pkg-type").val(resp.packagetypeid);
                // 价格
                $("#pkg-charge").val(resp.price);
                var spec = document.getElementById("spec");
                if (resp.price == 0) {
                    document.getElementById("radioFree").checked = "checked";
                    spec.style.display = "none";
                } else {
                    spec.style.display = "inline-block";
                    document.getElementById("radioCharge").checked = "checked";
                    $("#pkg-charge").val(resp.price);
                }

                // 简介
                $("#pkg-info").val(resp.packagedescription);
                // 缩略图, 二维码图

                $.get(getindexcontroller() + "/getPackageURLPrefix", null, function(prefix) {
                    // thumburl = g_root + "/resource/package/" + resp.thumb;
                    if (resp.thumb) {
                        var thumburl = prefix + "/" + resp.thumb;

                        if (typeof pkgThumb != "undefined" && pkgThumb != null) {
                            pkgThumb.src = (resp.thumb == null) ? '' : thumburl;
                        }
                    }
                    // 从数据库中读取二维码
                    if (resp.qrcode == null) {
                        resp.qrcode = "package"+g_id+"/"+g_id+"_qrcode.png"
                    }
                    document.getElementById("pkg-qrcode").src = prefix + '/' + resp.qrcode;
                });

                // 业务包图片路径
                var img = document.getElementById("js-imgname");
                if (typeof img != "undefined" && img != null) {
                    $(img).val('');
                    if (resp.hasOwnProperty('originaldir') && resp.originaldir != null) {
                        var a = resp.originaldir;
                        var name = a.substr(a.lastIndexOf('/')+1, a.length);
                        $(img).val(name);
                    }
                }

            });  // end ajax
            // 页面生成二维码
            // document.getElementById("pkg-qrcode").src = 'packageQRCode?id='+pkgid;
        });
    }); // Array.prototype.forEach.call(aa, ...
}

/**
 * 重置选择业务包
 * @param id
 * @returns {boolean}
 */
function resetPackage(id) {
    var wrapper = document.getElementById(id);

    if (typeof wrapper == 'undefined') {
        console.log("ERROR[resetPackage]: #" + id + " does not exist!");
        return false;
    }
    $(wrapper).find("a").each(function() {
        this.classList = [];
    });
    document.getElementById("js-id").value = 0;
    document.getElementById("js-pid").value = 0;
    document.getElementById("js-package-id").vale = 0;
    document.getElementById("pkg-thumb").src = "";
    g_id = 0;
    $("#pkg-qrcode").attr('src', '');
    // 业务包修改提交表单
    var form = document.getElementById("js-edit-package");
    form.reset();
}

/**
 * 新增业务包 对话框form提交 利用FormData
 * @param form #js-newpkg
 */
function doAddPackage(form) {
    var name = $("#n-name").val();
    if ($.trim(name) === "") {
        alert("业务名称不能为空!");
        return false;
    }

    var templateid = $("#n-tpl").val();
    if (templateid === undefined || templateid == 0) {
        alert("业务包模板必须选择!");
        return false;
    }
    // 设置父级业务包ID
    $("#js-pid").val(g_id);
    var formdata = new FormData(form);

    var u = document.URL;
    var url = u.substr(0, u.lastIndexOf('/')) + '/newPackage';

    $.ajax({
        url  : url,
        type : "POST",
        data : formdata,
        cache       : false,
        contentType : false,
        processData : false,
        dataType    : "html"
    }).done(function(data, textStatus, jqXHR) {
        var $dlg = $('#DialogAddPackage');
        $dlg.dialog('close');
        // @ref: index.js
        loadPackageTree(respPackage);   // 重新加载业务包树
        form.reset();
    })
}

function toggleCharge() {
    // hide/show pay specification
    var radioFree = document.getElementById("radioFree");
    var radioCharge = document.getElementById("radioCharge");
    var spec = document.getElementById("spec");

    if (typeof radioFree == "undefined" || null == radioFree) {
        return false;
    }

    radioFree.onchange = function () {
        if (this.checked) {
            spec.style.display = "none";
            document.getElementById("pkg-charge").value = 0;
            document.getElementById("pkg-pay-period").value = 0;
        } else {
            spec.style.display = "inline-block";
        }
    };

    radioCharge.onchange = function() {
        if (this.checked) {
            spec.style.display = "inline-block";
        } else {
            spec.style.display = "none";
        }
    };
    return true;
}

function editPackage() {
    var id = $("#js-package-id").val();
    if (null == id || 0 == id) {
        var dialog = $("#DialogRequireID");
        dialog.dialog("open");
        setTimeout((function() {
            dialog.dialog("close");
        }), 2000);
    } else {
        doEditPackage();
    }
}

function doEditPackage() {
    var form = document.getElementById("js-edit-package");
    var formdata = new FormData(form);

    /**
     * 免费, chargetypeid =0, price=0
     */
    /*
    if (document.getElementById("radioFree").checked) {
        formdata.set("Price", 0);
        formdata.set("ChargeTypeID", 0);
    }
    */

    $.ajax({
        url  : getindexcontroller() + "/editPackage",
        type : "POST",
        data : formdata,
        cache       : false,
        contentType : false,
        processData : false,
        dataType    : "json"
    }).done(function(data, textStatus, jqXHR) {
        var dialog = null;

        if (typeof data.packagename !== "undefined") {
            // success
            var a = data.originaldir;
            if (typeof a != 'undefined') {
                var name = a.substr(a.lastIndexOf('/')+1, a.length);
                $("#js-imgname").val(name);
            }

            if (data.thumb != undefined) {
                $.get(getindexcontroller() + "/getPackageURLPrefix", null, function(resp) {
                    // /PushUI/resource/package
                    // var imgSrc = g_root + "/resource/package/" + data.Thumb;
                    var imgSrc = resp + "/" + data.thumb;
                    document.getElementById("pkg-thumb").setAttribute("src", imgSrc);
                });
                // var imgSrc = g_root + "/resource/package/" + data.Thumb;
            }
            dialog = $("#DialogEditSuccess");
            dialog.dialog("open");
            setTimeout((function() {dialog.dialog("close");}), 2000);

            // package name might have changed
            $("#js-package-tree").find(".selected").html( data.packagename );
            return true;
        } else {
            // failed
            dialog = $("#DialogEditFailed");
            dialog.dialog("open");
            setTimeout((function() {dialog.dialog("close");}), 2000);
            return false;
        }
    }).fail(function(jqXHR, textStatus, errorThrown) {
        alert("fail: " + textStatus);
        console.error(errorThrown);
        return false;
    });

}

function delPackage() {
    $("#DialogDelPackage").dialog("open");
}

