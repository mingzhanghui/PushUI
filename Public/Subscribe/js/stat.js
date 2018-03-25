/**
 * Created by mzh on 4/23/17.
 * 播发统计列表
 */
window.addEventListener("load", function() {
    var tbody = document.getElementById("js_list_stat");
    var url = tbody.getAttribute("data-url");
    var data = {'date':document.getElementById("datepicker-2").value};
    $.ajax({
        type: 'GET',
        url: url,
        data: data
    }).done(function(resp) {
        tbody.innerHTML = "";

        var table = tbody.parentNode;
        var head = table.children[0].children[0]; // table > thead > tr

        resp.datelist.forEach(function(date, i) {
            var td = document.createElement("th");
            td.classList.add("th" + (2+i));
            td.innerHTML = date;
            head.appendChild(td);
        });

        for (var i = 0; i < resp.matrix.length; i++) {
            var tr = document.createElement("tr");

            var td = document.createElement("td");
            td.classList.add("td0");
            td.innerHTML = "<div class=\"tr_arrow\"></div>";
            tr.appendChild(td);

            // 业务包名
            td = document.createElement("td");
            td.classList.add("td1");
            td.innerHTML = resp.packagename[i];
            tr.appendChild(td);

            // 日期对应的播发总量
            for (var j = 0; j < resp.datelist.length; j++) {
                td = document.createElement("td");
                td.classList.add("td" + (2+j));
                var size = resp.matrix[i][j];
                if (size > 0) {
                    td.style.fontWeight = "bold";
                    td.innerHTML = Math.round(size/1024/1024/1024*1000)/1000 + 'G';
                } else {
                    td.innerHTML = 0;
                }
                tr.appendChild(td);
            }

            tbody.appendChild(tr);
        }

//          <tr>
//          <td class="td0"><div class="tr_arrow"></div></td>
//          <td class="td1">总量</td>
//          <td class="td2">0.00 G</td>
//          <td class="td3">0.00 G</td>
//          <td class="td4">0.29 G</td>
//          <td class="td5">0.29 G</td>
//          <td class="td6">0.29 G</td>
//          <td class="td7">0.29 G</td>
//          <td class="td8">0.00 G</td>
//          </tr>
        // 播发总量
        tr = document.createElement("tr");

        td = document.createElement("td");
        td.classList.add("td0");
        td.innerHTML = "<div class=\"tr_arrow\"></div>";
        tr.appendChild(td);

        // 业务包名
        td = document.createElement("td");
        td.classList.add("td1");
        td.innerHTML = "总量";
        tr.appendChild(td);

        for (j = 0; j < resp.datelist.length; j++) {
            var total = 0;
            td = document.createElement("td");
            td.classList.add("td" + (2+j));
            for (i = 0; i < resp.packagename.length; i++) {
                total += resp.matrix[i][j];

                if (total > 0) {
                    td.style.fontWeight = "bold";
                    td.innerHTML = Math.round(total/1024/1024/1024*1000)/1000 + 'G';
                } else {
                    td.innerHTML = 0;
                }
            }
            tr.appendChild(td);
        }

        tbody.appendChild(tr);

        // row = 10, col = 9  from index.js
        padScrollTable(table, 10, 9);

    }).fail(function( jqXHR, textStatus, errorThrown ) {
        alert(errorThrown);
    })
});
