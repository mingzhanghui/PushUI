$(function() {

    // 更新日期和时间
    setInterval(function() {
        var add_zero = function(temp) {
            return (temp < 10) ? "0" + temp : temp;
        };
        var currentDate = new Date();

        // date & weekday
        var week;
        switch (currentDate.getDay()) {
            case 0: week="星期日"; break;
            case 1: week="星期一"; break;
            case 2: week="星期二"; break;
            case 3: week="星期三"; break;
            case 4: week="星期四"; break;
            case 5: week="星期五"; break;
            case 6: week="星期六"; break;
            default: week="星期天";
        }
        var years = currentDate.getFullYear();
        var month = add_zero(currentDate.getMonth()+1);
        var date = add_zero(currentDate.getDate());
        var str = years+"年"+month+"月"+date+"日 " +" "+week;

        $( "#currentDate" ).text(str);

        // time
        var timeStr = add_zero(currentDate.getHours()) + ":" + add_zero(currentDate.getMinutes()) + ":" +  add_zero(currentDate.getSeconds()) ;
        for(var i = 0; i < 8; i++) {
            var s = timeStr.charAt(i);
            var p = 0;
            if(s == ":") {
                p = 10;
            } else {
                p = Number(s);
            }
            var pos = "-" + (p * 21) + "px 0px";
            $(".digitalB:eq("+i+")").css({"background-position":pos});
        }
    }, 999);

    // 定时刷新(16s) 播发列表
    listPush();
    // 各种状态 (包括表格上面的播发总进度)
    showStatus();

    setInterval(function() {
        listPush();
        showStatus();
    }, 16000);

});

// 定时刷新videoTable, 播发进度条
function listPush() {
    var tbody = document.getElementById("videoTable");
    var url = tbody.getAttribute("data-url");

    $.ajax({
        type: 'GET',
        url: url,
        cache: false,
        dataType: 'json'
    }).done(function(data) {
        tbody.innerHTML = "";

        data.forEach(function(elem, i) {
            var tr = document.createElement("tr");
            tr.setAttribute("data-oid", elem.oid);
            tbody.appendChild(tr);

            tr.innerHTML = "<td class=\"tr_arrow\"></td><td>"+(i+1)+"</td>" +
                "<td title='"+ elem.packagename +"'>"+ elem.packagename +"</td>" +
                "<td title='"+ elem.missionname +"'>"+ elem.missionname +"</td>" +
                "<td title='"+ elem.asset_name +"'>"+ elem.asset_name + "</td>" +
                "<td title='"+ elem.mediatype +"'>"+ elem.mediatype +"</td>" +
                "<td title='"+ elem.size +"'>"+ elem.size +"</td>" +
                "<td title='"+ (elem.roundcount/elem.round) +"'>"+ elem.roundcount + '/' + elem.round +"</td>";

            // progress bar
            var td = document.createElement("td");
            tr.appendChild(td);
            var div = document.createElement("div");
            td.appendChild(div);

            var span = document.createElement("span");
            span.classList.add("progress-value");
            span.innerText = elem.ratio + '%';
            div.appendChild(span);

            var progressbar = document.createElement("div");
            progressbar.className = "tdProItem3 ui-progressbar ui-widget ui-widget-content ui-corner-all";
            progressbar.setAttribute("role", "progressbar");
            progressbar.setAttribute("aria-valuemin", "0");
            progressbar.setAttribute("aria-valuemax", "100");
            progressbar.setAttribute("aria-valuenow", elem.ratio);
            div.appendChild(progressbar);

            // 进度条图片 __PUBLIC__/Pushctrl/img/phpThumb_generated_thumbnailpng.png
            var bg = document.createElement("div");
            bg.className = "ui-progressbar-value ui-widget-header ui-corner-left";
            bg.style.width = elem.ratio + '%';
            progressbar.appendChild(bg);

            // radio
            td = document.createElement("td");
            td.innerHTML = "<input type='radio' name='oid' value='"+ elem.oid +"' title='select package'/>";
            tr.appendChild(td);
        });

        // 点击播发列表行
        var issetCookie = false;
        if (getCookie('oid') != '') {
            issetCookie = true;
        }
        Array.prototype.forEach.call(tbody.children, function(tr, i) {
            tr.addEventListener("click", function() {
                var selected = tr.querySelector(".selected");
                selected && selected.classList.remove("selected");
                this.classList.contains("selected") || this.classList.add("selected");

                var radio = this.children[this.children.length-1].children[0];
                radio.checked = "checked";
                var oid = radio.value;
                show_media_info(oid);

                setCookie('oid', oid, 5);
            });
            if (issetCookie) {
                tr.getAttribute("data-oid") == getCookie('oid') && $(tr).trigger("click");
            }
        });
        issetCookie || $(tbody.children[0]).trigger("click");

        var table = tbody.parentNode;
        padScrollTable(table, 9, 10);
        if (data.length <= 9) {
            tbody.style.display = 'table';
        } else {
            tbody.style.display = 'block'; // scroll bar
        }
    //    TODO: pagination
        /*
        var sum = 0;
        data.forEach(function(elem) {
           sum += elem.ratio;
        });
        var progress = Math.round(sum / data.length);
        var progressbarValue = document.getElementById("progressbarValue");
        progressbarValue.innerText = "播发进度："+progress+"%";
        var progressBar = document.getElementById("progressBar");
        progressBar.style.width = progress + '%';
         */

    }).fail(function(jqXHR, textStatus, errorThrown) {
        console.error("listPush: ["+textStatus+"]" + errorThrown);
    });
}

// 各种状态
function showStatus() {
    var progress = document.getElementById("progressbarValue");
    var url = progress.getAttribute("data-url");

    $.ajax({
        type: "GET",
        cache: false,
        url: url
    }).done(function(data) {
        // 总播发进度 totalrate
        var pgval = Math.round( data.totalrate / 100 );
        progress.innerHTML = "播发进度："+pgval+"%";
        var pgb = document.getElementById("progressBar");
        pgb.style.width = pgval + '%';

        // 系统运行状态
        show_system_state(data);

        // 网络运行状态
        show_net_status();

        // 数据修复状态
        show_bbcounter();

    }).fail(function(jqXHR, textStatus, errorThrown) {
        console.error("showStatus ajax error: " + errorThrown);
    });
}

/**
 * 系统运行状态
 */
function show_system_state(data) {
    var a = ['cpurate', 'ramrate', 'hdiskrate'];
    Array.prototype.forEach.call(a, function(e, i) {
        var cpu = document.getElementById("pb_" + e);
        cpu.style.width = data[e] + '%';
        cpu.parentNode.nextElementSibling.innerHTML = data[e] + '%';
    });
}

/**
 * 网络运行状态
 */
function show_net_status() {
    // canvas wrapper
    var bcn = document.getElementById("js_bcn");
    var ip = bcn.parentElement.nextElementSibling.lastElementChild;

    var url = bcn.getAttribute("data-url");
    var path = bcn.getAttribute("data-path");

    // broadcast     // 广播网络
    var broadcast = function(dom, bcnArray) {
        require.config({
            paths: {echarts: path}
        });
        require(
            [
                'echarts',
                'echarts/theme/macarons',
                'echarts/chart/bar',
                'echarts/chart/line'
            ],
            function (ec, theme) {
                // var myChart = ec.init(dom, theme);
                var myChart = ec.init(dom, theme);
                var x = (function(aa) {
                    var a = [];
                    for (var i = 0; i < aa.length; i++) {
                        a.push(i);
                    }
                    return a;
                })(bcnArray);

                var option = {
                    tooltip: {
                        show: true
                    },
                    legend: {
                        data:[],
                        textStyle: {
                            color: '#FFF',
                            fontFamily: '微软雅黑',
                            fontSize: 10,
                            fontStyle: 'normal'
                        }
                    },
                    grid: {
                        x: 30,
                        y: 8,
                        x2: 10,
                        y2: 6,
                        borderWidth: 1,
                        borderColor: "#151A2A",
                        backgroudColor: "#272B3A"
                    },
                    xAxis : [
                        {
                            type : 'category',
                            // data : ["t","t+1","t+2","t+3","t+4","t+5", "t+6"],
                            data: x,
                            splitLine: {
                                show: false,
                                lineStyle: {
                                    color: '#151A2A',
                                    type: 'solid',
                                    width: 2
                                }
                            },
                            axisLine: {
                                show: true,
                                lineStyle: {
                                    color: '#151A2A',
                                    type: 'solid',
                                    width: 1
                                }
                            },
                            axisLabel: {
                                show: false,
                                interval: 'auto',
                                rotate: 0,
                                textStyle: {
                                    color: '#FFF',
                                    fontFamily: '微软雅黑',
                                    fontSize: 2,
                                    fontStyle: 'normal'
                                }
                            }
                        }
                    ],
                    yAxis : [
                        {
                            type : 'value',
                            splitLine: {
                                show: false,
                                lineStyle: {
                                    color: '#151A2A',
                                    type: 'solid',
                                    width: 2
                                }
                            },
                            axisLine: {
                                show: true,
                                lineStyle: {
                                    color: '#151A2A',
                                    type: 'solid',
                                    width: 2
                                }
                            },
                            axisLabel: {
                                show: true,
                                interval: 'auto',
                                rotate: 0,
                                textStyle: {
                                    color: '#FFF',
                                    fontFamily: '微软雅黑',
                                    fontSize: 2,
                                    fontStyle: 'normal'
                                }
                            }
                        }
                    ],
                    series : [
                        {
                            name:"bcnrate",
                            type:"line",
                            //data:[5, 20, 40, 10, 10, 20, 5],
                            data: bcnArray,
                            itemStyle: {
                                normal: {
                                    color: "#FFD700",
                                    label: {
                                        show: false,
                                        position: 'inside',
                                        formatter: '{b}\n' + '{c}',
                                        textStyle: {
                                            fontSize: '10',
                                            fontFamily: '微软雅黑',
                                            fontWeight: 'bold',
                                            color: '#000'
                                        }
                                    },
                                    barBorderRadius: [0, 0, 0, 0]
                                }
                            },
                            barWidth: 15
                        }
                    ]
                };
                myChart.setOption(option);
            }
        );
    };
    // bilateral     // 双向网络
    var bilateral = function (dom, ipArray) {

        require.config({
            paths: {echarts: path}
        });

        require(
            [
                'echarts',
                'echarts/theme/macarons',
                'echarts/chart/bar',
                'echarts/chart/line'
            ],
            function (ec, theme) {
                var myChart = ec.init(dom, theme);

                var x = (function(aa) {
                    var a = [];
                    for (var i = 0; i < aa.length; i++) {
                        a.push(i);
                    }
                    return a;
                })(ipArray);

                var option = {
                    tooltip: {
                        show: true
                    },
                    legend: {
                        data:[],
                        textStyle: {
                            color: '#FFF',
                            fontFamily: '微软雅黑',
                            fontSize: 10,
                            fontStyle: 'normal'
                        }
                    },
                    grid: {
                        x: 30,
                        y: 8,
                        x2: 10,
                        y2: 6,
                        borderWidth: 1,
                        borderColor: "#151A2A",
                        backgroudColor: "#272B3A"
                    },
                    xAxis : [
                        {
                            type : 'category',
                            // data : ["t","t+1","t+2","t+3","t+4","t+5", "t+6"],
                            data: x,
                            splitLine: {
                                show: false,
                                lineStyle: {
                                    color: '#151A2A',
                                    type: 'solid',
                                    width: 2
                                }
                            },
                            axisLine: {
                                show: true,
                                lineStyle: {
                                    color: '#151A2A',
                                    type: 'solid',
                                    width: 1
                                }
                            },
                            axisLabel: {
                                show: false,
                                interval: 'auto',
                                rotate: 0,
                                textStyle: {
                                    color: '#FFF',
                                    fontFamily: '微软雅黑',
                                    fontSize: 2,
                                    fontStyle: 'normal'
                                }
                            }
                        }
                    ],
                    yAxis : [
                        {
                            type : 'value',
                            splitLine: {
                                show: false,
                                lineStyle: {
                                    color: '#151A2A',
                                    type: 'solid',
                                    width: 2
                                }
                            },
                            axisLine: {
                                show: true,
                                lineStyle: {
                                    color: '#151A2A',
                                    type: 'solid',
                                    width: 2
                                }
                            },
                            axisLabel: {
                                show: true,
                                interval: 'auto',
                                rotate: 0,
                                textStyle: {
                                    color: '#FFF',
                                    fontFamily: '微软雅黑',
                                    fontSize: 2,
                                    fontStyle: 'normal'
                                }
                            }
                        }
                    ],
                    series : [
                        {
                            name:"iprate",
                            type:"line",
                            // data:[5, 20, 40, 10, 10, 20, 5],
                            data: ipArray,
                            itemStyle: {
                                normal: {
                                    color: "#FFD700",
                                    label: {
                                        show: false,
                                        position: 'inside',
                                        formatter: '{b}\n' + '{c}',
                                        textStyle: {
                                            fontSize: '10',
                                            fontFamily: '微软雅黑',
                                            fontWeight: 'bold',
                                            color: '#000'
                                        }
                                    },
                                    barBorderRadius: [0, 0, 0, 0]
                                }
                            },
                            barWidth: 15
                        }
                    ]
                };
                myChart.setOption(option);
            }
        );
    };

    $.ajax({
        type: 'GET',
        url: url,
        data: {'t': Date.now()}
    }).done(function(resp) {
        bcn.innerHTML = "";
        broadcast(bcn, resp.bcn);
        ip.innerHTML = "";
        bilateral(ip, resp.ip);
    });
}

/**
 * 数据修复状态
 */
function show_bbcounter() {
    var bb = document.getElementById("js_bb");
    var url = bb.getAttribute("data-url");
    var path = bb.getAttribute("data-path");

    var bbcounter = function(dom, array) {
        require.config({
            paths: {echarts: path}
        });
        require(
            [
                'echarts',
                'echarts/theme/macarons',
                'echarts/chart/bar',
                'echarts/chart/line'
            ],
            function (ec, theme) {
                var myChart = ec.init(dom, theme);

                var x = (function(aa) {
                    var a = [];
                    for (var i = 0; i < aa.length; i++) {
                        a.push(i);
                    }
                    return a;
                })(array);

                var option = {
                    tooltip: {
                        show: true
                    },
                    legend: {
                        data:[],
                        textStyle: {
                            color: '#FFF',
                            fontFamily: '微软雅黑',
                            fontSize: 10,
                            fontStyle: 'normal'
                        }
                    },
                    grid: {
                        x: 30,
                        y: 10,
                        x2: 10,
                        y2: 30,
                        borderWidth: 1,
                        borderColor: "#151A2A",
                        backgroudColor: "#272B3A"
                    },
                    xAxis : [
                        {
                            type : 'category',
                            data : x,
                            splitLine: {
                                show: false,
                                lineStyle: {
                                    color: '#151A2A',
                                    type: 'solid',
                                    width: 2
                                }
                            },
                            axisLine: {
                                show: true,
                                lineStyle: {
                                    color: '#151A2A',
                                    type: 'solid',
                                    width: 1
                                }
                            },
                            axisLabel: {
                                show: true,
                                interval: 'auto',
                                rotate: 0,
                                textStyle: {
                                    color: '#FFF',
                                    fontFamily: '微软雅黑',
                                    fontSize: 10,
                                    fontStyle: 'normal'
                                }
                            }
                        }
                    ],
                    yAxis : [
                        {
                            type : 'value',
                            splitLine: {
                                show: false,
                                lineStyle: {
                                    color: '#151A2A',
                                    type: 'solid',
                                    width: 2
                                }
                            },
                            axisLine: {
                                show: true,
                                lineStyle: {
                                    color: '#151A2A',
                                    type: 'solid',
                                    width: 2
                                }
                            },
                            axisLabel: {
                                show: true,
                                interval: 'auto',
                                rotate: 0,
                                textStyle: {
                                    color: '#FFF',
                                    fontFamily: '微软雅黑',
                                    fontSize: 10,
                                    fontStyle: 'normal'
                                }
                            }
                        }
                    ],
                    series : [
                        {
                            name:"bbcounter",
                            type:"bar",
                            data: array,
                            itemStyle: {
                                normal: {
                                    color: "#FFD700",
                                    label: {
                                        show: false,
                                        position: 'inside',
                                        formatter: '{b}\n' + '{c}',
                                        textStyle: {
                                            fontSize: '10',
                                            fontFamily: '微软雅黑',
                                            fontWeight: 'bold',
                                            color: '#000'
                                        }
                                    },
                                    barBorderRadius: [0, 0, 0, 0]
                                }
                            },
                            barWidth: 15
                        }
                    ]
                };
                myChart.setOption(option);
            }
        );
    };

    $.ajax({
        type: 'GET',
        url: url,
        data: {'t': Date.now()}
    }).done(function(resp) {
        bb.innerHTML = "";
        bbcounter(bb, resp.bb);
    });
}