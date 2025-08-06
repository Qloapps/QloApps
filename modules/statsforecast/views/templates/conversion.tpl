{**
 * 2010-2022 Webkul.
 *
 * NOTICE OF LICENSE
 *
 * All right is reserved,
 * Please go through LICENSE.txt file inside our module
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please refer to CustomizationPolicy.txt file inside our module for more information.
 *
 * @author Webkul IN
 * @copyright 2010-2022 Webkul IN
 * @license LICENSE.txt
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
