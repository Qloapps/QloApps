$(function () {
    var $table = $('#order-list');
    if (!$table.length) {
        return;
    }

    var $rows = $table.find('tbody > tr');
    var $search = $('#history-search-order');
    var $filter = $('#history-status-filter');
    var $pagination = $('#history-pagination');
    var $empty = $('#history-empty-filter');

    // Only activate enhanced behavior on the history page UI that has these controls.
    if (!$search.length && !$filter.length && !$pagination.length) {
        return;
    }

    var currentPage = 1;
    var pageSize = 10;
    var records = [];

    function normalize(value) {
        return $.trim(String(value || '')).toLowerCase();
    }

    $rows.each(function () {
        var $row = $(this);
        var stateId = parseInt($row.find('.history_state').attr('data-order-state-id'), 10) || 0;
        var searchableText = normalize($row.text());

        records.push({
            index: index,
            $row: $row,
            stateId: stateId,
            search: searchableText
        });
    });

    function getFilteredRecords() {
        var query = normalize($search.val());
        var selectedStateId = parseInt($filter.val(), 10) || 0;

        return $.grep(records, function (record) {
            var matchesQuery = !query || record.search.indexOf(query) !== -1;
            var matchesStatus = !selectedStateId || record.stateId === selectedStateId;
            return matchesQuery && matchesStatus;
        });
    }

    function buildPages(totalPages) {
        var pages = [];
        var start = Math.max(1, currentPage - 1);
        var end = Math.min(totalPages, currentPage + 1);

        if (start > 1) {
            pages.push(1);
            if (start > 2) {
                pages.push('...');
            }
        }

        for (var i = start; i <= end; i++) {
            pages.push(i);
        }

        if (end < totalPages) {
            if (end < totalPages - 1) {
                pages.push('...');
            }
            pages.push(totalPages);
        }

        return pages;
    }

    function renderPagination(totalPages) {
        if (!$pagination.length) {
            return;
        }

        $pagination.empty();

        var prevItemClass = 'page-item';
        var prevLinkClass = 'page-link';
        if (currentPage <= 1) {
            prevItemClass += ' disabled';
        }
        $pagination.append(
            $('<li />', { 'class': prevItemClass }).append(
                $('<a />', { href: '#', 'class': prevLinkClass, 'data-page': currentPage - 1, 'aria-label': 'Previous' }).html('<i class="icon-chevron-left"></i>')
            )
        );

        var pages = buildPages(totalPages);
        $.each(pages, function (_, page) {
            if (page === '...') {
                $pagination.append(
                    $('<li />', { 'class': 'page-item disabled' }).append($('<a />', { href: '#', 'class': 'page-link', tabindex: '-1', text: '...' }))
                );
                return;
            }

            var itemClass = 'page-item';
            if (page === currentPage) {
                itemClass += ' active';
            }

            $pagination.append(
                $('<li />', { 'class': itemClass }).append($('<a />', { href: '#', 'class': 'page-link', 'data-page': page, text: page }))
            );
        });

        var nextItemClass = 'page-item';
        if (currentPage >= totalPages) {
            nextItemClass += ' disabled';
        }
        $pagination.append(
            $('<li />', { 'class': nextItemClass }).append(
                $('<a />', { href: '#', 'class': 'page-link', 'data-page': currentPage + 1, 'aria-label': 'Next' }).html('<i class="icon-chevron-right"></i>')
            )
        );
    }

    function render() {
        var filtered = getFilteredRecords();
        var totalPages = Math.max(1, Math.ceil(filtered.length / pageSize));

        if (currentPage > totalPages) {
            currentPage = totalPages;
        }
        if (currentPage < 1) {
            currentPage = 1;
        }

        $rows.hide();
        $table.find('tr.footable-row-detail').hide();

        var start = (currentPage - 1) * pageSize;
        var end = start + pageSize;
        var pageRecords = filtered.slice(start, end);

        $.each(pageRecords, function (_, record) {
            record.$row.show();
            var $detailRow = record.$row.next('.footable-row-detail');
            if ($detailRow.length) {
                $detailRow.hide();
            }
        });

        if ($empty.length) {
            $empty.toggle(filtered.length === 0);
        }

        renderPagination(totalPages);

        // Keep mobile footable layout in sync after row visibility changes.
        $table.trigger('footable_redraw');
    }

    $search.on('input', function () {
        currentPage = 1;
        render();
    });

    $filter.on('change', function () {
        currentPage = 1;
        render();
    });

    $pagination.on('click', 'a.page-link', function (event) {
        event.preventDefault();
        var $item = $(this).closest('.page-item');
        if ($item.hasClass('disabled') || $item.hasClass('active')) {
            return;
        }

        var page = parseInt($(this).data('page'), 10);
        if (!page || page < 1) {
            return;
        }

        currentPage = page;
        render();
    });

    render();
});
