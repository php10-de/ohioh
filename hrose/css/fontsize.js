var lifetime = 1000 * 60 * 60 * 24 * 7;  function setCookieFontSize(){    var cookie_font = readCookie();    if (!cookie_font){      cookie_font = 100;    }    document.getElementById('main').style.fontSize = cookie_font + '.1%';  }  function bigger(value){  var cookie_font = readCookie();    size = document.getElementById('main').style.fontSize;    if(!size){      size = cookie_font;      size = size * value;      size = Math.round(size);    } else{           size = size.substring(0,size.length-3);        size = parseFloat(size);        size = size * value;        size = Math.round(size);    }    document.getElementById('main').style.fontSize = size + '.1%';    setCookie ('sdbpFontScale',size,lifetime);  }    function smaller(value){  var cookie_font = readCookie();    size = document.getElementById('main').style.fontSize;    if(!size){      size = cookie_font;      size = size / value;      size = Math.round(size);    } else{           size = size.substring(0,size.length-3);        size = parseFloat(size);        size = size / value;        size = Math.round(size);    }    document.getElementById('main').style.fontSize = size + '.1%';    setCookie ('sdbpFontScale',size,lifetime);  }    function setCookie (name, value, lifetime) {        var now = new Date();        var timestamp = new Date(now.getTime() + lifetime);        document.cookie = name + "=" + value + "; expires=" + timestamp.toGMTString() + "; path=/;";  }    function readCookie(){        var value = false;        if (document.cookie) {            var start_pos = document.cookie.indexOf("sdbpFontScale=");            if(start_pos > -1) {              var end_pos = document.cookie.indexOf(";",start_pos);              if (end_pos == -1){                  end_pos = document.cookie.length;              }              value = document.cookie.substring(start_pos+14, end_pos);            }        }        //value = 100;        return value;    }