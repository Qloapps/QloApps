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

<div class="panel">
    <div class="panel-heading">
        <i class="icon-terminal"></i> {l s='Cron Command' mod='qlocrontaskmanager'}
    </div>
    <div class="panel-body  form-horizontal ">
        <div class="alert alert-info">
            {l s='Add the following cron entry to your server. This single command will manage all module cron tasks automatically.' mod='qlocrontaskmanager'}
        </div>
        <div class="form-group">
            <label class="control-label col-lg-3" data-original-title="Cron Command" data-placement="top" data-toggle="tooltip">
                Cron Command
            </label>
            <div class="col-lg-8">
                <div class="input-group">
                    <input type="text"
                           id="qctm-cron-command"
                           class="form-control"
                           value='{$cronCommand|escape:'html':'UTF-8'}'
                           readonly="readonly" />
                    <span class="input-group-btn">
                        <button type="button"
                                id="qctm-copy-btn"
                                class="btn btn-default"
                                data-copied-text="{l s='Copied!' mod='qlocrontaskmanager'}"
                                title="{l s='Copy to clipboard' mod='qlocrontaskmanager'}">
                            <i class="icon-copy"></i> {l s='Copy' mod='qlocrontaskmanager'}
                        </button>
                    </span>
                </div>
                <p class="help-block">
                    {l s='This command runs every minute and dispatches all registered module tasks based on their cron expressions.' mod='qlocrontaskmanager'}
                </p>
            </div>
        </div>
    </div>
</div>

<span id="qctm-admin-js-text"
      class="hide"
      data-regenerate-confirm="{l s='Are you sure you want to regenerate the token? You will need to update your server cron entry.' mod='qlocrontaskmanager'}"></span>
