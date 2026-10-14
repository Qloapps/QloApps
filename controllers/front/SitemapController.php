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

class SitemapControllerCore extends FrontController
{
    public $php_self = 'sitemap';

    public function setMedia()
    {
        parent::setMedia();
        $this->addCSS(_THEME_CSS_DIR_.'sitemap.css');
        $this->addJS(_THEME_JS_DIR_.'tools/treeManagement.js');
    }

    /**
     * Assign template vars related to page content
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        parent::initContent();

        $categoriesTree = Category::getRootCategory()->recurseLiteCategTree(0);
        $this->addRoomTypes($categoriesTree);
        $this->context->smarty->assign('categoriesTree', $categoriesTree);
        $this->context->smarty->assign('categoriescmsTree', CMSCategory::getRecurseCategory($this->context->language->id, 1, 1, 1));
        $this->context->smarty->assign('voucherAllowed', (int)CartRule::isFeatureActive());
        $this->context->smarty->assign('PS_DISPLAY_BEST_SELLERS', Configuration::get('PS_DISPLAY_BEST_SELLERS'));
        $this->context->smarty->assign('display_store', Configuration::get('PS_STORES_DISPLAY_SITEMAP'));

        $this->setTemplate(_PS_THEME_DIR_.'sitemap.tpl');
    }

    /**
     * Add the room types of each hotel to the category tree (key room_types of the hotel category node).
     *
     * @param array $node Category tree node, updated by reference
     */
    protected function addRoomTypes(&$node)
    {
        static $hotelCategories = null;
        if ($hotelCategories === null) {
            $hotelCategories = array();
            $rows = Db::getInstance()->executeS(
                'SELECT `id`, `id_category` FROM `'._DB_PREFIX_.'htl_branch_info` WHERE `active` = 1'
            );
            foreach ((array) $rows as $row) {
                $hotelCategories[(int) $row['id_category']] = (int) $row['id'];
            }
        }

        if (isset($hotelCategories[$node['id']])) {
            $idLang = (int) $this->context->language->id;
            $idShop = (int) $this->context->shop->id;
            $node['room_types'] = array();
            $rows = Db::getInstance()->executeS(
                'SELECT hrt.`id_product` FROM `'._DB_PREFIX_.'htl_room_type` hrt
                INNER JOIN `'._DB_PREFIX_.'product_shop` ps ON (ps.`id_product` = hrt.`id_product` AND ps.`id_shop` = '.$idShop.')
                WHERE hrt.`id_hotel` = '.(int) $hotelCategories[$node['id']].' AND ps.`active` = 1 AND ps.`visibility` <> \'none\'
                ORDER BY hrt.`id_product`'
            );
            foreach ((array) $rows as $row) {
                $product = new Product((int) $row['id_product'], false, $idLang);
                if (!$product->link_rewrite) {
                    continue;
                }
                $node['room_types'][] = array(
                    'name' => $product->name,
                    'link' => $this->context->link->getProductLink($product, $product->link_rewrite, null, null, $idLang, $idShop),
                );
            }
        }

        foreach ($node['children'] as &$child) {
            $this->addRoomTypes($child);
        }
    }
}
