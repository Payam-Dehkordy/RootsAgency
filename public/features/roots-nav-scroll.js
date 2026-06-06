(function () {
    'use strict';

    var WORK_SECTION_ID = 'slider-cards';
    var TEAM_SECTION_ID = 'team';
    var SECTION_IDS = ['company', 'slider-cards', 'services', 'team', 'contact'];
    var scrollSpyPaused = false;
    var scrollSpyTimer = 0;

    function navBarHeight() {
        var bar = document.querySelector('.nav__bar');
        return bar ? bar.getBoundingClientRect().height : 0;
    }

    function sectionScrollOffset(id) {
        var offset = -navBarHeight();

        if (id === 'company') {
            offset += Math.min(window.innerHeight * 0.1, 120);
        }

        return offset;
    }

    function getScrollTarget(id) {
        var section = document.getElementById(id);
        if (!section) {
            return null;
        }

        if (id === 'services') {
            var headings = section.querySelectorAll('.companyData__heading');
            var i;

            for (i = 0; i < headings.length; i++) {
                if (headings[i].offsetParent !== null) {
                    return headings[i];
                }
            }

            if (headings.length) {
                return headings[0];
            }
        }

        return section;
    }

    function closeNavMenu() {
        document.documentElement.classList.remove('menu-open');
        var nav = document.getElementById('nav');
        if (nav) {
            nav.classList.remove('is-open');
        }
    }

    function getNavLinks() {
        return document.querySelectorAll('.nav__link, .nav__menuLink');
    }

    function setActiveSection(id) {
        if (!id) {
            return;
        }

        getNavLinks().forEach(function (link) {
            var hash = link.getAttribute('href');
            if (hash === '#' + id) {
                link.classList.add('active');
                link.classList.remove('wasActive');
                return;
            }

            if (link.classList.contains('active')) {
                link.classList.add('wasActive');
            }
            link.classList.remove('active');
        });
    }

    function getVisibleSectionId() {
        var offset = navBarHeight() + Math.min(window.innerHeight * 0.25, 120);
        var activeId = SECTION_IDS[0];

        SECTION_IDS.forEach(function (id) {
            var section = document.getElementById(id);
            if (!section) {
                return;
            }

            if (section.getBoundingClientRect().top <= offset) {
                activeId = id;
            }
        });

        return activeId;
    }

    function updateScrollSpy() {
        if (scrollSpyPaused) {
            return;
        }

        setActiveSection(getVisibleSectionId());
    }

    function pauseScrollSpy(ms) {
        scrollSpyPaused = true;
        window.clearTimeout(scrollSpyTimer);
        scrollSpyTimer = window.setTimeout(function () {
            scrollSpyPaused = false;
            updateScrollSpy();
        }, ms);
    }

    function getSliderComponent() {
        if (!window.components || !window.components.getComponent) {
            return null;
        }
        return window.components.getComponent('slider-cards');
    }

    function getLogosComponent() {
        if (!window.components || !window.components.getComponent) {
            return null;
        }
        return window.components.getComponent('logos');
    }

    function getBrowserRem() {
        if (window.components && window.components.browser && window.components.browser.state) {
            return window.components.browser.state.rem;
        }

        var width = window.innerWidth;
        if (width <= 600) {
            return width / 100 * (1000 / 375);
        }
        if (width <= 1000) {
            return width / 100 * (1000 / 834);
        }
        return width / 100 * (1000 / 1680);
    }

    function isMobileTeamLayout() {
        return window.matchMedia('(max-width: 800px)').matches;
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

    function forceLogosRender() {
        var logos = getLogosComponent();
        if (logos && typeof logos.render === 'function') {
            logos.render();
        }
    }

    function forceSectionRender() {
        forceSliderRender();
        forceLogosRender();
    }

    function setTeamActiveCard(index) {
        var logosWrap = document.getElementById('logos');
        if (!logosWrap) {
            return;
        }

        var cards = logosWrap.querySelectorAll('.clientLogos__logo.logo');
        var i;

        for (i = 0; i < cards.length; i++) {
            cards[i].classList.toggle('active', i === index);
        }
    }

    function scrollToY(y, done) {
        if (window.lenis && typeof window.lenis.scrollTo === 'function') {
            window.lenis.scrollTo(y, {
                onComplete: function () {
                    forceSectionRender();
                    if (done) {
                        done();
                    }
                }
            });
            return;
        }

        window.scrollTo({ top: y, behavior: 'smooth' });
        window.setTimeout(function () {
            forceSectionRender();
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

    function scrollToTeamFirstCard() {
        if (isMobileTeamLayout()) {
            return false;
        }

        var logosWrap = document.getElementById('logos');
        if (!logosWrap || logosWrap.offsetParent === null) {
            return false;
        }

        var firstCard = logosWrap.querySelector('.clientLogos__logo.logo');
        if (!firstCard) {
            return false;
        }

        var activationY = 50 * getBrowserRem();
        var scrollY = window.scrollY || document.documentElement.scrollTop;
        var cardRect = firstCard.getBoundingClientRect();
        var cardHeight = cardRect.height || firstCard.offsetHeight;

        if (cardHeight <= 0) {
            return false;
        }

        var cardDocTop = scrollY + cardRect.top;
        var targetScroll = cardDocTop + (cardHeight * 0.45) - activationY;
        var section = document.getElementById(TEAM_SECTION_ID);

        if (section) {
            var minScroll = scrollY + section.getBoundingClientRect().top - navBarHeight();
            if (targetScroll < minScroll) {
                targetScroll = minScroll;
            }
        }

        scrollToY(targetScroll, function () {
            setTeamActiveCard(0);
            forceLogosRender();
        });
        return true;
    }

    function scrollToSection(id, done) {
        pauseScrollSpy(900);
        setActiveSection(id);

        if (id === WORK_SECTION_ID && scrollToWorkSliderFirstCard()) {
            if (done) {
                window.setTimeout(done, 700);
            }
            return;
        }

        if (id === TEAM_SECTION_ID && scrollToTeamFirstCard()) {
            if (done) {
                window.setTimeout(done, 700);
            }
            return;
        }

        var target = getScrollTarget(id);
        if (!target) {
            if (done) {
                done();
            }
            return;
        }

        var offset = sectionScrollOffset(id);

        if (window.lenis && typeof window.lenis.scrollTo === 'function') {
            window.lenis.scrollTo(target, {
                offset: offset,
                onComplete: function () {
                    forceSectionRender();
                    if (done) {
                        done();
                    }
                }
            });
            return;
        }

        var top = target.getBoundingClientRect().top + window.scrollY + offset;
        window.scrollTo({ top: top, behavior: 'smooth' });
        window.setTimeout(function () {
            forceSectionRender();
            if (done) {
                done();
            }
        }, 500);
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

    function bindScrollSpy() {
        updateScrollSpy();

        if (window.lenis && typeof window.lenis.on === 'function') {
            window.lenis.on('scroll', updateScrollSpy);
        }

        window.addEventListener('scroll', updateScrollSpy, { passive: true });
        window.addEventListener('resize', updateScrollSpy);
    }

    function scrollToInitialHash() {
        var hash = window.location.hash;
        if (!hash || hash === '#') {
            updateScrollSpy();
            return;
        }

        var id = hash.slice(1);
        if (!document.getElementById(id)) {
            updateScrollSpy();
            return;
        }

        window.setTimeout(function () {
            scrollToSection(id, updateScrollSpy);
        }, 120);
    }

    function waitForLenis(callback, attemptsLeft) {
        if (window.lenis || attemptsLeft <= 0) {
            callback();
            return;
        }

        window.requestAnimationFrame(function () {
            waitForLenis(callback, attemptsLeft - 1);
        });
    }

    document.addEventListener('click', onInPageLinkClick);

    if (document.readyState === 'complete') {
        waitForLenis(function () {
            bindScrollSpy();
            scrollToInitialHash();
        }, 120);
    } else {
        window.addEventListener('load', function () {
            waitForLenis(function () {
                bindScrollSpy();
                scrollToInitialHash();
            }, 120);
        });
    }
})();
