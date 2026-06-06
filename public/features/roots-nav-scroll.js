(function () {
    'use strict';

    var WORK_SECTION_ID = 'slider-cards';

    function navBarHeight() {
        var bar = document.querySelector('.nav__bar');
        return bar ? bar.getBoundingClientRect().height : 0;
    }

    function closeNavMenu() {
        document.documentElement.classList.remove('menu-open');
        var nav = document.getElementById('nav');
        if (nav) {
            nav.classList.remove('is-open');
        }
    }

    function getSliderComponent() {
        if (!window.components || !window.components.getComponent) {
            return null;
        }
        return window.components.getComponent('slider-cards');
    }

    function sliderProgressOffset(section) {
        var slider = getSliderComponent();
        var base = (slider && slider.offset) || Number(section.getAttribute('data-slider-offset')) || 0;
        var isMobile = slider && slider.browser && slider.browser.state.isMobile;
        if (isMobile == null) {
            isMobile = window.matchMedia('(max-width: 600px)').matches;
        }
        return base * (isMobile ? 0.5 : 1);
    }

    function forceSliderRender() {
        var slider = getSliderComponent();
        if (slider && typeof slider.render === 'function') {
            slider.render();
        }
    }

    function scrollToY(y, done) {
        if (window.lenis && typeof window.lenis.scrollTo === 'function') {
            window.lenis.scrollTo(y, {
                onComplete: function () {
                    forceSliderRender();
                    if (done) {
                        done();
                    }
                }
            });
            return;
        }

        window.scrollTo({ top: y, behavior: 'smooth' });
        window.setTimeout(function () {
            forceSliderRender();
            if (done) {
                done();
            }
        }, 500);
    }

    function scrollForCardIndex(docTop, range, cardIndex, cardCount, offset) {
        var progress = (cardIndex + 0.5) / cardCount;
        return docTop + (progress - offset) * range;
    }

    function scrollToWorkSliderFirstCard() {
        var section = document.getElementById(WORK_SECTION_ID);
        var stick = document.getElementById('slider-stick');
        var rail = document.getElementById('slider-rail');
        if (!section || !stick || !rail) {
            return false;
        }

        var scrollY = window.scrollY || document.documentElement.scrollTop;
        var sectionRect = section.getBoundingClientRect();
        var stickRect = stick.getBoundingClientRect();
        var docTop = scrollY + sectionRect.top;
        var range = sectionRect.height - stickRect.height;
        var cardCount = rail.querySelectorAll('.sliderCard').length;

        if (range <= 0 || cardCount <= 0) {
            return false;
        }

        var targetScroll = scrollForCardIndex(
            docTop,
            range,
            0,
            cardCount,
            sliderProgressOffset(section)
        );

        scrollToY(targetScroll);
        return true;
    }

    function scrollToSection(id) {
        if (id === WORK_SECTION_ID && scrollToWorkSliderFirstCard()) {
            return;
        }

        var target = document.getElementById(id);
        if (!target) {
            return;
        }

        var offset = -navBarHeight();

        if (window.lenis && typeof window.lenis.scrollTo === 'function') {
            window.lenis.scrollTo(target, { offset: offset });
            return;
        }

        var top = target.getBoundingClientRect().top + window.scrollY + offset;
        window.scrollTo({ top: top, behavior: 'smooth' });
    }

    function onInPageLinkClick(event) {
        var link = event.target.closest('a[href^="#"]');
        if (!link) {
            return;
        }

        var hash = link.getAttribute('href');
        if (!hash || hash === '#') {
            return;
        }

        var id = hash.slice(1);
        if (!document.getElementById(id)) {
            return;
        }

        event.preventDefault();
        closeNavMenu();
        scrollToSection(id);

        if (window.history && window.history.replaceState) {
            window.history.replaceState(null, '', hash);
        }
    }

    document.addEventListener('click', onInPageLinkClick);
})();
