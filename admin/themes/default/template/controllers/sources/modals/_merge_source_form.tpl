{*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License version 3.0
* that is bundled with this package in the file LICENSE.md
*
* @author Webkul IN
* @copyright Since 2010 Webkul
* @license https://opensource.org/license/osl-3-0-php Open Software License version 3.0
*}
<form action="{$current_index}&token={$token}" method="post">
	<div class="modal-body">
		<div class="form-group">
			<label>{l s='Current Source'}</label>
			<input type="text" name="merge_current_source" id="merge_current_source" class="form-control" readonly="true"  value="{$current_source->name}"/>	
		</div>
		<div class="form-group">
			<label>{l s='Target Source'}</label>
			<select name="target_source" id="target_source" class="form-control">
				{foreach from=$sources item=source}
					<option value="{$source.id_source}">{$source.name}</option>
				{/foreach}
			</select>
		</div>
		<p class="help-block">{l s='Every order currently attributed to Current Source will be reattributed to Target Source. Current Source itself is left as-is.'}</p>
		<input type="hidden" name="id_source_type" value="{$id_source_type|intval}" />
		<input type="hidden" name="current_source" value="{$current_source->id|intval}" />
	</div>
	<div class="modal-footer">
		<button type="button" class="btn btn-default" data-dismiss="modal">{l s='Cancel'}</button>
		<button type="submit" name="submitMergeSource" class="btn btn-primary"> <i class="icon-random"></i> &nbsp  {l s='Update'}</button>
	</div>
</form>
