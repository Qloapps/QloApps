{*
* 2007-2014 PrestaShop
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
*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{include 'views/templates/front/header.tpl'}

<div style="width: 800px; height: 75px; background-color: #8AB50E; margin: 0 auto; color: white; font-family: arial; border-bottom: 5px solid #567500;">
    <div style="float: left; font-size: 36px;  margin-left: 20px; font-weight: bold; height: 75px; line-height: 75px; vertical-align: middle;">{l s='Affiliation space' mod='trackingfront'}</div>
    <div style="float: right; font-size: 18px; margin-right: 20px; font-weight: bold; height: 75px; line-height: 75px; vertical-align: middle;">
        <a href="{$smarty.server.PHP_SELF|escape:'html':'UTF-8'}?logout_tracking">{l s='Sign out' mod='trackingfront'}</a>
    </div>
</div>
<div style="width: 800px; height: 250px; background-color: #EEEEEE; margin: 0 auto; font-family: arial;">
    <div style="float:left; margin: 25px 0 0 50px;">
        <form action="{$smarty.server.REQUEST_URI|escape:'html':'UTF-8'}" method="post">
            <input type="submit" name="submitDateToday" class="button" value="{l s='Today' mod='trackingfront'}">
            <input type="submit" name="submitDateMonth" class="button" value="{l s='Month' mod='trackingfront'}">
            <input type="submit" name="submitDateYear" class="button" value="{l s='Year' mod='trackingfront'}">

            <p>{l s='From:' mod='trackingfront'} <input type="text" name="datepickerFrom" id="datepickerFrom"
                                                        value="{$datepickerFrom}"></p>

            <p>{l s='To:' mod='trackingfront'} <input type="text" name="datepickerTo" id="datepickerTo"
                                                      value="{$datepickerTo}"></p>
            <input type="submit" name="submitDatePicker" class="button"/>
        </form>
		<div>
			<a href="javascript:void(0)" onclick="showProductLines();" style="display:block;margin-top:20px;color:black">{l s='Show products details' mod='trackingfront'}</a>
		</div>
    </div>
    <div style="float:right; margin: 25px 50px 0 0;">
        <fieldset style="width: 415px; padding: 20px; font-size: 12px; border: 1px solid #88B60E;">
            <legend style="color: #88B60E; font-size:20px; font-weight: bold;">{$referrer->name}</legend>
            {foreach from=$displayTab key=data item=label}
                <div style="float:left; width: 150px; height: 20px; margin-right: 40px;"><span
                            style="float:left">{$label}</span><span id="{$data}"
                                                                    style="float: right; font-weight: bold;"></span>
                </div>
            {/foreach}
        </fieldset>
    </div>
</div>

<table style="width: 800px; margin: 0 auto; font-family: arial; font-size: 10px; border-bottom: 5px solid #8AB50E;"
       cellspacing="0" cellpadding="2">
    <tr style="background-color: #8AB50E; font-weight: bold; color: white; font-size: 12px;">
        <th>{l s='ID' mod='trackingfront'}</th>
        <th>{l s='Name' mod='trackingfront'}</th>
        <th>{l s='Visitors' mod='trackingfront'}</th>
        <th>{l s='Visits' mod='trackingfront'}</th>
        <th>{l s='Pages' mod='trackingfront'}</th>
        <th>{l s='Reg.' mod='trackingfront'}</th>
        <th>{l s='Ord.' mod='trackingfront'}</th>
        <th>{l s='Sales' mod='trackingfront'}</th>
        <th>{l s='Cart' mod='trackingfront'}</th>
        <th>{l s='Reg. rate' mod='trackingfront'}</th>
        <th>{l s='Ord. rate' mod='trackingfront'}</th>
        <th>{l s='Click' mod='trackingfront'}</th>
        <th>{l s='¤' mod='trackingfront'}</th>
        <th>{l s='%' mod='trackingfront'}</th>
    </tr>
    <tr id="trid_dummy">
        <td colspan="14" style="background: #567500;"/>
    </tr>
</table>

{include 'views/templates/front/footer.tpl'}
