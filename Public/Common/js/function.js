/**
 * Created by Administrator on 2017-02-16.
 */
function addZero(temp) {
  var t = parseInt(temp);
  if (t < 10)
    return "0" + temp;
  return temp;
}

function setmyTime(target) {
  if (typeof target == 'undefined' || target == null) {
    return false;
  }
  var mydate = new Date();
  var hour = addZero(mydate.getHours());
  var min = addZero(mydate.getMinutes());
  var sec = addZero(mydate.getSeconds());
  target.innerHTML = hour + ":" + min + ":" + sec;
  // setTimeout(function () {
  //   setmyTime(target);
  // }, 999);
  setTimeout(setmyTime.bind(null, target), 999);
}

function setmyWeek(target) {
  if (typeof target == 'undefined' || target == null) {
    return false;
  }
  var mydate = new Date();
  var index = mydate.getDay();
  var weekdays = ['星期日', '星期一', '星期二', '星期三', '星期四', '星期五', '星期六'];
  week = weekdays[index];
  target.innerHTML = week;
  setTimeout(setmyWeek.bind(null, target), 1000);
}

function setmyYear(target) {
  if (typeof target == 'undefined' || target == null) {
    return false;
  }
  var mydate = new Date();
  var year = mydate.getFullYear();
  var month = addZero(mydate.getMonth() + 1);
  var day = addZero(mydate.getDate());

  target.innerHTML = year + "-" + month + "-" + day;
  setTimeout(setmyYear.bind(null, target), 1000);
}

function tablepad(table, col, row) {
  if (typeof table === 'undefined') {
    return false;
  }
  var $table = $(table);
  // rows in use
  var rows = $table.children('tbody').find('tr');
  if (row <= rows.length) {
    return true;
  }
  var n = row - rows.length;
  while (n--) {
    var td = $('<td></td>');
    td.attr('colspan', col);
    var tr = $('<tr class="dumb"></tr>');
    tr.append(td);
    $table.children('tbody').append(tr);
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

var QueryString = function () {
  // This function is anonymous, is executed immediately and
  // the return value is assigned to QueryString!
  var query_string = {};
  var query = window.location.search.substring(1);
  var vars = query.split("&");
  for (var i=0;i<vars.length;i++) {
    var pair = vars[i].split("=");
    // If first entry with this name
    if (typeof query_string[pair[0]] === "undefined") {
      query_string[pair[0]] = decodeURIComponent(pair[1]);
      // If second entry with this name
    } else if (typeof query_string[pair[0]] === "string") {
      query_string[pair[0]] = [ query_string[pair[0]], decodeURIComponent(pair[1]) ];
      // If third or later entry with this name
    } else {
      query_string[pair[0]].push(decodeURIComponent(pair[1]));
    }
  }
  return query_string;
}();
