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

class DiscountControllerCore extends FrontController
{
    public $auth = true;
    public $php_self = 'discount';
    public $authRedirection = 'discount';
    public $ssl = true;

    /**
     * Assign template vars related to page content
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        parent::initContent();

        $cart_rules = CartRule::getCustomerCartRules($this->context->language->id, $this->context->customer->id, true, false, true);

        foreach ($cart_rules as $key => &$discount ) {

            if ((int)$discount['quantity_for_user'] === 0) {
                unset($cart_rules[$key]);
            }

            $discount['value'] = Tools::convertPriceFull(
                $discount['value'],
                new Currency((int)$discount['reduction_currency']),
                new Currency((int)$this->context->cart->id_currency)
            );
            
            if ((int)$discount['gift_product'] !== 0) {
                $product = new Product((int) $discount['gift_product'], false, (int)$this->context->language->id);
                if (!Validate::isLoadedObject($product) || !$product->isAssociatedToShop() || !$product->active) {
                    unset($cart_rules[$key]);
                }
                if (Combination::isFeatureActive() && (int)$discount['gift_product_attribute'] !== 0) {
                    $attributes = $product->getAttributeCombinationsById((int)$discount['gift_product_attribute'], (int)$this->context->language->id);
                    $giftAttributes = array();
                    foreach ($attributes as $attribute) {
                        $giftAttributes[] = $attribute['group_name'] . ' : ' . $attribute['attribute_name'];
                    }
                    $discount['gift_product_attributes'] = implode(', ', $giftAttributes);
                }
                $discount['gift_product_name'] = $product->name;
                $discount['gift_product_link'] = $this->context->link->getProductLink(
                    $product,
                    $product->link_rewrite,
                    $product->category,
                    $product->ean13,
                    $this->context->language->id,
                    $this->context->shop->id,
                    $discount['gift_product_attribute'],
                    false,
                    false,
                    true
                );
            }
        }

        $nb_cart_rules = count($cart_rules);

        $this->context->smarty->assign(array(
            'nb_cart_rules' => (int)$nb_cart_rules,
            'cart_rules' => $cart_rules,
            'discount' => $cart_rules, // retro compatibility
            'nbDiscounts' => (int)$nb_cart_rules // retro compatibility
        ));

        $this->setTemplate(_PS_THEME_DIR_.'discount.tpl');
    }
}
