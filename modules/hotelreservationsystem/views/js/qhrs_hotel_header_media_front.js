/**
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License version 3.0
* that is bundled with this package in the file LICENSE.md
* It is also available through the world-wide-web at this URL:
* https://opensource.org/license/osl-3-0-php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to support@qloapps.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to a newer
* versions in the future. If you wish to customize this module for your needs
* please refer to https://store.webkul.com/customisation-guidelines for more information.
*
* @author Webkul IN
* @copyright Since 2010 Webkul
* @license https://opensource.org/license/osl-3-0-php Open Software License version 3.0
*/


$(document).ready(function () {

    /* Background video autoplay fallback (handles strict browser autoplay policies) */
    var $bgVideo = $('video.header-bg-video');
    if ($bgVideo.length) {
        $bgVideo.each(function () {
            var vid = this;
            vid.muted = true;
            var p = vid.play();
            if (p !== undefined) {
                p.catch(function () {});
            }
        });
    }

    var $imgCarousel = $('.header-img-carousel');
    if (!$imgCarousel.length) { return; }

    var autoPlay  = parseInt($imgCarousel.data('auto-play'), 10) === 1;
    var interval  = parseInt($imgCarousel.data('interval'), 10) || 5000;
    var navType   = parseInt($imgCarousel.data('nav-type'), 10) || 1; // 1=dots 2=arrows 3=both
    var animType  = parseInt($imgCarousel.data('anim-type'), 10) || 1; // 1=slide 2=fade 3=zoom 4=blur
    var animNames = {1: 'Slide', 2: 'Fade', 3: 'Zoom', 4: 'Blur'};
    var animLabel = animNames[animType] || 'Slide';

    var showDots = (navType === 1 || navType === 3);
    $imgCarousel.owlCarousel({
        loop:               true,
        items:              1,
        dots:               showDots,
        dotsContainer:      showDots ? '#wk-header-owl-dots' : false,
        nav:                false,
        autoplay:           autoPlay,
        autoplayTimeout:    interval,
        autoplaySpeed:      800,
        autoplayHoverPause: false,
        animateOut:         'wk' + animLabel + 'Out',
        animateIn:          'wk' + animLabel + 'In',
        responsiveClass:    true,
        rtl:                (typeof language_is_rtl !== 'undefined' ? language_is_rtl : false)
    });

    var owl = $imgCarousel.data('owl.carousel');

    var _origLeave = $.proxy(owl.leave, owl);
    owl.leave = function (name) {
        _origLeave(name);
        var cur = owl._states.current;
        for (var k in cur) {
            if (cur[k] < 0) { cur[k] = 0; }
        }
    };


    var $descriptionEl = $('.js-header-description');
    var $titleEl       = $('.js-header-title');
    var _fadeTimer      = null;
    var _shownIndex     = -1;

    var $slides = $imgCarousel.find('.owl-item:not(.cloned) .header-slide-img');
    var titles = $slides.map(function () { return $(this).data('title') || ''; }).get();
    var descriptions = $slides.map(function () { return $(this).data('description') || ''; }).get();
    var descStyles = $slides.map(function () {
        return {
            color:      $(this).data('desc-color')       || '#ffffff',
            fontSize:   $(this).data('desc-font-size')   || 16,
            fontWeight: $(this).data('desc-font-weight') || '400'
        };
    }).get();

    function showTitle(title) {
        if (!$titleEl.length) { return; }
        $titleEl.text(title);
        $titleEl.toggle(!!title);
    }

    function showDescription(text, style) {
        if (!$descriptionEl.length) { return; }
        if (_fadeTimer) { clearTimeout(_fadeTimer); _fadeTimer = null; }

        style = style || { color: '#ffffff', fontSize: 16, fontWeight: '400' };
        $descriptionEl.css({
            color:       style.color,
            fontSize:    style.fontSize + 'px',
            fontWeight:  style.fontWeight
        });

        $descriptionEl.removeClass('wk-tagline-out wk-tagline-in').text(text);
        if (!text) { $descriptionEl.hide(); return; }

        $descriptionEl.show();
        $descriptionEl[0].offsetHeight; // force reflow so the transition actually runs
        $descriptionEl.addClass('wk-tagline-in');
        _fadeTimer = setTimeout(function () {
            _fadeTimer = null;
            $descriptionEl.removeClass('wk-tagline-in');
        }, 400);
    }

    _shownIndex = owl.relative(owl.current());
    showTitle(titles[_shownIndex] || '');
    showDescription(descriptions[_shownIndex] || '', descStyles[_shownIndex]);

    $imgCarousel.on('changed.owl.carousel', function () {
        var realIndex = owl.relative(owl.current());
        if (realIndex === _shownIndex) { return; } // loop-snap second fire — no-op
        _shownIndex = realIndex;
        showTitle(titles[realIndex] || '');
        showDescription(descriptions[realIndex] || '', descStyles[realIndex]);
    });


    $('.js-header-media-prev').on('click', function () {
        $imgCarousel.trigger('prev.owl.carousel');
    });
    $('.js-header-media-next').on('click', function () {
        $imgCarousel.trigger('next.owl.carousel');
    });


    if (autoPlay) {
        var ap = owl._plugins && owl._plugins.autoplay;

        if (ap) { ap.pause = function () {}; }

        $imgCarousel.on('translated.owl.carousel', function () {
            if (ap && owl.is('rotating') && !ap._paused) {
                ap._setAutoPlayInterval();
            }
        });

        document.addEventListener('visibilitychange', function () {
            if (!document.hidden) {
                $imgCarousel.trigger('stop.owl.autoplay');
                $imgCarousel.trigger('play.owl.autoplay');
            }
        });

        var _watchdogTimer = setInterval(function () {
            if (document.hidden) { return; }
            var apNow = owl._plugins && owl._plugins.autoplay;
            if (!owl.is('rotating') || (apNow && apNow._paused)) {
                $imgCarousel.trigger('stop.owl.autoplay');
                $imgCarousel.trigger('play.owl.autoplay');
            }
        }, interval + 1000);

        $(window).one('beforeunload', function () {
            clearInterval(_watchdogTimer);
        });
    }

});
