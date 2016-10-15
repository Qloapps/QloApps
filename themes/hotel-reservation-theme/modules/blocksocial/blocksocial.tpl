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

<div class="row">
    <section class="col-xs-12 col-sm-12">
        <div class="row margin-lr-0 footer-section-heading">
            <p>{l s='Follow us on' mod='blocksocial'}</p>
            <hr/>
        </div>
        <div class="row margin-lr-0" id="social_block">
            <ul>
                {if isset($facebook_url) && $facebook_url != ''}
                    <li class="facebook">
                        <a class="_blank" href="{$facebook_url|escape:html:'UTF-8'}">
                            <span>{l s='Facebook' mod='blocksocial'}</span>
                        </a>
                    </li>
                {/if}
                {if isset($twitter_url) && $twitter_url != ''}
                    <li class="twitter">
                        <a class="_blank" href="{$twitter_url|escape:html:'UTF-8'}">
                            <span>{l s='Twitter' mod='blocksocial'}</span>
                        </a>
                    </li>
                {/if}
                {if isset($rss_url) && $rss_url != ''}
                    <li class="rss">
                        <a class="_blank" href="{$rss_url|escape:html:'UTF-8'}">
                            <span>{l s='RSS' mod='blocksocial'}</span>
                        </a>
                    </li>
                {/if}
                {if isset($youtube_url) && $youtube_url != ''}
                    <li class="youtube">
                        <a class="_blank" href="{$youtube_url|escape:html:'UTF-8'}">
                            <span>{l s='Youtube' mod='blocksocial'}</span>
                        </a>
                    </li>
                {/if}
                {if isset($google_plus_url) && $google_plus_url != ''}
                    <li class="google-plus">
                        <a class="_blank" href="{$google_plus_url|escape:html:'UTF-8'}">
                            <span>{l s='Google Plus' mod='blocksocial'}</span>
                        </a>
                    </li>
                {/if}
                {if isset($pinterest_url) && $pinterest_url != ''}
                    <li class="pinterest">
                        <a class="_blank" href="{$pinterest_url|escape:html:'UTF-8'}">
                            <span>{l s='Pinterest' mod='blocksocial'}</span>
                        </a>
                    </li>
                {/if}
                {if isset($vimeo_url) && $vimeo_url != ''}
                    <li class="vimeo">
                        <a class="_blank" href="{$vimeo_url|escape:html:'UTF-8'}">
                            <span>{l s='Vimeo' mod='blocksocial'}</span>
                        </a>
                    </li>
                {/if}
                {if isset($instagram_url) && $instagram_url != ''}
                    <li class="instagram">
                        <a class="_blank" href="{$instagram_url|escape:html:'UTF-8'}">
                            <span>{l s='Instagram' mod='blocksocial'}</span>
                        </a>
                    </li>
                {/if}
            </ul>
            <div class="clearfix"></div>
        </div>
    </section>
</div>