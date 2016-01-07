<ul>
{foreach from=$virtualProducts item=product}
	<li>
		<a href="{$product.link|escape:'html'}">{$product.name}</a>
		{if isset($product.deadline)}
			expires on {$product.deadline}
		{/if}
		{if isset($product.downloadable)}
			downloadable {$product.downloadable} time(s)
		{/if}
	</li>
{/foreach}
</ul>