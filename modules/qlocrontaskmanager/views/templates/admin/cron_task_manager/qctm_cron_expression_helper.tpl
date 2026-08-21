{*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.md
* It is also available through the world-wide-web at this URL:
* https://opensource.org/licenses/afl-3.0.php
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
* @license https://opensource.org/licenses/afl-3.0.php Academic Free License 3.0
*}

<div class="row">
    <div class="col-lg-8">
        <p>The cron expression is made of five fields. Each field can have the following values.</p>
        <table class="table qctm-cron-fields-table">
            <thead>
                <tr>
                    <th>*</th>
                    <th>*</th>
                    <th>*</th>
                    <th>*</th>
                    <th>*</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{l s='minute (0-59)' mod='qlocrontaskmanager'}</td>
                    <td>{l s='hour (0 - 23)' mod='qlocrontaskmanager'}</td>
                    <td>{l s='day of the month (1 - 31)' mod='qlocrontaskmanager'}</td>
                    <td>{l s='month (1 - 12)' mod='qlocrontaskmanager'}</td>
                    <td>{l s='day of the week (0 - 6)' mod='qlocrontaskmanager'}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<input type="hidden" id="qctm-ajax-url" value="{$qctmAjaxUrl|escape:'html'}">