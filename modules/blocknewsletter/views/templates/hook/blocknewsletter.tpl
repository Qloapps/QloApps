{*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<!-- Block Newsletter module-->
<div id="newsletter_block_left" class="col-sm-10">
     <div class="col-sm-3 hotel_contact">Call us : 9999999999</div>
  <div class="col-sm-4 hotel_email">
  Email : greshanpalace$domail.com
  </div>
  <div class="col-sm-5">
    <div class="block_content">
      <form action="{$link->getPageLink('index', null, null, null, false, null, true)|escape:'html':'UTF-8'}" method="post">
        <div class="form-group{if isset($msg) && $msg } {if $nw_error}form-error{else}form-ok{/if}{/if}" >
          <input placeholder="{l s='Enter E-mail for NewsLetter' mod='blocknewsletter'}" class="inputNew form-control grey newsletter-input" id="newsletter-input" type="text" name="email" size="18" value="{if isset($msg) && $msg}{$msg}{elseif isset($value) && $value}{$value}{/if}" />
           <button type="submit" name="submitNewsletter" class="btn btn-default submitNewsletter">
                <span>{l s='Send' mod='blocknewsletter'}</span>
            </button>
            <input type="hidden" name="action" value="0" />
        </div>
      </form>
    </div>
  </div>

    {hook h="displayBlockNewsletterBottom" from='blocknewsletter'}
</div>
<!-- /Block Newsletter module-->
{strip}
{if isset($msg) && $msg}
{addJsDef msg_newsl=$msg|@addcslashes:'\''}
{/if}
{if isset($nw_error)}
{addJsDef nw_error=$nw_error}
{/if}
{addJsDefL name=placeholder_blocknewsletter}{l s='Enter your e-mail' mod='blocknewsletter' js=1}{/addJsDefL}
{if isset($msg) && $msg}
    {addJsDefL name=alert_blocknewsletter}{l s='Newsletter : %1$s' sprintf=$msg js=1 mod="blocknewsletter"}{/addJsDefL}
{/if}
{/strip}

<style type="text/css">
    #footer .clearfix
    {
        background-color: #bf9958;
    }
    .hotel_email
    {
        font-size:18px;
        font-family: 'PT Serif', serif;
        font-weight:400;
        color: #ffffff;
        padding-top: 10px;
    }   
    .hotel_contact
    {
        font-size:18px;
        font-family: 'PT Serif', serif;
        font-weight:400;
        color: #ffffff;
        padding-top: 13px;
    }
    .submitNewsletter
    {
        background-color: #bf9958;
        padding-right: 25px;
        padding-left: 25px;
        color: #ffffff;
        font-size:16px;
        font-family: 'PT Serif', serif;
        font-weight:400;
        border: 2px solid #ffffff;
        margin-left: 30px;
    }
    #newsletter-input
    {
        background-color: #bf9958!important;
        border: none!important;
        border-bottom: 2px solid #ffffff!important;
        color: #ffffff!important;
        font-size:16px!important;
        font-family: 'PT Serif', serif!important;
        font-weight:400!important;
        padding-right: 0px!important;
    }
</style>