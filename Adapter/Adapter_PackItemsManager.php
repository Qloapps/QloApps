<?php
/**
 * 2007-2015 PrestaShop
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
 *  @author 	PrestaShop SA <contact@prestashop.com>
 *  @copyright  2007-2015 PrestaShop SA
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

class Adapter_PackItemsManager
{
    /**
     * Get the Products contained in the given Pack.
     *
     * @param Pack $pack
     * @param integer $id_lang Optional
     * @return Array[Product] The products contained in this Pack, with special dynamic attributes [pack_quantity, id_pack_product_attribute]
     */
    public function getPackItems($pack, $id_lang = false)
    {
        if ($id_lang === false) {
            $configuration = Adapter_ServiceLocator::get('Core_Business_ConfigurationInterface');
            $id_lang = (int)$configuration->get('PS_LANG_DEFAULT');
        }
        return Pack::getItems($pack->id, $id_lang);
    }
    
    /**
     * Get all Packs that contains the given item in the corresponding declination.
     *
     * @param Product $item
     * @param integer $item_attribute_id
     * @param integer $id_lang Optional
     * @return Array[Pack] The packs that contains the given item, with special dynamic attribute [pack_item_quantity]
     */
    public function getPacksContainingItem($item, $item_attribute_id, $id_lang = false)
    {
        if ($id_lang === false) {
            $configuration = Adapter_ServiceLocator::get('Core_Business_ConfigurationInterface');
            $id_lang = (int)$configuration->get('PS_LANG_DEFAULT');
        }
        return Pack::getPacksContainingItem($item->id, $item_attribute_id, $id_lang);
    }
    
    /**
     * Is this product a pack?
     *
     * @param Product $product
     * @return boolean
     */
    public function isPack($product)
    {
        return Pack::isPack($product->id);
    }
    
    /**
     * Is this product in a pack?
     * If $id_product_attribute specified, then will restrict search on the given combination,
     * else this method will match a product if at least one of all its combination is in a pack.
     *
     * @param Product $product
     * @param integer $id_product_attribute Optional combination of the product
     * @return boolean
     */
    public function isPacked($product, $id_product_attribute = false)
    {
        return Pack::isPacked($product->id, $id_product_attribute);
    }
    
}
