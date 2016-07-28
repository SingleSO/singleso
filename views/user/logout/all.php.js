(function() {
	'use strict';

	var undef;
	var noop = function() {};
	var jsonpc = 0;

	function eventAdd(el, type, cb) {
		if (el.addEventListener) {
			el.addEventListener(type, cb, false);
		}
		else {
			el.attachEvent('on' + type, cb);
		}
	}

	function eventRemove(el, type, cb) {
		if (el.addEventListener) {
			el.removeEventListener(type, cb, false);
		}
		else {
			el.detachEvent('on' + type, cb);
		}
	}

	function jsonp(url, cb) {
		var head = document.head || document.getElementsByTagName('head')[0];
		var cbname = '___jsonp_logout_all_' + (++jsonpc);
		var success = false;
		var script = document.createElement('script');
		var done = function(data) {
			head.removeChild(script);
			script.onload = script.onerror = noop;
			window[cbname] = undef;
			delete window[cbname];
			cb(success, data);
		};
		window[cbname] = function(data) {
			success = true;
			done(data);
		};
		script.async = true;
		script.onload = function() {
			if (!success) {
				setTimeout(done, 100);
			}
		};
		script.onerror = function() {
			done();
		};
		script.src = url +
			(url.indexOf('?') < 0 ? '?' : '&') +
			'callback=' + cbname;
		head.appendChild(script);
	}

	function logoutRedirectToggle(on) {
		if (on) {
			logoutRedirect.setAttribute('href', logoutRedirectHref);
			logoutRedirect.removeAttribute('disabled');
		}
		else {
			logoutRedirect.removeAttribute('href');
			logoutRedirect.setAttribute('disabled', 'disabled');
		}
	}

	function listAllStatuses(el) {
		var list = [];
		var children = el.children;
		for (var i = -1, il = children.length; ++i < il;) {
			var child = children[i];
			if (child.getAttribute('data-logout')) {
				var subChildren = child.children;
				for (var j = -1, jl = subChildren.length; ++j < jl;) {
					var subChild = subChildren[j];
					if (subChild.getAttribute('data-status')) {
						list.push({
							status: subChild,
							logout: child
						});
						break;
					}
				}
			}
		}
		return list;
	}

	function processStatusList(list, cb) {
		var failed = null;
		var remaining = list.length;
		var itter = function(entry) {
			var logout = entry.logout;
			var status = entry.status;
			var logoutURL = logout.getAttribute('data-logout');
			logout.setAttribute('data-status', 'loading');
			jsonp(logoutURL, function(success, data) {
				var pass = success && data && data.success;
				var status = (pass ? 'success' : 'failure');
				logout.setAttribute('data-status', status);
				if (!pass) {
					failed = failed || [];
					failed.push(entry);
				}
				if (!--remaining) {
					cb(failed);
				}
			});
		}
		for (var i = -1, il = remaining; ++i < il;) {
			itter(list[i]);
		}
	}

	function processListHandler(failed) {
		logoutRedirectToggle(true);
		if (failed) {
			failedLogoutError.style.display = '';
			var handler = function() {
				eventRemove(logoutRetry, 'click', handler);
				failedLogoutError.style.display = 'none';
				logoutRedirectToggle(false);
				processStatusList(failed, processListHandler);
			};
			eventAdd(logoutRetry, 'click', handler);
		}
	}

	// Get the continue button, disable it, then show.
	var logoutRedirect = document.getElementById('logout-redirect');
	var logoutRedirectHref = logoutRedirect.getAttribute('href');
	logoutRedirectToggle(false);
	logoutRedirect.style.display = '';

	// Get the failed logout error element.
	var failedLogoutError = document.getElementById('failed-logout-error');

	// Get the failed logout retry button.
	var logoutRetry = document.getElementById('logout-retry');

	// Get the logout list.
	var statusList = listAllStatuses(document.getElementById('logout-list'));

	// Start processing.
	processStatusList(statusList, processListHandler);
})();
