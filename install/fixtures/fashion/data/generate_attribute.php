<?php

$products = array(
    'Fur_Coat' => array(
        'Color' => array('Grey'),
        'Size' => array('S','M','L')
    ),
    'Textured_Trench_Coat' => array(
        'Color' => array('Taupe'),
        'Size' => array('S','M','L')
    ),
    'Slim_Cut_Coat' => array(
        'Color' => array('Grey'),
        'Size' => array('S','M','L')
    ),
    'Quilted_Jacket' => array(
        'Color' => array('Beige'),
        'Size' => array('S','M','L')
    ),
    'Suit_Blazer' => array(
        'Color' => array('Blue'),
        'Size' => array('S','M','L')
    ),
    'Short_Blazer' => array(
        'Color' => array('White'),
        'Size' => array('S','M','L')
    ),
    'Puffer_Jacket' => array(
        'Color' => array('Red'),
        'Size' => array('S','M','L')
    ),
    'Leather_Jacket_1' => array(
        'Color' => array('Black'),
        'Size' => array('S','M','L')
    ),
    'Leather_Jacket_2' => array(
        'Color' => array('Camel'),
        'Size' => array('S','M','L')
    ),
    'Leather_Jacket_3' => array(
        'Color' => array('Black'),
        'Size' => array('S','M','L')
    ),
    'Short_Sleeve_T-shirts' => array(
        'Color' => array('Green'),
        'Size' => array('S','M','L')
    ),
    'Faded_Short_Sleeve_T-shirts' => array(
        'Color' => array('Orange','Blue'),
        'Size' => array('S','M','L')
    ),
    'Cami_Tank_Top' => array(
        'Color' => array('Green'),
        'Size' => array('S','M','L')
    ),
    'Blouse' => array(
        'Color' => array('Black','White'),
        'Size' => array('S','M','L')
    ),
    'Leapoard_Printed_Blouse' => array(
        'Color' => array('White'),
        'Size' => array('S','M','L')
    ),
    'Faded_Jeans' => array(
        'Color' => array('Blue'),
        'Size' => array('S','M','L')
    ),
    '5_Pocket_Jean_1' => array(
        'Color' => array('Blue'),
        'Size' => array('S','M','L')
    ),
    '5_Pocket_Jean_2' => array(
        'Color' => array('Blue'),
        'Size' => array('S','M','L')
    ),
    'Dress_Gathered_At_The_Waist' => array(
        'Color' => array('Brown'),
        'Size' => array('S','M','L')
    ),
    'Printed_Dress' => array(
        'Color' => array('Orange'),
        'Size' => array('S','M','L')
    ),
    'Floral_Dress' => array(
        'Color' => array('White'),
        'Size' => array('S','M','L')
    ),
    'Strapless_Dress_1' => array(
        'Color' => array('Black'),
        'Size' => array('S','M','L')
    ),
    'Long_Evening_Dress' => array(
        'Color' => array('Red'),
        'Size' => array('S','M','L')
    ),
    'Short_Evening_Dress' => array(
        'Color' => array('Black'),
        'Size' => array('S','M','L')
    ),
    'Strapless_Dress_2' => array(
        'Size' => array('S','M','L')
    ),
    'Dress_with_Sleeves' => array(
        'Color' => array('Black'),
        'Size' => array('S','M','L')
    ),
    'Printed_Dress_2' => array(
        'Color' => array('Beige'),
        'Size' => array('S','M','L')
    ),
    'Printed_Summer_Dress_1' => array(
        'Color' => array('Yellow','Blue','Orange','Black'),
        'Size' => array('S','M','L')
    ),
    'Long_Printed_Dress' => array(
        'Color' => array('Grey'),
        'Size' => array('S','M','L')
    ),
    'Printed_Summer_Dress_2' => array(
        'Color' => array('Yellow'),
        'Size' => array('S','M','L')
    ),
    'Printed_Chiffon_Dress' => array(
        'Color' => array('Yellow'),
        'Size' => array('S','M','L')
    ),
    'Studded_Leather_Belt' => array(
        'Color' => array('Brown'),
        'Size' => array('S','M','L')
    ),
    'Braided_Belt' => array(
        'Color' => array('Black'),
        'Size' => array('S','M','L')
    ),
    'Bucket_Hat' => array(
        'Color' => array('Grey'),
        'Size' => array('One_size')
    ),
    'Floppy_Hat' => array(
        'Color' => array('Black'),
        'Size' => array('One_size')
    ),
    'Men_Straw_Hat' => array(
        'Color' => array('Beige'),
        'Size' => array('One_size')
    ),
    'Bowling_Bag' => array(
        'Color' => array('Red'),
        'Size' => array('One_size')
    ),
    'Leather_Bag' => array(
        'Color' => array('Beige'),
        'Size' => array('One_size')
    ),
    'Snake_Skin_Bag' => array(
        'Color' => array('Black'),
        'Size' => array('One_size')
    ),
    'Quilted_Bag' => array(
        'Color' => array('Black'),
        'Size' => array('One_size')
    ),
    'Suede_Bag' => array(
        'Color' => array('Camel'),
        'Size' => array('One_size')
    ),
    'Wedge_Shoe' => array(
        'Color' => array('Taupe'),
        'Shoes_Size' => array('35','36','37','38','39','40')
    )
);

$content_product_attribute = '';
$content_product_attribute_combination = '';

foreach ($products as $product => $attribute_groups) {
    $default_on = 1;
    $pa_id = 1;
    $combinations = createCombinations($attribute_groups);

    $pa_id = 1;
    $pac_id = 1;
    foreach ($combinations as $attributes) {
        foreach ($attributes as $attribute_value) {
            $content_product_attribute_combination .= '<product_attribute_combination id="pac_'.$pac_id.'" id_attribute="'.$attribute_value.'" id_product_attribute="pa_'.$product.'_'.$pa_id.'"/>'."\n";
            ++$pac_id;
        }
        $content_product_attribute .= '<product_attribute id="pa_'.$product.'_'.$pa_id.'" id_product="'.$product.'" reference="" supplier_reference="" ean13="" upc="" wholesale_price="0.000000" price="0.000000" ecotax="0.000000" quantity="100" weight="0" unit_price_impact="0.00" default_on="'.(string)$default_on.'" minimal_quantity="1" available_date="0000-00-00"><location/></product_attribute>'."\n";
        $default_on = 0;

        ++$pa_id;
    }
}

echo "This is an XML file, look at the source!\n\n";
echo $content_product_attribute;
echo "\n\n";
echo $content_product_attribute_combination;

function createCombinations($list)
{
    if (count($list) <= 1) {
        return count($list) ? array_map(create_function('$v', 'return (array($v));'), array_shift($list)) : $list;
    }
    $res = array();
    $first = array_pop($list);
    foreach ($first as $attribute) {
        $tab = createCombinations($list);
        foreach ($tab as $to_add) {
            $res[] = is_array($to_add) ? array_merge($to_add, array($attribute)) : array($to_add, $attribute);
        }
    }
    return $res;
}
