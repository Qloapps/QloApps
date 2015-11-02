<?php
/*
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class SupplierControllerCore extends FrontController
{
    public $php_self = 'supplier';

    /** @var Supplier */
    protected $supplier;

    public function setMedia()
    {
        parent::setMedia();
        $this->addCSS(_THEME_CSS_DIR_.'product_list.css');
    }

    public function canonicalRedirection($canonicalURL = '')
    {
        if (Tools::getValue('live_edit')) {
            return;
        }
        if (Validate::isLoadedObject($this->supplier)) {
            parent::canonicalRedirection($this->context->link->getSupplierLink($this->supplier));
        }
    }

    /**
     * Initialize supplier controller
     * @see FrontController::init()
     */
    public function init()
    {
        parent::init();

        if ($id_supplier = (int)Tools::getValue('id_supplier')) {
            $this->supplier = new Supplier($id_supplier, $this->context->language->id);

            if (!Validate::isLoadedObject($this->supplier) || !$this->supplier->active) {
                header('HTTP/1.1 404 Not Found');
                header('Status: 404 Not Found');
                $this->errors[] = Tools::displayError('The chosen supplier does not exist.');
            } else {
                $this->canonicalRedirection();
            }
        }
    }

    /**
     * Assign template vars related to page content
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        parent::initContent();

        if (Validate::isLoadedObject($this->supplier) && $this->supplier->active && $this->supplier->isAssociatedToShop()) {
            $this->productSort(); // productSort must be called before assignOne
            $this->assignOne();
            $this->setTemplate(_PS_THEME_DIR_.'supplier.tpl');
        } else {
            $this->assignAll();
            $this->setTemplate(_PS_THEME_DIR_.'supplier-list.tpl');
        }
    }

    /**
     * Assign template vars if displaying one supplier
     */
    protected function assignOne()
    {
        if (Configuration::get('PS_DISPLAY_SUPPLIERS')) {
            $this->supplier->description = Tools::nl2br(trim($this->supplier->description));
            $nbProducts = $this->supplier->getProducts($this->supplier->id, null, null, null, $this->orderBy, $this->orderWay, true);
            $this->pagination((int)$nbProducts);

            $products = $this->supplier->getProducts($this->supplier->id, $this->context->cookie->id_lang, (int)$this->p, (int)$this->n, $this->orderBy, $this->orderWay);
            $this->addColorsToProductList($products);

            $this->context->smarty->assign(
                array(
                    'nb_products' => $nbProducts,
                    'products' => $products,
                    'path' => ($this->supplier->active ? Tools::safeOutput($this->supplier->name) : ''),
                    'supplier' => $this->supplier,
                    'comparator_max_item' => Configuration::get('PS_COMPARATOR_MAX_ITEM'),
                    'body_classes' => array(
                        $this->php_self.'-'.$this->supplier->id,
                        $this->php_self.'-'.$this->supplier->link_rewrite
                    )
                )
            );
        } else {
            Tools::redirect('index.php?controller=404');
        }
    }

    /**
     * Assign template vars if displaying the supplier list
     */
    protected function assignAll()
    {
        if (Configuration::get('PS_DISPLAY_SUPPLIERS')) {
            $result = Supplier::getSuppliers(true, $this->context->language->id, true);
            $nbProducts = count($result);
            $this->pagination($nbProducts);

            $suppliers = Supplier::getSuppliers(true, $this->context->language->id, true, $this->p, $this->n);
            foreach ($suppliers as &$row) {
                $row['image'] = (!file_exists(_PS_SUPP_IMG_DIR_.'/'.$row['id_supplier'].'-'.ImageType::getFormatedName('medium').'.jpg')) ? $this->context->language->iso_code.'-default' : $row['id_supplier'];
            }

            $this->context->smarty->assign(array(
                'pages_nb' => ceil($nbProducts / (int)$this->n),
                'nbSuppliers' => $nbProducts,
                'mediumSize' => Image::getSize(ImageType::getFormatedName('medium')),
                'suppliers_list' => $suppliers,
                'add_prod_display' => Configuration::get('PS_ATTRIBUTE_CATEGORY_DISPLAY'),
            ));
        } else {
            Tools::redirect('index.php?controller=404');
        }
    }
    
    /**
    * Get instance of current supplier
    */
    public function getSupplier()
    {
        return $this->supplier;
    }
}
