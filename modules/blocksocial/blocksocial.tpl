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

<section class="col-md-3 col-12">
    <div class="footer-section-heading footer-section-item ">
        <span>{l s='SOCIAL LINK' mod='blocksocial'}</span>
    </div>
    <ul class="row list-style-none gap-3 mb-0 d-block blocksocial">
        {if isset($facebook_url) && $facebook_url != ''}
            <li class="facebook footer-section-item">
                <a class="_blank" href="{$facebook_url|escape:html:'UTF-8'}">
                    <i class="icon-brands icon-facebook"></i>
                    <span>{l s='Facebook' mod='blocksocial'}</span>
                </a>
            </li>
        {/if}
        {if isset($twitter_url) && $twitter_url != ''}
            <li class="twitter footer-section-item">
                <a class="_blank" href="{$twitter_url|escape:html:'UTF-8'}">
                    <i class="icon-brands icon-x-twitter"></i>
                    <span>{l s='Twitter' mod='blocksocial'}</span>
                </a>
            </li>
        {/if}
        {if isset($rss_url) && $rss_url != ''}
            <li class="rss footer-section-item">
                <a class="_blank" href="{$rss_url|escape:html:'UTF-8'}">
                    <i class="icon-rss"></i>
                    <span>{l s='RSS' mod='blocksocial'}</span>
                </a>
            </li>
        {/if}
        {if isset($youtube_url) && $youtube_url != ''}
            <li class="youtube footer-section-item">
                <a class="_blank" href="{$youtube_url|escape:html:'UTF-8'}">
                    <i class="icon-brands icon-youtube"></i>
                    <span>{l s='Youtube' mod='blocksocial'}</span>
                </a>
            </li>
        {/if}
        {if isset($pinterest_url) && $pinterest_url != ''}
            <li class="pinterest footer-section-item">
                <a class="_blank" href="{$pinterest_url|escape:html:'UTF-8'}">
                    <i class="icon-brands icon-pinterest"></i>
                    <span>{l s='Pinterest' mod='blocksocial'}</span>
                </a>
            </li>
        {/if}
        {if isset($vimeo_url) && $vimeo_url != ''}
            <li class="vimeo footer-section-item">
                <a class="_blank" href="{$vimeo_url|escape:html:'UTF-8'}">
                    <i class="icon-brands icon-vimeo"></i>
                    <span>{l s='Vimeo' mod='blocksocial'}</span>
                </a>
            </li>
        {/if}
        {if isset($instagram_url) && $instagram_url != ''}
            <li class="instagram footer-section-item">
                <a class="_blank" href="{$instagram_url|escape:html:'UTF-8'}">
                    <i class="icon-brands icon-square-instagram"></i>
                    <span>{l s='Instagram' mod='blocksocial'}</span>
                </a>
            </li>
        {/if}
    </ul>
</section>
