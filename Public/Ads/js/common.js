/**
 * Created by Administrator on 2017-05-03.
 * 广告管理通用js
 */
// set date time in header
(function setDateTime() {
    function setmyTime() {
        var mydate = new Date();
        var hour = addZero(mydate.getHours());
        var min = addZero(mydate.getMinutes());
        var sec = addZero(mydate.getSeconds());
        var str = hour + ":" + min + ":" + sec;
        $("#CurrentTime").html(str);
        setTimeout(setmyTime, 999);
    }
    function setmyWeek() {
        var mydate = new Date();
        var week = mydate.getDay();
        var weekdays = ['星期日', '星期一', '星期二', '星期三', '星期四', '星期五', '星期六'];
        week = weekdays[week];
        $("#CurrentWeek").html(week);
        setTimeout(setmyWeek, 1000);
    }
    function setmyYear() {
        var mydate = new Date();
        var year = mydate.getFullYear();
        var month = addZero(mydate.getMonth() + 1);
        var day = addZero(mydate.getDate());
        var str = year + "-" + month + "-" + day;
        $("#CurrentYear").html(str);
        setTimeout(setmyYear, 1000);
    }
    setmyTime();
    setmyWeek();
    setmyYear();
})();

function addZero(temp) {
    var t = parseInt(temp);
    if (t < 10)
        return "0" + temp;
    return temp;
}

function tablepad(table, col, row) {
    if (typeof table === 'undefined') {
        return false;
    }
    // rows in use
    var rows = table.children('tbody').find('tr');
    if (row <= rows.length) {
        return true;
    }
    var n = row - rows.length;
    while (n--) {
        var td = $('<td></td>');
        td.attr('colspan', col);
        var tr = $('<tr class="dumb"></tr>');
        tr.append(td);
        table.children('tbody').append(tr);
    }
}

function checkAll(obj, name) {
    if (typeof obj === 'undefined') {
        console.log(obj);
        return false;
    }
    var checkboxes = document.getElementsByName(name);
    Array.prototype.forEach.call(checkboxes, function(el, i) {
        checkboxes[i].checked = obj.checked;
    });
}

/**
 * id array => id1,id2,id3...
 * @param v
 * @returns {string}
 */
function arraytostring(v) {
    var s = '';   // 广告id '51,52'
    for (var i = 0; i<v.length; i++) {
        s += v[i] + ',';
    }
    return s.substr(0, s.length-1);
}
