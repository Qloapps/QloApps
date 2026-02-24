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
    $(function () {
        var $dateInput = $('#filter_daterange_value');
        var $checkInInput = $('#filter_check_in_time');
        var $checkOutInput = $('#filter_check_out_time');
        var $searchOccupancyWrapper = $('#search_occupancy_wrapper');
        var $searchOccupancyOriginalParent = $searchOccupancyWrapper.parent();
        var filterAjaxRequest = null;

        if (!$dateInput.length || !$checkInInput.length || !$checkOutInput.length) {
            return;
        }

        function normalizeIsoDate(value) {
            if (!value) {
                return '';
            }

            var match = String(value).match(/^(\d{4}-\d{2}-\d{2})/);
            return match ? match[1] : '';
        }

        function formatIsoToDisplay(isoDate) {
            try {
                return $.datepicker.formatDate('dd-mm-yy', $.datepicker.parseDate('yy-mm-dd', isoDate));
            } catch (e) {
                return '';
            }
        }

        function escapeRegex(value) {
            return String(value).replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        }

        function removeRoomsFromOccupancyLabel(labelText) {
            if (!labelText) {
                return '';
            }

            var roomTerms = [];
            if (typeof room_txt !== 'undefined' && room_txt) {
                roomTerms.push(room_txt);
            }
            if (typeof rooms_txt !== 'undefined' && rooms_txt) {
                roomTerms.push(rooms_txt);
            }
            roomTerms.push('Room', 'Rooms');

            var roomPattern = roomTerms
                .filter(function (term, index, self) {
                    return term && self.indexOf(term) === index;
                })
                .map(function (term) {
                    return escapeRegex(term);
                })
                .join('|');

            if (!roomPattern) {
                return $.trim(labelText);
            }

            return $.trim(String(labelText).replace(new RegExp(',\\s*\\d+\\s*(?:' + roomPattern + ')\\s*$', 'i'), ''));
        }

        function updateDateLabel(checkInDate, checkOutDate) {
            var checkInText = typeof RangePickerCheckin !== 'undefined' ? RangePickerCheckin : 'Check In';
            var checkOutText = typeof RangePickerCheckout !== 'undefined' ? RangePickerCheckout : 'Check Out';
            var defaultLabel = checkInText + ' - ' + checkOutText;
            var $label = $dateInput.find('.filter-date-label');
            if (!$label.length) {
                return;
            }

            if (checkInDate && checkOutDate) {
                var checkInDisplay = formatIsoToDisplay(checkInDate);
                var checkOutDisplay = formatIsoToDisplay(checkOutDate);
                if (checkInDisplay && checkOutDisplay) {
                    $label.text(checkInDisplay + ' - ' + checkOutDisplay);
                    return;
                }
            }

            $label.text(defaultLabel);
        }

        function createFilterObj() {
            var filterData = {};
            $('.filter').each(function () {
                if ($(this).is(':checked')) {
                    var filterType = $(this).attr('data-type');
                    if (!filterData[filterType]) {
                        filterData[filterType] = [];
                    }
                    filterData[filterType].push($(this).val());
                }
            });

            return filterData;
        }

        function getSelectedSort() {
            var selectedSort = $('.sort_btn_span[data-sort-value!="0"]');
            if (selectedSort.length) {
                return {
                    sortBy: selectedSort.attr('data-sort-by'),
                    sortValue: selectedSort.attr('data-sort-value')
                };
            }

            return {
                sortBy: 0,
                sortValue: 0
            };
        }

        function getFilterResult(sortBy, sortValue) {
            var filterData = createFilterObj();
            var requestUrl = typeof cat_link !== 'undefined' ? cat_link : window.location.href;
            var data = {
                filter_data: filterData,
                ajax: 1,
                action: 'FilterResults'
            };

            var checkInDate = normalizeIsoDate($checkInInput.val());
            var checkOutDate = normalizeIsoDate($checkOutInput.val());

            if (checkInDate) {
                data.date_from = checkInDate;
            }
            if (checkOutDate) {
                data.date_to = checkOutDate;
            }
            if (sortBy && sortValue) {
                data.sort_by = sortBy;
                data.sort_value = sortValue;
            }

            if (filterAjaxRequest) {
                filterAjaxRequest.abort();
            }

            filterAjaxRequest = $.ajax({
                url: requestUrl,
                type: 'POST',
                dataType: 'json',
                data: data,
                success: function (response) {
                    if (response.status) {
                        $('#category_data_cont').html(response.html_room_type_list);
                    }
                }
            });
        }

        function triggerFilter(sortBy, sortValue) {
            var sortData = getSelectedSort();
            if (sortBy !== undefined && sortValue !== undefined) {
                sortData = {
                    sortBy: sortBy,
                    sortValue: sortValue
                };
            }

            getFilterResult(sortData.sortBy, sortData.sortValue);
        }

        function syncFilterDatesToSearchPanel() {
            var $searchCheckIn = $('#check_in_time');
            var $searchCheckOut = $('#check_out_time');

            if (!$searchCheckIn.length || !$searchCheckOut.length) {
                return;
            }

            var checkInDate = normalizeIsoDate($checkInInput.val());
            var checkOutDate = normalizeIsoDate($checkOutInput.val());

            if (checkInDate) {
                $searchCheckIn.val(checkInDate);
            }
            if (checkOutDate) {
                $searchCheckOut.val(checkOutDate);
            }

            if (typeof createDateRangePicker === 'function') {
                createDateRangePicker(
                    $('#max_order_date').val(),
                    $('#min_booking_offset').val(),
                    $searchCheckIn.val(),
                    $searchCheckOut.val()
                );
            }
        }

        function syncOccupancyLabelFromSearchPanel() {
            var $targetLabel = $('#filter_occupancy_value > span');
            var $searchLabel = $('#guest_occupancy > span');

            if (!$targetLabel.length || !$searchLabel.length) {
                return;
            }

            var labelText = removeRoomsFromOccupancyLabel($.trim($searchLabel.text()));
            if (labelText) {
                $targetLabel.text(labelText);
            }
        }

        function moveSearchOccupancyWrapperToFilter() {
            if (!$searchOccupancyWrapper.length) {
                return false;
            }

            var $filterOccupancyContainer = $('#filter_occupancy_value').closest('.dropdown');
            if (!$filterOccupancyContainer.length) {
                $filterOccupancyContainer = $('#filter_occupancy_value').closest('.form-group');
            }
            if (!$filterOccupancyContainer.length) {
                return false;
            }

            if (!$filterOccupancyContainer.find('#search_occupancy_wrapper').length) {
                $searchOccupancyWrapper.appendTo($filterOccupancyContainer);
            }

            return true;
        }

        function restoreSearchOccupancyWrapper() {
            if (!$searchOccupancyWrapper.length || !$searchOccupancyOriginalParent.length) {
                return;
            }

            if (!$searchOccupancyOriginalParent.find('#search_occupancy_wrapper').length) {
                $searchOccupancyWrapper.hide().appendTo($searchOccupancyOriginalParent);
            }
        }

        function syncSearchPanelStateToFilter() {
            var checkInDate = normalizeIsoDate($('#check_in_time').val());
            var checkOutDate = normalizeIsoDate($('#check_out_time').val());

            if (checkInDate) {
                $checkInInput.val(checkInDate);
            }
            if (checkOutDate) {
                $checkOutInput.val(checkOutDate);
            }

            if ($dateInput.data('dateRangePicker') && checkInDate && checkOutDate) {
                var checkInDisplay = formatIsoToDisplay(checkInDate);
                var checkOutDisplay = formatIsoToDisplay(checkOutDate);
                if (checkInDisplay && checkOutDisplay) {
                    $dateInput.data('dateRangePicker').setDateRange(checkInDisplay, checkOutDisplay);
                }
            }

            updateDateLabel(checkInDate, checkOutDate);
            syncOccupancyLabelFromSearchPanel();
        }

        function getDatePickerApi() {
            return $dateInput.data('dateRangePicker');
        }

        function isDatePickerVisible() {
            return $dateInput.siblings('.date-picker-wrapper').is(':visible');
        }

        function closeDatePicker() {
            var pickerApi = getDatePickerApi();
            if (pickerApi && isDatePickerVisible()) {
                pickerApi.close();
            }
        }

        $dateInput.on('click.filterDateToggle', function (e) {
            if (isDatePickerVisible()) {
                e.preventDefault();
                e.stopImmediatePropagation();
                closeDatePicker();
                return false;
            }

            if ($searchOccupancyWrapper.length && $searchOccupancyWrapper.is(':visible')) {
                restoreSearchOccupancyWrapper();
            }

            e.stopPropagation();
            return undefined;
        });

        function initializeDateRangePicker() {
            if (typeof $.fn.dateRangePicker === 'undefined') {
                return;
            }

            var maxOrderDate = normalizeIsoDate(typeof max_order_date !== 'undefined' ? max_order_date : '');
            var pickerMaxDate = false;
            if (maxOrderDate) {
                pickerMaxDate = formatIsoToDisplay(maxOrderDate);
            }

            $dateInput.dateRangePicker({
                startDate: $.datepicker.formatDate('dd-mm-yy', new Date()),
                separator: ' to ',
                endDate: pickerMaxDate,
                customOpenAnimation: function (cb) {
                    $(this).show(10, cb);
                },
                customCloseAnimation: function (cb) {
                    $(this).hide(10, cb);
                }
            }).on('datepicker-change', function (event, obj) {
                if (!obj || !obj.date1 || !obj.date2) {
                    return;
                }

                var checkInDate = $.datepicker.formatDate('yy-mm-dd', obj.date1);
                var checkOutDate = $.datepicker.formatDate('yy-mm-dd', obj.date2);
                $checkInInput.val(checkInDate);
                $checkOutInput.val(checkOutDate);
                updateDateLabel(checkInDate, checkOutDate);
                syncFilterDatesToSearchPanel();
            });
        }

        function setInitialDates() {
            var checkInDate = normalizeIsoDate($checkInInput.val() || (typeof date_from !== 'undefined' ? date_from : ''));
            var checkOutDate = normalizeIsoDate($checkOutInput.val() || (typeof date_to !== 'undefined' ? date_to : ''));

            if (checkInDate) {
                $checkInInput.val(checkInDate);
            }
            if (checkOutDate) {
                $checkOutInput.val(checkOutDate);
            }

            if ($dateInput.data('dateRangePicker') && checkInDate && checkOutDate) {
                var checkInDisplay = formatIsoToDisplay(checkInDate);
                var checkOutDisplay = formatIsoToDisplay(checkOutDate);
                if (checkInDisplay && checkOutDisplay) {
                    $dateInput.data('dateRangePicker').setDateRange(checkInDisplay, checkOutDisplay);
                }
            }

            updateDateLabel(checkInDate, checkOutDate);
        }

        initializeDateRangePicker();
        setInitialDates();
        syncFilterDatesToSearchPanel();
        syncOccupancyLabelFromSearchPanel();

        $(document).on('click', '.sort_result', function (e) {
            e.preventDefault();

            $('.sort_btn_span').attr('data-sort-by', 0);
            $('.sort_btn_span').attr('data-sort-value', 0);

            var sortText = $(this).text();
            var $sortButtonValue = $(this).parents('div.filter_dw_cont').find('button span.sort_btn_span');
            $sortButtonValue.text(sortText);
            $sortButtonValue.attr('data-sort-for', sortText);
            $sortButtonValue.attr('data-sort-by', $(this).attr('data-sort-by'));
            $sortButtonValue.attr('data-sort-value', $(this).attr('data-value'));

            triggerFilter($(this).attr('data-sort-by'), $(this).attr('data-value'));
        });

        $(document).on('click', '.filter', function () {
            triggerFilter();
        });

        $('#filter_occupancy_value').on('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            syncFilterDatesToSearchPanel();
            closeDatePicker();

            if (moveSearchOccupancyWrapperToFilter()) {
                if ($searchOccupancyWrapper.is(':visible')) {
                    restoreSearchOccupancyWrapper();
                } else {
                    $searchOccupancyWrapper.show();
                }
            }
        });

        $(document).on('click', '#filter_search_now_submit', function (e) {
            e.preventDefault();
            syncFilterDatesToSearchPanel();
            restoreSearchOccupancyWrapper();

            var $searchSubmit = $('#search_room_submit');
            if ($searchSubmit.length) {
                $searchSubmit.get(0).click();
            } else {
                triggerFilter();
            }
        });

        $(document).on('click', '#search_occupancy_wrapper .submit_occupancy_btn', function () {
            setTimeout(function () {
                syncOccupancyLabelFromSearchPanel();
                restoreSearchOccupancyWrapper();
            }, 0);
        });

        $(document).on('hidden.bs.modal', '#rmsearchmodal', function () {
            syncSearchPanelStateToFilter();
        });

        $(document).on('show.bs.modal', '#rmsearchmodal', function () {
            restoreSearchOccupancyWrapper();
        });

        $(document).on('click', function (e) {
            var $target = $(e.target);

            if (isDatePickerVisible()
                && !$target.closest('#filter_daterange_value').length
                && !$target.closest('.date-picker-wrapper').length
            ) {
                closeDatePicker();
            }

            if ($searchOccupancyWrapper.length
                && $searchOccupancyWrapper.is(':visible')
                && !$target.closest('#filter_occupancy_value').length
                && !$target.closest('#search_occupancy_wrapper').length
            ) {
                restoreSearchOccupancyWrapper();
            }
        });

        $dateInput.on('keydown', function (e) {
            if (e.which === 13 || e.which === 32) {
                e.preventDefault();
                $(this).click();
            }
        });
    });
})(jQuery);
