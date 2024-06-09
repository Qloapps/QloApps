<?php
/*
* 2007-2017 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2017 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/
if (!defined('_PS_ADMIN_DIR_')) {
    define('_PS_ADMIN_DIR_', getcwd());
}
include(_PS_ADMIN_DIR_.'/../config/config.inc.php');
/* Getting cookie or logout */
require_once(_PS_ADMIN_DIR_.'/init.php');

$query = Tools::getValue('q', false);
if (!$query or $query == '' or strlen($query) < 1) {
    die();
}

/*
 * In the SQL request the "q" param is used entirely to match result in database.
 * In this way if string:"(ref : #ref_pattern#)" is displayed on the return list,
 * they are no return values just because string:"(ref : #ref_pattern#)"
 * is not write in the name field of the product.
 * So the ref pattern will be cut for the search request.
 */
if ($pos = strpos($query, ' (ref:')) {
    $query = substr($query, 0, $pos);
}

$excludeIds = Tools::getValue('excludeIds', false);
if ($excludeIds && $excludeIds != 'NaN') {
    $excludeIds = implode(',', array_map('intval', explode(',', $excludeIds)));
} else {
    $excludeIds = '';
    $excludePackItself= Tools::getValue('packItself', false);
}

// Excluding downloadable products from packs because download from pack is not supported
$excludeVirtuals = (bool)Tools::getValue('excludeVirtuals', true);
$exclude_packs = (bool)Tools::getValue('exclude_packs', true);

$bookingProduct = (int) Tools::getValue('booking_product');

$idHotel = (int) Tools::getValue('id_hotel');

$context = Context::getContext();

$sqlSelect = 'SELECT p.`id_product`, pl.`link_rewrite`, p.`reference`, pl.`name`, image_shop.`id_image` id_image, il.`legend`, p.`cache_default_attribute`';

$sqlFrom = ' FROM `'._DB_PREFIX_.'product` p '.Shop::addSqlAssociation('product', 'p');

$sqlJoin ='	LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (pl.`id_product` = p.`id_product` AND pl.`id_lang` = '.(int) $context->language->id.Shop::addSqlRestrictionOnLang('pl').')
		LEFT JOIN `'._DB_PREFIX_.'image_shop` image_shop
			ON (image_shop.`id_product` = p.`id_product` AND image_shop.`cover`=1 AND image_shop.`id_shop`='.(int) $context->shop->id.')
		LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (image_shop.`id_image` = il.`id_image` AND il.`id_lang` = '.(int) $context->language->id.')';

$sqlWhere =	' WHERE (pl.name LIKE \'%'.pSQL($query).'%\' OR p.reference LIKE \'%'.pSQL($query).'%\')'.
        (!empty($excludeIds) ? ' AND p.id_product NOT IN ('.$excludeIds.') ' : ' ').
        (!empty($excludePackItself) ? ' AND p.id_product <> '.$excludePackItself.' ' : ' ').
        ($excludeVirtuals ? 'AND NOT EXISTS (SELECT 1 FROM `'._DB_PREFIX_.'product_download` pd WHERE (pd.id_product = p.id_product))' : '').
        ($exclude_packs ? 'AND (p.cache_is_pack IS NULL OR p.cache_is_pack = 0)' : '').
        ($bookingProduct ? 'AND p.`booking_product` = '.(int) $bookingProduct : '');

$sqlGroupBy = ' GROUP BY p.id_product';

if ($idHotel && $bookingProduct) {
    // To get hotel info for the booking product only
    $sqlSelect .= ', hbil.`hotel_name`, hbil.`id` AS id_hotel ';
    $sqlJoin .= '  LEFT JOIN `'._DB_PREFIX_.'htl_room_type` hrt
            ON hrt.`id_product` = p.`id_product`
            LEFT JOIN `'._DB_PREFIX_.'htl_branch_info_lang` hbil
            ON  pl.`id_lang` = hbil.`id_lang` AND hbil.`id` = hrt.`id_hotel`';
    $sqlWhere .= ' AND hbil.`id`='.(int) $idHotel ;
}

$sql = $sqlSelect.' '.$sqlFrom.' '.$sqlJoin.' '.$sqlWhere.' '.$sqlGroupBy;

$items = Db::getInstance()->executeS($sql);

if ($items && ($excludeIds || strpos($_SERVER['HTTP_REFERER'], 'AdminScenes') !== false)) {
    foreach ($items as $item) {
    	$item['name'] = str_replace('|', '&#124;', $item['name']);
        echo trim($item['name']).(!empty($item['reference']) ? ' (ref: '.$item['reference'].')' : '').'|'.(int)($item['id_product'])."\n";
    }
} elseif ($items) {
    // packs
    $results = array();
    foreach ($items as $item) {
        // check if product have combination
        if (Combination::isFeatureActive() && $item['cache_default_attribute']) {
            $sql = 'SELECT pa.`id_product_attribute`, pa.`reference`, ag.`id_attribute_group`, pai.`id_image`, agl.`name` AS group_name, al.`name` AS attribute_name,
						a.`id_attribute`
					FROM `'._DB_PREFIX_.'product_attribute` pa
					'.Shop::addSqlAssociation('product_attribute', 'pa').'
					LEFT JOIN `'._DB_PREFIX_.'product_attribute_combination` pac ON pac.`id_product_attribute` = pa.`id_product_attribute`
					LEFT JOIN `'._DB_PREFIX_.'attribute` a ON a.`id_attribute` = pac.`id_attribute`
					LEFT JOIN `'._DB_PREFIX_.'attribute_group` ag ON ag.`id_attribute_group` = a.`id_attribute_group`
					LEFT JOIN `'._DB_PREFIX_.'attribute_lang` al ON (a.`id_attribute` = al.`id_attribute` AND al.`id_lang` = '.(int)$context->language->id.')
					LEFT JOIN `'._DB_PREFIX_.'attribute_group_lang` agl ON (ag.`id_attribute_group` = agl.`id_attribute_group` AND agl.`id_lang` = '.(int)$context->language->id.')
					LEFT JOIN `'._DB_PREFIX_.'product_attribute_image` pai ON pai.`id_product_attribute` = pa.`id_product_attribute`
					WHERE pa.`id_product` = '.(int)$item['id_product'].'
					GROUP BY pa.`id_product_attribute`, ag.`id_attribute_group`
					ORDER BY pa.`id_product_attribute`';

            $combinations = Db::getInstance()->executeS($sql);
            if (!empty($combinations)) {
                foreach ($combinations as $k => $combination) {
                    $results[$combination['id_product_attribute']]['id'] = $item['id_product'];
                    $results[$combination['id_product_attribute']]['id_product_attribute'] = $combination['id_product_attribute'];
                    !empty($results[$combination['id_product_attribute']]['name']) ? $results[$combination['id_product_attribute']]['name'] .= ' '.$combination['group_name'].'-'.$combination['attribute_name']
                    : $results[$combination['id_product_attribute']]['name'] = $item['name'].' '.$combination['group_name'].'-'.$combination['attribute_name'];
                    if (!empty($combination['reference'])) {
                        $results[$combination['id_product_attribute']]['ref'] = $combination['reference'];
                    } else {
                        $results[$combination['id_product_attribute']]['ref'] = !empty($item['reference']) ? $item['reference'] : '';
                    }
                    if (empty($results[$combination['id_product_attribute']]['image'])) {
                        $results[$combination['id_product_attribute']]['image'] = str_replace('http://', Tools::getShopProtocol(), $context->link->getImageLink($item['link_rewrite'], $combination['id_image'], 'home_default'));
                    }
                }
            } else {
                $product = array(
                    'id' => (int)($item['id_product']),
                    'name' => $item['name'],
                    'ref' => (!empty($item['reference']) ? $item['reference'] : ''),
                    'image' => str_replace('http://', Tools::getShopProtocol(), $context->link->getImageLink($item['link_rewrite'], $item['id_image'], 'home_default')),
                );
                array_push($results, $product);
            }
        } else {
            $product = array(
                'id' => (int)($item['id_product']),
                'name' => $item['name'],
                'ref' => (!empty($item['reference']) ? $item['reference'] : ''),
                'image' => str_replace('http://', Tools::getShopProtocol(), $context->link->getImageLink($item['link_rewrite'], $item['id_image'], 'home_default')),
            );
            array_push($results, $product);
        }
    }
    $results = array_values($results);
    echo json_encode($results);
} else {
    json_encode(new stdClass);
}
