(function (global) {
    'use strict';

    var MOBILE_LAYOUT =
        '(orientation: portrait) and (max-width: 800px), ' +
        '(orientation: landscape) and (max-width: 1200px) and (max-height: 600px)';
    var DESKTOP_MIN = '(min-width: 800px)';
    var TEMPLATE_SLIDER_MOBILE = '(max-width: 600px)';

    global.rootsBreakpoints = {
        mqMobileLayout: global.matchMedia(MOBILE_LAYOUT),
        mqDesktopMin: global.matchMedia(DESKTOP_MIN),
        mqTemplateSliderMobile: global.matchMedia(TEMPLATE_SLIDER_MOBILE),
        isMobileLayout: function () {
            return global.rootsBreakpoints.mqMobileLayout.matches;
        },
        isTemplateSliderMobile: function () {
            return global.rootsBreakpoints.mqTemplateSliderMobile.matches;
        },
    };
})(typeof window !== 'undefined' ? window : this);
