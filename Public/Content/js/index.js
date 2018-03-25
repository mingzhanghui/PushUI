/**
 * Created by Administrator on 2017/1/16.
 */
// 补充导入列表t DOM到max行
function padtable(t, max) {
    if (t == undefined) {
        return false;
    }
    var tbd = t.querySelector('tbody');
    var n = null;
    if (tbd.querySelectorAll('tr').length) {
        var rs = t.querySelector('tbody').children;
        if (rs.length > max) {
            return true;
        }
        n = max - rs.length;
    } else {
        n = max;
    }
    // 取得表格列数
    var c = t.querySelectorAll('th').length;
    while (0 < n--) {
        var r = document.createElement('tr');
        r.classList.add("dumb");
        for (var i = 0; i < c; i++) {
            var d = document.createElement('td');
            d.innerHTML = "&nbsp";
            r.appendChild(d);
        }
        tbd.appendChild(r);
    }
}

(function () {
    if (document.readyState != 'loading') {
        fn();
    } else {
        document.addEventListener('DOMContentLoaded', fn);
    }

    function fn () {
        // 动态显示日期时间
        startTime();
    }

    function startTime () {
        var today = new Date();
        var checkTime = function (i) {
            if (i < 10) { i = "0" + i;}
            return i;
        };
        var year = 1900 + today.getYear();
        var month = checkTime(today.getMonth() + 1);
        var day = checkTime(today.getDate());
        var hour =  checkTime(today.getHours());
        var min = checkTime(today.getMinutes());
        var second = checkTime(today.getSeconds());
        $('#js-date').html(year + '-' + month + '-' + day);
        $('#js-time').html(hour + ':' + min + ':' + second);

        setTimeout(function () {
            startTime();
        }, 999);
    }

}).call();

// 内容导入 内容编辑 内容查询 系统设置 选项hover效果
$(function() {
    var li = $("li[role='presentation']");
    Array.prototype.forEach.call(li, function(elem, i) {
        if (!elem.classList.contains("active")) {
            var a = $(elem).children("a").first();
            var img = a.children("img").first();

            var url = img.attr("src");
            if (typeof url !== "undefined") {
                var index = url.lastIndexOf('/');

                var dir = url.substr(0, index);  // "/pushui/Public/Content/img"
                var file = url.substr(index+1, url.length);  // "down_import.gif"

                var parts = file.split('_');  // Array [ "down", "import.gif" ]

                a.on("mouseover", function() {
                    var u = dir + "/up_" + parts[1];
                    img.attr("src", u);
                }).on("mouseleave", function() {
                    var u = dir + "/down_" + parts[1];
                    img.attr("src", u);
                });
            }
        }
    })
});

