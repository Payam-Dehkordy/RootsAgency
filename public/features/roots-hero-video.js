(function () {
    'use strict';

    const HEADER = '#home-header';
    const CARD = '.homeHeader__mediaCard';
    const VIDEO = 'video.media.vid';
    const PRIME_TIMEOUT_MS = 12000;
    const HAVE_CURRENT_DATA = 2;

    function markReady(video) {
        const card = video.closest(CARD);
        video.classList.add('init');
        if (card) {
            card.classList.add('is-ready');
        }
    }

    function hasDecodedFrame(video) {
        return video.readyState >= HAVE_CURRENT_DATA && video.videoWidth > 0;
    }

    /**
     * Prime the first visible frame without play() — avoids AbortError races with
     * Avoid duplicate play/pause with bundle homeHeader handlers and inline onplaying handlers.
     */
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
                markReady(video);
                resolve();
            };

            const trySeekPrime = function () {
                if (hasDecodedFrame(video)) {
                    finish();
                    return;
                }

                const onSeeked = function () {
                    finish();
                };

                video.addEventListener('seeked', onSeeked, { once: true });

                try {
                    if (video.currentTime < 0.0001) {
                        video.currentTime = 0.001;
                    } else {
                        video.currentTime = 0;
                    }
                } catch (_) {
                    video.removeEventListener('seeked', onSeeked);
                    finish();
                }
            };

            const onData = function () {
                if (hasDecodedFrame(video)) {
                    finish();
                    return;
                }
                trySeekPrime();
            };

            video.preload = 'auto';
            video.muted = true;

            video.addEventListener('error', finish, { once: true });
            video.addEventListener('loadeddata', onData, { once: true });
            window.setTimeout(finish, PRIME_TIMEOUT_MS);

            if (hasDecodedFrame(video)) {
                finish();
            } else if (video.readyState >= HAVE_CURRENT_DATA) {
                onData();
            } else if (video.readyState === 0) {
                video.load();
            }
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
