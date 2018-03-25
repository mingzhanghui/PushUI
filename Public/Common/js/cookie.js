/**
 * Created by Administrator on 2017-02-22.
 */
// クッキー保存　setCookie(クッキー名, クッキーの値, クッキーの有効minute数); //
function setCookie(c_name,value,expireminutes) {
  // pathの指定
  var path = location.pathname;
  // pathをフォルダ毎に指定する場合のIE対策
  var paths = new Array();
  paths = path.split("/");
  if(paths[paths.length-1] != "") {
    paths[paths.length-1] = "";
    path = paths.join("/");
  }
  // 有効期限の日付
  var extime = new Date().getTime();
  var cltime = new Date(extime + (60*1000*expireminutes));
  var exdate = cltime.toUTCString();
  // クッキーに保存する文字列を生成
  var s="";
  s += c_name +"="+ escape(value);// 値はエンコードしておく
  s += "; path="+ path;
  if(expireminutes) {
    s += "; expires=" +exdate+"; ";
  }else{
    s += "; ";
  }
  // クッキーに保存
  document.cookie=s;
}

function unsetCookie(c_name) {
  return setCookie(c_name, "", -1);
}
// クッキーをセット
// window.onload = setCookie('testName','sample',7);

// クッキーの値を取得 getCookie(クッキー名); //
function getCookie(c_name) {
  if (document.cookie.length > 0) {
    var a = document.cookie.split("; ");
    for (var i = 0; i < a.length; i++) {
      var pair = a[i].split("=");
      if (c_name == pair[0]) {
        return pair[1];
      }
    }
  }
  return "";
}
