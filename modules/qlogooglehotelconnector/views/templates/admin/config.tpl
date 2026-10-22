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

{if !$is_secure}
  <div class="alert alert-danger">
    <strong>{l s='Unsecure server detected.' mod='qlogooglehotelconnector'}</strong>
    {l s='Your store is not running on a secure (HTTPS) server, so the connection with Google Hotel cannot be created or updated.' mod='qlogooglehotelconnector'}
    <a href="{$ssl_settings_url|escape:'html':'UTF-8'}" class="alert-link">{l s='Please enable HTTPS to continue.' mod='qlogooglehotelconnector'}</a>
  </div>
{/if}

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
          <div class="qlo-ghc-warning-title">
            {l s='To complete the setup, click' mod='qlogooglehotelconnector'}
            <strong>{l s='Create Connection' mod='qlogooglehotelconnector'}</strong>.
          </div>
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
            <button type="submit" form="ghc-regenerate-form" class="btn btn-default btn-sm"{if !$is_secure} disabled="disabled"{/if}>
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
    <button type="submit" form="ghc-connection-form" class="btn btn-default"{if !$is_secure} disabled="disabled"{/if}>
      <i class="process-icon-save"></i>&nbsp;
      {if $is_connected}
        {l s='Update Connection' mod='qlogooglehotelconnector'}
      {else}
        {l s='Create Connection' mod='qlogooglehotelconnector'}
      {/if}
    </button>
  </div>

</div>

{if !$is_secure}
<script type="text/javascript">
  {literal}
  (function () {
    var form = document.getElementById('ghc-connection-form');
    if (!form) { return; }
    // Unsecure server: never let the connection form be submitted, even if the disabled attribute is removed.
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      e.stopImmediatePropagation();
      return false;
    }, true);
    form.removeAttribute('action');
    form.onsubmit = function () { return false; };

    var regenForm = document.getElementById('ghc-regenerate-form');
    if (regenForm) {
      regenForm.addEventListener('submit', function (e) {
        e.preventDefault();
        e.stopImmediatePropagation();
        return false;
      }, true);
      regenForm.removeAttribute('action');
      regenForm.onsubmit = function () { return false; };
    }
  })();
  {/literal}
</script>
{/if}

{* ── Cron job setting ─────────────────────────────────────────────────── *}
<form method="post" action="{$action_url|escape:'html':'UTF-8'}" class="form-horizontal">
  <div class="panel">

    <div class="panel-heading">
      <i class="icon-clock-o"></i>
      {l s='Cron job setting' mod='qlogooglehotelconnector'}
    </div>

    {if $cron_task}
      <div class="form-group">
        <label class="control-label col-lg-3" for="ghc_cron">
          <span class="label-tooltip" data-toggle="tooltip" title="{l s='How often should the API logs older than 2 months be deleted automatically?' mod='qlogooglehotelconnector'}">
            {l s='API log cleanup schedule' mod='qlogooglehotelconnector'}
          </span>
        </label>
        <div class="col-lg-3">
          <select name="QGHC_CRON" id="ghc_cron" class="form-control">
            {if !$cron_task.option}
              <option value="" selected="selected" disabled="disabled">{l s='Custom' mod='qlogooglehotelconnector'}: {$cron_task.readable|escape:'html':'UTF-8'}</option>
            {/if}
            {foreach from=$cron_options key=cron_value item=cron_label}
              <option value="{$cron_value|escape:'html':'UTF-8'}"{if $cron_value == $cron_task.option} selected="selected"{/if}>{$cron_label|escape:'html':'UTF-8'}</option>
            {/foreach}
          </select>
        </div>
      </div>
    {/if}

    <div class="alert alert-info">
      {if $cron_manager_enabled}
        <p><strong>{l s='Automatic API log cleanup' mod='qlogooglehotelconnector'}</strong></p>
        <ul>
          {if $cron_task && !$cron_task.active}
            <li>{l s='Automatic API log cleanup is turned off.' mod='qlogooglehotelconnector'}</li>
            <li>{l s='To turn it on, select Daily, Weekly or Monthly from the API log cleanup schedule.' mod='qlogooglehotelconnector'}</li>
          {elseif $cron_task}
            <li>{l s='API logs older than 2 months are deleted automatically. Current schedule:' mod='qlogooglehotelconnector'} <strong>{$cron_task.readable|escape:'html':'UTF-8'}</strong>.</li>
            <li>{l s='To change it, select another option from the API log cleanup schedule.' mod='qlogooglehotelconnector'}</li>
          {else}
            <li>{l s='API logs older than 2 months are deleted automatically. You can configure the schedule in the' mod='qlogooglehotelconnector'} <a href="{$cron_manager_link|escape:'html':'UTF-8'}">{l s='Cron Task Manager' mod='qlogooglehotelconnector'}</a>.</li>
          {/if}
        </ul>
      {else}
        <ul>
          <li>
            {if $cron_manager_installed}
              <strong>{l s='Automatic:' mod='qlogooglehotelconnector'}</strong> {l s='the Cron Task Manager module is disabled, so the API logs are not cleaned up automatically.' mod='qlogooglehotelconnector'}
              <a href="{$cron_manager_link|escape:'html':'UTF-8'}">{l s='Enable the module' mod='qlogooglehotelconnector'}</a>
              {l s='to turn on the automatic cleanup.' mod='qlogooglehotelconnector'}
            {elseif $cron_manager_link}
              <strong>{l s='Automatic:' mod='qlogooglehotelconnector'}</strong> {l s='the Cron Task Manager module is not installed, so the API logs are not cleaned up automatically.' mod='qlogooglehotelconnector'}
              <a href="{$cron_manager_link|escape:'html':'UTF-8'}">{l s='Install the module' mod='qlogooglehotelconnector'}</a>
              {l s='to turn on the automatic cleanup.' mod='qlogooglehotelconnector'}
            {else}
              <strong>{l s='Automatic:' mod='qlogooglehotelconnector'}</strong> {l s='the Cron Task Manager module (qlocrontaskmanager) is not present, so the API logs are not cleaned up automatically. Add the module to turn on the automatic cleanup.' mod='qlogooglehotelconnector'}
            {/if}
          </li>
        </ul>
      {/if}
    </div>

    {if $cron_task}
      <div class="panel-footer">
        <button type="submit" name="submitGhcCron" class="btn btn-default pull-right"><i class="process-icon-save"></i> {l s='Save' mod='qlogooglehotelconnector'}</button>
      </div>
    {/if}

  </div>
</form>

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
