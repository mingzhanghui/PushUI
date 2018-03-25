/**
 * Created by Administrator on 2017-04-28.
 */
$(function() {
    // datepicker
    var date = document.getElementById("js_date");
    date.onfocus = function() {
        var div = this.parentNode;
        div.style.border = '1px solid #3c82be';
    };
    date.onblur = function() {
        this.parentNode.style.border = '1px solid #666';
    };
    $(date).datepicker({
        dateFormat:'yy-mm-dd'
    });

    // submit button
    $("#js_submit").on("mousedown", function() {
        var img = $(this).children('img').first();
        img.css({
            "transform":"translateX(1px) translateY(1px)",
            "opacity": "0.9"
        });
    }).on("mouseup", function() {
        var img = $(this).children('img').first();
        img.css({
            "transform":"translateX(0) translateY(0)",
            "opacity": "1.0"
        });
    }).on("click", function() {
        this.parentNode.submit();
    });

    // 播发列表
    var tasklist = document.getElementById("tasklist");
    Array.prototype.forEach.call(tasklist.children, function(tr, i) {
        tr.addEventListener("click", function() {
            var input = this.lastElementChild.children[0];
            input.checked = "checked";

            var s = tasklist.querySelector(".selected");
            s && s.classList.remove("selected");
            this.classList.contains("selected") || this.classList.add("selected");

            var oid = input.value;
            show_media_info(oid);
        });
    });
    $(tasklist.children[0]).trigger("click");

    var MAXROW = 12;
    if (tasklist.childElementCount > MAXROW) {
        tasklist.style.display = 'block';
        tasklist.style.overflowY = 'scroll';
    } else {
        padScrollTable(tasklist, MAXROW, 8);
        tasklist.style.display = 'table';
    }

});
