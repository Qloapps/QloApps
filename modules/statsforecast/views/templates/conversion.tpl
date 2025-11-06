{**
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

 <div class="col-xs-12 col-sm-offset-2 col-sm-8 col-md-offset-1 col-md-10 col-lg-offset-3 col-lg-6">
 <div class="col-xs-12 graph-level">
     <div class="stat-boxes">
         <div class="stat-box" id="graph-visitors" data-connection-to='["stat-visitors-registered", "stat-visitors-unregistered"]'>
             <p class="text-center"><b>{l s='Visitors' mod='statsforecast'}</b></p>
             <p class="text-center">{$conversion_graph_data.values.visitors|escape:'html':'UTF-8'}</p>
         </div>
     </div>
 </div>
 <div class="col-xs-12 graph-level">
     <div class="stat-box" id="stat-visitors-registered" data-connection-to="stat-carts-registered">
         <p class="text-center"><b>{l s='Registered' mod='statsforecast'}</b></p>
         <p class="text-center">{$conversion_graph_data.values.visitors_registered|escape:'html':'UTF-8'} ({$conversion_graph_data.percentages.visitors_registered|escape:'html':'UTF-8'}%)</p>
     </div>
     <div class="stat-box" id="stat-visitors-unregistered" data-connection-to="stat-carts-unregistered">
         <p class="text-center"><b>{l s='Unregistered' mod='statsforecast'}</b></p>
         <p class="text-center">{$conversion_graph_data.values.visitors_unregistered|escape:'html':'UTF-8'} ({$conversion_graph_data.percentages.visitors_unregistered|escape:'html':'UTF-8'}%)</p>
     </div>
 </div>
 <div class="col-xs-12 graph-level">
     <div class="stat-box" id="stat-carts-registered" data-connection-to="stat-orders" data-percentage="{$conversion_graph_data.percentages.orders_top_registered|escape:'html':'UTF-8'}">
         <p class="text-center"><b>{l s='Carts' mod='statsforecast'}</b></p>
         <p class="text-center">{$conversion_graph_data.values.carts_registered|escape:'html':'UTF-8'}</p>
     </div>
     <div class="stat-box" id="stat-carts-unregistered" data-connection-to="stat-orders" data-percentage="{$conversion_graph_data.percentages.orders_top_unregistered|escape:'html':'UTF-8'}">
         <p class="text-center"><b>{l s='Carts' mod='statsforecast'}</b></p>
         <p class="text-center">{$conversion_graph_data.values.carts_unregistered|escape:'html':'UTF-8'}</p>
     </div>
 </div>
 <div class="col-xs-12 graph-level">
     <div class="stat-boxes">
         <div class="stat-box" id="stat-orders" data-connection-to='["stat-orders-registered", "stat-orders-unregistered"]'>
             <p class="text-center"><b>{l s='Orders' mod='statsforecast'}</b></p>
             <p class="text-center">{$conversion_graph_data.values.orders|escape:'html':'UTF-8'}</p>
         </div>
     </div>
 </div>
 <div class="col-xs-12 graph-level">
     <div class="stat-box" id="stat-orders-registered">
         <p class="text-center"><b>{l s='Registered' mod='statsforecast'}</b></p>
         <p class="text-center">{$conversion_graph_data.values.orders_registered|escape:'html':'UTF-8'} ({$conversion_graph_data.percentages.orders_bottom_registered|escape:'html':'UTF-8'}%)</p>
     </div>
     <div class="stat-box" id="stat-orders-unregistered">
         <p class="text-center"><b>{l s='Unregistered' mod='statsforecast'}</b></p>
         <p class="text-center">{$conversion_graph_data.values.orders_unregistered|escape:'html':'UTF-8'} ({$conversion_graph_data.percentages.orders_bottom_unregistered|escape:'html':'UTF-8'}%)</p>
     </div>
 </div>
</div>
