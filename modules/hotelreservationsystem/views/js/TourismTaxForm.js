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
    var CALCULATION_TYPE_PERCENTAGE = 1;

    var tierIdx  = $('#tourism-tax-tiers-body tr').length;
    var childIdx = $('#tourism-tax-child-body tr').length;
    var validityIdx = $('#tourism-tax-validity-body tr').length;

    function bindValidityDatepickers($scope) {
        $scope.find('.tourism-tax-validity-date').datepicker({ dateFormat: 'yy-mm-dd' });
    }
    bindValidityDatepickers($('#tourism-tax-validity-body'));

    function isTourismOn()       { return $('input[name="is_tourism_tax"]:checked').val()      == '1'; }
    function isChildRateOn()     { return $('input[name="has_child_rate"]:checked').val()      == '1'; }
    function isChildAgeRangeOn() { return $('input[name="has_child_age_range"]:checked').val() == '1'; }
    function isTieredOn()        { return $('input[name="is_tiered"]:checked').val()           == '1'; }
    function isMultiValidOn()    { return $('input[name="has_multiple_valid_ranges"]:checked').val() == '1'; }

    function setVisible($el, visible, animate) {
        var duration = animate ? 200 : 0;
        visible ? $el.show(duration) : $el.hide(duration);
    }

    function ensureOneRow(bodyId, tplId, idxRef, signClass, signFn) {
        if ($('#' + bodyId + ' tr').length === 0) {
            var tpl = $('#' + tplId).html().replace(/__IDX__/g, idxRef.val);
            var $row = $(tpl);
            if (signClass && signFn) {
                $row.find('.' + signClass).text(signFn());
            }
            bindValidityDatepickers($row);
            $('#' + bodyId).append($row);
            idxRef.val++;
        }
    }

    var tierIdxRef     = { val: tierIdx };
    var childIdxRef    = { val: childIdx };
    var validityIdxRef = { val: validityIdx };

    function updateAllVisibility(animate) {
        var t = isTourismOn();
        setVisible($('.tourism-tax-field').not('.tourism-tax-child-field, .tourism-tax-child-band-field, .tourism-tax-tiered-field, .tourism-tax-multi-valid-field'), t, animate);
        setVisible($('.tourism-tax-non-tourism'), !t, animate);

        var showChild = t && isChildRateOn();
        setVisible($('.tourism-tax-child-field'), showChild, animate);

        var showChildBands = showChild && isChildAgeRangeOn();
        setVisible($('.tourism-tax-child-band-field'), showChildBands, animate);
        if (showChildBands) {
            ensureOneRow('tourism-tax-child-body', 'tourism-tax-child-row-tpl', childIdxRef, 'tourism-tax-child-row-type-sign', currentChildTypeSign);
        }

        var showTiered = t && isTieredOn();
        setVisible($('.tourism-tax-tiered-field'), showTiered, animate);
        if (showTiered) {
            ensureOneRow('tourism-tax-tiers-body', 'tourism-tax-tier-row-tpl', tierIdxRef, 'tourism-tax-row-type-sign', currentTypeSign);
        }

        var showMultiValid = t && isMultiValidOn();
        setVisible($('.tourism-tax-multi-valid-field'), showMultiValid, animate);
        if (showMultiValid) {
            ensureOneRow('tourism-tax-validity-body', 'tourism-tax-validity-row-tpl', validityIdxRef, null, null);
        }
    }

    updateAllVisibility(false);
    updateTypeSign();
    updateChildTypeSign();

    $('input[name="is_tourism_tax"], input[name="has_child_rate"], input[name="has_child_age_range"], input[name="is_tiered"], input[name="has_multiple_valid_ranges"]').on('change', function () {
        setTimeout(function () { updateAllVisibility(true); }, 0);
    });

    $(document).on('click', '.prestashop-switch .slide-button', function () {
        setTimeout(function () { updateAllVisibility(true); }, 0);
    });

    function currentTypeSign() {
        var $sign = $('#tourism-tax-type-sign');
        return $('select[name="tax_calc_type"]').val() == CALCULATION_TYPE_PERCENTAGE ? '%' : $sign.data('currency');
    }

    function updateTypeSign() {
        var sign = currentTypeSign();
        $('#tourism-tax-type-sign').text(sign);
        $('.tourism-tax-row-type-sign').text(sign);
    }

    function currentChildTypeSign() {
        return $('select[name="child_calc_type"]').val() == CALCULATION_TYPE_PERCENTAGE ? '%' : $('#tourism-tax-type-sign').data('currency');
    }

    function updateChildTypeSign() {
        $('.tourism-tax-child-row-type-sign').text(currentChildTypeSign());
    }

    $('select[name="tax_calc_type"]').on('change', updateTypeSign);
    $('select[name="child_calc_type"]').on('change', updateChildTypeSign);

    $(document).on('click', '#tourism-tax-add-tier', function (e) {
        e.preventDefault();
        var tpl = $('#tourism-tax-tier-row-tpl').html().replace(/__IDX__/g, tierIdxRef.val);
        var $row = $(tpl);
        $row.find('.tourism-tax-row-type-sign').text(currentTypeSign());
        $('#tourism-tax-tiers-body').append($row);
        tierIdxRef.val++;
    });

    $(document).on('click', '#tourism-tax-add-child', function (e) {
        e.preventDefault();
        var tpl = $('#tourism-tax-child-row-tpl').html().replace(/__IDX__/g, childIdxRef.val);
        var $row = $(tpl);
        $row.find('.tourism-tax-child-row-type-sign').text(currentChildTypeSign());
        $('#tourism-tax-child-body').append($row);
        childIdxRef.val++;
    });

    $(document).on('click', '#tourism-tax-add-validity', function (e) {
        e.preventDefault();
        var tpl = $('#tourism-tax-validity-row-tpl').html().replace(/__IDX__/g, validityIdxRef.val);
        var $row = $(tpl);
        bindValidityDatepickers($row);
        $('#tourism-tax-validity-body').append($row);
        validityIdxRef.val++;
    });

    $(document).on('click', '.tourism-tax-remove-row', function (e) {
        e.preventDefault();
        $(this).closest('tr').remove();
    });
});
