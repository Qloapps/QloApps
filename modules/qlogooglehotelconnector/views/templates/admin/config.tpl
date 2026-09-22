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

{* ── Google Hotel Setup ───────────────────────────────────────────────── *}
<div class="panel qlo-ghc-form-left-labels">

  <div class="panel-heading qlo-ghc-panel-heading-flex">
    <span class="qlo-ghc-heading-text">
      <i class="icon-link"></i>
      {l s='Google Hotel Setup' mod='qlogooglehotelconnector'}
    </span>
    {if $is_connected}
      <span class="label label-success qlo-ghc-conn-badge">
        <i class="icon-check"></i>&nbsp;{l s='Connected' mod='qlogooglehotelconnector'}
      </span>
    {else}
      <span class="label label-danger qlo-ghc-conn-badge">
        <i class="icon-times"></i>&nbsp;{l s='Not Connected' mod='qlogooglehotelconnector'}
      </span>
    {/if}
  </div>

  {* Connection form — fields only, no footer here *}
  <form id="ghc-connection-form"
        method="post"
        action="{$action_url|escape:'html':'UTF-8'}"
        class="form-horizontal">
    <input type="hidden" name="submitConfigurationConnection" value="1">
    <div class="panel-body">

      <div class="alert alert-warning qlo-ghc-alert-mb">
        <strong>{l s='Important:' mod='qlogooglehotelconnector'}</strong>
        {l s='This key was automatically generated for the Google Hotel connection. Please do not change the permissions of this webservice key.' mod='qlogooglehotelconnector'}
        {if !$is_connected}
          <br><br>
          {l s='To complete the setup, click' mod='qlogooglehotelconnector'}
          <strong>{l s='Create Connection' mod='qlogooglehotelconnector'}</strong>.
        {/if}
      </div>

      <div class="form-group">
        <label class="control-label col-lg-3" for="QGHC_WS_KEY">
          {l s='Webservice Key' mod='qlogooglehotelconnector'}
        </label>
        <div class="col-lg-7">
          <input type="text"
                 class="form-control"
                 id="QGHC_WS_KEY"
                 name="QGHC_WS_KEY"
                 value="{$ws_key|escape:'html':'UTF-8'}"
                 placeholder="{l s='Enter your webservice key' mod='qlogooglehotelconnector'}" />
          {if $ws_key}
          <p class="qlo-ghc-helpblock-top">
            <button type="submit" form="ghc-regenerate-form" class="btn btn-default btn-sm">
              <i class="icon-refresh"></i>
              {l s='Regenerate Key' mod='qlogooglehotelconnector'}
            </button>
          </p>
          {/if}
        </div>
      </div>

    </div>
  </form>

  {* Delete form — empty, button lives in the shared footer below *}
  {if $is_connected}
  <form id="ghc-delete-form"
        method="post"
        action="{$action_url|escape:'html':'UTF-8'}"
        onsubmit="return confirm('{l s='Are you sure you want to delete the Google Hotel connection?' mod='qlogooglehotelconnector' js=1}')">
    <input type="hidden" name="submitDeleteConfigurationConnection" value="1">
  </form>
  {/if}

  {* Regenerate form — empty, button lives under the Webservice Key field above *}
  {if $ws_key}
  <form id="ghc-regenerate-form"
        method="post"
        action="{$action_url|escape:'html':'UTF-8'}"
        onsubmit="return confirm('{l s='Regenerating the key will invalidate the current webservice key immediately. Continue?' mod='qlogooglehotelconnector' js=1}')">
    <input type="hidden" name="submitRegenerateWebserviceKey" value="1">
  </form>
  {/if}

  {* Single shared footer — both buttons sit here *}
  <div class="panel-footer qlo-ghc-panel-footer-flex">
    {if $is_connected}
      <button type="submit" form="ghc-delete-form" class="btn btn-default">
        <i class="process-icon-delete"></i>
        {l s='Delete Connection' mod='qlogooglehotelconnector'}
      </button>
    {else}
      <span></span>
    {/if}
    <button type="submit" form="ghc-connection-form" class="btn btn-default">
      <i class="process-icon-save"></i>&nbsp;
      {if $is_connected}
        {l s='Update Connection' mod='qlogooglehotelconnector'}
      {else}
        {l s='Create Connection' mod='qlogooglehotelconnector'}
      {/if}
    </button>
  </div>

</div>

{* ── Cron job setting ─────────────────────────────────────────────────── *}
<div class="panel">

  <div class="panel-heading">
    <i class="icon-clock-o"></i>
    {l s='Cron job setting' mod='qlogooglehotelconnector'}
  </div>

  <div class="panel-body">
    <div class="alert alert-info qlo-ghc-no-margin">
      <strong>
        {l s='For automatic cleanup of API logs older than 2 months, set the following cron job to run once every 24 hours:' mod='qlogooglehotelconnector'}
      </strong>
      <br><br>
      <strong>
        <code>0 0 * * * curl {$cron_url|escape:'html':'UTF-8'}</code>
      </strong>
    </div>
  </div>

</div>

{* ── Connected Properties ───────────────────────────────────────────────── *}
<div class="panel">

  <div class="panel-heading">
    <i class="icon-globe"></i>
    {l s='Google Hotel — Connected Properties' mod='qlogooglehotelconnector'}
    {if $configs}
      &nbsp;<span class="badge">{$configs|@count}</span>
    {/if}
  </div>

  <div class="panel-body">
    <p class="text-muted qlo-ghc-no-margin">
      {l s='Hotels listed here are enabled for Google Hotel. To add or remove a hotel, go to' mod='qlogooglehotelconnector'}
      <strong>
        <a href="{$hotel_list_url|escape:'html':'UTF-8'}">{l s='Hotels' mod='qlogooglehotelconnector'}</a>
        {l s='→ Edit Hotel → Google Hotel tab' mod='qlogooglehotelconnector'}
      </strong>.
    </p>
  </div>

  {if !$configs}

    <div class="panel-body qlo-ghc-panel-body-no-pt">
      <div class="alert alert-info qlo-ghc-no-margin">
        <i class="icon-info-circle"></i>
        {l s='No hotels are connected to Google Hotel yet. Open any hotel from the Hotels section and enable it in the Google Hotel tab.' mod='qlogooglehotelconnector'}
      </div>
    </div>

  {else}

    <div class="table-responsive">
      <table class="table table-striped qlo-ghc-table qlo-ghc-no-mb">
        <thead>
          <tr>
            <th>{l s='Hotel' mod='qlogooglehotelconnector'}</th>
            <th>{l s='Google Status' mod='qlogooglehotelconnector'}</th>
            <th>{l s='Last Updated' mod='qlogooglehotelconnector'}</th>
          </tr>
        </thead>
        <tbody>
          {foreach from=$configs item=cfg}
            <tr>
              <td>
                <a href="{$cfg.edit_url|escape:'html':'UTF-8'}">
                  {$cfg.hotel_name|escape:'html':'UTF-8'}
                </a>
              </td>
              <td>
                <span class="label {$cfg.status_class|escape:'html':'UTF-8'}">
                  <i class="{$cfg.status_icon|escape:'html':'UTF-8'}"></i>
                  {$cfg.status_label|escape:'html':'UTF-8'}
                </span>
              </td>
              <td class="qlo-ghc-nowrap">
                {$cfg.date_add|escape:'html':'UTF-8'}
              </td>
            </tr>
          {/foreach}
        </tbody>
      </table>
    </div>

  {/if}

</div>
