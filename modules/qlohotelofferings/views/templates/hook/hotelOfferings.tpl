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

{block name='offering_block'}
    {if isset($offeringData) && $offeringData}
        <div class="qlo-container-fluid qlo-full-width">
            <div id="hotelofferingBlock" class="home_block_container">
                <div>
                    {if $OFFERING_BLOCK_HEADING && $OFFERING_BLOCK_CONTENT}
                        <div class="home_block_desc_wrapper">
                            <div>
                                {block name='offering_block_heading'}
                                    <p class="home_block_heading">{$OFFERING_BLOCK_HEADING|escape:'htmlall':'UTF-8'}</p>
                                {/block}
                                {block name='offering_block_description'}
                                    <div class="block_description">
                                        {$OFFERING_BLOCK_CONTENT|escape:'htmlall':'UTF-8'}
                                        <hr class="block_desc_line"/>
                                    </div>
                                {/block}
                            </div>
                        </div>
                    {/if}
                    {block name='offering_block_content'}
                        <div class="home_block_content htlOfferings-owlCarousel qlo-container-fluid">
                            <div class="offering_container">
                                <div class="offering_cards_container">
                                    <div class="owl-carousel owl-theme owl-loaded">
                                        {foreach $offeringData as $offering}
                                            <div class="col-12">
                                                <div class="offering_card">
                                                    <div class="offering_content">
                                                        <img src="{$offering.img_url|escape:'htmlall':'UTF-8'}" alt="{$offering.name|escape:'htmlall':'UTF-8'}">
                                                    </div>
                                                    <div class="offering_details">
                                                        <div class="offering_name">{$offering.name}</div>
                                                        <div class="offering_details">{$offering.description|escape:'htmlall':'UTF-8'}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        {/foreach}
                                    </div>
                                </div>
                            </div>
                        </div>
                    {/block}
                </div>
            </div>
        </div>
        <hr class="block_seperator"/>
    {/if}
{/block}
