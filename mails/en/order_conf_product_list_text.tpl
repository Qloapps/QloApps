{foreach $list as $product}

{$product['reference']}

{$product['name']}

{$product['unit_price']}

{$product['quantity']}

{$product['price']}


{foreach $product['customization'] as $customization}
{$product['name']}

{$customization['customization_text']}

{$product['unit_price']}

{$customization['customization_quantity']}

{$customization['quantity']}

{/foreach}
{/foreach}