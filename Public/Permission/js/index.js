/**
 * Created by Administrator on 2017-02-17.
 */
var g_uid = 0, g_yonghuming = '';    // 当前选中的用户ID
var g_root = document.getElementById('js_root').href;

window.onload = function () {
    setmyTime(document.getElementById('CurrentTime'));
    setmyYear(document.getElementById('CurrentYear'));

    // 用户权限列表
    listUsers();

    // 添加用户
    $('#dialog-1').dialog({
        width: 380,
        heigth: 180,
        autoOpen: false,
        modal: true,
        close: function() {
            var form = document.getElementById("form_adduser");
            form.reset();
        },
        buttons: {
            "提交保存": function () {
                var form = document.getElementById("form_adduser");
                var dlg = this;

                $.ajax({
                    type: 'POST',
                    data: $(form).serialize(),
                    url: form.action
                }).done(function(data) {
                    // validate
                    if (data.code > 0) {
                        $('#help-' + data.code).html(data.msg);
                    } else if(data.code == '0') {   // success
                        $(dlg).dialog('close');
                        listUsers();
                    } else {
                        $('#js_error').html(data.msg);
                        $('#dialog-5').dialog('open');
                    }
                });
                for (var i = 1; i < 4; i++) {
                    $('#help-' + i).html('');
                }
            },
            "取消": function() {
                $( this ).dialog( "close" );
            }
        }
    });
    $('#js-add').click(function () {
        $('#dialog-1').dialog('open');
    });
    $('#dismiss-add').click(function () {
        $('#dialog-1').dialog('close');
    });

    // 修改密码
    $('#dialog-3').dialog({
        width: 380,
        heigth: 190,
        autoOpen: false,
        modal: true,
        open: function() {
            var form = document.getElementById("form_modPwd");
            form.firstElementChild.value = g_uid;
            document.getElementById('js_uname').value = g_yonghuming;
        },
        buttons: {
            "提交保存": function () {
                var form = document.getElementById("form_modPwd");
                var dlg = this;

                $.ajax({
                    type: 'POST',
                    data: $(form).serialize(),
                    url: form.action
                }).done(function(data) {
                    // validate
                    if (data.code > 0) {
                        $('#help_' + data.code).html(data.msg);
                    } else if(data.code == '0') {   // success
                        $(dlg).dialog('close');
                        listUsers();
                    } else {
                        $('#js_error').html(data.msg);
                        $('#dialog-5').dialog('open');
                    }
                });
                for (var i = 1; i < 4; i++) {   // 1,2,3
                    $('#help_' + i).html('');
                }
            },
            "取消": function() {
                $( this ).dialog( "close" );
            }
        }
    });
    $('.edpwd').click(function () {
        $('#dialog-2').dialog('open');
    });
    $('#dismiss-edpwd').click(function () {

    });

    $('#dialog-2').dialog({
        autoOpen: false,
        modal: true,
        buttons: {
            Ok: function() {
                $( this ).dialog( "close" );
            }
        }
    });
    $('.getlog').click(function() {
        $('#dialog-2').dialog('open');
    });

    // 删除用户?
    $('#dialog-4').dialog({
        resizable: false,
        height: "auto",
        width: 400,
        modal: true,
        autoOpen: false,
        buttons: {
            "确定": function() {
                var dlg = this;

                $.ajax({
                    type: 'GET',
                    url: 'delUser',
                    data: {'uid':g_uid}
                }).done(function(data) {
                    $(dlg).dialog('close');
                    listUsers();
                }).fail(function(jqXHR, textStatus, errorThrown) {
                    $('js_error').html('删除用户XHR通信错误: ' + errorThrown);
                    $('#dialog-5').dialog('open');
                })
            },
            "取消": function() {
                $(this).dialog("close");
            }
        }
    });

    // 出错
    $('#dialog-5').dialog({
        resizable: false,
        height: "auto",
        width: 400,
        modal: true,
        autoOpen: false,
        buttons: {
            "确定": function() {
                $(this).dialog("close");
            }
        }
    });

    // 修改权限成功
    $('#dialog-6').dialog({
        resizable: false,
        height: "auto",
        width: 400,
        modal: false,
        autoOpen: false,
        buttons: {
            "确定": function() {
                $(this).dialog("close");
            }
        }
    });
};

function listUsers() {
    var permlist = $('#js_permission').html('');
    $.ajax({
        type: 'GET',
        url: 'listUsers',
        cache: false
    }).done(function(data) {
            // 播发管控	内容编辑	业务管理	广告管理	用户管理	审核管理
            var types = ['pushcontrol', 'content', 'subscribe', 'advertise', 'customer', 'verifier'];

            Array.prototype.forEach.call(data, function(elem) {

                var tr = $("<tr>").attr("data-uid", elem.uid);
                permlist.append(tr);

                var isSuper = false;

                $('<td>').addClass('uname').html(elem.yonghuming).appendTo(tr);
                if (elem.super == '1') {
                    $('<td>').addClass('super').html('超级管理员').appendTo(tr);
                    isSuper = true;
                } else if (elem.super == '2') {
                    $('<td>').html('审核管理员').appendTo(tr);
                } else {
                    $('<td>').html('一般管理员').appendTo(tr);
                }

                types.forEach(function(type) {
                    var input = $('<input>').attr('type', 'checkbox');
                    if (isSuper) {
                        input.attr('disabled', 'disabled');
                    }
                    if (elem[type] == '1') {
                        input.attr('checked', 'checked');
                    }
                    $('<td>').addClass('p_'+type).append(input).appendTo(tr);

                    // BEGIN 修改权限
                    input.on('change', function() {
                        var name = this.parentNode.classList[0].substr(2);   // 'p_xxx'
                        var value = this.checked ? 1:0;
                        var data = {'uid':elem.uid};
                        data[name] = value;
                        $.ajax({
                            type: 'GET',
                            url: "chMod",
                            data: data
                        }).done(function(resp) {
                            if (resp.code === 0) {
                                $('#js_success').html('<font color="green">' + resp.msg + '</font>');
                                $('#dialog-6').dialog('open');
                                setTimeout(function() {
                                    $('#dialog-6').dialog('close');
                                }, 2000);
                            } else {
                                $('#js_error').html('<font color="red">' + resp.msg + '</font>');
                                $('#dialog-5').dialog('open');
                            }
                        })
                    });
                    // END 修改权限
                });

                var td = $('<td>').addClass('operate').appendTo(tr);
                $('<input>').attr('type', 'button').addClass('class', 'getlog').val('查看日志').appendTo(td);
                $('<input>').attr('type', 'button').addClass('class', 'edpwd').val('修改密码').appendTo(td);
                $('<input>').attr('type', 'button').addClass('class', 'delete').val('删除').appendTo(td);

                // 删除用户trigger
                td.children().last().on('click', function() {
                    g_uid = elem.uid;
                    $('#dialog-4').dialog('open');
                });
                // 修改密码trigger
                td.children().eq(1).on('click', function() {
                    g_uid = elem.uid;
                    g_yonghuming = elem.yonghuming;
                    $('#dialog-3').dialog('open');
                });
                // 查看log trigger
                td.children().eq(0).on('click', function() {
                    window.open(g_root + "Log/" + elem.yonghuming + '.log',
                        "DescriptiveWindowName",
                        "resizable=yes,scrollbars=yes,status=yes,height=400,width=500");
                })
            });

            // 动态调整表格背景高度
            document.getElementById('iframe').style.height = data.length * 60 + 160 + 'px';

            $('#loading').hide();

        }).fail(function(jqXHR, textStatus, errorThrown) {
        console.error('xhr取得用户权限列表失败: ' + errorThrown);
    });

}
