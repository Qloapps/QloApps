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
{if isset($js_def) && is_array($js_def) && $js_def|@count}
<script type="text/javascript">
{foreach from=$js_def key=k item=def}
{if !empty($k) && is_string($k)}
{if is_bool($def)}
var {$k} = {$def|var_export:true};
{elseif is_int($def)}
var {$k} = {$def|intval};
{elseif is_float($def)}
var {$k} = {$def|floatval|replace:',':'.'};
{elseif is_string($def)}
var {$k} = '{$def|strval}';
{elseif is_array($def) || is_object($def)}
var {$k} = {$def|json_encode};
{elseif is_null($def)}
var {$k} = null;
{else}
var {$k} = '{$def|@addcslashes:'\''}';
{/if}
{/if}
{/foreach}
</script>
{/if}
{if isset($js_files) && $js_files|@count}
{foreach from=$js_files key=k item=js_uri}
<script type="text/javascript" src="{$js_uri}"></script>
{/foreach}
{/if}
{if isset($js_inline) && $js_inline|@count}
<script type="text/javascript">
{foreach from=$js_inline key=k item=inline}
{$inline}
{/foreach}
</script>
{/if}
