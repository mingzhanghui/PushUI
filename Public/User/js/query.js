/**
 * Created by Administrator on 2017-05-10.
 */
var form,  // 用户查询
    baseurl,
    lasturl,
    g_total,
    g_id, // 当前点击的customerid
    lastChecked = null, //  shift-multi-select
    g_v = [];     // customerid array

window.onload = function() {
    baseurl = document.getElementById('js-baseurl').value;
    // $("#query-mode").selectmenu();
    // $("#CCount").selectmenu();
    $('#js-jump').button();

    // dialog feedback
    $('#dialog-1').dialog({
        width: 300,
        height: 250,
        autoOpen: false,
        modal: true,
        buttons: {
            "确定": function() {
                $(this).dialog("close");
            }
        }
    });

    // dialog prompt
    $('#dialog-3').dialog({
        width: 300,
        height: 250,
        autoOpen: false,
        modal: true,
        buttons: [{
           text: "删除",
            icons: {
               primary: "ui-icon-trash"
            },
            click: function() {
                var dlg = this;
                // batch delete
                $.ajax({
                    type: 'GET',
                    data: {'string': g_v.join(',')},
                    url: baseurl + 'bulkDeleteCustomer'
                }).done(function(data) {
                    $(dlg).dialog("close");
                    if (data.n > 0) {
                        success('删除'+data.n+'条机顶盒');
                        search(lasturl);
                    } else {
                        myalert('删除机顶盒记录失败!');
                    }
                    document.querySelector("input[type='checkbox']").checked = false;
                }).fail(function(jqXHR, textStatus, errorThrown) {
                    myalert('批量删除用户ajax通信错误: ' + errorThrown);
                });
            }
        }, {
            text: "取消",
            icons: {
                primary: "ui-icon-arrowreturnthick-1-w"
            },
            click: function() {
                $(this).dialog("close");
            }
        }]
    });

    // 查询用户 查找按钮
    var btn = document.getElementById('sub-1');
    // 查询用户提交
    form = btn.parentNode.parentNode;
    form.addEventListener("submit", function(e) {
        e.preventDefault();
    });

    // 切换查找方式
    toggleQuery();

    btn.addEventListener("click", function (e) {
        e.preventDefault();
        search();
    });
    // 查询用户
    search();

    // 取消修改 用户详细信息
    $('#js_cancel').on('click', function() {
        var customerid = $('#cus_customerid').val();
        getCustomerInfo(customerid);
    });
    // 确定修改 用户详细信息
    $('#js_save').on('click', function() {
        // var btnTxt = this.value;  // "保存"
        var names = ['customerid', 'customerstate', 'customerdateadded', 'starttime', 'endtime', 'stbid'];
        var obj = {};
        Array.prototype.forEach.call(names, function(name) {
            obj[name] = $('#cus_' + name).val();
        });

        $.ajax({
            type: 'GET',
            url: baseurl + 'setCustomerInfo',
            data: $.param(obj)
        }).done(function(data) {
            if (data.code == 0) {   // 修改成功
                success(data.msg);
                search(lasturl);
            } else {
                myalert(data.msg);
            }
        }).fail(function(jqXHR, textStatus, errorThrown) {
            myalert('修改用户详细信息xhr通信错误: ' + errorThrown);
        });
    });

    // (批量)删除用户信息
    $('#js_delete').on('click', function() {
        // g_v = [];
        while (g_v.length) {
           g_v.pop();
        }
        var stb = document.getElementsByName("stb");
        stb.forEach(function(box) {
            if (box.checked) {
                g_v.push(box.value);
            }
        });

        if (g_v.length < 2) {
            // delete one by one
            var customerid = $('#cus_customerid').val();
            if (customerid === '') {
                myalert('还未选择用户!');
                return;
            }
            $.ajax({
                type: 'GET',
                data: {'customerid': customerid},
                url: baseurl + 'deleteCustomer'
            }).done(function(data) {
                if (data.n > 0) {
                    success('删除条机顶盒: ' + data.stbid);
                    search(lasturl);
                } else {
                    myalert('删除机顶盒记录失败!');
                }
            }).fail(function(jqXHR, textStatus, errorThrown) {
                myalert('删除用户ajax通信错误: ' + errorThrown);
            });
        } else {
            // batch delete
            $('#dialog-3').dialog("open");
            $('#js_stb_count').html( g_v.length );
        }

    });

    // 导出用户信息
    $('#dialog-2').dialog({
        width: 480,
        height: 250,
        autoOpen: false,
        modal: true,
        create: function() {
            $('#datepicker-1').datepicker({
                dateFormat: 'yy-mm-dd',
                showOn: 'button',
                buttonImage: '../../../Public/User/img/calendar32.svg',
                buttonImageOnly: true
            }).on('change', function() {
                getExportCount();
            });
            $('#datepicker-2').datepicker({
                dateFormat: 'yy-mm-dd',
                showOn: 'button',
                buttonImage: '../../../Public/User/img/calendar32.svg',
                buttonImageOnly: true
            }).on('change', function() {
                getExportCount();
            });
        },
        close: function() {
            $('#datepicker-1').val('');
            $('#datepicker-2').val('');
            $('#js-item-stat').html('');
        },
        buttons: {
            "确定导出": function() {
                var start = $('#datepicker-1').val();
                var end = $('#datepicker-2').val();

                var count = $('#js-item-stat').text();

                if (count == 0) {
                    myalert('选择用户信息条数为0');
                    return;
                } else if (count > 65535) {
                    myalert('选择条数('+count+')过多>65535');
                    return;
                }

                var data ={'start': start, 'end': end};
                var url = baseurl + 'excelExport?' + $.param(data);
                window.open(url);
                $(this).dialog("close");
            },
            "取消": function() {
                $(this).dialog("close");
            }
        }
    });
    $('#js_export').on('click', function() {
       $('#dialog-2').dialog('open');
    });
};

/**
 * 取得导出数据条目数
 */
function getExportCount() {
    var start = $('#datepicker-1').val();
    var end = $('#datepicker-2').val();

    $.ajax({
        type: 'GET',
        data: {'start': start, 'end': end},
        url: baseurl + 'getExportCount'
    }).done(function (data) {
        $('#js-item-stat').html(data.cnt);
    }).fail(function (jqXHR, textStatus, errorThrown) {
        myalert('取得导出数据条目数XHR ERROR: ' + errorThrown);
    });
}

// 切换用户查找方式
function toggleQuery() {
    // 默认使用机顶盒号查找
    for (var i = 2; i < 5; i++) {
        var input = document.getElementById('query-value-' + i);
        input.style.display = 'none';
        input.setAttribute('disabled', 'disabled');
    }
    document.getElementById('query-mode').addEventListener("change", function() {
        for (var i = 1; i < 5; i++) {
            var input = document.getElementById('query-value-' + i);
            input.style.display = 'none';
            input.setAttribute('disabled', 'disabled');
        }
        var target = document.getElementById('query-value-' + this.value);
        target.style.display = 'block';
        target.removeAttribute('disabled');
    }, false);
}

function search(url) {
    if (typeof url === 'undefined') {
        url = form.action;
    }
    $.ajax({
        type: 'GET',
        url: url,
        data: $(form).serialize()
    }).done(function(resp) {
        search_handler(resp);
    }).fail(function(jqXHR, textStatus, errorThrown) {
        myalert('查找用户XHR通信错误: ' + errorThrown);
    });
}

/**
 * 处理XHR查找出来的结果
 * @param resp
 */
function search_handler(resp) {
    var table = document.getElementById('js-stblist');
    var tbody = table.children[1];
    tbody.innerHTML = '';
    // render table
    if (resp.rows.length == 0) {
        tbody.innerHTML = '<tr class="dumb"><td colspan="7">暂时没有数据</td></tr>';
    } else {
        Array.prototype.forEach.call(resp.rows, function(elem, i) {
            var tr = document.createElement('tr');
            tr.setAttribute('data-customerid', elem.customerid);
            tbody.appendChild(tr);

            // 序号
            var td = document.createElement('td');
            td.innerHTML = resp.offset + i + 1;
            tr.appendChild(td);
            // 机顶盒ID
            td = document.createElement('td');
            td.innerHTML = elem.stbid;
            tr.appendChild(td);
            // 用户状态
            td = document.createElement('td');
            td.innerHTML = (elem.customerstate == '1') ? '开通' : '未开通';
            tr.appendChild(td);
            // 开通日期
            td = document.createElement('td');
            td.innerHTML = elem.customerdateadded;
            tr.appendChild(td);
            // 机顶盒开通时间
            td = document.createElement('td');
            td.innerHTML = elem.starttime;
            tr.appendChild(td);
            // 机顶盒过期时间
            td = document.createElement('td');
            td.innerHTML = elem.endtime;
            // checkbox
            tr.appendChild(td);
            td = document.createElement('td');
            // td.innerHTML = '<input type="radio" name="stb" title="选择一条用户" value="'+ elem.customerid +'">';
            var input = document.createElement("input");
            input.type = "checkbox";
            input.name = 'stb';
            input.title = '选择用户';
            input.classList.add("chkbox");
            input.value = elem.customerid;
            td.appendChild(input);

            tr.appendChild(td);
        });
    }

    // pagination url
    (function() {
        var btn = document.getElementById('sub-1');
        var form = btn.parentNode.parentNode;
        var data = resp.get;

        if (data.p < 1) {
            data.p = 1;
        }
        var $fp = $('#js_first_page');
        var $prev = $('#js_prev_page');

        if (data.p == 1 || data.p == 0) {
            // already first page
            $fp.css({"cursor":"not-allowed"}).off("click").on("click", function(e) {
                e.preventDefault();
            });
            $prev.css({"cursor":"not-allowed"}).off("click").on("click", function(e) {
                e.preventDefault();
            });
        } else {
            $fp.css({"cursor":"pointer"}).off("click").on("click", function(e) {
                e.preventDefault();
                if (data.length == 0) {
                    data = {'p':1};
                } else {
                    data.p = 1;
                }
                lasturl = form.action + '?' + $.param(data);   // 记录查询用户表格数据请求URL
                $.ajax({
                    type: 'GET',
                    url: form.action,
                    data: data
                }).done(function(data) {
                    search_handler(data);
                }).fail(function(jqXHR, textStatus, errorThrown) {
                });
            });
            $prev.css({"cursor":"pointer"}).off("click").on("click", function(e) {
                e.preventDefault();
                var p = resp.p -1;
                if (data.length == 0) {
                    data = {'p': p};
                } else {
                    data.p = (p < 1) ? 1:p;
                }
                lasturl = form.action + '?' + $.param(data);

                $.ajax({
                    type: 'GET',
                    url: form.action,
                    data: data
                }).done(function(data) {
                    search_handler(data);
                }).fail(function(jqXHR, textStatus, errorThrown) {
                    console.error( [textStatus, errorThrown].join(": ") );
                });
            })
        }

        var $next = $('#js_next_page');
        var $last = $('#js_last_page');

        if (data.p == resp.totalpages) {
            // already last page, disable click
            $next.css({"cursor":"not-allowed"}).off("click").on("click", function(e) {
               e.preventDefault();
            });
            $last.css({"cursor":"not-allowed"}).off("click").on("click", function(e) {
                e.preventDefault();
            });
        } else {
            $next.css({"cursor":"pointer"}).off("click").on("click", function(e) {
                e.preventDefault();
                var p = parseInt(resp.p)+1;

                if (data.length == 0) {
                    data = {'p':p};
                } else {
                    data.p = (p > resp.totalpages) ? resp.totalpages : p;
                }
                lasturl = form.action + '?' + $.param(data);

                $.ajax({
                    type: 'GET',
                    url: form.action,
                    data: data
                }).done(function(data) {
                    search_handler(data);
                }).fail(function(jqXHR, textStatus, errorThrown) {
                });
            });

            $last.css({"cursor":"pointer"}).off("click").on("click", function(e) {
                e.preventDefault();
                if (data.length == 0) {
                    data = {'p':resp.totalpages};
                } else {
                    data.p = resp.totalpages;
                }
                lasturl = form.action + '?' + $.param(data);

                $.ajax({
                    type: 'GET',
                    url: form.action,
                    data: data
                }).done(function(data) {
                    search_handler(data);
                }).fail(function(jqXHR, textStatus, errorThrown) {
                });
            })
        }

        $('#js_pagestate').html('第'+ resp.p +'/'+resp.totalpages+'页');

        // 输入页码跳转
        $('#js-jump').off('click').on('click', function(e) {
            e.preventDefault();

            var p = $(this).prev().val();

            if (data.length == 0) {
                data = {'p':p};
            } else {
                data.p = (p > resp.totalpages) ? resp.totalpages : (p < 1) ? 1: p;
            }
            lasturl = form.action + '?' + $.param(data);

            $.ajax({
                type: 'GET',
                url: form.action,
                data: data
            }).done(function(data) {
                search_handler(data);
            }).fail(function(jqXHR, textStatus, errorThrown) {
                console.error(textStatus + ": " + errorThrown);
            });
        });
    })();

    // 用户信息详细信息
    Array.prototype.forEach.call(tbody.children, function(tr, i) {
        tr.addEventListener("click", function() {
            var input = tr.lastElementChild.firstElementChild;

            g_id = input.value;   // customerid
            getCustomerInfo(input.value);

            var marked = tbody.querySelector('.marked');
            if (marked) {
                marked.classList.remove('marked');
            }
            this.classList.contains('marked') || this.classList.add('marked');
        });
    });

    tablepad(table, 7, resp.pagesize);

    // 查询结果总数, 用于导出提示
    g_total = resp.total;

    // shift multi-select
    (function() {
        lastChecked = null;
        var $chkboxes = $('.chkbox');
        $chkboxes.off("click").on("click", function(e) {
            if (!lastChecked) {
                lastChecked = this;
                return;
            }
            if (e.shiftKey) {
                var start = $chkboxes.index(this);
                var end = $chkboxes.index(lastChecked);

                $chkboxes.slice( Math.min(start,end), Math.max(start,end)+1 )
                    .prop('checked', lastChecked);
            }
            lastChecked = this;
        });
    }).call(this);
}

function getCustomerInfo(customerid) {
    $.ajax({
        type: 'GET',
        url: baseurl + 'getCustomerInfo',
        data: {'customerid': customerid}
    }).done(function(obj) {
        Object.keys(obj).forEach(function(key, idx) {
            $('#cus_' + key).val(obj[key]);
        });
    }).fail(function(jqXHR, textStatus, errorThrown) {
        myalert('取得用户信息详细信息XHR ERROR: ' + errorThrown);
    });
}

function success(msg) {
    $('#js_feedback').html('<span class="ui-icon ui-icon-check"></span><font color="green">'+msg+'</font>');
    var $dlg = $('#dialog-1');
    $dlg.dialog("open");
    setTimeout(function() {
        $dlg.dialog("close");
    }, 3000);
}

function myalert(msg) {
    $('#js_feedback').html('<span class="ui-icon ui-icon-alert"></span><font color="red">'+msg+'</font>');
    $('#dialog-1').dialog('open');
}
