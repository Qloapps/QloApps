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
<tr>
	<td class="box" style="border:1px solid #D6D4D4;background-color:#f8f8f8;padding:7px 0">
		<table class="table" style="width:100%">
			<tr>
				<td width="10" style="padding:7px 0">&nbsp;</td>
				<td style="padding:7px 0">
					<font size="2" face="Open-sans, sans-serif" color="#555454">
                        <p style="border-bottom:1px solid #D6D4D4;margin:3px 0 7px;text-transform:uppercase;font-weight:500;font-size:18px;padding-bottom:10px">
                            {l s='Here are the bank details for your transfer:' mod='bankwire' lang=$lang}
                        </p>
                        <span style="color:#777">
                            <span style="color:#333"><strong>{l s='Amount:' mod='bankwire' lang=$lang}</strong></span> {$total_paid}<br />
                            <span style="color:#333"><strong>{l s='Account owner:' mod='bankwire' lang=$lang}</strong></span> {$bankwire_owner}<br />
                            <span style="color:#333"><strong>{l s='Account details:' mod='bankwire' lang=$lang}</strong></span> {$bankwire_details}<br />
                            <span style="color:#333"><strong>{l s='Bank address:' mod='bankwire' lang=$lang}</strong></span> {$bankwire_address}
                        </span>
                    </font>
                </td>
                <td width="10" style="padding:7px 0">&nbsp;</td>
            </tr>
        </table>
    </td>
</tr>
<tr>
	<td class="space_footer" style="padding:0!important">&nbsp;</td>
</tr>