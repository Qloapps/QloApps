{**
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
*}

<div class="col-sm-12">
    <section id="dashinsights" class="panel widget allow_push">
        <header class="panel-heading">
            <i class="icon-area-chart"></i> {l s='Insights' mod='dashinsights'}
            <span><small class="text-muted" id="dashinsights_heading_zone_two"></small></span>
            <span class="panel-heading-action">
                <a class="list-toolbar-btn" href="#" onclick="refreshDashboard('dashinsights'); return false;" title="{l s='Refresh' mod='dashinsights'}">
                    <i class="process-icon-refresh"></i>
                </a>
            </span>
        </header>

        <section>
            <div class="row">
                <div class="col-md-12 col-lg-6">
                    <p class="chart-label">{l s='Room Nights' mod='dashinsights'}</p>
                    <div class="chart with-transitions insight-chart-wrap" id="dashinsights_room_nights">
                        <svg></svg>
                    </div>
                </div>
                <div class="col-md-12 col-lg-6">
                    <p class="chart-label">{l s='Days of the Week' mod='dashinsights'}</p>
                    <div class="chart with-transitions insight-chart-wrap" id="dashinsights_days_of_the_week">
                        <svg></svg>
                    </div>
                </div>
            </div>
        </section>
    </section>
</div>
<div class="clearfix"></div>
