/**
 * Created by Administrator on 2017-02-10.
 */
$(function() {
    // jquery-ui tabs
    $( "#tabs" ).tabs();

    // 加载业务包周期列表
    listCycleType();

    // trigger 添加业务包周期
    $("#js_add").on("click", function(e) {
        e.preventDefault();
        $("#dialog-1").dialog("open");
    });

    // 添加业务包周期
    $("#dialog-1").dialog({
        title: "添加业务包周期",
        autoOpen: false,
        modal: true,
        width: 450,
        height: 400,
        resizable: true,
        closeOnEscape: true,
        show: {effect: "fade", duration: 100},
        hide: {effect: "fade", duration: 100},
        create: function(event, ui) {},
        open: function(event, ui) {},
        beforeClose: function(event, ui) {},
        close: function(event, ui) {this.children[0].reset();},
        buttons: [{
            text: "添加",
            icons: {
                primary: "ui-icon-check"
            },
            click: function() {
                var dlg = this;
                var form = dlg.children[0];
                $.ajax({
                    type: 'POST',
                    url: form.action,
                    data: $(form).serialize()
                }).done(function(data) {
                    $(dlg).dialog("close");
                    setCookie('typeid', data.id, 5);
                    listCycleType();
                }).fail(function(jqXHR, textStatus, errorThrown) {
                    alert('#dialog-1 添加 error: ' + errorThrown);
                })
            }
        }, {
            text: "取消",
            icons: {
                primary: 'ui-icon-arrowreturnthick-1-w'
            },
            click: function() {
                $(this).dialog("close");
            }
        }]
    });

    // 修改业务包周期 直接form提交

    // 删除业务包周期
    $("#js_del").on("click", function(e) {
        e.preventDefault();
        // this.innerHTML = "删除中...";
        var id = document.getElementById("js-typeid").value;

        var form = document.createElement("form");
        form.action = this.getAttribute("data-url");
        form.method = 'GET';

        var input = document.createElement("input");
        input.setAttribute("type", "hidden");
        input.setAttribute("name", 'id');
        input.setAttribute("value", id);
        form.appendChild(input);

        var body = document.getElementsByTagName("body")[0];
        body.appendChild(form);

        form.submit();
        listCycleType();
    });

    // 加载内容存放路径
    loadLogPath();

    // 修改内容存放路径
    var form = document.getElementById("form_logpath");
    form.addEventListener("submit", function(e) {
        e.preventDefault();

        var help = document.getElementById("js_helppath");
        help.innerHTML = "<span class='text-info'>修改日志路径...</span>";

        var url = this.action;
        var data = $(this).serialize();

        $.ajax({
            type: 'POST',
            url: url,
            data: data
        }).done(function(resp) {
            help.innerHTML = '';
            if (resp.code == 1) {
                help.innerHTML = "<span class='text-success'>"+resp.msg+"</span>";
            } else {
                help.innerHTML = "<span class='text-danger'>"+resp.msg+"</span>";
            }
            setTimeout(function() {
                help.innerHTML = '';
            }, 8000);
        }).fail(function(jqXHR, textStatus, errorThrown) {
            help.innerHTML = "<span class='text-danger'>修改内容存放路径失败: "+ errorThrown +"</span>";
        });
    });
});

// 业务包周期列表
function listCycleType() {
    var table = document.getElementById("pkgperiod");
    var tbody = table.children[1];
    var url = tbody.getAttribute("data-url");
    tbody.innerHTML = '<tr><td colspan="4" class="text-center">正在加载业务包周期...</td></tr>';

    $.ajax({
        type: "GET",
        data: {'t': Date.now()},
        url: url
    }).done(function(resp) {
        tbody.innerHTML = '';
        if (resp.length == 0) {
            tbody.innerHTML = '<tr><td colspan="4" class="text-center">暂时没有业务包周期</td></tr>';
            return;
        }
        resp.forEach(function(elem, i) {
            var tr = document.createElement("tr");
            tr.setAttribute("data-id", elem.id);
            tbody.appendChild(tr);
            tr.innerHTML = '<td class="td0">&nbsp;</td>' +
                '<td class="td1">'+(i+1)+'</td>' +
                '<td class="td2" title="'+elem.type+'">'+elem.type+'</td>' +
                '<td class="td3" title="'+elem.desc+'">'+elem.desc+'</td>' +
                '<td class="td4"><input type="radio" name="package" title="period" value="'+elem.id+'"></td>';
        });

        var clicked = false;
        Array.prototype.forEach.call(tbody.children, function(tr) {
            tr.addEventListener("click", function() {
                var selected = tbody.querySelector(".selected");
                if (selected) {
                    selected.classList.contains("selected") && selected.classList.remove("selected");
                }
                this.classList.contains("selected") || this.classList.add("selected");
                var input = this.children[this.children.length-1].children[0];
                input.checked = "checked";

                $("#js-typeid").val(input.value);
                $("#period-type").val(this.querySelector('.td2').title);
                $("#period-desc").val(this.querySelector('.td3').title);

                setCookie('typeid', input.value, 5);
            });
            if ( !clicked ) {
                if (tr.getAttribute("data-id") == getCookie('typeid')) {
                    $(tr).trigger("click");
                    clicked = true;
                }
            }
        });
        clicked || $(tbody).children('tr').first().trigger("click");

        padScrollTable(table, 15, 5);
    }).fail(function(jqXHR, textStatus, errorThrown) {
        alert('listcycleType: ' + errorThrown);
    })
}

// 加载内容存放路径
function loadLogPath() {
    var target = $("#js_logpath");
    var url = target.attr('data-url');
    $.getJSON(url, {'t':Date.now()}, function(data) {
        target.val( data.dir );
    });
}