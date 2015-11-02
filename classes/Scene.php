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

class SceneCore extends ObjectModel
{
    /** @var string Name */
    public $name;

    /** @var bool Active Scene */
    public $active = true;

    /** @var array Zone for image map */
    public $zones = array();

    /** @var array list of category where this scene is available */
    public $categories = array();

    /** @var array Products */
    public $products;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'scene',
        'primary' => 'id_scene',
        'multilang' => true,
        'fields' => array(
            'active' =>    array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true),

            /* Lang fields */
            'name' =>        array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'required' => true, 'size' => 100),
        ),
    );

    protected static $feature_active = null;

    public function __construct($id = null, $id_lang = null, $lite_result = true, $hide_scene_position = false)
    {
        parent::__construct($id, $id_lang);

        if (!$lite_result) {
            $this->products = $this->getProducts(true, (int)$id_lang, false);
        }
        if ($hide_scene_position) {
            $this->name = Scene::hideScenePosition($this->name);
        }
        $this->image_dir = _PS_SCENE_IMG_DIR_;
    }

    public function update($null_values = false)
    {
        if (!$this->updateZoneProducts()) {
            return false;
        }
        if (!$this->updateCategories()) {
            return false;
        }

        if (parent::update($null_values)) {
            // Refresh cache of feature detachable
            Configuration::updateGlobalValue('PS_SCENE_FEATURE_ACTIVE', Scene::isCurrentlyUsed($this->def['table'], true));
            return true;
        }
        return false;
    }

    public function add($autodate = true, $null_values = false)
    {
        if (!empty($this->zones)) {
            $this->addZoneProducts($this->zones);
        }
        if (!empty($this->categories)) {
            $this->addCategories($this->categories);
        }

        if (parent::add($autodate, $null_values)) {
            // Put cache of feature detachable only if this new scene is active else we keep the old value
            if ($this->active) {
                Configuration::updateGlobalValue('PS_SCENE_FEATURE_ACTIVE', '1');
            }
            return true;
        }
        return false;
    }

    public function delete()
    {
        $this->deleteZoneProducts();
        $this->deleteCategories();
        if (parent::delete()) {
            return $this->deleteImage() &&
                Configuration::updateGlobalValue('PS_SCENE_FEATURE_ACTIVE', Scene::isCurrentlyUsed($this->def['table'], true));
        }
        return false;
    }

    public function deleteImage($force_delete = false)
    {
        if (file_exists($this->image_dir.'thumbs/'.$this->id.'-m_scene_default.'.$this->image_format)
            && !unlink($this->image_dir.'thumbs/'.$this->id.'-m_scene_default.'.$this->image_format)) {
            return false;
        }
        if (!(isset($_FILES) && count($_FILES))) {
            return parent::deleteImage();
        }
        return true;
    }

    public function addCategories($categories)
    {
        $data = array();
        foreach ($categories as $category) {
            $data[] = array(
                'id_scene' => (int)$this->id,
                'id_category' => (int)$category,
            );
        }
        return Db::getInstance()->insert('scene_category', $data);
    }

    public function deleteCategories()
    {
        return Db::getInstance()->execute('
		DELETE FROM `'._DB_PREFIX_.'scene_category`
		WHERE `id_scene` = '.(int)$this->id);
    }

    public function updateCategories()
    {
        if (!$this->deleteCategories()) {
            return false;
        }
        if (!empty($this->categories) && !$this->addCategories($this->categories)) {
            return false;
        }
        return true;
    }

    public function addZoneProducts($zones)
    {
        $data = array();
        foreach ($zones as $zone) {
            $data[] = array(
                'id_scene' => (int)$this->id,
                'id_product' => (int)$zone['id_product'],
                'x_axis' => (int)$zone['x1'],
                'y_axis' => (int)$zone['y1'],
                'zone_width' => (int)$zone['width'],
                'zone_height' => (int)$zone['height'],
            );
        }

        return Db::getInstance()->insert('scene_products', $data);
    }

    public function deleteZoneProducts()
    {
        return Db::getInstance()->execute('
		DELETE FROM `'._DB_PREFIX_.'scene_products`
		WHERE `id_scene` = '.(int)$this->id);
    }

    public function updateZoneProducts()
    {
        if (!$this->deleteZoneProducts()) {
            return false;
        }
        if ($this->zones && !$this->addZoneProducts($this->zones)) {
            return false;
        }
        return true;
    }

    /**
     * Get all scenes of a category
     *
     * @return array Products
     */
    public static function getScenes($id_category, $id_lang = null, $only_active = true, $lite_result = true, $hide_scene_position = true,
        Context $context = null)
    {
        if (!Scene::isFeatureActive()) {
            return array();
        }

        $cache_key = 'Scene::getScenes'.$id_category.(int)$lite_result;
        if (!Cache::isStored($cache_key)) {
            if (!$context) {
                $context = Context::getContext();
            }
            $id_lang = is_null($id_lang) ? $context->language->id : $id_lang;

            $sql = 'SELECT s.*
					FROM `'._DB_PREFIX_.'scene_category` sc
					LEFT JOIN `'._DB_PREFIX_.'scene` s ON (sc.id_scene = s.id_scene)
					'.Shop::addSqlAssociation('scene', 's').'
					LEFT JOIN `'._DB_PREFIX_.'scene_lang` sl ON (sl.id_scene = s.id_scene)
					WHERE sc.id_category = '.(int)$id_category.'
						AND sl.id_lang = '.(int)$id_lang
                        .($only_active ? ' AND s.active = 1' : '').'
					ORDER BY sl.name ASC';
            $scenes = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

            if (!$lite_result && $scenes) {
                foreach ($scenes as &$scene) {
                    $scene = new Scene($scene['id_scene'], $id_lang, false, $hide_scene_position);
                }
            }
            Cache::store($cache_key, $scenes);
        } else {
            $scenes = Cache::retrieve($cache_key);
        }
        return $scenes;
    }

    /**
    * Get all products of this scene
    *
    * @return array Products
    */
    public function getProducts($only_active = true, $id_lang = null, $lite_result = true, Context $context = null)
    {
        if (!Scene::isFeatureActive()) {
            return array();
        }

        if (!$context) {
            $context = Context::getContext();
        }
        $id_lang = is_null($id_lang) ? $context->language->id : $id_lang;

        $products = Db::getInstance()->executeS('
		SELECT s.*
		FROM `'._DB_PREFIX_.'scene_products` s
		LEFT JOIN `'._DB_PREFIX_.'product` p ON (p.id_product = s.id_product)
		'.Shop::addSqlAssociation('product', 'p').'
		WHERE s.id_scene = '.(int)$this->id.($only_active ? ' AND product_shop.active = 1' : ''));

        if (!$lite_result && $products) {
            foreach ($products as &$product) {
                $product['details'] = new Product($product['id_product'], !$lite_result, $id_lang);
                if (Validate::isLoadedObject($product['details'])) {
                    $product['link'] = $context->link->getProductLink(
                        $product['details']->id,
                        $product['details']->link_rewrite,
                        $product['details']->category,
                        $product['details']->ean13
                    );
                    $cover = Product::getCover($product['details']->id);
                    if (is_array($cover)) {
                        $product = array_merge($cover, $product);
                    }
                }
            }
        }
        return $products;
    }

    /**
    * Get categories where scene is indexed
    *
    * @param int $id_scene Scene id
    * @return array Categories where scene is indexed
    */
    public static function getIndexedCategories($id_scene)
    {
        return Db::getInstance()->executeS('
		SELECT `id_category`
		FROM `'._DB_PREFIX_.'scene_category`
		WHERE `id_scene` = '.(int)$id_scene);
    }

    /**
     * Hide scene prefix used for position
     *
     * @param string $name Scene name
     * @return string Name without position
     */
    public static function hideScenePosition($name)
    {
        return preg_replace('/^[0-9]+\./', '', $name);
    }

    /**
     * This method is allow to know if a feature is used or active
     * @since 1.5.0.1
     * @return bool
     */
    public static function isFeatureActive()
    {
        return Configuration::get('PS_SCENE_FEATURE_ACTIVE');
    }
}
