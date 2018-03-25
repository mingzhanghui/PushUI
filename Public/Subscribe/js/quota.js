function CreateXmlHttp() {
	var xhrobj = null;
	try {
		xhrobj = new ActiveXObject('Msxml2.XMLHTTP'); // ie msxml 3.0+
	} catch(e) {
		try {
			xhrobj = new ActiveXObject('Microsoft.XMLHTTP'); // ie msxml 2.6
		} catch (e2) {
			xhrobj = null;
		}
	}
	if (!xhrobj && typeof XMLHttpRequest != 'undefined') {   // firefox opera 8.0 safari
		xhrobj = new XMLHttpRequest();
	}
	return xhrobj;
}

function Get(xhr, url, data, callback) {
	// data object
	var datastring = '?';
	for (var prop in data) {
		if (data.hasOwnProperty(prop)) {
			datastring += prop + '=' + data[prop] + '&';
		}
	}
	url += datastring.substr(0, datastring.lastIndexOf('&'));

	xhr.open("GET", url, true);
	xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xhr.setRequestHeader("If-Modified-Since", "0");
	xhr.onreadystatechange = function() {
		if (xhr.readyState == 4) {
			if (xhr.status == 200) {
				var resp = xhr.responseText;
				callback(resp);
			}
		}
	};

	xhr.send(null);
}

// 播发总量控制 control.html
window.addEventListener("load", function() {
	var form = document.getElementById("edit-volume");

	var help = form.children[1];
	var btn = document.getElementById("js_submit");

	form.onsubmit = function(e) {
		e.preventDefault();

		help.innerHTML = '';
		var text = btn.innerText;
		btn.innerText = "修改中...";

		var url = this.action;
		var xhr = CreateXmlHttp();
		var data = {'intvalue': document.getElementById("new_volume").value};

		Get(xhr, url, data, function(resp) {
			try {
				var data = JSON.parse(resp);
				if (data.code == 1) {
					if (data.msg == 'update') {
						help.innerHTML = '<span class="text-success">更新成功!</span>';
					} else if (data.msg == 'add') {
						help.innerHTML = '<span class="text-success">添加成功</span>';
					}
					document.getElementById("cur_volume").value = data.value;
				} else {
					help.innerHTML = '<span class="text-danger">修改总量失败!</span>';
				}
				btn.innerText = text;
			} catch (e) {
				alert('parsing failed: ' + url);
				help.innerHTML = '<span class="text-danger">修改总量失败!</span>';
			}
			setTimeout(function() {
				help.innerHTML = '';
			}, 3000);
		});
	};

});