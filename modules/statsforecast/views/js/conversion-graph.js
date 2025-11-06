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
