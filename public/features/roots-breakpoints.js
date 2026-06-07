(function (global) {
    'use strict';

    var MOBILE_LAYOUT =
        '(orientation: portrait) and (max-width: 800px), ' +
        '(orientation: landscape) and (max-width: 1200px) and (max-height: 600px)';
    var DESKTOP_MIN = '(min-width: 800px)';
    var SLIDER_MOBILE_MQ = '(max-width: 600px)';

    global.rootsBreakpoints = {
        mqMobileLayout: global.matchMedia(MOBILE_LAYOUT),
        mqDesktopMin: global.matchMedia(DESKTOP_MIN),
        mqSliderMobile: global.matchMedia(SLIDER_MOBILE_MQ),
        isMobileLayout: function () {
            return global.rootsBreakpoints.mqMobileLayout.matches;
        },
        isSliderMobile: function () {
            return global.rootsBreakpoints.mqSliderMobile.matches;
        },
    };
})(typeof window !== 'undefined' ? window : this);
