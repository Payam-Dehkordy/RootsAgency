(function () {
    'use strict';

    var BATCH = 1;

    function isSliderMobileLayout(slider) {
        if (slider && slider.browser && slider.browser.state.isMobile != null) {
            return slider.browser.state.isMobile;
        }
        if (window.rootsBreakpoints && typeof window.rootsBreakpoints.isMobileLayout === 'function') {
            return window.rootsBreakpoints.isMobileLayout();
        }
        return window.matchMedia('(max-width: 600px)').matches;
    }

    function initWorkVideos(scope) {
        scope.querySelectorAll('[data-vid]').forEach(function (mediaEl) {
            if (mediaEl.querySelector('video')) {
                return;
            }

            var video = document.createElement('video');
            video.className = 'media vid';
            video.muted = true;
            video.autoplay = false;
            video.loop = true;
            video.setAttribute('playsinline', '');

            var source = document.createElement('source');
            source.setAttribute('src', mediaEl.getAttribute('data-vid'));
            video.appendChild(source);

            var poster = mediaEl.getAttribute('data-poster');
            if (poster) {
                video.setAttribute('poster', poster);
                mediaEl.removeAttribute('data-poster');
            }

            mediaEl.appendChild(video);
            mediaEl.removeAttribute('data-vid');
        });
    }

    function sliderCardCount(rail) {
        return rail.querySelectorAll('.sliderCard').length;
    }

    function setSliderHeight(count) {
        var heightEl = document.querySelector('#slider-cards .workSlider__height');
        if (!heightEl) {
            return;
        }
        Array.prototype.forEach.call(heightEl.classList, function (cls) {
            if (/^workSlider__height--\d+$/.test(cls)) {
                heightEl.classList.remove(cls);
            }
        });
        heightEl.classList.add('workSlider__height--' + count);
    }

    function syncSliderHeight(rail) {
        setSliderHeight(sliderCardCount(rail));
    }

    function syncSliderCards(rail) {
        if (!window.components || !window.components.getComponent) {
            return;
        }
        var slider = window.components.getComponent('slider-cards');
        if (slider) {
            slider.cards = rail.querySelectorAll('.sliderCard');
        }
    }

    function forceSliderRender() {
        if (!window.components || !window.components.getComponent) {
            return;
        }
        var slider = window.components.getComponent('slider-cards');
        if (slider && typeof slider.render === 'function') {
            slider.render();
        }
    }

    function activeCardIndex(section) {
        var cards = section.querySelectorAll('#slider-rail .sliderCard');
        for (var i = 0; i < cards.length; i++) {
            if (cards[i].classList.contains('active')) {
                return i;
            }
        }
        return -1;
    }

    function syncWorkSliderTitle() {
        var section = document.getElementById('slider-cards');
        if (!section) {
            return;
        }

        var activeIndex = activeCardIndex(section);
        var showTitle = section.classList.contains('before')
            || (section.classList.contains('inside') && activeIndex === 0);

        section.classList.toggle('roots-show-work-title', showTitle);
    }

    function hookSliderTitleSync(attemptsLeft) {
        var slider = getSliderComponent();
        if (slider && typeof slider.render === 'function' && !slider._rootsTitlePatched) {
            slider._rootsTitlePatched = true;
            var originalRender = slider.render.bind(slider);
            slider.render = function () {
                originalRender();
                syncWorkSliderTitle();
            };
            syncWorkSliderTitle();
            return;
        }

        if (attemptsLeft <= 0) {
            return;
        }

        window.requestAnimationFrame(function () {
            hookSliderTitleSync(attemptsLeft - 1);
        });
    }

    function getSliderComponent() {
        if (!window.components || !window.components.getComponent) {
            return null;
        }
        return window.components.getComponent('slider-cards');
    }

    function progressOffset(section, slider) {
        var base = (slider && slider.offset) || Number(section.getAttribute('data-slider-offset')) || 0;
        return base * (isSliderMobileLayout(slider) ? 0.5 : 1);
    }

    function sectionMetrics(section, stick) {
        var scrollY = window.scrollY || document.documentElement.scrollTop;
        var sectionRect = section.getBoundingClientRect();
        var stickRect = stick.getBoundingClientRect();
        return {
            scrollY: scrollY,
            docTop: scrollY + sectionRect.top,
            sectionTop: sectionRect.top,
            range: sectionRect.height - stickRect.height
        };
    }

    function isInStickyTrack(section, stick) {
        var metrics = sectionMetrics(section, stick);
        return metrics.range > 0 && -metrics.sectionTop > 0;
    }

    function activeIndexFromProgress(section, stick, offset, cardCount) {
        if (cardCount <= 0) {
            return 0;
        }
        var metrics = sectionMetrics(section, stick);
        if (metrics.range <= 0) {
            return 0;
        }
        var progress = -metrics.sectionTop / metrics.range + offset;
        return Math.max(0, Math.min(cardCount - 1, Math.floor(progress * cardCount)));
    }

    function activeIndexBeforeReveal(rail, slider, section, stick, offset) {
        var cardCount = sliderCardCount(rail);
        var cards = slider && slider.cards ? slider.cards : rail.querySelectorAll('.sliderCard');

        for (var i = 0; i < cards.length; i++) {
            if (cards[i].classList.contains('active')) {
                return i;
            }
        }

        return activeIndexFromProgress(section, stick, offset, cardCount);
    }

    function seeMoreIndex(rail) {
        var cards = rail.querySelectorAll('.sliderCard');
        for (var i = 0; i < cards.length; i++) {
            if (cards[i].classList.contains('workCard--seeMore')) {
                return i;
            }
        }
        return Math.max(0, cards.length - 1);
    }

    function targetIndexAfterReveal(activeBefore, seeMoreBefore) {
        if (activeBefore < seeMoreBefore) {
            return activeBefore;
        }

        /* Land on the first newly revealed work card (not the See More card). */
        return seeMoreBefore;
    }

    function applyScrollAfterReveal(section, stick, before, targetIndex, newCount, offset, instantReveal) {
        requestAnimationFrame(function () {
            requestAnimationFrame(function () {
                var after = sectionMetrics(section, stick);
                preserveScrollState(before, after, targetIndex, newCount, offset, instantReveal);
            });
        });
    }

    function scrollForCardIndex(docTop, range, cardIndex, cardCount, offset) {
        if (range <= 0 || cardCount <= 0) {
            return null;
        }
        var progress = (cardIndex + 0.5) / cardCount;
        return docTop + (progress - offset) * range;
    }

    function scrollToY(y) {
        if (window.lenis && typeof window.lenis.scrollTo === 'function') {
            window.lenis.scrollTo(y, { immediate: true, force: true });
            return;
        }
        window.scrollTo(0, y);
    }

    function preserveScrollState(before, after, targetIndex, newCount, offset, instantReveal) {
        var targetScroll = scrollForCardIndex(
            before.docTop,
            after.range,
            targetIndex,
            newCount,
            offset
        );

        if (targetScroll == null) {
            return;
        }

        var section = document.getElementById('slider-cards');
        if (instantReveal && section) {
            section.classList.add('roots-work-reveal-instant');
        }

        scrollToY(targetScroll);
        forceSliderRender();

        if (instantReveal && section) {
            requestAnimationFrame(function () {
                requestAnimationFrame(function () {
                    section.classList.remove('roots-work-reveal-instant');
                });
            });
        }
    }

    function revealMore(rail, pool, cta) {
        var pending = Array.prototype.slice.call(pool.children);
        if (!pending.length) {
            return;
        }

        var section = document.getElementById('slider-cards');
        var stick = document.getElementById('slider-stick');
        if (!section || !stick) {
            return;
        }

        var slider = getSliderComponent();
        var offset = progressOffset(section, slider);
        var seeMoreBefore = seeMoreIndex(rail);
        var activeBefore = activeIndexBeforeReveal(rail, slider, section, stick, offset);
        var before = sectionMetrics(section, stick);
        var advanceFromSeeMore = activeBefore >= seeMoreBefore;

        var batch = pending.splice(0, BATCH);
        batch.forEach(function (card) {
            rail.insertBefore(card, cta);
        });

        initWorkVideos(rail);

        var ctaRemoved = !pool.children.length;
        if (ctaRemoved) {
            cta.remove();
        }

        syncSliderHeight(rail);
        syncSliderCards(rail);

        if (advanceFromSeeMore) {
            section.classList.add('roots-work-reveal-instant');
            var revealedCards = rail.querySelectorAll('.sliderCard');
            var i;
            for (i = 0; i < revealedCards.length; i++) {
                revealedCards[i].classList.toggle('active', i === seeMoreBefore);
            }
        }

        if (!advanceFromSeeMore && !isInStickyTrack(section, stick)) {
            return;
        }

        var newCount = sliderCardCount(rail);
        var targetIndex = targetIndexAfterReveal(activeBefore, seeMoreBefore);

        applyScrollAfterReveal(section, stick, before, targetIndex, newCount, offset, advanceFromSeeMore);
    }

    function boot() {
        var rail = document.getElementById('slider-rail');
        var pool = document.getElementById('roots-work-pending');
        var cta = rail && rail.querySelector('.workCard--seeMore');

        if (rail) {
            syncSliderHeight(rail);
        }

        hookSliderTitleSync(120);

        if (!rail || !pool || !cta) {
            return;
        }

        var trigger = cta.querySelector('.roots-work-seeMore, button, [type="button"]');
        if (!trigger) {
            return;
        }

        trigger.addEventListener('click', function (event) {
            event.preventDefault();
            event.stopPropagation();
            revealMore(rail, pool, cta);
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot);
    } else {
        boot();
    }

})();
