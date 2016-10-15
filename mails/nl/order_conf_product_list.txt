{foreach $list as $product}
						{$product['reference']}

						{$product['name']}

						{$product['price']}

						{$product['quantity']}

						{$product['price']}

	{foreach $product['customization'] as $customization}
							{$product['name']} {$customization['customization_text']}

							{$product['price']}

							{$product['customization_quantity']}

							{$product['quantity']}
	{/foreach}
{/foreach}