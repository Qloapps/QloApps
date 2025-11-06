{if isset($list['service_products']) && $list['service_products']}

{l s='Service Products Detail'}


{l s="Product name"}

{if isset($list['is_hotel_products']) && $list['is_hotel_products']}
{l s="Hotel"}
{/if}

{l s="Quantity"}

{foreach $list['service_products'] as $product}
{$product['name']}{if isset($product['option_name']) && $product['option_name']} : {$product['option_name']}{/if}

{if isset($list['is_hotel_products']) && $list['is_hotel_products']}
{$product['hotel_name']}
{/if}

{$product['quantity']}

{/foreach}
{/if}