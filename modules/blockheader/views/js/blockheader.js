$(document).ready(function() {
    // Mobile Navigation Toggle
    const mobileNavToggle = $('.bh-mobile-nav-toggle');
    const mobileNavPanel = $('.bh-mobile-nav-panel');
    const customHeaderBlock = $('#custom_header_block'); // Main header container

    if (mobileNavToggle.length && mobileNavPanel.length) {
        mobileNavToggle.on('click', function() {
            mobileNavPanel.toggleClass('open');
            // Optional: Add a body class to prevent scrolling when menu is open
            $('body').toggleClass('bh-mobile-nav-open');
            // Optional: Create and manage an overlay
            if (mobileNavPanel.hasClass('open')) {
                if ($('.bh-mobile-nav-overlay').length === 0) {
                    $('body').append('<div class="bh-mobile-nav-overlay"></div>');
                    $('.bh-mobile-nav-overlay').on('click', function() {
                        mobileNavPanel.removeClass('open');
                        $('body').removeClass('bh-mobile-nav-open');
                        $(this).remove();
                    });
                }
            } else {
                $('.bh-mobile-nav-overlay').remove();
            }
        });
    }

    // Sticky Header Logic
    const header = $('#custom_header_block');
    const desktopHeader = $('.bh-desktop-header');
    const mobileHeaderTopOverlay = $('.bh-mobile-header .bh-mobile-top-overlay'); // The part of mobile header that becomes sticky

    let stickyOffsetDesktop = desktopHeader.length ? desktopHeader.offset().top + desktopHeader.outerHeight() : 0;
    let stickyOffsetMobile = mobileHeaderTopOverlay.length ? mobileHeaderTopOverlay.offset().top + mobileHeaderTopOverlay.outerHeight() : 0;

    // Recalculate offset if images inside header load late and change its height
    $(window).on('load', function() {
        stickyOffsetDesktop = desktopHeader.length ? desktopHeader.offset().top + desktopHeader.outerHeight() : 0;
        stickyOffsetMobile = mobileHeaderTopOverlay.length ? mobileHeaderTopOverlay.offset().top + mobileHeaderTopOverlay.outerHeight() : 0;
    });


    $(window).on('scroll', function() {
        const scrollPos = $(window).scrollTop();

        // Check if desktop header is visible
        if (desktopHeader.is(':visible')) {
            if (scrollPos > stickyOffsetDesktop) {
                header.addClass('is-sticky-desktop');
                // Add padding to body to prevent content jump if header is not position:sticky
                // $('body').css('padding-top', desktopHeader.outerHeight());
            } else {
                header.removeClass('is-sticky-desktop');
                // $('body').css('padding-top', 0);
            }
        } else { // Mobile header is visible or neither (should not happen with current CSS)
            header.removeClass('is-sticky-desktop');
            // $('body').css('padding-top', 0);
        }

        // Check if mobile header top overlay is visible
        if (mobileHeaderTopOverlay.is(':visible')) {
            if (scrollPos > stickyOffsetMobile) {
                header.addClass('is-sticky-mobile');
                // Example: Add padding to body to prevent content jump by sticky mobile header part
                // $('body').addClass('has-sticky-mobile-header').css('padding-top', mobileHeaderTopOverlay.outerHeight());
            } else {
                header.removeClass('is-sticky-mobile');
                // $('body').removeClass('has-sticky-mobile-header').css('padding-top', 0);
            }
        } else {
             header.removeClass('is-sticky-mobile');
             // $('body').removeClass('has-sticky-mobile-header').css('padding-top', 0);
        }
    });

    // Initial check in case the page is loaded scrolled
    $(window).trigger('scroll');

    // Add some CSS for the overlay if it's used
    if (mobileNavPanel.length) { // Only add if mobile nav exists
        $('<style>')
            .prop('type', 'text/css')
            .html(`
                .bh-mobile-nav-overlay {
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background-color: rgba(0,0,0,0.5);
                    z-index: 1004; /* Below nav panel, above content */
                }
                body.bh-mobile-nav-open {
                    overflow: hidden; /* Prevent body scroll when mobile nav is open */
                }
            `)
            .appendTo('head');
    }
});
