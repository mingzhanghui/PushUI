/**
 * 业务包相关的函数 (业务编辑 + 业务管理 2个页面)
 */
// /PushUI
g_root = getroot();

function getroot() {
    var root = getCookie("mbis_root");
    return root.replace("%2F", "/");
}

function getindexcontroller() {
    var s = document.getElementById("js-indexcontroller").value;
    return s.substr(0, s.lastIndexOf('/'));
}

// 加载页面业务包结构树, 完成之后执行handler函数
function loadPackageTree(handler) {
    var target = document.getElementById("js-package-tree");
    // init package tree wrapper
    target.innerHTML = "";

    $.ajax({
        url: getindexcontroller() + "/getPackageList",
        context: target
    }).done(function(data) {
        // $( this ).addClass( "done" );
        renderTree(data);

        typeof handler === "function" && handler();
    });
}

function renderTree(data) {
    var obj = document.getElementById("js-package-tree");
    doRenderTree(obj, data);
}

/**
 * append node to obj
 * @param obj   target
 * @param data  json
 */
function doRenderTree(obj, data) {

    var tree = $("<ul></ul>").addClass("tree");

    // debug render tree
    // console.log(obj);
    $(obj).append(tree);

    if (data == null) {
        return false;
    }
    Array.prototype.forEach.call(data, function (el, i) {
        var li = $("<li></li>").attr("data-id", el.id).attr("data-pid", el.parentid).attr("title", el.packagename);
        li.appendTo(tree);

        if (1 == el.isnode) {
            li.addClass("file");

            $("<a></a>").attr("href", "javascript:;").text(el.packagename).appendTo(li);
            return true;

        } else {
            li.addClass("directory");
            var icon = $("<span></span>").addClass("ui-icon").addClass("ui-icon-plusthick");
            li.append(icon);

            var a = $("<a></a>").attr("href", "javascript:;").text(el.packagename).appendTo(li);

            // click icon => fold/unfold tree nodes
            icon.on('click', function (e) {
                e.preventDefault();
                var that = $(this);
                var ul = that.next('a').next('ul');
                if (that.hasClass('ui-icon-plusthick')) {
                    that.removeClass('ui-icon-plusthick').addClass('ui-icon-minusthick');
                    ul.show();
                } else if (that.hasClass('ui-icon-minusthick')) {
                    that.removeClass('ui-icon-minusthick').addClass('ui-icon-plusthick');
                    ul.hide();
                }
            });

            // RECURSIVELY
            if (data[i].hasOwnProperty("childs")) {
                doRenderTree($(obj).children("ul").children("li").get(i), data[i].childs);
            }
        }
    });
}