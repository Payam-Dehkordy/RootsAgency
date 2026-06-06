(function () {
    'use strict';

    const HEADER = '#home-header';
    const CARD = '.homeHeader__mediaCard';
    const VIDEO = 'video.media.vid';
    const PRIME_TIMEOUT_MS = 10000;

    function markReady(video) {
        const card = video.closest(CARD);
        video.classList.add('init');
        if (card) {
            card.classList.add('is-ready');
        }
    }

    function primeVideo(video) {
        return new Promise(function (resolve) {
            if (video.classList.contains('init')) {
                markReady(video);
                resolve();
                return;
            }

            let settled = false;
            const finish = function () {
                if (settled) {
                    return;
                }
                settled = true;
                try {
                    video.pause();
                } catch (_) {}
                markReady(video);
                resolve();
            };

            video.preload = 'auto';

            video.addEventListener(
                'playing',
                function () {
                    finish();
                },
                { once: true }
            );

            video.addEventListener(
                'loadeddata',
                function () {
                    if (video.readyState < 2) {
                        return;
                    }
                    video.play().catch(function () {
                        video.addEventListener('canplay', finish, { once: true });
                    });
                },
                { once: true }
            );

            if (video.readyState >= 2) {
                video.play().catch(finish);
            } else {
                video.load();
            }

            window.setTimeout(finish, PRIME_TIMEOUT_MS);
        });
    }

    function boot() {
        const header = document.querySelector(HEADER);
        if (!header) {
            return;
        }

        const videos = header.querySelectorAll(VIDEO);
        if (!videos.length) {
            return;
        }

        videos.forEach(function (video) {
            video.setAttribute('preload', 'auto');
            video.addEventListener('play', function () {
                if (video.classList.contains('init')) {
                    return;
                }
                video.addEventListener(
                    'playing',
                    function () {
                        try {
                            video.pause();
                        } catch (_) {}
                        markReady(video);
                    },
                    { once: true }
                );
            });
        });

        Promise.all(Array.prototype.map.call(videos, primeVideo)).then(function () {
            header.classList.add('roots-hero-videos-ready');
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot);
    } else {
        boot();
    }
})();
