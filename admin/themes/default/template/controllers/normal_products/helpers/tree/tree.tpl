{*
* Since 2010 Webkul.
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
*  @copyright Since 2010 Webkul IN
*  @license   https://store.webkul.com/license.html
*}
{extends file="helpers/tree/tree.tpl"}
<script>
    {block name="script" append}
            $(document).ready(function () {
                $('#{$id|escape:'html':'UTF-8'}').find('[name="hotel_box[]"]').on('click', function(){
                    if ($(this).is(":checked")) {
                        $(this).closest('.tree-folder').find(':input[type=checkbox]').each(function(){
                            $(this).prop('checked', true);
                            $(this).parent().addClass('tree-selected');
                        });
                    } else {
                        $(this).closest('.tree-folder').find(':input[type=checkbox]').each(function(){
                            $(this).prop('checked', false);
                            $(this).parent().removeClass('tree-selected');
                        });
                    }
                });
            });

    {/block}
</script>