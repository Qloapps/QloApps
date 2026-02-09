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

{extends file="helpers/list/list_header.tpl"}

{block name="list_filter_items" append}
	<div class="pull-right col-xs-4 col-sm-3 col-md-2 col-lg-2">
		<div class="list_availibility_container">
			<button type="button" class="btn btn-default btn-left btn-block dropdown-toggle" data-toggle="dropdown" data-target="MeaningStatus">
				<span>{l s='Thread Statuses'}
				<i class="icon-caret-down pull-right"></i>
			</button>
			<div id="MeaningStatus" class="dropdown-menu">
				<ul class="list-unstyled">
					<li><p><i class="icon-circle text-success"></i> {l s='Open'}</p></li>
					<li><p><i class="icon-circle text-danger"></i> {l s='Closed'}</p></li>
					<li><p><i class="icon-circle text-warning"></i> {l s='Pending 1'}</p></li>
					<li><p><i class="icon-circle text-warning"></i> {l s='Pending 2'}</p></li>
				</ul>
			</div>
		</div>
	</div>
{/block}
