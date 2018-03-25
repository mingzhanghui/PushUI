/**
 * Created by Administrator on 2017-04-20.
 * 业务计费 => 电视剧/电视节目
 */
var g_page = 1;
var g_pagesize = 14;   // 每页显示行数
var g_totalpage = 1;

$(document).ready(function () {

    // 文件列表
    (function () {
        var mediatypeid = document.getElementById("js-mediatypeid").value;
        listChargeContent(mediatypeid, g_page, g_pagesize);

        // 首页
        var a_first = document.getElementById("js_first_page");
        a_first.addEventListener("click", function() {
            listChargeContent(mediatypeid, 1, g_pagesize);
        });
        // 上一页
        var a_prev = document.getElementById("js_prev_page");
        a_prev.addEventListener("click", function() {
            listChargeContent(mediatypeid, g_page-1, g_pagesize);
        });
        // 下一页
        var a_next = document.getElementById("js_next_page");
        a_next.addEventListener("click", function() {
            listChargeContent(mediatypeid, g_page+1, g_pagesize);
        });
        // 尾页
        var a_last = document.getElementById("js_last_page");
        a_last.addEventListener("click", function() {
            listChargeContent(mediatypeid, g_totalpage, g_pagesize);
        });
        // 根据页数跳转
        $("#js_page_jump").on("click", function() {
            var val = $("#js_page_number").val();
            var p = Number(val);
            listChargeContent(mediatypeid, p, g_pagesize);
        });
    }).call(this);

    $('#js-free').on("click", function () {
        $('#charge_sp').hide();
    });
    $('#js-charge').on("click", function () {
        $('#charge_sp').show();
    });

});

function get_table_by_mediatypeid(mediatypeid) {
    var table = null;
    var typeid = Number(mediatypeid);
    switch (typeid) {
        case 5:
            table =  document.getElementById("tbtvplay");
            break;
        case 6:
            table =  document.getElementById("tbtvprogram");
            break;
        // case 8:
        //     table =  document.getElementById("tvspecial");
        //     break;
        default:
            alert("unknown mediatypeid" + typeid);
    }
    return table;
}

/**
 * 左边 文件名 + 大小 + 价格 列表
 * @param mediatypeid
 * @param page
 * @param pagesize
 */
function listChargeContent(mediatypeid, page, pagesize) {
    var table = get_table_by_mediatypeid(mediatypeid);
    var url = table.getAttribute("data-url");

    var data = {
        "mediatypeid":mediatypeid,
        "page":page,
        "pagesize":pagesize
    };

    $.ajax({
        type: "GET",
        url: url,   // /PushUI/index.php/Subscribe/Charge/listMediaCharge.html
        data: data
    }).done(function(resp) {
        var tbody = table.children[1];
        tbody.innerHTML = "";
        resp.data.forEach(function(elem, i) {
            var tr = document.createElement("tr");
            tr.setAttribute("data-oid", elem.oid);
            tr.setAttribute("title", elem.title);
            tbody.appendChild(tr);

            var filename = elem.title;
            if (elem.title.length > 20) {
                filename = elem.title.substr(0, 20) + "...";
            }

            tr.innerHTML =
                "<td><div class='tr_arrow'></div></td>" +
                "<td>" + (i+1) + "</td>" +
                "<td>" +  filename +"</td>" +
                "<td>"+ elem.size +"</td>" +
                "<td title='"+elem.price+"'>￥" + elem.price + "</td>" +
                "<td><a href='javascript:;' onclick='deleteChargeContent('"+elem.oid+"',"+mediatypeid+")'>删除</a></td>";
        });

        // 点击文件列表 显示费用类型
        var c_oid = getCookie("oid");

        Array.prototype.forEach.call(tbody.children, function(elem, i) {
            if (!elem.classList.contains("dumb")) {
                // table#tbtvprogram >tbody >tr click!
                elem.addEventListener("click", function(e) {
                    e.preventDefault();
                    tbody.querySelector(".selected") && tbody.querySelector(".selected").classList.remove("selected");
                    elem.classList.add("selected");

                    var oid = elem.getAttribute("data-oid");
                    setCookie("oid", oid, 5);

                    var td = elem.querySelector("td:nth-child(5)");
                    var price = Number( td.getAttribute("title") );
                    var charge_sp = document.getElementById("charge_sp");

                    if (price === 0) {
                        charge_sp.style.display = "none";
                        charge_sp.querySelector("input").value = 0;
                        document.getElementById("js-free").checked = "checked";
                    } else {
                        charge_sp.style.display = "inline";
                        document.getElementById("js-charge").checked = "checked";
                        charge_sp.querySelector("input").value = price;
                    }

                    // 清空播发任务列表
                    var tasktable = document.getElementById("caption").querySelector("tbody");
                    tasktable.innerHTML = "";
                    paddingTable(tasktable, 6, 5);

                    // 列出全集内容
                    // list_package_task(oid);
                    list_episodes(oid);
                });
                c_oid === elem.getAttribute("data-oid") && $(elem).trigger("click");
            }  // end if !elem.classList.contains("dumb")

        });  // end forEach...
        tbody.querySelector(".selected") || $(tbody.children[0]).trigger("click");

        document.getElementById("js_save_price").addEventListener("click", function() {
            save_price(this);
        });

        paddingTable(table, 6, g_pagesize);

        // set global variables
        g_page = resp.page;
        g_totalpage = resp.totalpage;

        // 首页,尾页 按钮样式
        var first = $("#js_first_page");
        var prev = $("#js_prev_page");
        (page == 1) ?
        first.css({"cursor":"not-allowed", "color":"#2b2b2b"}) && prev.css({"cursor":"not-allowed", "color":"#2b2b2b"}) :
        first.css({"cursor":"pointer", "color":"#007fff"}) && prev.css({"cursor":"pointer", "color":"#007fff"});

        var last = $("#js_last_page");
        var next = $("#js_next_page");
        (page == g_totalpage) ?
        last.css({"cursor":"not-allowed", "color":"#2b2b2b"}) && next.css({"cursor":"not-allowed", "color":"#2b2b2b"}):
        last.css({"cursor":"pointer", "color":"#007fff"}) && next.css({"cursor":"pointer", "color":"#007fff"});

        var nav = document.getElementById("js_page_nav");
        nav.innerHTML = "第"+g_page+"页/共"+g_totalpage+"页";
    })
}

/**
 * load "全集内容列表"
 * @param oid
 */
function list_episodes(oid) {
    var table = document.getElementById("serial");
    var url = table.getAttribute("data-url");  // "/pushui/index.php/Subscribe/Charge/listEpisodes.html"
    var tbody = table.querySelector("tbody");
    var data = {
        "oid" : oid,
        "mediatypeid": document.getElementById("js-mediatypeid").value
    };
    var searchurl = document.getElementById("js_search_url").value;

    $.getJSON(url, data, function(resp) {
        tbody.innerHTML = "";
        resp.forEach(function(elem, i) {
            var tr = document.createElement("tr");
            tr.setAttribute("data-oid", elem.oid);
            tr.title = elem.title;

            tr.innerHTML =
                "<td class='td-0'><div class='tr_arrow'></div></td>" +
                "<td class='td-1'>"+(i+1)+"</td>" +
                "<td class='td-2'>"+elem.title+"</td>" +
                "<td class='td-3'>"+elem.size+"</td>" +
                "<td class='td-4'><a href='"+searchurl+"'>查看详情</a></td>" +
                "<td class='td-5'><input name='serial' checked='checked'  title='全集多选' type='checkbox' value='"+elem.oid+"'></td>";

            tbody.appendChild(tr);
        });
        // 列出多个媒体资源 所属业务包 业务期
        var checkboxes = document.getElementsByName("serial");
        checkboxes.forEach(function(checkbox) {
            checkbox.addEventListener("change", function(e) {
                e.preventDefault();
                checkbox_handler(checkboxes);
            });
        });
        checkbox_handler(checkboxes);

        paddingTable(table, 6, 5);   // #serial 全集内容列表
    });
}

function checkbox_handler(checkboxes) {
    if (typeof checkboxes === "undefined" || checkboxes == null) {
        checkboxes = document.getElementsByName("serial");
    }
    var oidstring = '';
    checkboxes.forEach(function(checkbox) {
        if (checkbox.checked) {    // 选中的checkbox 不是所有的
            oidstring += checkbox.value + ',';
        }
    });
    oidstring.substr(0, oidstring.length-1);  // trim last ','

    list_package_task(oidstring);
}

function list_package_task(oidstring) {
    var table = document.getElementById("caption");

    var _pad = function() {
        paddingTable(table, 6, 5);   // table, cols, maxRows
    };

    // 播发任务为空
    if (oidstring.trim() === '') {
        table.querySelector("tbody").innerHTML = '';
        _pad();
        return;
    }

    var url = table.getAttribute("data-url");
    $.ajax({
        type: "POST",
        url: url,
        data: {"oidstring":oidstring}
    }).done(function(data) {
        var tbody = table.children[2];  // caption, thead, tbody
        tbody.innerHTML = "";
        Array.prototype.forEach.call(data, function(elem, i) {
            var tr = document.createElement("tr");
            tr.setAttribute("data-packageid", elem.packageid);
            tr.setAttribute("data-missionid", elem.missionid);
            // +td...
            tr.innerHTML =
                "<td class='td-0'><div class='tr_arrow'></div></td>" +
                "<td class='td-1'>"+ (i+1) +"</td>" +
                "<td class='td-2' title='"+ elem.packagename +"'>"+elem.packagename+"</td>" +
                "<td class='td-3' title='"+elem.missionname+"'>"+elem.missionname+"</td>" +
                "<td class='td-4'>" + elem.date + "</td>" +
                "<td class='td-5'>"+elem.round + "</td>";
            tbody.appendChild(tr);
        });
        _pad();

    }).fail(function(jqXHR, textStatus, errorThrown) {
        alert("list_package_task: " + errorThrown);
    });
}

// 费用类型 点击保存 handler
function save_price(obj) {
    obj.innerHTML = "保存中...";

    var free = document.getElementById("js-free");
    var charge_sp = document.getElementById("charge_sp");
    var charge = charge_sp.querySelector("input");

    var price;
    if (charge_sp.style.display === "none") {
        price = 0;
    } else {
        price = charge.value;
    }

    var mediatypeid = document.getElementById("js-mediatypeid").value;
    var table = get_table_by_mediatypeid(mediatypeid);
    var tbody = table.children[1];
    var index = 0;
    for (var i = 0; i < tbody.children.length; i++) {
        if (tbody.children[i].classList.contains("selected")) {
            index = i;
            break;
        }
    }

    var oid = tbody.children[index].getAttribute("data-oid");
    var url = obj.getAttribute("data-url");  // setPriceByOID

    $.ajax({
        type: "GET",
        url: url,
        data: {
            "oid":oid,
            "price": price
        }
    }).done(function(resp) {
        if (resp.data != null) {
            var td = tbody.children[index].querySelector("td:nth-child(5)");
            var price = resp.data / 100;
            td.title = price;
            td.innerHTML = "￥" + price;
        } else {
            alert("保存价格失败!");
        }
        obj.innerHTML = "保存";
    });
}

// 左边价格列表 填充
function paddingTable(table, cols, maxRows) {
    if (typeof table == 'undefined') {
        return false;
    }
    var tbody = $(table).children('tbody');
    var rows = tbody.children('tr').length;
    if (rows < maxRows) {
        // rows to fill
        var n = maxRows - rows;
        while (n--) {
            var ntr = $('<tr class="dumb"></tr>');
            for (var i = 0; i < cols; i++) {
                var td = $("<td>&nbsp;</td>");
                td.addClass('td-' + i);
                ntr.append(td);
            }
            tbody.append(ntr);
        }
    }
}

// 左边价格列表 删除
function deleteChargeContent(oid, mediatypeid) {
    // {:U('Content/Edit/delContent')}
    var url = document.getElementById("js-contentedit").value + "/delContent";
    $.ajax({
        type: "GET",
        url: url,
        data: {
            "oid": oid.trim().toUpperCase(),
            "mediatypeid": mediatypeid
        }
    }).done(function() {
        listChargeContent(mediatypeid, g_page, g_pagesize);
    });
}

