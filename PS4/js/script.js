(function () {
  var cookies = document.cookie.split("; ");
  for (var c = 0; c < cookies.length; c++) {
      var d = window.location.hostname.split(".");
      while (d.length > 0) {
          var cookieBase = encodeURIComponent(cookies[c].split(";")[0].split("=")[0]) + '=; expires=Thu, 01-Jan-1970 00:00:01 GMT; domain=' + d.join('.') + ' ;path=';
          var p = location.pathname.split('/');
          document.cookie = cookieBase + '/';
          while (p.length > 0) {
              document.cookie = cookieBase + p.join('/');
              p.pop();
          };
          d.shift();
      }
  }
})();

let dde = document.documentElement;
dde.addEventListener("mousemove", e => {
  let ow = dde.offsetWidth; 
  let oh = dde.offsetHeight; 
  dde.style.setProperty('--mouseX', e.clientX * 100 / ow + "%");
  dde.style.setProperty('--mouseY', e.clientY * 100 / oh + "%");
});

function isInArray(value, array) {
  return array.indexOf(value) > -1;
}

function updatePage(title, header, buttons) {
  document.title = "HAKKURAIFU | PS4xploit";
  document.getElementById("title").innerHTML = title;
  document.getElementById("header").innerHTML = header;
  document.getElementById("buttons").innerHTML = buttons;
}

function resetPage() {
  history.pushState("", document.title, window.location.pathname + window.location.search);
  updatePage("HAKKURAIFU | PS4xploit", "Choose Firmware", firmwares);
}

function getFirmwares() {
  var currentFirmware = navigator.userAgent.substring(navigator.userAgent.indexOf('5.0 ('), navigator.userAgent.indexOf(') Apple'));
  currentFirmware = currentFirmware.replace('5.0 (PlayStation 4 ', '');
  var firmwares = "";
  x = 0;
  for (var i = 0, len = data["Choose Firmware"].length; i < len; i++) {
    x += 1;
    if (currentFirmware == data["Choose Firmware"][i]) {
      firmwares += "<a href=\"#" + data["Choose Firmware"][i] + "\"><button class=\"btn btn-main\">" + data["Choose Firmware"][i] + "</button></a>";
    } else {
      firmwares += "<a href=\"#" + data["Choose Firmware"][i] + "\"><button class=\"btn btn-disabled\">" + data["Choose Firmware"][i] + "</button></a>";
    }
    if (x >= 3) {
      firmwares += "<br>";
      x = 0;
    }
  }
  // similar behavior as clicking on a link
  if (currentFirmware == "7.55") {
    window.location.href = ".#" + "7.5x";
  } else if (currentFirmware == "7.51") {
    window.location.href = ".#" + "7.5x";
  } else if (currentFirmware == "7.50") {
    window.location.href = ".#" + "7.5x";
  } else if (currentFirmware == "9.99") {
    window.alert("You are in spoof mod, please choose a firmware manually");
  } else if (currentFirmware == "5.05") {
    window.location.href = ".#" + "5.05";
  } else {
    window.location.href = ".#" + currentFirmware;
  }
  

  
  return firmwares;
}

function getExploits() {
  var hash = window.location.hash.substr(1);
  var exploits = "";
  x = 0;
  for (var i = 0, len = data[hash].length; i < len; i++) {
    x += 1;
    if (data[hash][i] == "[Back]") {
      exploits += "<a href=\"#back\"><button class=\"btn btn-main\">" + data[hash][i] + "</button></a>";
    } else {
      exploits += "<a href=\"."  + exploitBase + "Firmware/" + hash + "/" + data[hash][i] + "/index.html\"><button class=\"btn btn-main\">" + data[hash][i] + "</button></a>";
    }
    if (x >= 3) {
      exploits += "<br>";
      x = 0;
    }
  }
  return exploits;
}

function firmwareSelected() {
  var hash = window.location.hash.substr(1);
  if (!isInArray(hash, firmwares)) {
    resetPage();
  } else {
    var exploits = getExploits();
    updatePage("Firmware Exploit | " + hash, hash, exploits);
  }
}

