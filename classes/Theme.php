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

class ThemeCore extends ObjectModel
{
    public $name;
    public $directory;
    public $responsive;
    public $default_left_column;
    public $default_right_column;
    public $product_per_page;

    const CACHE_FILE_CUSTOMER_THEMES_LIST = '/config/xml/customer_themes_list.xml';

    const CACHE_FILE_MUST_HAVE_THEMES_LIST = '/config/xml/must_have_themes_list.xml';

    const UPLOADED_THEME_DIR_NAME = 'uploaded';

    /** @var int access rights of created folders (octal) */
    public static $access_rights = 0775;
    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'theme',
        'primary' => 'id_theme',
        'fields' => array(
            'name' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 64, 'required' => true),
            'directory' => array('type' => self::TYPE_STRING, 'validate' => 'isDirName', 'size' => 64, 'required' => true),
            'responsive' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'default_left_column' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'default_right_column' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'product_per_page' => array('type' => self::TYPE_INT, 'validate' => 'isInt')
        ),
    );

    public static function getThemes()
    {
        $themes = new PrestaShopCollection('Theme');
        $themes->orderBy('name');
        return $themes;
    }

    public static function getAllThemes($excluded_ids = false)
    {
        $themes = new PrestaShopCollection('Theme');

        if (is_array($excluded_ids) && !empty($excluded_ids)) {
            $themes->where('id_theme', 'notin', $excluded_ids);
        }

        $themes->orderBy('name');
        return $themes;
    }

    /**
     * return an array of all available theme (installed or not)
     *
     * @param bool $installed_only
     * @return array string (directory)
     */
    public static function getAvailable($installed_only = true)
    {
        static $dirlist = array();
        $available_theme = array();

        if (empty($dirlist)) {
            $themes = scandir(_PS_ALL_THEMES_DIR_);
            foreach ($themes as $theme) {
                if (is_dir(_PS_ALL_THEMES_DIR_.DIRECTORY_SEPARATOR.$theme) && $theme[0] != '.') {
                    $dirlist[] = $theme;
                }
            }
        }

        if ($installed_only) {
            $themes = Theme::getThemes();
            foreach ($themes as $theme_obj) {
                /** @var Theme $theme_obj */
                $themes_dir[] = $theme_obj->directory;
            }

            foreach ($dirlist as $theme) {
                if (false !== array_search($theme, $themes_dir)) {
                    $available_theme[] = $theme;
                }
            }
        } else {
            $available_theme = $dirlist;
        }

        return $available_theme;
    }

    /**
     * check if a theme is used by a shop
     *
     * @return bool
     */
    public function isUsed()
    {
        return Db::getInstance()->getValue('SELECT count(*)
			FROM '._DB_PREFIX_.'shop WHERE id_theme = '.(int)$this->id);
    }

    /**
     * add only theme if the directory exists
     *
     * @param bool $null_values
     * @param bool $autodate
     * @return bool Insertion result
     */
    public function add($autodate = true, $null_values = false)
    {
        if (!is_dir(_PS_ALL_THEMES_DIR_.$this->directory)) {
            return false;
        }

        return parent::add($autodate, $null_values);
    }

    /**
     * Checks if theme exists (by folder) and returns Theme object.
     *
     * @param string $directory
     *
     * @return bool|Theme
     */
    public static function getByDirectory($directory)
    {
        if (is_string($directory) && strlen($directory) > 0 && file_exists(_PS_ALL_THEMES_DIR_.$directory) && is_dir(_PS_ALL_THEMES_DIR_.$directory)) {
            $id_theme = (int)Db::getInstance()->getValue('SELECT id_theme FROM '._DB_PREFIX_.'theme WHERE directory="'.pSQL($directory).'"');

            return $id_theme ? new Theme($id_theme) : false;
        }

        return false;
    }

    public static function getInstalledThemeDirectories()
    {
        $list = array();
        $tmp = Db::getInstance()->executeS('SELECT `directory` FROM '._DB_PREFIX_.'theme');
        foreach ($tmp as $t) {
            $list[] = $t['directory'];
        }

        return $list;
    }

    public static function getThemeInfo($id_theme)
    {
        $theme = new Theme((int)$id_theme);
        $theme_arr = array();

        if (file_exists(_PS_ROOT_DIR_.'/config/xml/themes/'.$theme->directory.'.xml')) {
            $config_file = _PS_ROOT_DIR_.'/config/xml/themes/'.$theme->directory.'.xml';
        } elseif ($theme->name == 'hotel-theme') {
            $config_file = _PS_ROOT_DIR_.'/config/xml/themes/default.xml';
        } else {
            $config_file = false;
        }

        if ($config_file) {
            $theme_arr['theme_id'] = (int)$theme->id;
            $xml_theme = @simplexml_load_file($config_file);

            if ($xml_theme !== false) {
                foreach ($xml_theme->attributes() as $key => $value) {
                    $theme_arr['theme_'.$key] = (string)$value;
                }

                foreach ($xml_theme->author->attributes() as $key => $value) {
                    $theme_arr['author_'.$key] = (string)$value;
                }

                if ($theme_arr['theme_name'] == 'hotel-theme') {
                    $theme_arr['tc'] = Module::isEnabled('wkthemeconfigurator');
                }
            }
        } else {
            // If no xml we use data from database
            $theme_arr['theme_id'] = (int)$theme->id;
            $theme_arr['theme_name'] = $theme->name;
            $theme_arr['theme_directory'] = $theme->directory;
        }

        return $theme_arr;
    }

    public static function getNonInstalledTheme()
    {
        $installed_theme_directories = Theme::getInstalledThemeDirectories();
        $not_installed_theme = array();
        foreach (glob(_PS_ALL_THEMES_DIR_.'*', GLOB_ONLYDIR) as $theme_dir) {
            $dir = basename($theme_dir);
            $config_file = _PS_ALL_THEMES_DIR_.$dir.'/config.xml';
            if (!in_array($dir, $installed_theme_directories) && @filemtime($config_file)) {
                if ($xml_theme = @simplexml_load_file($config_file)) {
                    $theme = array();
                    foreach ($xml_theme->attributes() as $key => $value) {
                        $theme[$key] = (string)$value;
                    }

                    if (!empty($theme)) {
                        $not_installed_theme[] = $theme;
                    }
                }
            }
        }

        return $not_installed_theme;
    }

    /**
     * update the table PREFIX_theme_meta for the current theme
     * @param array $metas
     * @param bool  $full_update If true, all the meta of the theme will be deleted prior the insert, otherwise only the current $metas will be deleted
     *
     */
    public function updateMetas($metas, $full_update = false)
    {
        if ($full_update) {
            Db::getInstance()->delete('theme_meta', 'id_theme='.(int)$this->id);
        }

        $values = array();
        if ($this->id > 0) {
            foreach ($metas as $meta) {
                if (!$full_update) {
                    Db::getInstance()->delete('theme_meta', 'id_theme='.(int)$this->id.' AND id_meta='.(int)$meta['id_meta']);
                }

                $values[] = array(
                    'id_theme'     => (int)$this->id,
                    'id_meta'      => (int)$meta['id_meta'],
                    'left_column'  => (int)$meta['left'],
                    'right_column' => (int)$meta['right']
                );
            }
            Db::getInstance()->insert('theme_meta', $values);
        }
    }

    public function hasColumns($page)
    {
        return Db::getInstance()->getRow('
		SELECT IFNULL(left_column, default_left_column) as left_column, IFNULL(right_column, default_right_column) as right_column
		FROM '._DB_PREFIX_.'theme t
		LEFT JOIN '._DB_PREFIX_.'theme_meta tm ON (t.id_theme = tm.id_theme)
		LEFT JOIN '._DB_PREFIX_.'meta m ON (m.id_meta = tm.id_meta)
		WHERE t.id_theme ='.(int)$this->id.' AND m.page = "'.pSQL($page).'"');
    }

    public function hasColumnsSettings($page)
    {
        return (bool)Db::getInstance()->getValue('
		SELECT m.`id_meta`
		FROM '._DB_PREFIX_.'theme t
		LEFT JOIN '._DB_PREFIX_.'theme_meta tm ON (t.id_theme = tm.id_theme)
		LEFT JOIN '._DB_PREFIX_.'meta m ON (m.id_meta = tm.id_meta)
		WHERE t.id_theme ='.(int)$this->id.' AND m.page = "'.pSQL($page).'"');
    }

    public function hasLeftColumn($page = null)
    {
        return (bool)Db::getInstance()->getValue(
            'SELECT IFNULL(
			(
				SELECT left_column
				FROM '._DB_PREFIX_.'theme t
				LEFT JOIN '._DB_PREFIX_.'theme_meta tm ON ( t.id_theme = tm.id_theme )
				LEFT JOIN '._DB_PREFIX_.'meta m ON ( m.id_meta = tm.id_meta )
				WHERE t.id_theme ='.(int)$this->id.'
				AND m.page = "'.pSQL($page).'" ) , default_left_column
			)
			FROM '._DB_PREFIX_.'theme
			WHERE id_theme ='.(int)$this->id
        );
    }

    public function hasRightColumn($page = null)
    {
        return (bool)Db::getInstance()->getValue(
            'SELECT IFNULL(
			(
				SELECT right_column
				FROM '._DB_PREFIX_.'theme t
				LEFT JOIN '._DB_PREFIX_.'theme_meta tm ON ( t.id_theme = tm.id_theme )
				LEFT JOIN '._DB_PREFIX_.'meta m ON ( m.id_meta = tm.id_meta )
				WHERE t.id_theme ='.(int)$this->id.'
				AND m.page = "'.pSQL($page).'" ) , default_right_column
			)
			FROM '._DB_PREFIX_.'theme
			WHERE id_theme ='.(int)$this->id);
    }

    /**
     * @return array|bool
     */
    public function getMetas()
    {
        if (!Validate::isUnsignedId($this->id) || $this->id == 0) {
            return false;
        }

        return Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'theme_meta WHERE id_theme = '.(int)$this->id);
    }

    /**
     * @return bool
     */
    public function removeMetas()
    {
        if (!Validate::isUnsignedId($this->id) || $this->id == 0) {
            return false;
        }

        return Db::getInstance()->delete('theme_meta', 'id_theme = '.(int)$this->id);
    }

    public function toggleResponsive()
    {
        // Object must have a variable called 'responsive'
        if (!array_key_exists('responsive', $this)) {
            throw new PrestaShopException('property "responsive" is missing in object '.get_class($this));
        }

        // Update only responsive field
        $this->setFieldsToUpdate(array('responsive' => true));

        // Update active responsive on object
        $this->responsive = !(int)$this->responsive;

        // Change responsive to active/inactive
        return $this->update(false);
    }

    public function toggleDefaultLeftColumn()
    {
        if (!array_key_exists('default_left_column', $this)) {
            throw new PrestaShopException('property "default_left_column" is missing in object '.get_class($this));
        }

        $this->setFieldsToUpdate(array('default_left_column' => true));

        $this->default_left_column = !(int)$this->default_left_column;

        return $this->update(false);
    }

    public function toggleDefaultRightColumn()
    {
        if (!array_key_exists('default_right_column', $this)) {
            throw new PrestaShopException('property "default_right_column" is missing in object '.get_class($this));
        }

        $this->setFieldsToUpdate(array('default_right_column' => true));

        $this->default_right_column = !(int)$this->default_right_column;

        return $this->update(false);
    }
}
