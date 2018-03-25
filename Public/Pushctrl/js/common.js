/**
 * Created by Administrator on 2017-04-28.
 */
/**
 * pad table [table] to [row] rows and [col] cols
 * @param table   DOM <table> or: <tbody>
 * @param row
 * @param col
 * @returns {boolean}
 */
function padScrollTable(table, row, col) {
    if (table === undefined) {
        return false;
    }
    var tbody = null;
    if (table.tagName === "TABLE") {
        tbody = $(table).children('tbody').first();
    } else if (table.tagName === "TBODY") {
        tbody = $(table);
    } else {
        console.log("expect tagName TABLE or: TBODY");
        return false;
    }
    var trs = tbody.children('tr');
    if (trs.length > row) {
        return true;
    }
    var n = row - trs.length;
    while (n--) {
        var tr = $('<tr>').addClass('dumb');
        var td = $('<td>').attr('colspan', col);
        tr.append(td).appendTo(tbody);
    }
}

// load右边海报 片名 类型 简介
function show_media_info(oid) {
    var url = $("#RightvideoInfo").attr("data-url");   // {:U('Pushctrl/Index/getMediaInfo')}
    $.ajax({
        type: 'GET',
        data: {'oid':oid},
        url: url
    }).done(function(obj) {
        Object.keys(obj).forEach(function(key) {
            if (key == 'url') {
                $("#js_" + key).attr("src", obj[key]);
            } else {
                $("#js_" + key).html(obj[key]);
            }
        });
    }).fail(function(jqXHR, textStatus, errorThrown) {
        alert('show_media_info error: ' + errorThrown);
    });
}
