{**
 * 2010-2023 Webkul.
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
 * @copyright 2010-2023 Webkul IN
 * @license LICENSE.txt
 *}

<script type="text/javascript">
    $(document).ready(function(){
        $('#translations_form input:text,textarea').each(function(){
            $(this).data('name',$(this).attr('name'));
            $(this).removeAttr('name');
        });
        $('#translations_form').on('change','input:text,textarea',function(){
            var name = $(this).data('name');
            if(name) $(this).attr('name',name);
        });
    });
</script>