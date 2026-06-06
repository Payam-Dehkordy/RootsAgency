(function () {
    'use strict';

    function round(value) {
        return Math.round(value * 10) / 10;
    }

    function logFooterLayout(reason) {
        var pageEnd = document.querySelector('.roots-page-end');
        var studio = pageEnd && pageEnd.querySelector('section.officeList');
        var studioList = studio && studio.querySelector('.officeList__list');
        var studioOffice = studio && studio.querySelector('.officeList__office');
        var footer = document.querySelector('footer.roots-footer');
        var footerBody = footer && footer.querySelector('.roots-footer-body');
        var phone = document.querySelector('.roots-iphone-frame__bezel');
        var navBar = document.querySelector('.nav__bar');

        if (!pageEnd || !studio || !phone) {
            console.warn('[Roots footer layout] Missing nodes', {
                reason: reason,
                pageEnd: Boolean(pageEnd),
                studio: Boolean(studio),
                phone: Boolean(phone)
            });
            return;
        }

        var viewportH = window.innerHeight;
        var scrollY = window.scrollY || document.documentElement.scrollTop;
        var navH = navBar ? navBar.getBoundingClientRect().height : 0;
        var pageEndRect = pageEnd.getBoundingClientRect();
        var studioRect = studio.getBoundingClientRect();
        var studioListRect = studioList ? studioList.getBoundingClientRect() : null;
        var studioOfficeRect = studioOffice ? studioOffice.getBoundingClientRect() : null;
        var footerRect = footer ? footer.getBoundingClientRect() : null;
        var footerBodyRect = footerBody ? footerBody.getBoundingClientRect() : null;
        var phoneRect = phone.getBoundingClientRect();

        var studioBottomViewport = studioRect.bottom;
        var phoneTopViewport = phoneRect.top;
        var gapStudioBottomToPhoneTop = phoneTopViewport - studioBottomViewport;
        var gapStudioBottomToFooterTop = footerRect
            ? footerRect.top - studioBottomViewport
            : null;
        var footerPaddingTop = footer
            ? parseFloat(getComputedStyle(footer).paddingTop) || 0
            : 0;
        var flexSpacerEstimate = gapStudioBottomToFooterTop != null
            ? Math.max(0, gapStudioBottomToFooterTop - footerPaddingTop)
            : null;

        console.groupCollapsed('[Roots footer layout] ' + (reason || 'measure'));
        console.log('Viewport', {
            innerHeight: viewportH,
            scrollY: round(scrollY),
            navBarHeight: round(navH)
        });
        console.log('Studio section (.officeList)', {
            topViewport: round(studioRect.top),
            bottomViewport: round(studioRect.bottom),
            height: round(studioRect.height),
            topDocument: round(scrollY + studioRect.top),
            bottomDocument: round(scrollY + studioRect.bottom)
        });
        if (studioListRect) {
            console.log('Studio list line (.officeList__list)', {
                bottomViewport: round(studioListRect.bottom),
                gapListBottomToPhoneTop: round(phoneTopViewport - studioListRect.bottom)
            });
        }
        if (studioOfficeRect) {
            console.log('Studio office row (.officeList__office)', {
                bottomViewport: round(studioOfficeRect.bottom),
                gapOfficeBottomToPhoneTop: round(phoneTopViewport - studioOfficeRect.bottom)
            });
        }
        console.log('Phone (.roots-iphone-frame__bezel)', {
            topViewport: round(phoneTopViewport),
            bottomViewport: round(phoneRect.bottom),
            height: round(phoneRect.height),
            width: round(phoneRect.width)
        });
        console.log('Space above phone', {
            gapStudioSectionBottomToPhoneTop: round(gapStudioBottomToPhoneTop),
            gapStudioBottomToFooterTop: gapStudioBottomToFooterTop != null
                ? round(gapStudioBottomToFooterTop)
                : null,
            footerPaddingTop: round(footerPaddingTop),
            flexSpacerAboveFooterBody: round(flexSpacerEstimate),
            gapFooterBodyTopToPhoneTop: footerBodyRect
                ? round(phoneTopViewport - footerBodyRect.top)
                : null
        });
        if (footerRect) {
            console.log('Footer', {
                topViewport: round(footerRect.top),
                bottomViewport: round(footerRect.bottom),
                height: round(footerRect.height)
            });
        }
        console.log('Page end wrapper (.roots-page-end)', {
            topViewport: round(pageEndRect.top),
            bottomViewport: round(pageEndRect.bottom),
            height: round(pageEndRect.height),
            targetHeight: round(parseFloat(getComputedStyle(document.documentElement)
                .getPropertyValue('--roots-page-end-height')) || 0),
            fitsViewport: Math.abs(pageEndRect.height - (viewportH - navH)) < 2
        });
        console.groupEnd();
    }

    function boot() {
        logFooterLayout('load');

        window.addEventListener('resize', function () {
            logFooterLayout('resize');
        });

        if (window.lenis && typeof window.lenis.on === 'function') {
            var scrollTimer = 0;
            window.lenis.on('scroll', function () {
                window.clearTimeout(scrollTimer);
                scrollTimer = window.setTimeout(function () {
                    logFooterLayout('scroll');
                }, 400);
            });
        }
    }

    if (document.readyState === 'complete') {
        boot();
    } else {
        window.addEventListener('load', boot);
    }
})();
