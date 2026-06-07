(function () {
    'use strict';

    var SWIPE_CLOSE_PX = 56;
    var SWIPE_MAX_VERTICAL_PX = 48;

    function isNavMenuOpen() {
        return document.documentElement.classList.contains('menu-open');
    }

    function closeNavMenu() {
        if (!isNavMenuOpen()) {
            return;
        }
        document.documentElement.classList.remove('menu-open');
        var nav = document.getElementById('nav');
        if (nav) {
            nav.classList.remove('is-open');
        }
    }

    function isMobileNavLayout() {
        if (window.rootsBreakpoints && typeof window.rootsBreakpoints.isMobileLayout === 'function') {
            return window.rootsBreakpoints.isMobileLayout();
        }
        return window.matchMedia(
            '(orientation: portrait) and (max-width: 800px), ' +
            '(orientation: landscape) and (max-width: 1200px) and (max-height: 600px)'
        ).matches;
    }

    function bindScrollDismiss() {
        var lastScrollY = window.scrollY || document.documentElement.scrollTop;

        var onScroll = function () {
            if (!isNavMenuOpen()) {
                lastScrollY = window.scrollY || document.documentElement.scrollTop;
                return;
            }

            var nextScrollY = window.scrollY || document.documentElement.scrollTop;
            if (nextScrollY !== lastScrollY) {
                lastScrollY = nextScrollY;
                closeNavMenu();
            }
        };

        window.addEventListener('scroll', onScroll, { passive: true });

        if (window.lenis && typeof window.lenis.on === 'function') {
            window.lenis.on('scroll', onScroll);
            return;
        }

        var attemptsLeft = 120;
        function waitForLenis() {
            if (window.lenis && typeof window.lenis.on === 'function') {
                window.lenis.on('scroll', onScroll);
                return;
            }
            if (attemptsLeft <= 0) {
                return;
            }
            attemptsLeft -= 1;
            window.requestAnimationFrame(waitForLenis);
        }
        waitForLenis();
    }

    function bindOutsideDismiss() {
        document.addEventListener(
            'click',
            function (event) {
                if (!isNavMenuOpen() || !isMobileNavLayout()) {
                    return;
                }

                if (event.target.closest('.nav__menuInner')) {
                    return;
                }

                if (event.target.closest('#nav-toggle, .nav__toggle')) {
                    return;
                }

                closeNavMenu();
            },
            true
        );
    }

    function bindBackdropDismiss() {
        var backdrop = document.querySelector('.nav__menuBg');
        if (!backdrop) {
            return;
        }
        backdrop.addEventListener('click', function () {
            closeNavMenu();
        });
    }

    function bindMenuLinkDismiss() {
        var menuInner = document.querySelector('.nav__menuInner');
        if (!menuInner) {
            return;
        }
        menuInner.addEventListener('click', function (event) {
            if (!isNavMenuOpen()) {
                return;
            }
            if (event.target.closest('a')) {
                closeNavMenu();
            }
        });
    }

    function bindSwipeDismiss() {
        var panel = document.querySelector('.nav__menuInner');
        if (!panel) {
            return;
        }

        var startX = 0;
        var startY = 0;
        var tracking = false;

        panel.addEventListener(
            'touchstart',
            function (event) {
                if (!isNavMenuOpen() || event.touches.length !== 1) {
                    tracking = false;
                    return;
                }
                startX = event.touches[0].clientX;
                startY = event.touches[0].clientY;
                tracking = true;
            },
            { passive: true }
        );

        panel.addEventListener(
            'touchmove',
            function (event) {
                if (!tracking || event.touches.length !== 1) {
                    return;
                }
                var dx = event.touches[0].clientX - startX;
                var dy = Math.abs(event.touches[0].clientY - startY);
                if (dy > SWIPE_MAX_VERTICAL_PX && dx < SWIPE_CLOSE_PX) {
                    tracking = false;
                }
            },
            { passive: true }
        );

        panel.addEventListener(
            'touchend',
            function (event) {
                if (!tracking) {
                    return;
                }
                tracking = false;
                var endX = event.changedTouches[0].clientX;
                if (endX - startX >= SWIPE_CLOSE_PX) {
                    closeNavMenu();
                }
            },
            { passive: true }
        );

        panel.addEventListener(
            'touchcancel',
            function () {
                tracking = false;
            },
            { passive: true }
        );
    }

    function boot() {
        bindScrollDismiss();
        bindOutsideDismiss();
        bindBackdropDismiss();
        bindMenuLinkDismiss();
        bindSwipeDismiss();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot);
    } else {
        boot();
    }
})();
