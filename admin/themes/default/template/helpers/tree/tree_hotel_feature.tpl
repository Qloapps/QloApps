{*
* Copyright since 2010 Webkul.
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
*  @copyright since 2010 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

<div class="panel">
	{if isset($header)}{$header}{/if}
	{if isset($nodes)}
	<ul id="{$id|escape:'html':'UTF-8'}" class="cattree tree">
		{$nodes}
	</ul>
	{/if}
</div>
<script type="text/javascript">
	var currentToken="{$token|@addslashes}";
	var treeClickFunc = function() {
		var newURL = window.location.protocol + "//" + window.location.host + window.location.pathname;
		var queryString = window.location.search.replace(/&id_category=[0-9]*/, "") + "&id_category=" + $(this).val();
		location.href = newURL+queryString; // hash part is dropped: window.location.hash
	};

	{if isset($use_checkbox) && $use_checkbox == true}
		function checkAllAssociatedCategories($tree)
		{
			$tree.find(':input[type=checkbox]').each(function(){
				$(this).prop('checked', true);

				$(this).parent().addClass('tree-selected');
			});
		}

		function uncheckAllAssociatedCategories($tree)
		{
			$tree.find(':input[type=checkbox]').each(function(){
				$(this).prop('checked', false);

				$('select#id_category_default option[value='+$(this).val()+']').remove();
				if ($('select#id_category_default option').length == 0)
				{
					$('select#id_category_default').closest('.form-group').hide();
					$('#no_default_category').show();
				}

				$(this).parent().removeClass('tree-selected');
			});
		}

		function checkAllChildFeatures(node)
		{
			$(node).find(':input[type=checkbox]').each(function(){
				$(this).prop('checked', true);
				$(this).parent().addClass('tree-selected');
			});
		}

		function unCheckAllChildFeatures(node)
		{
			$(node).find(':input[type=checkbox]').each(function(){
				$(this).prop('checked', false);
				$(this).parent().removeClass('tree-selected');
			});
		}
	{/if}
	$('#{$id|escape:'html':'UTF-8'}').find('.select_feature_parent').on('click', function(){
		if ($(this).is(":checked")) {
			checkAllChildFeatures($(this).closest('li.tree-folder').find('ul.tree'));
		} else {
			unCheckAllChildFeatures($(this).closest('li.tree-folder').find('ul.tree'));
		}
	});

	$('#{$id|escape:'html':'UTF-8'}').find(':input[type=checkbox].select_child_feature').on('change', function(){
		checkParentFolder(this);
	});

	function checkParentFolder(element) {
		var totalNodes = $(element).closest('ul.tree').find('li.tree-item');
		var checkedNodes = $(element).closest('ul.tree').find('li.tree-item :input[type=checkbox].select_child_feature:checked');
		console.log(totalNodes.length);
		console.log(checkedNodes.length);
		if (totalNodes.length == checkedNodes.length) {
			$(element).closest('li.tree-folder').find(':input[type=checkbox].select_feature_parent').prop('checked', true).parent().addClass('tree-selected');
		} else {
			$(element).closest('li.tree-folder').find(':input[type=checkbox].select_feature_parent').prop('checked', false).parent().removeClass('tree-selected');
		}
	}
	function startTree() {
		if (typeof $.fn.tree === 'undefined') {
			setTimeout(startTree, 100);
			return;
		}

		$('#{$id|escape:'html':'UTF-8'}').find(':input[type=radio]').click(treeClickFunc);
		{if isset($selected_child_features)}
			$('#no_default_category').hide();
			{assign var=imploded_selected_child_features value='","'|implode:$selected_child_features}
			var selected_child_features = new Array("{$imploded_selected_child_features}");
			$('#{$id|escape:'html':'UTF-8'}').find(':input.select_child_feature').each(function(){
				if ($.inArray($(this).val(), selected_child_features) != -1)
				{
					$(this).prop("checked", true);
					$(this).parent().addClass("tree-selected");
					$(this).parents('ul.tree').each(function(){
						$(this).show();
						$(this).prev().find('.icon-folder-close').removeClass('icon-folder-close').addClass('icon-folder-open');
					});
				}
				checkParentFolder(this);
			});
		{/if}
		$('#{$id|escape:'html':'UTF-8'}').tree('collapseAll');
		$('#collapse-all-{$id|escape:'html':'UTF-8'}').hide();
	}
	startTree();
</script>
