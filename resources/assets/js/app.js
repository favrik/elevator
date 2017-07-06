
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Echo.channel('elevator').listen('ElevatorStateUpdated', (e) => {
  refreshStatus(e.state);
});

function ready(fn) {
  if (document.attachEvent ? document.readyState === "complete" : document.readyState !== "loading") {
    fn();
  } else {
    document.addEventListener('DOMContentLoaded', fn);
  }
}

ready(function () {
  E_STATE.queue = E_STATE.queue || [];
  document.querySelector('.control').addEventListener('click', eventHandler);
  refreshStatus(E_STATE);
});


function eventHandler(e) {
  if (e.target.tagName === 'LABEL') {
    toggleBulkRequests();
  }

  if (e.target.tagName === 'BUTTON') {
    e.preventDefault();
    if (e.target.id === 'add-request') {
      queueRequest();
    } else {
      callAPI(e.target.id);
    }
  }
}

function refreshStatus(state) {
  var fragments = {
    current_floor: document.querySelector('#floor'),
    direction: document.querySelector('#direction'),
    signal: document.querySelector('#signal')
  };

  for (name in fragments) {
    fragments[name].innerHTML = '';
    fragments[name].appendChild(document.createTextNode(state[name]));
  }

  var currentFloor = document.querySelector('.elevator__floor--current');
  if (!currentFloor) {
    var floor = document.querySelector('#floor-' + state.current_floor);
    floor.classList.add('elevator__floor--current');

  } else if (currentFloor.id !== 'floor-' + state.current_floor) {
    currentFloor.classList.remove('elevator__floor--current');

    var floor = document.querySelector('#floor-' + state.current_floor);
    floor.classList.add('elevator__floor--current');
  }
}

function getRequestData(url) {
  if (url === 'reset') {
    return '';
  }

  if (url === 'trigger') {
    return 'signal=alarm';
  }

  var data = getFormData();
  var encodedData = [];
  for (name in data) {
    encodedData.push(encodeURIComponent(name) + '=' + encodeURIComponent(data[name]));
  }

  return encodedData.join('&').replace(/%20/g, '+');
}

function getFormData() {
  return {
    from: document.querySelector('#from').value,
    to: document.querySelector('#to').value
  };
}

function toggleBulkRequests(e) {
  var style = document.querySelector('#add-request').style;
  style.display = style.display === 'none' ? 'inline' : 'none';

  var sendButton = document.querySelector('#request')
  if (null === sendButton) {
    sendButton = document.querySelector('#bulk-request');
  }
  sendButton.id = sendButton.id === 'request' ? 'bulk-request' : 'request';
}

function queueRequest() {
  var request = getFormData();
  E_STATE.queue.push([request.from, request.to]);
  var queue = document.querySelector('#queue');

  var li = document.createElement('li');
  li.appendChild(document.createTextNode(request.from + ' -> ' + request.to));
  queue.appendChild(li);
}

function callAPI(url) {
  var request = new XMLHttpRequest();
  request.open(url === 'reset' ? 'DELETE' : 'POST', '/api/' + url, true);
  request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
  request.send(getRequestData(url));
}
