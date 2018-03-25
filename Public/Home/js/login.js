/**
 * Created by Administrator on 2017-05-17.
 */
window.onload = function() {
    document.body.onkeyup = function(event) {
        if(event.keyCode==13) {
            document.forms['login'].submit();
        }
    };

    // 记住密码
    (function() {
        var username = document.getElementsByName("username")[0];
        var password = document.getElementsByName("password")[0];

        var checkbox = document.getElementsByName("remember")[0];
        checkbox.addEventListener('change', function() {
            if (this.checked) {
                docCookies.setItem("username", username.value, Infinity, "/");
                docCookies.setItem("password", password.value, Infinity, "/");
            } else {
                docCookies.removeItem("username", "/");
                docCookies.removeItem("password", "/");
            }
        });

        username.value = docCookies.getItem("username");
        password.value = docCookies.getItem("password");
    })();

};


