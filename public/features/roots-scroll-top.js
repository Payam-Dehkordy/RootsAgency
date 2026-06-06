(function () {
    'use strict';

    function hasInitialHash() {
        var hash = window.location.hash;
        return Boolean(hash && hash !== '#');
    }

    function scrollTop() {
        if (hasInitialHash()) {
            return;
        }

        if (window.lenis && typeof window.lenis.scrollTo === 'function') {
            window.lenis.scrollTo(0, { immediate: true, force: true });
            return;
        }
        window.scrollTo(0, 0);
    }

    if ('scrollRestoration' in history) {
        history.scrollRestoration = 'manual';
    }

    scrollTop();
    window.addEventListener('load', scrollTop);
    window.addEventListener('pageshow', function (event) {
        if (event.persisted && !hasInitialHash()) {
            scrollTop();
        }
    });

    if (!hasInitialHash()) {
        requestAnimationFrame(function () {
            requestAnimationFrame(scrollTop);
        });
    }
})();
