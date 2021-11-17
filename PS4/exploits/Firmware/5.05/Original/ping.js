function ping(url) {
    "use strict";
    var xhr = new XMLHttpRequest();
    xhr.open("GET", url, false);
    xhr.send(null);
}