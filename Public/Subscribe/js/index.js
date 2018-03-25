/**
 * Created by Administrator on 2017-02-03.
 * header 时间显示 + 表格填充
 */
$(document).ready(function() {
  // header动态显示时间
  (function startTime () {
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
  }).call(this);

  // set thinkphp __ROOT__
});

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

// check all checkbox
function checkAll(source, name, callback) {
  checkboxes = document.getElementsByName(name);
  Array.prototype.forEach.call(checkboxes, function (el, i) {
    if (source.hasAttribute('checked')) {
      checkboxes[i].checked = source.checked;
    } else {
      checkboxes[i].checked = 'checked';
    }
  });
  callback && callback();
}

// check toggle - reverse - checkbox
function checkToggle(name, callback) {
  checkboxes = document.getElementsByName(name);
  Array.prototype.forEach.call(checkboxes, function (el, i) {
    if (el.checked) {
      checkboxes[i].checked = false;
    } else {
      checkboxes[i].checked = 'checked';
    }
  });
  callback && callback();
}

// yyyy-mm-dd
function gettoday() {
  var now = new Date();
  var month = now.getMonth()+1;
  if (month<10) {
    month = "0" + month;
  }
  var date = now.getDate();
  if (date < 10) {
    date = "0" + date;
  }
  return now.getFullYear() + '-' + month + '-' + date;
}