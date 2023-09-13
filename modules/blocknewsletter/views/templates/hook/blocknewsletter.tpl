{*
* 2007-2017 PrestaShop
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
*  @copyright  2007-2017 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<!-- Block Newsletter module-->
<div class="row">
    <section class="col-xs-12 col-sm-12" id="blocknewsletter">
        <div class="row margin-lr-0 footer-section-heading">
            <p>{l s='GET NOTIFICATIONS' mod='blocknewsletter'}</p>
            <hr/>
        </div>
        <div class="row margin-lr-0">
            <form action="{$link->getModuleLink('newsletter', 'subscription')|escape:'html':'UTF-8'}" method="post">
                <div class="form-group">
                    <input type="hidden" name="ajax" value="1" />
                    <input type="hidden" name="action" value="SubscribeNewsletter" />
                    <input type="hidden" name="token" value="{$csrf_token}" />
                    <input type="hidden" name="newsletter_action" value="0" />
                    <input type="text" class="inputNew form-control newsletter-input" id="newsletter-input" name="email" placeholder="{l s='Your email address' mod='blocknewsletter'}" />
                    <div class="message-block" style="display: none;"></div>
                    {* Hook added for GDPR *}
                    {if isset($id_module)}
                        {hook h='displayGDPRConsent' id_module=$id_module}
                    {/if}
                    <button type="submit" name="submitNewsletter" class="btn button button-medium newsletter-btn">
                        <span>{l s='Subscribe' mod='blocknewsletter'}</span>
                    </button>
                    <span class="loader loading" style="display: none;"></span>
                </div>
            </form>
        </div>
        {hook h="displayBlockNewsletterBottom" from='blocknewsletter'}
    </section>
</div>

{addJsDefL name=no_internet_txt}{l s='No internet. Please try later.' mod='blocknewsletter' js=1}{/addJsDefL}

<!-- /Block Newsletter module-->
