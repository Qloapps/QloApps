{if isset($list) && $list}

{l s='Image'}

{l s='Name'}

{l s='Unit Price'}

{l s='Qty'}

{l s='Total'}

{foreach from=$list key=key item=product}

<img src="{$product['cover_img']}" class="img-responsive" />

{$product['name']}{if isset($product['option_name']) && $product['option_name']} : {$product['option_name']}{/if}

{convertPrice price=$product['unit_price_tax_excl']}

{$product['quantity']}

{convertPrice price=$product['total_price_tax_excl']}

{/foreach}
{/if}
