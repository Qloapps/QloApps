/**
 * 2010-2022 Webkul.
 *
 * NOTICE OF LICENSE
 *
 * All right is reserved,
 * Please go through LICENSE.txt file inside our module
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please refer to CustomizationPolicy.txt file inside our module for more information.
 *
 * @author Webkul IN
 * @copyright 2010-2022 Webkul IN
 * @license LICENSE.txt
 */

 $(document).ready(function () {
    $('.stat-box').each(function(i, statBox) {
        let statBoxId = $(statBox).attr('id');
        let connections = $(statBox).data('connection-to');
        let percentage = $(statBox).data('percentage');

        if (typeof connections !== 'undefined') {
            connections = typeof connections === 'string' ? [connections] : connections;
            $.each(connections, function(i, connection) {
                let connectionSelector = '#' + statBoxId + ', #' + connection;
                let connectionClass = statBoxId + '-' + i;
                $(connectionSelector).connections({
                    class: connectionClass,
                    borderClasses: {
                        top: 'connection-border-top',
                        right: 'connection-border-right',
                        bottom: 'connection-border-bottom',
                        left: 'connection-border-left',
                    },
                });

                if (typeof percentage !== 'undefined') {
                    $('.' + connectionClass).html('<span>' + percentage + '%</span>');
                }
            });
        }
    });

    $(window).resize(function() {
        $('connection').connections('update');
    });
});
