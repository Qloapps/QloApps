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

class SearchControllerCore extends FrontController
{
    public $php_self = 'search';
    public $instant_search;
    public $ajax_search;

    /**
     * Initialize search controller
     * @see FrontController::init()
     */
    public function init()
    {
        parent::init();

        $this->instant_search = Tools::getValue('instantSearch');

        $this->ajax_search = Tools::getValue('ajaxSearch');

        if ($this->instant_search || $this->ajax_search) {
            $this->display_header = false;
            $this->display_footer = false;
        }
    }

    /**
     * Assign template vars related to page content
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        $original_query = Tools::getValue('q');
        $query = Tools::replaceAccentedChars(urldecode($original_query));
        if ($this->ajax_search) {
            $searchResults = Search::find((int)(Tools::getValue('id_lang')), $query, 1, 10, 'position', 'desc', true);
            if (is_array($searchResults)) {
                foreach ($searchResults as &$product) {
                    $product['product_link'] = $this->context->link->getProductLink($product['id_product'], $product['prewrite'], $product['crewrite']);
                }
                Hook::exec('actionSearch', array('expr' => $query, 'total' => count($searchResults)));
            }
            $this->ajaxDie(json_encode($searchResults));
        }

        //Only controller content initialization when the user use the normal search
        parent::initContent();

        $product_per_page = isset($this->context->cookie->nb_item_per_page) ? (int)$this->context->cookie->nb_item_per_page : Configuration::get('PS_PRODUCTS_PER_PAGE');

        if ($this->instant_search && !is_array($query)) {
            $this->productSort();
            $this->n = abs((int)(Tools::getValue('n', $product_per_page)));
            $this->p = abs((int)(Tools::getValue('p', 1)));
            $search = Search::find($this->context->language->id, $query, 1, 10, 'position', 'desc');
            Hook::exec('actionSearch', array('expr' => $query, 'total' => $search['total']));
            $nbProducts = $search['total'];
            $this->pagination($nbProducts);

            $this->addColorsToProductList($search['result']);

            $this->context->smarty->assign(array(
                'products' => $search['result'], // DEPRECATED (since to 1.4), not use this: conflict with block_cart module
                'search_products' => $search['result'],
                'nbProducts' => $search['total'],
                'search_query' => $original_query,
                'instant_search' => $this->instant_search,
                'homeSize' => Image::getSize(ImageType::getFormatedName('home'))));
        } elseif (($query = Tools::getValue('search_query', Tools::getValue('ref'))) && !is_array($query)) {
            $this->productSort();
            $this->n = abs((int)(Tools::getValue('n', $product_per_page)));
            $this->p = abs((int)(Tools::getValue('p', 1)));
            $original_query = $query;
            $query = Tools::replaceAccentedChars(urldecode($query));
            $search = Search::find($this->context->language->id, $query, $this->p, $this->n, $this->orderBy, $this->orderWay);
            if (is_array($search['result'])) {
                foreach ($search['result'] as &$product) {
                    $product['link'] .= (strpos($product['link'], '?') === false ? '?' : '&').'search_query='.urlencode($query).'&results='.(int)$search['total'];
                }
            }

            Hook::exec('actionSearch', array('expr' => $query, 'total' => $search['total']));
            $nbProducts = $search['total'];
            $this->pagination($nbProducts);

            $this->addColorsToProductList($search['result']);

            $this->context->smarty->assign(array(
                'products' => $search['result'], // DEPRECATED (since to 1.4), not use this: conflict with block_cart module
                'search_products' => $search['result'],
                'nbProducts' => $search['total'],
                'search_query' => $original_query,
                'homeSize' => Image::getSize(ImageType::getFormatedName('home'))));
        } elseif (($tag = urldecode(Tools::getValue('tag'))) && !is_array($tag)) {
            $nbProducts = (int)(Search::searchTag($this->context->language->id, $tag, true));
            $this->pagination($nbProducts);
            $result = Search::searchTag($this->context->language->id, $tag, false, $this->p, $this->n, $this->orderBy, $this->orderWay);
            Hook::exec('actionSearch', array('expr' => $tag, 'total' => count($result)));

            $this->addColorsToProductList($result);

            $this->context->smarty->assign(array(
                'search_tag' => $tag,
                'products' => $result, // DEPRECATED (since to 1.4), not use this: conflict with block_cart module
                'search_products' => $result,
                'nbProducts' => $nbProducts,
                'homeSize' => Image::getSize(ImageType::getFormatedName('home'))));
        } else {
            $this->context->smarty->assign(array(
                'products' => array(),
                'search_products' => array(),
                'pages_nb' => 1,
                'nbProducts' => 0));
        }
        $this->context->smarty->assign(array('add_prod_display' => Configuration::get('PS_ATTRIBUTE_CATEGORY_DISPLAY'), 'comparator_max_item' => Configuration::get('PS_COMPARATOR_MAX_ITEM')));

        $this->setTemplate(_PS_THEME_DIR_.'search.tpl');
    }

    public function displayHeader($display = true)
    {
        if (!$this->instant_search && !$this->ajax_search) {
            parent::displayHeader();
        } else {
            $this->context->smarty->assign('static_token', Tools::getToken(false));
        }
    }

    public function displayFooter($display = true)
    {
        if (!$this->instant_search && !$this->ajax_search) {
            parent::displayFooter();
        }
    }

    public function setMedia()
    {
        parent::setMedia();

        if (!$this->instant_search && !$this->ajax_search) {
            $this->addCSS(_THEME_CSS_DIR_.'product_list.css');
        }
    }
}
