{**
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License version 3.0
* that is bundled with this package in the file LICENSE.md
* It is also available through the world-wide-web at this URL:
* https://opensource.org/license/osl-3-0-php
*
* @author Webkul IN
* @copyright Since 2010 Webkul
* @license https://opensource.org/license/osl-3-0-php Open Software License version 3.0
*}

<script type="text/javascript">
$(document).ready(function() {
    {if isset($smarty.get.report) && $smarty.get.report}
    var form = document.getElementById('calendar_form');
    if (form && form.action.indexOf('&report=') === -1) {
        form.action += '&report=' + encodeURIComponent('{$smarty.get.report|escape:'html':'UTF-8'}');
    }
    {/if}

    $(document).on('change', '.qlo-report-filters select[name="id_hotel"]', function() {
        $(this).closest('form').submit();
    });
});
</script>

{include file=$group_tpl_path}
