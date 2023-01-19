{*
* 2010-2022 Webkul.
*
* NOTICE OF LICENSE
*
* All right is reserved,
* Please go through this link for complete license : https://store.webkul.com/license.html
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to https://store.webkul.com/customisation-guidelines/ for more information.
*
*  @author    Webkul IN <support@webkul.com>
*  @copyright 2010-2022 Webkul IN
*  @license   https://store.webkul.com/license.html
*}
{l s='Here are the bank details for your check:' mod='cheque' lang=$lang}

{l s='Amount:' mod='cheque' lang=$lang}  {literal}{total_paid}{/literal}
{l s='Payable to the order of:' mod='cheque' lang=$lang} {$cheque_name}
{l s='Please mail your check to:' mod='cheque' lang=$lang} {$cheque_address}
