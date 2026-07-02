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
    var tierIdx  = $('#tt-tiers-body tr').length;
    var childIdx = $('#tt-child-body tr').length;

    $('#htl_valid_from').datepicker({
        onSelect: function () {
            $('#htl_valid_to').datepicker('option', 'minDate', $(this).datepicker('getDate'));
            validateDates();
        }
    });

    $('#htl_valid_to').datepicker({ onSelect: validateDates });

    function validateDates() {
        var from = $('#htl_valid_from').val();
        var to   = $('#htl_valid_to').val();
        var $err = $('#tt-date-error');
        if (from && to) {
            try {
                var fromTs = $.datepicker.parseDate('yy-mm-dd', from).getTime();
                var toTs   = $.datepicker.parseDate('yy-mm-dd', to).getTime();
                if (toTs < fromTs) {
                    $err.text('"Valid to" must be on or after "Valid from".').show();
                    return false;
                }
            } catch (e) {}
        }
        $err.hide();
        return true;
    }

    function isTourismOn()   { return $('input[name="is_tourism_tax"]:checked').val()    == '1'; }
    function isChildRateOn() { return $('input[name="htl_has_child_rate"]:checked').val() == '1'; }
    function isTieredOn()    { return $('input[name="htl_is_tiered"]:checked').val()     == '1'; }

    function setVisible($el, visible, animate) {
        if (animate) {
            visible ? $el.slideDown(250) : $el.slideUp(250);
        } else {
            visible ? $el.show() : $el.hide();
        }
    }

    function ensureOneRow(bodyId, tplId, idxRef) {
        if ($('#' + bodyId + ' tr').length === 0) {
            var tpl = $('#' + tplId).html().replace(/__IDX__/g, idxRef.val);
            var $row = $(tpl);
            $row.find('.tt-row-type-sign').text(currentTypeSign());
            $('#' + bodyId).append($row);
            idxRef.val++;
        }
    }

    var tierIdxRef  = { val: tierIdx };
    var childIdxRef = { val: childIdx };

    function updateAllVisibility(animate) {
        var t = isTourismOn();
        setVisible($('.tt-field').not('.tt-child-field, .tt-tiered-field'), t, animate);
        setVisible($('.tt-non-tourism'), !t, animate);

        var showChild = t && isChildRateOn();
        setVisible($('.tt-child-field'), showChild, animate);
        if (showChild) {
            ensureOneRow('tt-child-body', 'tt-child-row-tpl', childIdxRef);
        }

        var showTiered = t && isTieredOn();
        setVisible($('.tt-tiered-field'), showTiered, animate);
        if (showTiered) {
            ensureOneRow('tt-tiers-body', 'tt-tier-row-tpl', tierIdxRef);
        }
    }

    updateAllVisibility(false);
    updateTypeSign();

    $('input[name="is_tourism_tax"], input[name="htl_has_child_rate"], input[name="htl_is_tiered"]').on('change', function () {
        setTimeout(function () { updateAllVisibility(true); }, 0);
    });

    $(document).on('click', '.prestashop-switch .slide-button', function () {
        setTimeout(function () { updateAllVisibility(true); }, 0);
    });

    function currentTypeSign() {
        var $sign = $('#tt-type-sign');
        return $('select[name="htl_tax_type"]').val() == 1 ? '%' : $sign.data('currency');
    }

    function updateTypeSign() {
        var sign = currentTypeSign();
        $('#tt-type-sign').text(sign);
        $('.tt-row-type-sign').text(sign);
    }

    $('select[name="htl_tax_type"]').on('change', updateTypeSign);

    $(document).on('click', '#tt-add-tier', function (e) {
        e.preventDefault();
        var tpl = $('#tt-tier-row-tpl').html().replace(/__IDX__/g, tierIdxRef.val);
        var $row = $(tpl);
        $row.find('.tt-row-type-sign').text(currentTypeSign());
        $('#tt-tiers-body').append($row);
        tierIdxRef.val++;
    });

    $(document).on('click', '#tt-add-child', function (e) {
        e.preventDefault();
        var tpl = $('#tt-child-row-tpl').html().replace(/__IDX__/g, childIdxRef.val);
        var $row = $(tpl);
        $row.find('.tt-row-type-sign').text(currentTypeSign());
        $('#tt-child-body').append($row);
        childIdxRef.val++;
    });

    $(document).on('click', '.tt-remove-row', function (e) {
        e.preventDefault();
        $(this).closest('tr').remove();
    });

    $('form.defaultForm').on('submit', function () {
        var hasError = false;

        if (isChildRateOn()) {
            $('#tt-child-body tr').each(function () {
                $(this).find('.tt-req').each(function () {
                    if ($.trim($(this).val()) === '') {
                        $(this).addClass('tt-field-error');
                        hasError = true;
                    }
                });
            });
        }

        if (isTieredOn()) {
            $('#tt-tiers-body tr').each(function () {
                $(this).find('.tt-req').each(function () {
                    if ($.trim($(this).val()) === '') {
                        $(this).addClass('tt-field-error');
                        hasError = true;
                    }
                });
            });
        }

        if (hasError) {
            setTimeout(function () {
                var $first = $('.tt-field-error').first();
                if ($first.length) {
                    $first[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }, 50);
        }
        return !hasError;
    });

    $(document).on('input', '.tt-req', function () {
        if ($.trim($(this).val()) !== '') {
            $(this).removeClass('tt-field-error');
        }
    });
});
