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

class MetaCore extends ObjectModel
{
    public $page;
    public $configurable = 1;
    public $title;
    public $description;
    public $keywords;
    public $url_rewrite;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'meta',
        'primary' => 'id_meta',
        'multilang' => true,
        'multilang_shop' => true,
        'fields' => array(
            'page' =>            array('type' => self::TYPE_STRING, 'validate' => 'isFileName', 'required' => true, 'size' => 64),
            'configurable' =>            array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),

            /* Lang fields */
            'title' =>            array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 128),
            'description' =>    array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 255),
            'keywords' =>        array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 255),
            'url_rewrite' =>    array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isLinkRewrite', 'size' => 255),
        ),
    );

    public static function getPages($exclude_filled = false, $add_page = false)
    {
        $selected_pages = array();
        if (!$files = Tools::scandir(_PS_CORE_DIR_.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.'front'.DIRECTORY_SEPARATOR, 'php', '', true)) {
            die(Tools::displayError('Cannot scan "root" directory'));
        }

        if (!$override_files = Tools::scandir(_PS_CORE_DIR_.DIRECTORY_SEPARATOR . 'override' . DIRECTORY_SEPARATOR . 'controllers' . DIRECTORY_SEPARATOR . 'front' . DIRECTORY_SEPARATOR, 'php', '', true)) {
            die(Tools::displayError('Cannot scan "override" directory'));
        }

        $files = array_values(array_unique(array_merge($files, $override_files)));

        // Exclude pages forbidden
        $exlude_pages = array(
            'category', 'changecurrency', 'cms', 'footer', 'header',
            'pagination', 'product', 'product-sort', 'statistics'
        );

        foreach ($files as $file) {
            if ($file != 'index.php' && !in_array(strtolower(str_replace('Controller.php', '', $file)), $exlude_pages)) {
                $class_name = str_replace('.php', '', $file);
                $reflection = class_exists($class_name) ? new ReflectionClass(str_replace('.php', '', $file)) : false;
                $properties = $reflection ? $reflection->getDefaultProperties() : array();
                if (isset($properties['php_self'])) {
                    $selected_pages[$properties['php_self']] = $properties['php_self'];
                } elseif (preg_match('/^[a-z0-9_.-]*\.php$/i', $file)) {
                    $selected_pages[strtolower(str_replace('Controller.php', '', $file))] = strtolower(str_replace('Controller.php', '', $file));
                } elseif (preg_match('/^([a-z0-9_.-]*\/)?[a-z0-9_.-]*\.php$/i', $file)) {
                    $selected_pages[strtolower(sprintf(Tools::displayError('%2$s (in %1$s)'), dirname($file), str_replace('Controller.php', '', basename($file))))] = strtolower(str_replace('Controller.php', '', basename($file)));
                }
            }
        }

        // Add modules controllers to list (this function is cool !)
        foreach (glob(_PS_MODULE_DIR_.'*/controllers/front/*.php') as $file) {
            $filename = Tools::strtolower(basename($file, '.php'));
            if ($filename == 'index') {
                continue;
            }

            $module = Tools::strtolower(basename(dirname(dirname(dirname($file)))));
            $selected_pages[$module.' - '.$filename] = 'module-'.$module.'-'.$filename;
        }

        // Exclude page already filled
        if ($exclude_filled) {
            $metas = Meta::getMetas();
            foreach ($metas as $meta) {
                if (in_array($meta['page'], $selected_pages)) {
                    unset($selected_pages[array_search($meta['page'], $selected_pages)]);
                }
            }
        }
        // Add selected page
        if ($add_page) {
            $name = $add_page;
            if (preg_match('#module-([a-z0-9_-]+)-([a-z0-9]+)$#i', $add_page, $m)) {
                $add_page = $m[1].' - '.$m[2];
            }
            $selected_pages[$add_page] = $name;
            asort($selected_pages);
        }
        return $selected_pages;
    }

    public static function getMetas()
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT * FROM '._DB_PREFIX_.'meta ORDER BY page ASC');
    }

    public static function getMetasByIdLang($id_lang)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT *
		FROM `'._DB_PREFIX_.'meta` m
		LEFT JOIN `'._DB_PREFIX_.'meta_lang` ml ON m.`id_meta` = ml.`id_meta`
		WHERE ml.`id_lang` = '.(int)$id_lang
            .Shop::addSqlRestrictionOnLang('ml').
        'ORDER BY page ASC');
    }

    public static function getMetaByPage($page, $id_lang)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
		SELECT *
		FROM '._DB_PREFIX_.'meta m
		LEFT JOIN '._DB_PREFIX_.'meta_lang ml ON m.id_meta = ml.id_meta
		WHERE (
			m.page = "'.pSQL($page).'"
			OR m.page = "'.pSQL(str_replace('-', '', strtolower($page))).'"
		)
		AND ml.id_lang = '.(int)$id_lang.'
		'.Shop::addSqlRestrictionOnLang('ml'));
    }

    public function update($null_values = false)
    {
        if (!parent::update($null_values)) {
            return false;
        }

        return Tools::generateHtaccess();
    }

    public function delete()
    {
        if (!parent::delete()) {
            return false;
        }

        return Tools::generateHtaccess();
    }

    public function deleteSelection($selection)
    {
        if (!is_array($selection)) {
            die(Tools::displayError());
        }
        $result = true;
        foreach ($selection as $id) {
            $this->id = (int)$id;
            $result = $result && $this->delete();
        }

        return $result && Tools::generateHtaccess();
    }

    public static function getEquivalentUrlRewrite($new_id_lang, $id_lang, $url_rewrite)
    {
        return Db::getInstance()->getValue('
		SELECT url_rewrite
		FROM `'._DB_PREFIX_.'meta_lang`
		WHERE id_meta = (
			SELECT id_meta
			FROM `'._DB_PREFIX_.'meta_lang`
			WHERE url_rewrite = \''.pSQL($url_rewrite).'\' AND id_lang = '.(int)$id_lang.'
			AND id_shop = '.Context::getContext()->shop->id.'
		)
		AND id_lang = '.(int)$new_id_lang.'
		AND id_shop = '.Context::getContext()->shop->id);
    }

    /**
     * @since 1.5.0
     */
    public static function getMetaTags($id_lang, $page_name, $title = '')
    {
        global $maintenance;
        if (!(isset($maintenance) && (!in_array(Tools::getRemoteAddr(), explode(',', Configuration::get('PS_MAINTENANCE_IP')))))) {
            if ($page_name == 'product' && ($id_product = Tools::getValue('id_product'))) {
                return Meta::getProductMetas($id_product, $id_lang, $page_name);
            } elseif ($page_name == 'category' && ($id_category = Tools::getValue('id_category'))) {
                return Meta::getCategoryMetas($id_category, $id_lang, $page_name, $title);
            } elseif ($page_name == 'manufacturer' && ($id_manufacturer = Tools::getValue('id_manufacturer'))) {
                return Meta::getManufacturerMetas($id_manufacturer, $id_lang, $page_name);
            } elseif ($page_name == 'supplier' && ($id_supplier = Tools::getValue('id_supplier'))) {
                return Meta::getSupplierMetas($id_supplier, $id_lang, $page_name);
            } elseif ($page_name == 'cms' && ($id_cms = Tools::getValue('id_cms'))) {
                return Meta::getCmsMetas($id_cms, $id_lang, $page_name);
            } elseif ($page_name == 'cms' && ($id_cms_category = Tools::getValue('id_cms_category'))) {
                return Meta::getCmsCategoryMetas($id_cms_category, $id_lang, $page_name);
            }
        }

        return Meta::getHomeMetas($id_lang, $page_name);
    }

    /**
     * Get meta tags for a given page
     *
     * @since 1.5.0
     * @param int $id_lang
     * @param string $page_name
     * @return array Meta tags
     */
    public static function getHomeMetas($id_lang, $page_name)
    {
        $metas = Meta::getMetaByPage($page_name, $id_lang);
        $ret['meta_title'] = (isset($metas['title']) && $metas['title']) ? $metas['title'].' - '.Configuration::get('PS_SHOP_NAME') : Configuration::get('PS_SHOP_NAME');
        $ret['meta_description'] = (isset($metas['description']) && $metas['description']) ? $metas['description'] : '';
        $ret['meta_keywords'] = (isset($metas['keywords']) && $metas['keywords']) ? $metas['keywords'] :  '';
        return $ret;
    }

    /**
     * Get product meta tags
     *
     * @since 1.5.0
     * @param int $id_product
     * @param int $id_lang
     * @param string $page_name
     * @return array
     */
    public static function getProductMetas($id_product, $id_lang, $page_name)
    {
        $sql = 'SELECT `name`, `meta_title`, `meta_description`, `meta_keywords`, `description_short`
				FROM `'._DB_PREFIX_.'product` p
				LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (pl.`id_product` = p.`id_product`'.Shop::addSqlRestrictionOnLang('pl').')
				'.Shop::addSqlAssociation('product', 'p').'
				WHERE pl.id_lang = '.(int)$id_lang.'
					AND pl.id_product = '.(int)$id_product.'
					AND product_shop.active = 1';
        if ($row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql)) {
            if (empty($row['meta_description'])) {
                $row['meta_description'] = strip_tags($row['description_short']);
            }
            return Meta::completeMetaTags($row, $row['name']);
        }

        return Meta::getHomeMetas($id_lang, $page_name);
    }

    /**
     * Get category meta tags
     *
     * @since 1.5.0
     * @param int $id_category
     * @param int $id_lang
     * @param string $page_name
     * @return array
     */
    public static function getCategoryMetas($id_category, $id_lang, $page_name, $title = '')
    {
        if (!empty($title)) {
            $title = ' - '.$title;
        }
        $page_number = (int)Tools::getValue('p');
        $sql = 'SELECT `name`, `meta_title`, `meta_description`, `meta_keywords`, `description`
				FROM `'._DB_PREFIX_.'category_lang` cl
				WHERE cl.`id_lang` = '.(int)$id_lang.'
					AND cl.`id_category` = '.(int)$id_category.Shop::addSqlRestrictionOnLang('cl');

        $cache_id = 'Meta::getCategoryMetas'.(int)$id_category.'-'.(int)$id_lang;
        if (!Cache::isStored($cache_id)) {
            if ($row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql)) {
                if (empty($row['meta_description'])) {
                    $row['meta_description'] = strip_tags($row['description']);
                }

                // Paginate title
                if (!empty($row['meta_title'])) {
                    $row['meta_title'] = $title.$row['meta_title'].(!empty($page_number) ? ' ('.$page_number.')' : '').' - '.Configuration::get('PS_SHOP_NAME');
                } else {
                    $row['meta_title'] = $row['name'].(!empty($page_number) ? ' ('.$page_number.')' : '').' - '.Configuration::get('PS_SHOP_NAME');
                }

                if (!empty($title)) {
                    $row['meta_title'] = $title.(!empty($page_number) ? ' ('.$page_number.')' : '').' - '.Configuration::get('PS_SHOP_NAME');
                }

                $result = Meta::completeMetaTags($row, $row['name']);
            } else {
                $result = Meta::getHomeMetas($id_lang, $page_name);
            }
            Cache::store($cache_id, $result);
            return $result;
        }
        return Cache::retrieve($cache_id);
    }

    /**
     * Get manufacturer meta tags
     *
     * @since 1.5.0
     * @param int $id_manufacturer
     * @param int $id_lang
     * @param string $page_name
     * @return array
     */
    public static function getManufacturerMetas($id_manufacturer, $id_lang, $page_name)
    {
        $page_number = (int)Tools::getValue('p');
        $sql = 'SELECT `name`, `meta_title`, `meta_description`, `meta_keywords`
				FROM `'._DB_PREFIX_.'manufacturer_lang` ml
				LEFT JOIN `'._DB_PREFIX_.'manufacturer` m ON (ml.`id_manufacturer` = m.`id_manufacturer`)
				WHERE ml.id_lang = '.(int)$id_lang.'
					AND ml.id_manufacturer = '.(int)$id_manufacturer;
        if ($row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql)) {
            if (!empty($row['meta_description'])) {
                $row['meta_description'] = strip_tags($row['meta_description']);
            }
            $row['meta_title'] = ($row['meta_title'] ? $row['meta_title'] : $row['name']).(!empty($page_number) ? ' ('.$page_number.')' : '');
            $row['meta_title'] .= ' - '.Configuration::get('PS_SHOP_NAME');
            return Meta::completeMetaTags($row, $row['meta_title']);
        }

        return Meta::getHomeMetas($id_lang, $page_name);
    }

    /**
     * Get supplier meta tags
     *
     * @since 1.5.0
     * @param int $id_supplier
     * @param int $id_lang
     * @param string $page_name
     * @return array
     */
    public static function getSupplierMetas($id_supplier, $id_lang, $page_name)
    {
        $sql = 'SELECT `name`, `meta_title`, `meta_description`, `meta_keywords`
				FROM `'._DB_PREFIX_.'supplier_lang` sl
				LEFT JOIN `'._DB_PREFIX_.'supplier` s ON (sl.`id_supplier` = s.`id_supplier`)
				WHERE sl.id_lang = '.(int)$id_lang.'
					AND sl.id_supplier = '.(int)$id_supplier;
        if ($row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql)) {
            if (!empty($row['meta_description'])) {
                $row['meta_description'] = strip_tags($row['meta_description']);
            }
            if (!empty($row['meta_title'])) {
                $row['meta_title'] = $row['meta_title'].' - '.Configuration::get('PS_SHOP_NAME');
            }
            return Meta::completeMetaTags($row, $row['name']);
        }

        return Meta::getHomeMetas($id_lang, $page_name);
    }

    /**
     * Get CMS meta tags
     *
     * @since 1.5.0
     * @param int $id_cms
     * @param int $id_lang
     * @param string $page_name
     * @return array
     */
    public static function getCmsMetas($id_cms, $id_lang, $page_name)
    {
        $sql = 'SELECT `meta_title`, `meta_description`, `meta_keywords`
				FROM `'._DB_PREFIX_.'cms_lang`
				WHERE id_lang = '.(int)$id_lang.'
					AND id_cms = '.(int)$id_cms.
                    ((int)Context::getContext()->shop->id ?
                        ' AND id_shop = '.(int)Context::getContext()->shop->id : '');

        if ($row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql)) {
            $row['meta_title'] = $row['meta_title'].' - '.Configuration::get('PS_SHOP_NAME');
            return Meta::completeMetaTags($row, $row['meta_title']);
        }

        return Meta::getHomeMetas($id_lang, $page_name);
    }

    /**
     * Get CMS category meta tags
     *
     * @since 1.5.0
     * @param int $id_cms_category
     * @param int $id_lang
     * @param string $page_name
     * @return array
     */
    public static function getCmsCategoryMetas($id_cms_category, $id_lang, $page_name)
    {
        $sql = 'SELECT `meta_title`, `meta_description`, `meta_keywords`
				FROM `'._DB_PREFIX_.'cms_category_lang`
				WHERE id_lang = '.(int)$id_lang.'
					AND id_cms_category = '.(int)$id_cms_category.
                    ((int)Context::getContext()->shop->id ?
                        ' AND id_shop = '.(int)Context::getContext()->shop->id : '');
        if ($row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql)) {
            $row['meta_title'] = $row['meta_title'].' - '.Configuration::get('PS_SHOP_NAME');
            return Meta::completeMetaTags($row, $row['meta_title']);
        }

        return Meta::getHomeMetas($id_lang, $page_name);
    }

    /**
     * @since 1.5.0
     */
    public static function completeMetaTags($meta_tags, $default_value, Context $context = null)
    {
        if (!$context) {
            $context = Context::getContext();
        }

        if (empty($meta_tags['meta_title'])) {
            $meta_tags['meta_title'] = $default_value.' - '.Configuration::get('PS_SHOP_NAME');
        }
        if (empty($meta_tags['meta_description'])) {
            $meta_tags['meta_description'] = Configuration::get('PS_META_DESCRIPTION', $context->language->id) ? Configuration::get('PS_META_DESCRIPTION', $context->language->id) : '';
        }
        if (empty($meta_tags['meta_keywords'])) {
            $meta_tags['meta_keywords'] = Configuration::get('PS_META_KEYWORDS', $context->language->id) ? Configuration::get('PS_META_KEYWORDS', $context->language->id) : '';
        }
        return $meta_tags;
    }
}
