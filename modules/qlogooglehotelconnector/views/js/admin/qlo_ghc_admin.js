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
(function ($) {
    'use strict';

    // Exposed globally — called via onclick attribute on room-type checkboxes
    window.checkAllRoomTypes = function (checked) {
        $('.qlo-ghc-room-type-cb').prop('checked', checked);
        if (checked) {
            $('#room-type-error').remove();
        }
    };

    $(document).ready(function () {

        // ── Hotel edit page — move "not connected" warning above the panel ──
        var $topWarning = $('#ghc-hotel-top-warning');
        if ($topWarning.length && $('#ajax_confirmation').length) {
            $topWarning.insertBefore('#ajax_confirmation');
        }

        // ── Log detail view — clipboard copy ────────────────────────────

        $(document).on('click', '.qlo-ghc-copy-btn', function () {
            var $btn     = $(this);
            var targetId = $btn.attr('data-target');
            var $pre     = $('#' + targetId);
            if (!$pre.length) {
                return;
            }
            var text = $pre[0].innerText || $pre[0].textContent;

            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(text).then(function () {
                    showCopied($btn);
                });
            } else {
                var $ta = $('<textarea>').val(text).css({ position: 'fixed', opacity: 0 }).appendTo('body');
                $ta[0].select();
                document.execCommand('copy');
                $ta.remove();
                showCopied($btn);
            }
        });

        // ── Hotel edit page — Google Hotel tab ──────────────────────────

        $('input[name="hotel_status"]').on('change', function () {
            if ('0' === $(this).val()) {
                var msg = (typeof ghcDisableHotelMsg !== 'undefined' && ghcDisableHotelMsg)
                    ? ghcDisableHotelMsg
                    : 'Are you sure you want to disable this hotel from Google Hotels?';
                if (!confirm(msg)) {
                    $('input#hotel_status_on').prop('checked', true);
                    return;
                }
                $('.qlo-ghc-room-type-cb, #room_types_all').prop('checked', false);
                $('#room-types-section').addClass('qlo-ghc-hidden');
            } else {
                // Auto-select all rooms when GHC is first enabled
                $('.qlo-ghc-room-type-cb, #room_types_all').prop('checked', true);
                $('#room-types-section').removeClass('qlo-ghc-hidden');
            }
        });

        // Sync "select all" checkbox state with individual room-type rows
        $(document).on('change', '.qlo-ghc-room-type-cb', function () {
            var total   = $('.qlo-ghc-room-type-cb').length;
            var checked = $('.qlo-ghc-room-type-cb:checked').length;
            $('#room_types_all').prop('checked', total === checked);
            if (checked > 0) {
                $('#room-type-error').remove();
            }
        });

        // ── Hotel Information tab — normalize lat/long precision before submit ──
        // Google's map picker can return coordinates with 15+ decimal digits (float
        // arithmetic artifacts, e.g. 28.629770299999997 — real precision to only ~7
        // decimals, the rest is noise). PrestaShop's own Validate::isFloat() rejects
        // those — it checks strval((float)$v) == strval($v), which fails once a value
        // has more significant digits than PHP's default float-to-string precision
        // reproduces — so the hotel save is blocked with "Property ...->latitude is not
        // valid". Rounding here to 7 decimals (far more precision than a hotel location
        // needs, and within the DB's decimal(*,8) columns) and letting parseFloat() strip
        // trailing zeros produces a clean string that always round-trips. Applies to
        // whichever field is present — the map's hidden inputs when maps are enabled, or
        // the manual fallback inputs when not.
        var $latLngForm = $('#loclatitude, #loclongitude').closest('form');
        if ($latLngForm.length) {
            $latLngForm.on('submit', function () {
                ['#loclatitude', '#loclongitude'].forEach(function (selector) {
                    var $field = $(selector);
                    var val = $field.val();
                    if (val !== '' && !isNaN(val)) {
                        $field.val(parseFloat(parseFloat(val).toFixed(7)).toString());
                    }
                });
            });
        }

        // ── Hotel form — client-side GHC validation ──────────────────────
        // hookActionBeforeAddHotelValidation is non-standard — may not exist on all
        // QloApps builds. JS validation is the portable, module-only alternative.
        var $statusInput = $('input[name="hotel_status"]');
        if ($statusInput.length) {
            $statusInput.closest('form').on('submit', function (e) {
                var isEnabled = $('input[name="hotel_status"][value="1"]').prop('checked');
                if (!isEnabled) {
                    return true;
                }
                if (0 === $('.qlo-ghc-room-type-cb:checked').length) {
                    e.preventDefault();
                    showRoomTypeError();
                    return false;
                }
                $('#room-type-error').remove();
                return true;
            });
        }

    });

    // Mirrors alerts.tpl's own alert-danger markup (dismissible, inserted at the top
    // of the page above #ajax_confirmation) so this reads like every other QloApps
    // admin error instead of an ad-hoc box next to the field.
    function showRoomTypeError() {
        $('#room-type-error').remove();
        // This check is client-side only, so blocking submit here never triggers a new
        // page load — a confirmation banner left over from an earlier successful save
        // (alerts.tpl's .bootstrap > .alert-success) would otherwise keep showing
        // alongside this new error, which a real server-side error page never would.
        // #ajax_confirmation itself is excluded — it's the anchor #ajax_confirmation
        // that the new error box is inserted before, a few lines down, and on this
        // page it can end up matching this same selector, which silently breaks that
        // insertion (jQuery's .before() on an empty selection is a no-op).
        $('.bootstrap .alert-success').not('#ajax_confirmation').remove();
        var msg = (typeof noRoomTypeError !== 'undefined' && noRoomTypeError)
            ? noRoomTypeError
            : 'Please select at least one room type before enabling Google Hotel.';

        var $alert = $('<div>', { class: 'alert alert-danger' }).text(msg);
        $('<button>', { type: 'button', class: 'close', 'data-dismiss': 'alert' }).html('&times;').prependTo($alert);
        var $err = $('<div>', { id: 'room-type-error', class: 'bootstrap' }).append($alert);

        $('#ajax_confirmation').before($err);

        // Switch to Google Hotel tab so the admin can see and fix the checkboxes
        var $tabLink = $('a[href="#hotel-tab"]');
        if ($tabLink.length) {
            $tabLink.tab('show');
        }
        $('html, body').animate({ scrollTop: 0 }, 300);
    }

    function showCopied($btn) {
        var orig = $btn.html();
        $btn.html('<i class="icon-check"></i> Copied!').addClass('btn-success').removeClass('btn-default');
        setTimeout(function () {
            $btn.html(orig).removeClass('btn-success').addClass('btn-default');
        }, 2000);
    }

}(jQuery));
