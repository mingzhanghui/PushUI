/**
 * Created by mzh on 2017-01-23.
 */
$(function() {
  var geturlprefix = function() {
    var url = document.getElementById("js-settingscontroller").value;
    return url.substr(0, url.lastIndexOf('/'));
  };

  $("#tabs").tabs();
  $("#panel-tab-mediatype").tabs();

  var resetTable = function(table) {
    var rows = $(table).children("tbody").find("tr").not(".dumb");
    rows.each(function(i, tr) {
      tr.addEventListener("click", function() {
        this.classList.add("selected");
        rows.not($(this)).removeClass("selected");
        $(this).find("input[type='radio']").get(0).checked = "checked";
      });
      tr.style.cursor = "pointer";
    });
    padtable(table, 12);
  };

  var LoadTypes = function (s) {
    var tbody = document.getElementById("js-" + s);
    tbody.innerHTML = "";
    $.ajax({
      type: "GET",
      url: geturlprefix() + "/listTypes",
      data: {"table":s, "field":s}
    }).done(function(data) {
      Array.prototype.forEach.call(data, function(elem, i) {
        var tr = $("<tr>").attr("data-id", elem.id).appendTo($(tbody));
        $("<td>").addClass("td-0").html("<div class='tr_arrow'></div>").appendTo(tr);
        $("<td>").addClass("td-1").html(i+1).appendTo(tr);
        $("<td>").addClass("td-2").html(elem.name).attr("title", elem.name).appendTo(tr);
        var input = $('<input type="radio" name="'+s+'id" value="'+elem.id+'">');
        $("<td>").addClass("td-3").append(input).appendTo(tr);
      });
      resetTable(tbody.parentNode);
    })
  };

  var tables = document.getElementsByClassName("table-scroll");
  Array.prototype.forEach.call(tables, function(table, i) {
    resetTable(table);
  });

  // dialogs #tabs-1
  $("#dlg_add_mediatype").dialog({
    width: 300,
    height: 150,
    autoOpen: false,
    modal: true,
    create: function() {
      $("#js-addmediatype").css({"cursor":"not-allowed"});
    },
    close: function() {
      document.getElementById("form-1").reset();
    },
    buttons: [{
      text: "添加",
      icons: {
        primary: "ui-icon-plusthick"
      },
      click: function() {
        var form = document.getElementById("form-1");
        var dlg = this;
        $.ajax({
          type: form.method,
          url: form.action,
          data: $(form).serialize()
        }).done(function(data) {
          if (data.code > 0) {
            LoadTypes('mediatype');
          } else {
            alert("添加媒体类型失败!");
          }
          $(dlg).dialog("close");
        });
      }
    }]
  });
  $("#js-addmediatype").button().on("click", function() {
    $("#dlg_add_mediatype").dialog("open");
  });

  $("#dlg_edit_mediatype").dialog({
    width: 300,
    height: 150,
    autoOpen: false,
    modal: true,
    create: function() {
      $("#js-editmediatype").css({"cursor":"not-allowed"});
    },
    open: function() {
      var row = $("#js-mediatype").children(".selected");
      if (row.length == 0) {
        alert("还没有选择主文件类型");
        return false;
      }
      var id = row.attr("data-id");
      var name = row.find(".td-2").attr("title");
      $("#js-prompt-editmediaid").val(id);
      $("#js-prompt-editmediatype").val(name);
    },
    close: function() {document.getElementById("form-2").reset();},
    buttons: [{
      text: "修改",
      icons: {
        primary: "ui-icon-pencil"
      },
      click: function() {
        var form = document.getElementById("form-2");
        var dlg = this;
        $.ajax({
          type: form.method,
          url: form.action,
          data: $(form).serialize()
        }).done(function(data) {
          if (data.code > 0) {
            LoadTypes('mediatype');
          } else {
            alert("修改媒体类型失败!");
          }
          $(dlg).dialog("close");
        });
      }
    }]
  });
  $("#js-editmediatype").button().on("click", function() {
    var row = $("#js-mediatype").children(".selected");
    if (row.length == 0) {
      alert("还没有选择主文件类型");
      return false;
    }
    $("#dlg_edit_mediatype").dialog("open");
  });

  $("#dlg_add_appendixtype").dialog({
    width: 300,
    height: 150,
    autoOpen: false,
    modal: true,
    create: function() {
      $("#js-addappendixtype").css({"cursor":"not-allowed"});
    },
    close: function() {
      document.getElementById("form-3").reset();
    },
    buttons: [{
      text: "添加",
      icons: {
        primary: "ui-icon-plusthick"
      },
      click: function() {
        var form = document.getElementById("form-3");
        var dlg = this;
        $.ajax({
          type: form.method,
          url: form.action,
          data: $(form).serialize()
        }).done(function(data) {
          if (data.code > 0) {
            LoadTypes('appendixtype');
          } else {
            alert("添加媒体类型失败!");
          }
          $(dlg).dialog("close");
        });
      }
    }]
  });
  $("#js-addappendixtype").button().on("click", function() {
    $("#dlg_add_appendixtype").dialog("open");
  });

  $("#dlg_edit_appendixtype").dialog({
    width: 300,
    height: 150,
    autoOpen: false,
    modal: true,
    create: function() {
      $("#js-editappendixtype").css({"cursor":"not-allowed"});
    },
    open: function() {
      var row = $("#js-appendixtype").children(".selected");
      if (row.length == 0) {
        alert("还没有选择附件类型");
        return false;
      }
      var id = row.attr("data-id");
      var name = row.find(".td-2").attr("title");
      $("#js-prompt-editappendixid").val(id);
      $("#js-prompt-editappendixtype").val(name);
    },
    close: function() {document.getElementById("form-4").reset();},
    buttons: [{
      text: "修改",
      icons: {
        primary: "ui-icon-pencil"
      },
      click: function() {
        var form = document.getElementById("form-4");
        var dlg = this;
        $.ajax({
          type: form.method,
          url: form.action,
          data: $(form).serialize()
        }).done(function(data) {
          if (data.code > 0) {
            LoadTypes('appendixtype');
          } else {
            alert("修改媒体类型失败!");
          }
          $(dlg).dialog("close");
        });
      }
    }]
  });
  $("#js-editappendixtype").button().on("click", function() {
    var row = $("#js-appendixtype").children(".selected");
    if (row.length == 0) {
      alert("还没有选择主文件类型");
      return false;
    }
    $("#dlg_edit_appendixtype").dialog("open");
  });

//  dialogs #tabs-2
  // genre 剧情类型管理
  $("#dlg_add_genre").dialog({
    width: 300,
    height: 150,
    autoOpen: false,
    modal: true,
    close: function() {document.getElementById("form-5").reset();},
    buttons: [{
      text: "添加",
      icons: {primary: "ui-icon-plusthick"},
      click: function() {
        var form = document.getElementById("form-5");
        var dlg = this;
        $.ajax({
          type: form.method,
          url: form.action,
          data: $(form).serialize()
        }).done(function(data) {
          if (data.code > 0) {
            LoadTypes('genre');
          } else {
            alert("添加媒体类型失败!");
          }
          $(dlg).dialog("close");
        });
      }
    }]
  });
  $("#js-addgenre").button().on("click", function() {
    $("#dlg_add_genre").dialog("open");
  });

  $("#dlg_edit_genre").dialog({
    width: 300,
    height: 150,
    autoOpen: false,
    modal: true,
    open: function() {
      var row = $("#js-genre").children(".selected");
      if (row.length == 0) {
        alert("还没有选择剧情类型");
        return false;
      }
      var id = row.attr("data-id");
      var name = row.find(".td-2").attr("title");
      $("#js-prompt-editgenreid").val(id);
      $("#js-prompt-editgenre").val(name);
    },
    close: function() {document.getElementById("form-6").reset();},
    buttons: [{
      text: "修改",
      icons: {primary: "ui-icon-pencil"},
      click: function() {
        var form = document.getElementById("form-6");
        var dlg = this;
        $.ajax({
          type: form.method,
          url: form.action,
          data: $(form).serialize()
        }).done(function(data) {
          if (data.code > 0) {
            LoadTypes('genre');
          } else {
            alert("修改剧情类型失败!");
          }
          $(dlg).dialog("close");
        });
      }
    }]
  });
  $("#js-editgenre").button().on("click", function() {
    var row = $("#js-genre").children(".selected");
    if (row.length == 0) {
      alert("还没有选择主文件类型");
      return false;
    }
    $("#dlg_edit_genre").dialog("open");
  });

  $("#dlg_del_genre").dialog({
    width: 300,
    height: 150,
    autoOpen: false,
    modal: true,
    open: function() {
      var row = $("#js-genre").children(".selected");
      if (row.length == 0) {
        alert("还没有选择剧情类型");
        return false;
      }
      var id = row.attr("data-id");
      var name = row.find(".td-2").attr("title");
      $("#js-prompt-delgenreid").val(id);
      $("#js-prompt-delgenre").html(name);
    },
    close: function() {document.getElementById("form-7").reset();},
    buttons: [{
      text: "确定",
      icons: {primary: "ui-icon-trash"},
      click: function() {
        var form = document.getElementById("form-7");
        var dlg = this;
        $.ajax({
          type: form.method,
          url: form.action,
          data: $(form).serialize()
        }).done(function(data) {
          if (data.code > 0) {
            LoadTypes('genre');
          } else {
            alert(data.msg);
          }
          $(dlg).dialog("close");
        });
      }
    },{
      text: "取消",
      icons: {primary: "ui-icon-arrowreturnthick-1-w"},
      click: function() {$(this).dialog("close");}
    }]
  });
  $("#js-delgenre").button().on("click", function() {
    var row = $("#js-genre").children(".selected");
    if (row.length == 0) {
      alert("还没有选择主文件类型");
      return false;
    }
    $("#dlg_del_genre").dialog("open");
  });
  
  // country 地区类型管理
  $("#dlg_add_country").dialog({
    width: 300,
    height: 150,
    autoOpen: false,
    modal: true,
    close: function() {document.getElementById("form-8").reset();},
    buttons: [{
      text: "添加",
      icons: {primary: "ui-icon-plusthick"},
      click: function() {
        var form = document.getElementById("form-8");
        var dlg = this;
        $.ajax({
          type: form.method,
          url: form.action,
          data: $(form).serialize()
        }).done(function(data) {
          if (data.code > 0) {
            LoadTypes('country');
          } else {
            alert("添加国家类型失败!");
          }
          $(dlg).dialog("close");
        });
      }
    }]
  });
  $("#js-addcountry").button().on("click", function() {
    $("#dlg_add_country").dialog("open");
  });

  $("#dlg_edit_country").dialog({
    width: 300,
    height: 150,
    autoOpen: false,
    modal: true,
    open: function() {
      var row = $("#js-country").children(".selected");
      if (row.length == 0) {
        alert("还没有选择剧情类型");
        return false;
      }
      var id = row.attr("data-id");
      var name = row.find(".td-2").attr("title");
      $("#js-prompt-editcountryid").val(id);
      $("#js-prompt-editcountry").val(name);
    },
    close: function() {document.getElementById("form-9").reset();},
    buttons: [{
      text: "修改",
      icons: {primary: "ui-icon-pencil"},
      click: function() {
        var form = document.getElementById("form-9");
        var dlg = this;
        $.ajax({
          type: form.method,
          url: form.action,
          data: $(form).serialize()
        }).done(function(data) {
          if (data.code > 0) {
            LoadTypes('country');
          } else {
            alert("修改国家地区类型失败!");
          }
          $(dlg).dialog("close");
        });
      }
    }]
  });
  $("#js-editcountry").button().on("click", function() {
    var row = $("#js-country").children(".selected");
    if (row.length == 0) {
      alert("还没有选择主文件类型");
      return false;
    }
    $("#dlg_edit_country").dialog("open");
  });

  $("#dlg_del_country").dialog({
    width: 300,
    height: 150,
    autoOpen: false,
    modal: true,
    open: function() {
      var row = $("#js-country").children(".selected");
      if (row.length == 0) {
        alert("还没有选择剧情类型");
        return false;
      }
      var id = row.attr("data-id");
      var name = row.find(".td-2").attr("title");
      $("#js-prompt-delcountryid").val(id);
      $("#js-prompt-delcountry").html(name);
    },
    close: function() {document.getElementById("form-10").reset();},
    buttons: [{
      text: "确定",
      icons: {primary: "ui-icon-trash"},
      click: function() {
        var form = document.getElementById("form-10");
        var dlg = this;
        $.ajax({
          type: form.method,
          url: form.action,
          data: $(form).serialize()
        }).done(function(data) {
          if (data.code > 0) {
            LoadTypes('country');
          } else {
            alert(data.msg);
          }
          $(dlg).dialog("close");
        });
      }
    },{
      text: "取消",
      icons: {primary: "ui-icon-arrowreturnthick-1-w"},
      click: function() {$(this).dialog("close");}
    }]
  });
  $("#js-delcountry").button().on("click", function() {
    var row = $("#js-country").children(".selected");
    if (row.length == 0) {
      alert("还没有选择国家类型");
      return false;
    }
    $("#dlg_del_country").dialog("open");
  });

//  year 时间类型管理
  $("#dlg_add_year").dialog({
    width: 300,
    height: 150,
    autoOpen: false,
    modal: true,
    close: function() {document.getElementById("form-11").reset();},
    buttons: [{
      text: "添加",
      icons: {primary: "ui-icon-plusthick"},
      click: function() {
        var form = document.getElementById("form-11");
        var dlg = this;
        $.ajax({
          type: form.method,
          url: form.action,
          data: $(form).serialize()
        }).done(function(data) {
          if (data.code > 0) {
            LoadTypes('year');
          } else {
            alert("添加年份时间类型失败!");
          }
          $(dlg).dialog("close");
        });
      }
    }]
  });
  $("#js-addyear").button().on("click", function() {
    $("#dlg_add_year").dialog("open");
  });

  $("#dlg_edit_year").dialog({
    width: 300,
    height: 150,
    autoOpen: false,
    modal: true,
    open: function() {
      var row = $("#js-year").children(".selected");
      if (row.length == 0) {
        alert("还没有选择剧情类型");
        return false;
      }
      var id = row.attr("data-id");
      var name = row.find(".td-2").attr("title");
      $("#js-prompt-edityearid").val(id);
      $("#js-prompt-edityear").val(name);
    },
    close: function() {document.getElementById("form-12").reset();},
    buttons: [{
      text: "修改",
      icons: {primary: "ui-icon-pencil"},
      click: function() {
        var form = document.getElementById("form-12");
        var dlg = this;
        $.ajax({
          type: form.method,
          url: form.action,
          data: $(form).serialize()
        }).done(function(data) {
          if (data.code > 0) {
            LoadTypes('year');
          } else {
            alert("修改年份时间类型失败!");
          }
          $(dlg).dialog("close");
        });
      }
    }]
  });
  $("#js-edityear").button().on("click", function() {
    var row = $("#js-year").children(".selected");
    if (row.length == 0) {
      alert("还没有选择年份类型");
      return false;
    }
    $("#dlg_edit_year").dialog("open");
  });

  $("#dlg_del_year").dialog({
    width: 300,
    height: 150,
    autoOpen: false,
    modal: true,
    open: function() {
      var row = $("#js-year").children(".selected");
      if (row.length == 0) {
        alert("还没有选择剧情类型");
        return false;
      }
      var id = row.attr("data-id");
      var name = row.find(".td-2").attr("title");
      $("#js-prompt-delyearid").val(id);
      $("#js-prompt-delyear").html(name);
    },
    close: function() {document.getElementById("form-13").reset();},
    buttons: [{
      text: "确定",
      icons: {primary: "ui-icon-trash"},
      click: function() {
        var form = document.getElementById("form-13");
        var dlg = this;
        $.ajax({
          type: form.method,
          url: form.action,
          data: $(form).serialize()
        }).done(function(data) {
          if (data.code > 0) {
            LoadTypes('year');
          } else {
            alert(data.msg);
          }
          $(dlg).dialog("close");
        });
      }
    },{
      text: "取消",
      icons: {primary: "ui-icon-arrowreturnthick-1-w"},
      click: function() {$(this).dialog("close");}
    }]
  });
  $("#js-delyear").button().on("click", function() {
    var row = $("#js-year").children(".selected");
    if (row.length == 0) {
      alert("还没有选择年份时间类型");
      return false;
    }
    $("#dlg_del_year").dialog("open");
  });
//  END #tabs-2

  // #tabs-3 管理设置
  var loadConfig = function() {
    var url = geturlprefix() + "/listConfig";
    var data = {"t":Date.now()};
    $.ajax({
      type: "GET",
      url: url,
      data: data
    }).done(function(data) {
      // typeof data "object"
      for (var prop in data) {
        // if (data.hasOwnProperty(prop)) { }
        // console.log(data[prop]);
        $("#js-" + prop).val(data[prop]);
      }
    })
  };
  loadConfig();

  // 管理设置 修改配置 putConfig
  document.getElementById("form-config").addEventListener("submit", function(e) {
    e.preventDefault();
    var form = this;

    var submitBtn = $(form).find("input[type='submit']").get(0);
    $.ajax({
      type: "POST",
      url: form.action,
      data: $(form).serialize()
    }).done(function(data) {
      submitBtn.value = "提交修改";

      var feedback = document.getElementById("js-feedback");
      if (data.count == 8) {
        feedback.innerHTML = "配置设置成功!";
        feedback.style.color = "#3c763d";
        loadConfig();   // refresh
      } else {
        feedback.innerHTML = "修改配置失败！";
        feedback.style.color = "#a94442";
      }
      $("#dlg_feedback").dialog("open");
    }).fail(function( jqXHR, textStatus, errorThrown) {
      alert(errorThrown);
    });
    submitBtn.value = "正在提交...";
  });

  $("#dlg_feedback").dialog({
    width: 300,
    height: 200,
    autoOpen: false,
    modal: true,
    close: function() {
      $("#js-feedback").css({"color":"#353535"}).html('');
    },
    buttons: [{
      text: "OK",
      icons: {primary: "ui-icon-arrowreturnthick-1-w"},
      click: function() {$(this).dialog("close");}
    }]
  });

});



