{*
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

<div class="panel qlo-ghc-form-left-labels">
    <div class="panel-heading">
        <i class="icon-file-text"></i>
        {l s='API Log Detail' mod='qlogooglehotelconnector'}
        &nbsp;&mdash;&nbsp;
        <span class="text-muted">{l s='Log #' mod='qlogooglehotelconnector'}{$ghc_log.id_qghc_api_log|intval}</span>
    </div>

    <div class="panel-body">
        <div class="form-horizontal">

            <div class="form-group">
                <label class="control-label col-lg-3 col-md-4">
                    {l s='Date' mod='qlogooglehotelconnector'}
                </label>
                <div class="col-lg-9 col-md-8">
                    <p class="form-control-static">{$ghc_log.date_add|escape:'html':'UTF-8'}</p>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-lg-3 col-md-4">
                    {l s='Hotel' mod='qlogooglehotelconnector'}
                </label>
                <div class="col-lg-9 col-md-8">
                    <p class="form-control-static">{$ghc_log.hotel_label|escape:'html':'UTF-8'}</p>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-lg-3 col-md-4">
                    {l s='Log Type' mod='qlogooglehotelconnector'}
                </label>
                <div class="col-lg-9 col-md-8">
                    <p class="form-control-static">{$ghc_log.type_label|escape:'html':'UTF-8'}</p>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-lg-3 col-md-4">
                    {l s='Status' mod='qlogooglehotelconnector'}
                </label>
                <div class="col-lg-9 col-md-8">
                    <p class="form-control-static">
                        {if $ghc_log.status}
                            <span class="label label-success">{l s='Success' mod='qlogooglehotelconnector'}</span>
                        {else}
                            <span class="label label-danger">{l s='Error' mod='qlogooglehotelconnector'}</span>
                        {/if}
                    </p>
                </div>
            </div>

            {if $ghc_log.message}
            <div class="form-group">
                <label class="control-label col-lg-3 col-md-4">
                    {l s='Message' mod='qlogooglehotelconnector'}
                </label>
                <div class="col-lg-9 col-md-8">
                    <p class="form-control-static">{$ghc_log.message|escape:'html':'UTF-8'}</p>
                </div>
            </div>
            {/if}

            <div class="form-group">
                <label class="control-label col-lg-3 col-md-4">
                    {l s='Request' mod='qlogooglehotelconnector'}
                </label>
                <div class="col-lg-9 col-md-8">
                    {if $ghc_log.request}
                        <div class="qlo-ghc-log-block">
                            <button type="button" class="btn btn-default btn-xs qlo-ghc-copy-btn" data-target="ghc-request">
                                <i class="icon-copy"></i> {l s='Copy' mod='qlogooglehotelconnector'}
                            </button>
                            <pre class="qlo-ghc-log-pre" id="ghc-request">{$ghc_log.request|escape:'html':'UTF-8'}</pre>
                        </div>
                    {else}
                        <p class="form-control-static text-muted">{l s='(empty)' mod='qlogooglehotelconnector'}</p>
                    {/if}
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-lg-3 col-md-4">
                    {l s='Response' mod='qlogooglehotelconnector'}
                </label>
                <div class="col-lg-9 col-md-8">
                    {if $ghc_log.response}
                        <div class="qlo-ghc-log-block">
                            <button type="button" class="btn btn-default btn-xs qlo-ghc-copy-btn" data-target="ghc-response">
                                <i class="icon-copy"></i> {l s='Copy' mod='qlogooglehotelconnector'}
                            </button>
                            <pre class="qlo-ghc-log-pre" id="ghc-response">{$ghc_log.response|escape:'html':'UTF-8'}</pre>
                        </div>
                    {else}
                        <p class="form-control-static text-muted">{l s='(empty)' mod='qlogooglehotelconnector'}</p>
                    {/if}
                </div>
            </div>

        </div>
    </div>

    <div class="panel-footer">
        <a href="{$back_url|escape:'html':'UTF-8'}" class="btn btn-default">
            <i class="process-icon-back"></i>
            {l s='Back to Logs' mod='qlogooglehotelconnector'}
        </a>
    </div>
</div>

