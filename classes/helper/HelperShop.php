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
 *  @author     PrestaShop SA <contact@prestashop.com>
 *  @copyright  2007-2015 PrestaShop SA
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

class HelperShopCore extends Helper
{
    /**
     * Render shop list
     *
     * @return string
     */
    public function getRenderedShopList()
    {
        if (!Shop::isFeatureActive() || Shop::getTotalShops(false, null) < 2) {
            return '';
        }

        $shop_context = Shop::getContext();
        $context = Context::getContext();
        $tree = Shop::getTree();

        if ($shop_context == Shop::CONTEXT_ALL || ($context->controller->multishop_context_group == false && $shop_context == Shop::CONTEXT_GROUP)) {
            $current_shop_value = '';
            $current_shop_name = Translate::getAdminTranslation('All shops');
        } elseif ($shop_context == Shop::CONTEXT_GROUP) {
            $current_shop_value = 'g-'.Shop::getContextShopGroupID();
            $current_shop_name = sprintf(Translate::getAdminTranslation('%s group'), $tree[Shop::getContextShopGroupID()]['name']);
        } else {
            $current_shop_value = 's-'.Shop::getContextShopID();

            foreach ($tree as $group_id => $group_data) {
                foreach ($group_data['shops'] as $shop_id => $shop_data) {
                    if ($shop_id == Shop::getContextShopID()) {
                        $current_shop_name = $shop_data['name'];
                        break;
                    }
                }
            }
        }

        $tpl = $this->createTemplate('helpers/shops_list/list.tpl');
        $tpl->assign(array(
            'tree' => $tree,
            'current_shop_name' => $current_shop_name,
            'current_shop_value' => $current_shop_value,
            'multishop_context' => $context->controller->multishop_context,
            'multishop_context_group' => $context->controller->multishop_context_group,
            'is_shop_context'  => ($context->controller->multishop_context & Shop::CONTEXT_SHOP),
            'is_group_context' => ($context->controller->multishop_context & Shop::CONTEXT_GROUP),
            'shop_context' => $shop_context,
            'url' => $_SERVER['REQUEST_URI'].(($_SERVER['QUERY_STRING']) ? '&' : '?').'setShopContext='
        ));

        return $tpl->fetch();
    }
}
