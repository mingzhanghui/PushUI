/**
 * Created by Administrator on 2017-02-20.
 */
var lastChecked = null;

$(function () {
    // begin dialogs
    $('#loadingInsert').dialog({
        modal: true,
        autoOpen: false,
        width: 340
    });
    $('#requireMedia').dialog({
        modal: true,
        autoOpen: false,
        width: 340,
        height: 200,
        buttons: {
            "确定": function () {
                $(this).dialog('close');
            }
        }
    });
    $('#Fileformaterror').dialog({
        modal: true,
        autoOpen: false,
        width: 340,
        height: 120,
        buttons: {
            "确定": function () {
                $(this).dialog('close');
            }
        }
    });
    $('#uploadFeedback').dialog({
        modal: true,
        autoOpen: false,
        width: 340,
        height: 120,
        buttons: {
            "确定": function () {
                $(this).dialog('close');
                window.location.reload();
            }
        }
    });
    // end dialogs
    // 补充列表空白行, 内容导入
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

    var tl = $('#table-import').get(0);
    padtable(tl, 14);
    // 今日导入列表
    var tr = $('#table-today').get(0);
    padtable(tr, 14);

    var check = document.getElementById('js-checkall');
    check.addEventListener('click', function() {
        checkAll(this, 'ftp');
    });

    // id="js-upload"
    var upload = document.getElementById('js-upload');
    upload.addEventListener('click', function(e) {
        e.preventDefault();
        uploadfile();
    });

    // 点击行触发checkbox
    $("#table-import").children("tbody").find("tr").not("dumb").each(function() {
        // leave out last td
        var td = $(this).children();
        for (var i = 0; i < td.length-1; i++) {
            td.get(i).addEventListener("click", function(event) {
                event.stopPropagation();
                var checkbox = $(this).parent().children().last().children("input").get(0);
                if (checkbox.checked) {
                    // $(checkbox).removeAttr("checked");
                    checkbox.checked = false;
                } else {
                    checkbox.checked = 'checked';
                }
            });
        }
    });

    // shift-select-multiple-checkboxes-like-gmail
    var checkboxes = document.getElementsByName('ftp');
    for (var i = 0; i < checkboxes.length; i++) {
        lastChecked = null;
        checkboxes[i].addEventListener('click', function(e) {
            if (!lastChecked) {
                lastChecked = this;
                return;
            }
            if (e.shiftKey) {
                var thisIndex, lastIndex;
                for (var ii = 0; ii < checkboxes.length; ii++) {
                    if (this == checkboxes[ii]) {
                        thisIndex = ii;
                    }
                    if (lastChecked == checkboxes[ii]) {
                        lastIndex = ii;
                    }
                }
                var start = Math.min(thisIndex, lastIndex);
                var end = Math.max(thisIndex, lastIndex);

                Array.prototype.slice.call(checkboxes).forEach(function(node, i) {
                    if (start <= i && i < end+1) {
                        node.checked = lastChecked.checked;
                    }
                });
            }
        });
    }

});

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

function uploadfile() {
    var selectedFiles = "";
    var srcDir = document.getElementById('before-import').innerText.replace(/\\/g, "/");
    var dstDir = document.getElementById('after-import').innerText.replace(/\\/g, "/");
    // add checked items
    var $checkboxes = $('#table-import').children('tbody').find("input[type=checkbox]");
    $checkboxes.each(function() {
        if (this.checked) {
            selectedFiles += this.value + '|';
        }
    });
    // remove last '|'
    selectedFiles = selectedFiles.substr(0, selectedFiles.length-1);

    // select at least 1 file
    if ("" == selectedFiles) {
        $('#requireMedia').dialog('open');
        return false;
    }

    var mediatype = document.getElementById('mediatype').value;
    var data = {
        'srcdir' : srcDir,
        'dstdir' : dstDir,
        'names': selectedFiles,
        'mediatype': mediatype
    };
    var baseurl = document.getElementById('js-urlbase').value;

    $.ajax({
        type: "POST",
        url: baseurl + '/upfile',
        data: data
    }).done(function(resp) {
        $('#loadingInsert').dialog('close');
        var $fb = $('#uploadFeedback');
        $fb.html("<br /><h5>"+resp.msg+"</h5>");
        $fb.dialog('open');
    }).fail(function (jqXHR, textStatus) {
        alert( "Request failed: " + textStatus );
    });
    $('#loadingInsert').dialog('open');
}

function editMedia(obj) {
    var baseurl = document.getElementById("js-urlbase").value;
    var url = baseurl + "/getediturl";

    var td = obj.parentNode;
    var data = {
        'oid'    : td.getAttribute("data-oid"),
        'typeid' : td.getAttribute("data-typeid")
    };
    var request = $.ajax({
        type: "GET",
        url: url,
        data: data
    });
    request.done(function(resp) {
        console.log(resp);
        location.href = resp;
    });
    request.fail(function(jqXHR, textStatus) {
        alert( "editMedia -> getediturl failed: " + textStatus );
    });
}