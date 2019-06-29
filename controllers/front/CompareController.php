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

class CompareControllerCore extends FrontController
{
    public $php_self = 'products-comparison';

    public function setMedia()
    {
        parent::setMedia();
        $this->addCSS(_THEME_CSS_DIR_.'comparator.css');
    }

    /**
     * Display ajax content (this function is called instead of classic display, in ajax mode)
     */
    public function displayAjax()
    {
        // Add or remove product with Ajax
        if (Tools::getValue('ajax') && Tools::getValue('id_product') && Tools::getValue('action')) {
            if (Tools::getValue('action') == 'add') {
                $id_compare = isset($this->context->cookie->id_compare) ? $this->context->cookie->id_compare: false;
                if (CompareProduct::getNumberProducts($id_compare) < Configuration::get('PS_COMPARATOR_MAX_ITEM')) {
                    CompareProduct::addCompareProduct($id_compare, (int)Tools::getValue('id_product'));
                } else {
                    $this->ajaxDie('0');
                }
            } elseif (Tools::getValue('action') == 'remove') {
                if (isset($this->context->cookie->id_compare)) {
                    CompareProduct::removeCompareProduct((int)$this->context->cookie->id_compare, (int)Tools::getValue('id_product'));
                } else {
                    $this->ajaxDie('0');
                }
            } else {
                $this->ajaxDie('0');
            }
            $this->ajaxDie('1');
        }
        $this->ajaxDie('0');
    }

    /**
     * Assign template vars related to page content
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        if (Tools::getValue('ajax')) {
            return;
        }
        parent::initContent();

        //Clean compare product table
        CompareProduct::cleanCompareProducts('week');

        $hasProduct = false;

        if (!Configuration::get('PS_COMPARATOR_MAX_ITEM')) {
            return Tools::redirect('index.php?controller=404');
        }

        $ids = null;
        if (($product_list = urldecode(Tools::getValue('compare_product_list'))) && ($postProducts = (isset($product_list) ? rtrim($product_list, '|') : ''))) {
            $ids = array_unique(explode('|', $postProducts));
        } elseif (isset($this->context->cookie->id_compare)) {
            $ids = CompareProduct::getCompareProducts($this->context->cookie->id_compare);
            if (count($ids)) {
                Tools::redirect($this->context->link->getPageLink('products-comparison', null, $this->context->language->id, array('compare_product_list' => implode('|', $ids))));
            }
        }

        if ($ids) {
            if (count($ids) > 0) {
                if (count($ids) > Configuration::get('PS_COMPARATOR_MAX_ITEM')) {
                    $ids = array_slice($ids, 0, Configuration::get('PS_COMPARATOR_MAX_ITEM'));
                }

                $listProducts = array();
                $listFeatures = array();

                foreach ($ids as $k => &$id) {
                    $curProduct = new Product((int)$id, true, $this->context->language->id);
                    if (!Validate::isLoadedObject($curProduct) || !$curProduct->active || !$curProduct->isAssociatedToShop()) {
                        if (isset($this->context->cookie->id_compare)) {
                            CompareProduct::removeCompareProduct($this->context->cookie->id_compare, $id);
                        }
                        unset($ids[$k]);
                        continue;
                    }

                    foreach ($curProduct->getFrontFeatures($this->context->language->id) as $feature) {
                        $listFeatures[$curProduct->id][$feature['id_feature']] = $feature['value'];
                    }

                    $cover = Product::getCover((int)$id);

                    $curProduct->id_image = Tools::htmlentitiesUTF8(Product::defineProductImage(array('id_image' => $cover['id_image'], 'id_product' => $id), $this->context->language->id));
                    $curProduct->allow_oosp = Product::isAvailableWhenOutOfStock($curProduct->out_of_stock);
                    $listProducts[] = $curProduct;
                }

                if (count($listProducts) > 0) {
                    $width = 80 / count($listProducts);

                    $hasProduct = true;
                    $ordered_features = Feature::getFeaturesForComparison($ids, $this->context->language->id);
                    $this->context->smarty->assign(array(
                        'ordered_features' => $ordered_features,
                        'product_features' => $listFeatures,
                        'products' => $listProducts,
                        'width' => $width,
                        'HOOK_COMPARE_EXTRA_INFORMATION' => Hook::exec('displayCompareExtraInformation', array('list_ids_product' => $ids)),
                        'HOOK_EXTRA_PRODUCT_COMPARISON' => Hook::exec('displayProductComparison', array('list_ids_product' => $ids)),
                        'homeSize' => Image::getSize(ImageType::getFormatedName('home'))
                    ));
                } elseif (isset($this->context->cookie->id_compare)) {
                    $object = new CompareProduct((int)$this->context->cookie->id_compare);
                    if (Validate::isLoadedObject($object)) {
                        $object->delete();
                    }
                }
            }
        }
        $this->context->smarty->assign('hasProduct', $hasProduct);

        $this->setTemplate(_PS_THEME_DIR_.'products-comparison.tpl');
    }
}
