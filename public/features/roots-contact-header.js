(function () {
    'use strict';

    function replayIn(el) {
        if (!el) {
            return;
        }
        el.classList.remove('out');
        if (el.classList.contains('in')) {
            el.classList.remove('in');
            void el.offsetWidth;
        }
        el.classList.add('in');
    }

    function resetAnim(el) {
        if (!el) {
            return;
        }
        el.classList.remove('in', 'out');
    }

    function init() {
        var section = document.getElementById('contact');
        if (!section) {
            return;
        }

        var heading = section.querySelector('.shortHeader__heading');
        var bottomItems = section.querySelectorAll('.shortHeader__bottom .anima');
        if (!heading) {
            return;
        }

        var active = false;

        var observer = new IntersectionObserver(
            function (entries) {
                entries.forEach(function (entry) {
                    if (entry.target !== section) {
                        return;
                    }

                    if (entry.isIntersecting && entry.intersectionRatio >= 0.25) {
                        if (active) {
                            return;
                        }
                        active = true;
                        replayIn(heading);
                        window.setTimeout(function () {
                            bottomItems.forEach(replayIn);
                        }, 160);
                        return;
                    }

                    if (!entry.isIntersecting || entry.intersectionRatio < 0.1) {
                        if (!active) {
                            return;
                        }
                        active = false;
                        resetAnim(heading);
                        bottomItems.forEach(resetAnim);
                    }
                });
            },
            { threshold: [0, 0.1, 0.25, 0.4] }
        );

        observer.observe(section);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
