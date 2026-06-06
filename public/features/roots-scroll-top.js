(function () {
    'use strict';

    function scrollTop() {
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
    window.addEventListener('pageshow', scrollTop);

    requestAnimationFrame(function () {
        requestAnimationFrame(scrollTop);
    });
})();
