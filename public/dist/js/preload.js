(function (window, document) {
    'use strict';

    var PAGE_MESSAGE = 'Cargando...';
    var PROCESS_MESSAGE = 'Cargando operación...';

    var counter = 0;
    var pageLoading = true;
    var root = null;
    var textEl = null;

    function getRoot() {
        if (!root) {
            root = document.getElementById('preload');
            textEl = root ? root.querySelector('.preload__text') : null;
        }
        return root;
    }

    function setMessage(message) {
        if (textEl && message) {
            textEl.textContent = message;
        }
    }

    function applyVisibleState(visible) {
        var el = getRoot();
        if (!el) {
            return;
        }

        el.classList.toggle('is-active', visible);
        el.setAttribute('aria-hidden', visible ? 'false' : 'true');
        document.body.classList.toggle('preload-open', visible);
    }

    function restartLineAnimation() {
        var animated = getRoot() ? getRoot().querySelectorAll('.preload__stroke, .preload__fill') : [];
        animated.forEach(function (element) {
            element.style.animation = 'none';
            element.offsetHeight;
            element.style.animation = '';
        });
    }

    function show(message) {
        counter += 1;

        if (message) {
            setMessage(message);
        } else if (!pageLoading) {
            setMessage(PROCESS_MESSAGE);
        }

        applyVisibleState(true);
        restartLineAnimation();
    }

    function hide(force) {
        if (force) {
            counter = 0;
        } else {
            counter = Math.max(0, counter - 1);
        }

        if (counter === 0) {
            applyVisibleState(false);
        }
    }

    function finishPageLoad() {
        pageLoading = false;
        setMessage(PROCESS_MESSAGE);

        if (counter === 0) {
            applyVisibleState(false);
        }
    }

    function setupSvgLines() {
        var lines = getRoot() ? getRoot().querySelectorAll('.preload__line') : [];

        lines.forEach(function (line) {
            if (!line.classList.contains('preload__stroke') || typeof line.getTotalLength !== 'function') {
                return;
            }

            var length = Math.ceil(line.getTotalLength());
            line.style.setProperty('--line-length', String(length));
            line.style.strokeDasharray = String(length);
            line.style.strokeDashoffset = String(length);
        });
    }

    function init() {
        var el = getRoot();
        if (!el) {
            return;
        }

        setupSvgLines();
        setMessage(PAGE_MESSAGE);
        applyVisibleState(true);

        if (document.readyState === 'complete') {
            finishPageLoad();
            return;
        }

        window.addEventListener('load', finishPageLoad, { once: true });
        window.addEventListener('pageshow', function (event) {
            if (event.persisted) {
                finishPageLoad();
            }
        });
    }

    window.HorizonPreload = {
        show: show,
        hide: hide,
        setMessage: setMessage,
        init: init,
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})(window, document);