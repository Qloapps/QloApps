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

<div id="product-options" class="panel product-tab">
	<input type="hidden" name="submitted_tabs[]" value="Options" />
	<h3 class="tab"> <i class="icon-info"></i> {l s='Options'}</h3>

    <div class="table-responsive">
        <table id="product_options_table" class="table table-striped">
            <thead>
                <th>{l s='Name'}</th>
                <th>{l s='Price Impact'}</th>
                <th>{l s='Action'}</th>
            </thead>
            <tbody>
                {if $serviceProductOptions}
                    {foreach $serviceProductOptions as $productOption}
                        <tr>
                            <td>
                                <input class="form-control" type="text" name="product_option_name[]" value="{$productOption['name']}">
                            </td>
                            <td>
                                <div class="input-group">
                                    <span class="input-group-addon">{$currency->prefix}{$currency->suffix}</span>
                                    <input type="text" name="product_option_price[]" value="{$productOption['price_impact']}"/>
                                    <input type="hidden" name="product_option_id[]" value="{$productOption['id_product_option']}"/>
                                </div>
                            </td>
                            <td>
                                <a href="#" class="btn btn-default delete_product_option" data-id_product_option="{$productOption['id_product_option']}"><i class="icon-trash"></i></a>
                            </td>
                        </tr>
                    {/foreach}
                {/if}
                <tr>
                    <td>
                        <input class="form-control" type="text" name="product_option_name[]">
                    </td>
                    <td>
                        <div class="input-group">
                            <span class="input-group-addon">{$currency->prefix}{$currency->suffix}</span>
                            <input type="text" name="product_option_price[]" value=""/>
                            <input type="hidden" name="product_option_id[]" value="0"/>
                        </div>
                    </td>
                    <td>
                    </td>
                </tr>
                <tr>
                    <td>
                        <input class="form-control" type="text" name="product_option_name[]">
                    </td>
                    <td>
                        <div class="input-group">
                            <span class="input-group-addon">{$currency->prefix}{$currency->suffix}</span>
                            <input type="text" name="product_option_price[]" value=""/>
                        </div>
                    </td>
                    <td>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

	<div class="panel-footer">
		<a href="{$link->getAdminLink('AdminNormalProducts')|escape:'html':'UTF-8'}{if isset($smarty.request.page) && $smarty.request.page > 1}&amp;submitFilterproduct={$smarty.request.page|intval}{/if}" class="btn btn-default"><i class="process-icon-cancel"></i> {l s='Cancel'}</a>
		<button type="submit" name="submitAddproduct" class="btn btn-default pull-right" disabled="disabled"><i class="process-icon-loading"></i> {l s='Save'}</button>
		<button type="submit" name="submitAddproductAndStay" class="btn btn-default pull-right" disabled="disabled"><i class="process-icon-loading"></i> {l s='Save and stay'}</button>
	</div>
</div>


<script>
    var service_product_link = "{$link->getAdminLink('AdminNormalProducts')}";
    var option_delete_success_txt = '{l s='Product option is deleted successfully' js=1}';
    $(document).ready(function() {
        $(document).on('click', '.delete_product_option', function(e){
            e.preventDefault();
            var $current = $(this);
            var id_product_option = $(this).attr('data-id_product_option');
            $.ajax({
                url: service_product_link,
                type: 'POST',
                dataType: 'JSON',
                data: {
                    ajax:true,
                    action:'deleteServiceProductOption',
                    id_product_option: id_product_option,
                },
                success: function (response) {
                    if (response.hasError) {
                        showErrorMessage(response.error);
                    } else {
                        $current.closest('tr').remove();
                        showSuccessMessage(option_delete_success_txt);
                    }
                }
            });
        });
    });
</script>