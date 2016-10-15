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

class LanguageCore extends ObjectModel
{
    public $id;

    /** @var string Name */
    public $name;

    /** @var string 2-letter iso code */
    public $iso_code;

    /** @var string 5-letter iso code */
    public $language_code;

    /** @var string date format http://http://php.net/manual/en/function.date.php with the date only */
    public $date_format_lite = 'Y-m-d';

    /** @var string date format http://http://php.net/manual/en/function.date.php with hours and minutes */
    public $date_format_full = 'Y-m-d H:i:s';

    /** @var bool true if this language is right to left language */
    public $is_rtl = false;

    /** @var bool Status */
    public $active = true;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'lang',
        'primary' => 'id_lang',
        'fields' => array(
            'name' =>                array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true, 'size' => 32),
            'iso_code' =>            array('type' => self::TYPE_STRING, 'validate' => 'isLanguageIsoCode', 'required' => true, 'size' => 2),
            'language_code' =>        array('type' => self::TYPE_STRING, 'validate' => 'isLanguageCode', 'size' => 5),
            'active' =>            array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'is_rtl' =>            array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'date_format_lite' =>    array('type' => self::TYPE_STRING, 'validate' => 'isPhpDateFormat', 'required' => true, 'size' => 32),
            'date_format_full' =>    array('type' => self::TYPE_STRING, 'validate' => 'isPhpDateFormat', 'required' => true, 'size' => 32),
        ),
    );


    /** @var array Languages cache */
    protected static $_checkedLangs;
    protected static $_LANGUAGES;
    protected static $countActiveLanguages = array();

    protected $webserviceParameters = array(
        'objectNodeName' => 'language',
        'objectsNodeName' => 'languages',
    );

    protected $translationsFilesAndVars = array(
            'fields' => '_FIELDS',
            'errors' => '_ERRORS',
            'admin' => '_LANGADM',
            'pdf' => '_LANGPDF',
            'tabs' => 'tabs',
        );

    public function __construct($id = null, $id_lang = null)
    {
        parent::__construct($id);
    }

    /**
     * @see ObjectModel::getFields()
     * @return array
     */
    public function getFields()
    {
        $this->iso_code = strtolower($this->iso_code);
        if (empty($this->language_code)) {
            $this->language_code = $this->iso_code;
        }

        return parent::getFields();
    }

    /**
     * Generate translations files
     *
     */
    protected function _generateFiles($newIso = null)
    {
        $iso_code = $newIso ? $newIso : $this->iso_code;

        if (!file_exists(_PS_TRANSLATIONS_DIR_.$iso_code)) {
            if (@mkdir(_PS_TRANSLATIONS_DIR_.$iso_code)) {
                @chmod(_PS_TRANSLATIONS_DIR_.$iso_code, 0777);
            }
        }

        foreach ($this->translationsFilesAndVars as $file => $var) {
            $path_file = _PS_TRANSLATIONS_DIR_.$iso_code.'/'.$file.'.php';
            if (!file_exists($path_file)) {
                if ($file != 'tabs') {
                    @file_put_contents($path_file, '<?php
	global $'.$var.';
	$'.$var.' = array();
?>');
                } else {
                    @file_put_contents($path_file, '<?php
	$'.$var.' = array();
	return $'.$var.';
?>');
                }
            }

            @chmod($path_file, 0777);
        }
    }

    /**
     * Move translations files after editing language iso code
     */
    public function moveToIso($newIso)
    {
        if ($newIso == $this->iso_code) {
            return true;
        }

        if (file_exists(_PS_TRANSLATIONS_DIR_.$this->iso_code)) {
            rename(_PS_TRANSLATIONS_DIR_.$this->iso_code, _PS_TRANSLATIONS_DIR_.$newIso);
        }

        if (file_exists(_PS_MAIL_DIR_.$this->iso_code)) {
            rename(_PS_MAIL_DIR_.$this->iso_code, _PS_MAIL_DIR_.$newIso);
        }

        $modulesList = Module::getModulesDirOnDisk();
        foreach ($modulesList as $moduleDir) {
            if (file_exists(_PS_MODULE_DIR_.$moduleDir.'/mails/'.$this->iso_code)) {
                rename(_PS_MODULE_DIR_.$moduleDir.'/mails/'.$this->iso_code, _PS_MODULE_DIR_.$moduleDir.'/mails/'.$newIso);
            }

            if (file_exists(_PS_MODULE_DIR_.$moduleDir.'/'.$this->iso_code.'.php')) {
                rename(_PS_MODULE_DIR_.$moduleDir.'/'.$this->iso_code.'.php', _PS_MODULE_DIR_.$moduleDir.'/'.$newIso.'.php');
            }
        }

        foreach (Theme::getThemes() as $theme) {
            /** @var Theme $theme */
            $theme_dir = $theme->directory;
            if (file_exists(_PS_ALL_THEMES_DIR_.$theme_dir.'/lang/'.$this->iso_code.'.php')) {
                rename(_PS_ALL_THEMES_DIR_.$theme_dir.'/lang/'.$this->iso_code.'.php', _PS_ALL_THEMES_DIR_.$theme_dir.'/lang/'.$newIso.'.php');
            }

            if (file_exists(_PS_ALL_THEMES_DIR_.$theme_dir.'/mails/'.$this->iso_code)) {
                rename(_PS_ALL_THEMES_DIR_.$theme_dir.'/mails/'.$this->iso_code, _PS_ALL_THEMES_DIR_.$theme_dir.'/mails/'.$newIso);
            }

            foreach ($modulesList as $module) {
                if (file_exists(_PS_ALL_THEMES_DIR_.$theme_dir.'/modules/'.$module.'/'.$this->iso_code.'.php')) {
                    rename(_PS_ALL_THEMES_DIR_.$theme_dir.'/modules/'.$module.'/'.$this->iso_code.'.php', _PS_ALL_THEMES_DIR_.$theme_dir.'/modules/'.$module.'/'.$newIso.'.php');
                }
            }
        }
    }

    /**
      * Return an array of theme
      *
      * @return array([theme dir] => array('name' => [theme name]))
      * @deprecated 1.5.5.0
      */
    protected function _getThemesList()
    {
        Tools::displayAsDeprecated();

        static $themes = array();

        if (empty($themes)) {
            $installed_themes = Theme::getThemes();
            foreach ($installed_themes as $theme) {
                /** @var Theme $theme */
                $themes[$theme->directory] = array('name' => $theme->name);
            }
        }
        return $themes;
    }

    public function add($autodate = true, $nullValues = false, $only_add = false)
    {
        if (!parent::add($autodate, $nullValues)) {
            return false;
        }

        if ($only_add) {
            return true;
        }

        // create empty files if they not exists
        $this->_generateFiles();

        // @todo Since a lot of modules are not in right format with their primary keys name, just get true ...
        $this->loadUpdateSQL();

        return true;
    }

    public function checkFiles()
    {
        return Language::checkFilesWithIsoCode($this->iso_code);
    }


    /**
     * This functions checks if every files exists for the language $iso_code.
     * Concerned files are those located in translations/$iso_code/
     * and translations/mails/$iso_code .
     *
     * @param mixed $iso_code
     * @returntrue if all files exists
     */
    public static function checkFilesWithIsoCode($iso_code)
    {
        if (isset(self::$_checkedLangs[$iso_code]) && self::$_checkedLangs[$iso_code]) {
            return true;
        }

        foreach (array_keys(Language::getFilesList($iso_code, _THEME_NAME_, false, false, false, true)) as $key) {
            if (!file_exists($key)) {
                return false;
            }
        }
        self::$_checkedLangs[$iso_code] = true;
        return true;
    }

    public static function getFilesList($iso_from, $theme_from, $iso_to = false, $theme_to = false, $select = false, $check = false, $modules = false)
    {
        if (empty($iso_from)) {
            die(Tools::displayError());
        }

        $copy = ($iso_to && $theme_to) ? true : false;

        $lPath_from = _PS_TRANSLATIONS_DIR_.(string)$iso_from.'/';
        $tPath_from = _PS_ROOT_DIR_.'/themes/'.(string)$theme_from.'/';
        $pPath_from = _PS_ROOT_DIR_.'/themes/'.(string)$theme_from.'/pdf/';
        $mPath_from = _PS_MAIL_DIR_.(string)$iso_from.'/';

        if ($copy) {
            $lPath_to = _PS_TRANSLATIONS_DIR_.(string)$iso_to.'/';
            $tPath_to = _PS_ROOT_DIR_.'/themes/'.(string)$theme_to.'/';
            $pPath_to = _PS_ROOT_DIR_.'/themes/'.(string)$theme_to.'/pdf/';
            $mPath_to = _PS_MAIL_DIR_.(string)$iso_to.'/';
        }

        $lFiles = array('admin.php', 'errors.php', 'fields.php', 'pdf.php', 'tabs.php');

        // Added natives mails files
        $mFiles = array(
            'account.html', 'account.txt',
            'backoffice_order.html', 'backoffice_order.txt',
            'bankwire.html', 'bankwire.txt',
            'cheque.html', 'cheque.txt',
            'contact.html', 'contact.txt',
            'contact_form.html', 'contact_form.txt',
            'credit_slip.html', 'credit_slip.txt',
            'download_product.html', 'download_product.txt',
            'employee_password.html', 'employee_password.txt',
            'forward_msg.html', 'forward_msg.txt',
            'guest_to_customer.html', 'guest_to_customer.txt',
            'in_transit.html', 'in_transit.txt',
            'log_alert.html', 'log_alert.txt',
            'newsletter.html', 'newsletter.txt',
            'order_canceled.html', 'order_canceled.txt',
            'order_conf.html', 'order_conf.txt',
            'order_customer_comment.html', 'order_customer_comment.txt',
            'order_merchant_comment.html', 'order_merchant_comment.txt',
            'order_return_state.html', 'order_return_state.txt',
            'outofstock.html', 'outofstock.txt',
            'password.html', 'password.txt',
            'password_query.html', 'password_query.txt',
            'payment.html', 'payment.txt',
            'payment_error.html', 'payment_error.txt',
            'preparation.html', 'preparation.txt',
            'refund.html', 'refund.txt',
            'reply_msg.html', 'reply_msg.txt',
            'shipped.html', 'shipped.txt',
            'test.html', 'test.txt',
            'voucher.html', 'voucher.txt',
            'voucher_new.html', 'voucher_new.txt',
            'order_changed.html', 'order_changed.txt'
        );

        $number = -1;

        $files = array();
        $files_tr = array();
        $files_theme = array();
        $files_mail = array();
        $files_modules = array();

        // When a copy is made from a theme in specific language
        // to an other theme for the same language,
        // it's avoid to copy Translations, Mails files
        // and modules files which are not override by theme.
        if (!$copy || $iso_from != $iso_to) {
            // Translations files
            if (!$check || ($check && (string)$iso_from != 'en')) {
                foreach ($lFiles as $file) {
                    $files_tr[$lPath_from.$file] = ($copy ? $lPath_to.$file : ++$number);
                }
            }
            if ($select == 'tr') {
                return $files_tr;
            }
            $files = array_merge($files, $files_tr);

            // Mail files
            if (!$check || ($check && (string)$iso_from != 'en')) {
                $files_mail[$mPath_from.'lang.php'] = ($copy ? $mPath_to.'lang.php' : ++$number);
            }
            foreach ($mFiles as $file) {
                $files_mail[$mPath_from.$file] = ($copy ? $mPath_to.$file : ++$number);
            }
            if ($select == 'mail') {
                return $files_mail;
            }
            $files = array_merge($files, $files_mail);

            // Modules
            if ($modules) {
                $modList = Module::getModulesDirOnDisk();
                foreach ($modList as $mod) {
                    $modDir = _PS_MODULE_DIR_.$mod;
                    // Lang file
                    if (file_exists($modDir.'/translations/'.(string)$iso_from.'.php')) {
                        $files_modules[$modDir.'/translations/'.(string)$iso_from.'.php'] = ($copy ? $modDir.'/translations/'.(string)$iso_to.'.php' : ++$number);
                    } elseif (file_exists($modDir.'/'.(string)$iso_from.'.php')) {
                        $files_modules[$modDir.'/'.(string)$iso_from.'.php'] = ($copy ? $modDir.'/'.(string)$iso_to.'.php' : ++$number);
                    }
                    // Mails files
                    $modMailDirFrom = $modDir.'/mails/'.(string)$iso_from;
                    $modMailDirTo = $modDir.'/mails/'.(string)$iso_to;
                    if (file_exists($modMailDirFrom)) {
                        $dirFiles = scandir($modMailDirFrom);
                        foreach ($dirFiles as $file) {
                            if (file_exists($modMailDirFrom.'/'.$file) && $file != '.' && $file != '..' && $file != '.svn') {
                                $files_modules[$modMailDirFrom.'/'.$file] = ($copy ? $modMailDirTo.'/'.$file : ++$number);
                            }
                        }
                    }
                }
                if ($select == 'modules') {
                    return $files_modules;
                }
                $files = array_merge($files, $files_modules);
            }
        } elseif ($select == 'mail' || $select == 'tr') {
            return $files;
        }

        // Theme files
        if (!$check || ($check && (string)$iso_from != 'en')) {
            $files_theme[$tPath_from.'lang/'.(string)$iso_from.'.php'] = ($copy ? $tPath_to.'lang/'.(string)$iso_to.'.php' : ++$number);

            // Override for pdf files in the theme
            if (file_exists($pPath_from.'lang/'.(string)$iso_from.'.php')) {
                $files_theme[$pPath_from.'lang/'.(string)$iso_from.'.php'] = ($copy ? $pPath_to.'lang/'.(string)$iso_to.'.php' : ++$number);
            }

            $module_theme_files = (file_exists($tPath_from.'modules/') ? scandir($tPath_from.'modules/') : array());
            foreach ($module_theme_files as $module) {
                if ($module !== '.' && $module != '..' && $module !== '.svn' && file_exists($tPath_from.'modules/'.$module.'/translations/'.(string)$iso_from.'.php')) {
                    $files_theme[$tPath_from.'modules/'.$module.'/translations/'.(string)$iso_from.'.php'] = ($copy ? $tPath_to.'modules/'.$module.'/translations/'.(string)$iso_to.'.php' : ++$number);
                }
            }
        }
        if ($select == 'theme') {
            return $files_theme;
        }
        $files = array_merge($files, $files_theme);

        // Return
        return $files;
    }

    /**
     * loadUpdateSQL will create default lang values when you create a new lang, based on default id lang
     *
     * @return bool true if succeed
     */
    public function loadUpdateSQL()
    {
        $tables = Db::getInstance()->executeS('SHOW TABLES LIKE \''.str_replace('_', '\\_', _DB_PREFIX_).'%\_lang\' ');
        $langTables = array();

        foreach ($tables as $table) {
            foreach ($table as $t) {
                if ($t != _DB_PREFIX_.'configuration_lang') {
                    $langTables[] = $t;
                }
            }
        }

        $return = true;

        $shops = Shop::getShopsCollection(false);
        foreach ($shops as $shop) {
            /** @var Shop $shop */
            $id_lang_default = Configuration::get('PS_LANG_DEFAULT', null, $shop->id_shop_group, $shop->id);

            foreach ($langTables as $name) {
                preg_match('#^'.preg_quote(_DB_PREFIX_).'(.+)_lang$#i', $name, $m);
                $identifier = 'id_'.$m[1];

                $fields = '';
                // We will check if the table contains a column "id_shop"
                // If yes, we will add "id_shop" as a WHERE condition in queries copying data from default language
                $shop_field_exists = $primary_key_exists = false;
                $columns = Db::getInstance()->executeS('SHOW COLUMNS FROM `'.$name.'`');
                foreach ($columns as $column) {
                    $fields .= $column['Field'].', ';
                    if ($column['Field'] == 'id_shop') {
                        $shop_field_exists = true;
                    }
                    if ($column['Field'] == $identifier) {
                        $primary_key_exists = true;
                    }
                }
                $fields = rtrim($fields, ', ');

                if (!$primary_key_exists) {
                    continue;
                }

                $sql = 'INSERT IGNORE INTO `'.$name.'` ('.$fields.') (SELECT ';

                // For each column, copy data from default language
                reset($columns);
                foreach ($columns as $column) {
                    if ($identifier != $column['Field'] && $column['Field'] != 'id_lang') {
                        $sql .= '(
							SELECT `'.bqSQL($column['Field']).'`
							FROM `'.bqSQL($name).'` tl
							WHERE tl.`id_lang` = '.(int)$id_lang_default.'
							'.($shop_field_exists ? ' AND tl.`id_shop` = '.(int)$shop->id : '').'
							AND tl.`'.bqSQL($identifier).'` = `'.bqSQL(str_replace('_lang', '', $name)).'`.`'.bqSQL($identifier).'`
						),';
                    } else {
                        $sql .= '`'.bqSQL($column['Field']).'`,';
                    }
                }
                $sql = rtrim($sql, ', ');
                $sql .= ' FROM `'._DB_PREFIX_.'lang` CROSS JOIN `'.bqSQL(str_replace('_lang', '', $name)).'`)';
                $return &= Db::getInstance()->execute($sql);
            }
        }
        return $return;
    }

    public static function recurseDeleteDir($dir)
    {
        if (!is_dir($dir)) {
            return false;
        }
        if ($handle = @opendir($dir)) {
            while (false !== ($file = readdir($handle))) {
                if ($file != '.' && $file != '..') {
                    if (is_dir($dir.'/'.$file)) {
                        Language::recurseDeleteDir($dir.'/'.$file);
                    } elseif (file_exists($dir.'/'.$file)) {
                        @unlink($dir.'/'.$file);
                    }
                }
            }
            closedir($handle);
        }
        if (is_writable($dir)) {
            rmdir($dir);
        }
    }

    public function delete()
    {
        if (!$this->hasMultishopEntries() || Shop::getContext() == Shop::CONTEXT_ALL) {
            if (empty($this->iso_code)) {
                $this->iso_code = Language::getIsoById($this->id);
            }

            // Database translations deletion
            $result = Db::getInstance()->executeS('SHOW TABLES FROM `'._DB_NAME_.'`');
            foreach ($result as $row) {
                if (isset($row['Tables_in_'._DB_NAME_]) && !empty($row['Tables_in_'._DB_NAME_]) && preg_match('/'.preg_quote(_DB_PREFIX_).'_lang/', $row['Tables_in_'._DB_NAME_])) {
                    if (!Db::getInstance()->execute('DELETE FROM `'.$row['Tables_in_'._DB_NAME_].'` WHERE `id_lang` = '.(int)$this->id)) {
                        return false;
                    }
                }
            }


            // Delete tags
            Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'tag WHERE id_lang = '.(int)$this->id);

            // Delete search words
            Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'search_word WHERE id_lang = '.(int)$this->id);

            // Files deletion
            foreach (Language::getFilesList($this->iso_code, _THEME_NAME_, false, false, false, true, true) as $key => $file) {
                if (file_exists($key)) {
                    unlink($key);
                }
            }

            $modList = scandir(_PS_MODULE_DIR_);
            foreach ($modList as $mod) {
                Language::recurseDeleteDir(_PS_MODULE_DIR_.$mod.'/mails/'.$this->iso_code);
                $files = @scandir(_PS_MODULE_DIR_.$mod.'/mails/');
                if (count($files) <= 2) {
                    Language::recurseDeleteDir(_PS_MODULE_DIR_.$mod.'/mails/');
                }

                if (file_exists(_PS_MODULE_DIR_.$mod.'/'.$this->iso_code.'.php')) {
                    unlink(_PS_MODULE_DIR_.$mod.'/'.$this->iso_code.'.php');
                    $files = @scandir(_PS_MODULE_DIR_.$mod);
                    if (count($files) <= 2) {
                        Language::recurseDeleteDir(_PS_MODULE_DIR_.$mod);
                    }
                }
            }

            if (file_exists(_PS_MAIL_DIR_.$this->iso_code)) {
                Language::recurseDeleteDir(_PS_MAIL_DIR_.$this->iso_code);
            }
            if (file_exists(_PS_TRANSLATIONS_DIR_.$this->iso_code)) {
                Language::recurseDeleteDir(_PS_TRANSLATIONS_DIR_.$this->iso_code);
            }

            $images = array(
                '.jpg',
                '-default-'.ImageType::getFormatedName('thickbox').'.jpg',
                '-default-'.ImageType::getFormatedName('home').'.jpg',
                '-default-'.ImageType::getFormatedName('large').'.jpg',
                '-default-'.ImageType::getFormatedName('medium').'.jpg',
                '-default-'.ImageType::getFormatedName('small').'.jpg'
            );
            $images_directories = array(_PS_CAT_IMG_DIR_, _PS_MANU_IMG_DIR_, _PS_PROD_IMG_DIR_, _PS_SUPP_IMG_DIR_);
            foreach ($images_directories as $image_directory) {
                foreach ($images as $image) {
                    if (file_exists($image_directory.$this->iso_code.$image)) {
                        unlink($image_directory.$this->iso_code.$image);
                    }
                    if (file_exists(_PS_ROOT_DIR_.'/img/l/'.$this->id.'.jpg')) {
                        unlink(_PS_ROOT_DIR_.'/img/l/'.$this->id.'.jpg');
                    }
                }
            }
        }

        if (!parent::delete()) {
            return false;
        }

        return true;
    }

    public function deleteSelection($selection)
    {
        if (!is_array($selection)) {
            die(Tools::displayError());
        }

        $result = true;
        foreach ($selection as $id) {
            $language = new Language($id);
            $result = $result && $language->delete();
        }

        return $result;
    }

    /**
     * Returns available languages
     *
     * @param bool     $active   Select only active languages
     * @param int|bool $id_shop  Shop ID
     * @param bool     $ids_only If true, returns an array of language IDs
     *
     * @return array Languages
     */
    public static function getLanguages($active = true, $id_shop = false, $ids_only = false)
    {
        if (!self::$_LANGUAGES) {
            Language::loadLanguages();
        }

        $languages = array();
        foreach (self::$_LANGUAGES as $language) {
            if ($active && !$language['active'] || ($id_shop && !isset($language['shops'][(int)$id_shop]))) {
                continue;
            }

            $languages[] = $ids_only ? $language['id_lang'] : $language;
        }

        return $languages;
    }

    /**
     * Returns an array of language IDs
     *
     * @param bool     $active  Select only active languages
     * @param int|bool $id_shop Shop ID
     *
     * @return array
     */
    public static function getIDs($active = true, $id_shop = false)
    {
        return self::getLanguages($active, $id_shop, true);
    }

    public static function getLanguage($id_lang)
    {
        if (!array_key_exists((int)$id_lang, self::$_LANGUAGES)) {
            return false;
        }
        return self::$_LANGUAGES[(int)($id_lang)];
    }

    /**
     * Return iso code from id
     *
     * @param int $id_lang Language ID
     * @return string Iso code
     */
    public static function getIsoById($id_lang)
    {
        if (isset(self::$_LANGUAGES[(int)$id_lang]['iso_code'])) {
            return self::$_LANGUAGES[(int)$id_lang]['iso_code'];
        }
        return false;
    }

    /**
     * Return id from iso code
     *
     * @param string $iso_code Iso code
     * @param bool $no_cache
     * @return false|null|string
     */
    public static function getIdByIso($iso_code, $no_cache = false)
    {
        if (!Validate::isLanguageIsoCode($iso_code)) {
            die(Tools::displayError('Fatal error: ISO code is not correct').' '.Tools::safeOutput($iso_code));
        }

        $key = 'Language::getIdByIso_'.$iso_code;
        if ($no_cache || !Cache::isStored($key)) {
            $id_lang = Db::getInstance()->getValue('SELECT `id_lang` FROM `'._DB_PREFIX_.'lang` WHERE `iso_code` = \''.pSQL(strtolower($iso_code)).'\'');

            Cache::store($key, $id_lang);
            return $id_lang;
        }
        return Cache::retrieve($key);
    }

    public static function getLanguageCodeByIso($iso_code)
    {
        if (!Validate::isLanguageIsoCode($iso_code)) {
            die(Tools::displayError('Fatal error: ISO code is not correct').' '.Tools::safeOutput($iso_code));
        }

        return Db::getInstance()->getValue('SELECT `language_code` FROM `'._DB_PREFIX_.'lang` WHERE `iso_code` = \''.pSQL(strtolower($iso_code)).'\'');
    }

    public static function getLanguageByIETFCode($code)
    {
        if (!Validate::isLanguageCode($code)) {
            die(sprintf(Tools::displayError('Fatal error: IETF code %s is not correct'), Tools::safeOutput($code)));
        }

        // $code is in the form of 'xx-YY' where xx is the language code
        // and 'YY' a country code identifying a variant of the language.
        $lang_country = explode('-', $code);
        // Get the language component of the code
        $lang = $lang_country[0];

        // Find the id_lang of the language.
        // We look for anything with the correct language code
        // and sort on equality with the exact IETF code wanted.
        // That way using only one query we get either the exact wanted language
        // or a close match.
        $id_lang = Db::getInstance()->getValue(
            'SELECT `id_lang`, IF(language_code = \''.pSQL($code).'\', 0, LENGTH(language_code)) as found
			FROM `'._DB_PREFIX_.'lang`
			WHERE LEFT(`language_code`,2) = \''.pSQL($lang).'\'
			ORDER BY found ASC'
        );

        // Instantiate the Language object if we found it.
        if ($id_lang) {
            return new Language($id_lang);
        } else {
            return false;
        }
    }

    /**
     * Return array (id_lang, iso_code)
     *
     * @param string $iso_code Iso code
     * @return array  Language (id_lang, iso_code)
     */
    public static function getIsoIds($active = true)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT `id_lang`, `iso_code` FROM `'._DB_PREFIX_.'lang` '.($active ? 'WHERE active = 1' : ''));
    }

    public static function copyLanguageData($from, $to)
    {
        $result = Db::getInstance()->executeS('SHOW TABLES FROM `'._DB_NAME_.'`');
        foreach ($result as $row) {
            if (preg_match('/_lang/', $row['Tables_in_'._DB_NAME_]) && $row['Tables_in_'._DB_NAME_] != _DB_PREFIX_.'lang') {
                $result2 = Db::getInstance()->executeS('SELECT * FROM `'.$row['Tables_in_'._DB_NAME_].'` WHERE `id_lang` = '.(int)$from);
                if (!count($result2)) {
                    continue;
                }
                Db::getInstance()->execute('DELETE FROM `'.$row['Tables_in_'._DB_NAME_].'` WHERE `id_lang` = '.(int)$to);
                $query = 'INSERT INTO `'.$row['Tables_in_'._DB_NAME_].'` VALUES ';
                foreach ($result2 as $row2) {
                    $query .= '(';
                    $row2['id_lang'] = $to;
                    foreach ($row2 as $field) {
                        $query .= (!is_string($field) && $field == null) ? 'NULL,' : '\''.pSQL($field, true).'\',';
                    }
                    $query = rtrim($query, ',').'),';
                }
                $query = rtrim($query, ',');
                Db::getInstance()->execute($query);
            }
        }
        return true;
    }

    /**
     * Load all languages in memory for caching
     */
    public static function loadLanguages()
    {
        self::$_LANGUAGES = array();

        $sql = 'SELECT l.*, ls.`id_shop`
				FROM `'._DB_PREFIX_.'lang` l
				LEFT JOIN `'._DB_PREFIX_.'lang_shop` ls ON (l.id_lang = ls.id_lang)';

        $result = Db::getInstance()->executeS($sql);
        foreach ($result as $row) {
            if (!isset(self::$_LANGUAGES[(int)$row['id_lang']])) {
                self::$_LANGUAGES[(int)$row['id_lang']] = $row;
            }
            self::$_LANGUAGES[(int)$row['id_lang']]['shops'][(int)$row['id_shop']] = true;
        }
    }

    public static function checkAndAddLanguage($iso_code, $lang_pack = false, $only_add = false, $params_lang = null)
    {
        if (Language::getIdByIso($iso_code)) {
            return true;
        }

        // Initialize the language
        $lang = new Language();
        $lang->iso_code = Tools::strtolower($iso_code);
        $lang->language_code = $iso_code; // Rewritten afterwards if the language code is available
        $lang->active = true;

        // If the language pack has not been provided, retrieve it from prestashop.com
        if (!$lang_pack) {
            $lang_pack = Tools::jsonDecode(Tools::file_get_contents('http://www.prestashop.com/download/lang_packs/get_language_pack.php?version='._PS_VERSION_.'&iso_lang='.$iso_code));
        }

        // If a language pack has been found or provided, prefill the language object with the value
        if ($lang_pack) {
            foreach (get_object_vars($lang_pack) as $key => $value) {
                if ($key != 'iso_code' && isset(Language::$definition['fields'][$key])) {
                    $lang->$key = $value;
                }
            }
        }

        // Use the values given in parameters to override the data retrieved automatically
        if ($params_lang !== null && is_array($params_lang)) {
            foreach ($params_lang as $key => $value) {
                if ($key != 'iso_code' && isset(Language::$definition['fields'][$key])) {
                    $lang->$key = $value;
                }
            }
        }

        if (!$lang->name && $lang->iso_code) {
            $lang->name = $lang->iso_code;
        }

        if (!$lang->validateFields() || !$lang->validateFieldsLang() || !$lang->add(true, false, $only_add)) {
            return false;
        }

        if (isset($params_lang['allow_accented_chars_url']) && in_array($params_lang['allow_accented_chars_url'], array('1', 'true'))) {
            Configuration::updateGlobalValue('PS_ALLOW_ACCENTED_CHARS_URL', 1);
        }

        $flag = Tools::file_get_contents('http://www.prestashop.com/download/lang_packs/flags/jpeg/'.$iso_code.'.jpg');
        if ($flag != null && !preg_match('/<body>/', $flag)) {
            $file = fopen(_PS_ROOT_DIR_.'/img/l/'.(int)$lang->id.'.jpg', 'w');
            if ($file) {
                fwrite($file, $flag);
                fclose($file);
            } else {
                Language::_copyNoneFlag((int)$lang->id);
            }
        } else {
            Language::_copyNoneFlag((int)$lang->id);
        }

        $files_copy = array(
            '/en.jpg',
            '/en-default-'.ImageType::getFormatedName('thickbox').'.jpg',
            '/en-default-'.ImageType::getFormatedName('home').'.jpg',
            '/en-default-'.ImageType::getFormatedName('large').'.jpg',
            '/en-default-'.ImageType::getFormatedName('medium').'.jpg',
            '/en-default-'.ImageType::getFormatedName('small').'.jpg',
            '/en-default-'.ImageType::getFormatedName('scene').'.jpg'
        );

        foreach (array(_PS_CAT_IMG_DIR_, _PS_MANU_IMG_DIR_, _PS_PROD_IMG_DIR_, _PS_SUPP_IMG_DIR_) as $to) {
            foreach ($files_copy as $file) {
                @copy(_PS_ROOT_DIR_.'/img/l'.$file, $to.str_replace('/en', '/'.$iso_code, $file));
            }
        }

        return true;
    }

    protected static function _copyNoneFlag($id)
    {
        return copy(_PS_ROOT_DIR_.'/img/l/none.jpg', _PS_ROOT_DIR_.'/img/l/'.$id.'.jpg');
    }

    protected static $_cache_language_installation = null;
    public static function isInstalled($iso_code)
    {
        if (self::$_cache_language_installation === null) {
            self::$_cache_language_installation = array();
            $result = Db::getInstance()->executeS('SELECT `id_lang`, `iso_code` FROM `'._DB_PREFIX_.'lang`');
            foreach ($result as $row) {
                self::$_cache_language_installation[$row['iso_code']] = $row['id_lang'];
            }
        }
        return (isset(self::$_cache_language_installation[$iso_code]) ? self::$_cache_language_installation[$iso_code] : false);
    }

    public static function countActiveLanguages($id_shop = null)
    {
        if (isset(Context::getContext()->shop) && is_object(Context::getContext()->shop) && $id_shop === null) {
            $id_shop = (int)Context::getContext()->shop->id;
        }

        if (!isset(self::$countActiveLanguages[$id_shop])) {
            self::$countActiveLanguages[$id_shop] = Db::getInstance()->getValue('
				SELECT COUNT(DISTINCT l.id_lang) FROM `'._DB_PREFIX_.'lang` l
				JOIN '._DB_PREFIX_.'lang_shop lang_shop ON (lang_shop.id_lang = l.id_lang AND lang_shop.id_shop = '.(int)$id_shop.')
				WHERE l.`active` = 1
			');
        }
        return self::$countActiveLanguages[$id_shop];
    }

    public static function downloadAndInstallLanguagePack($iso, $version = null, $params = null, $install = true)
    {
        if (!Validate::isLanguageIsoCode((string)$iso)) {
            return false;
        }

        if ($version == null) {
            $version = _PS_VERSION_;
        }

        $lang_pack = false;
        $lang_pack_ok = false;
        $errors = array();
        $file = _PS_TRANSLATIONS_DIR_.(string)$iso.'.gzip';

        if (!$lang_pack_link = Tools::file_get_contents('http://www.prestashop.com/download/lang_packs/get_language_pack.php?version='.$version.'&iso_lang='.Tools::strtolower((string)$iso))) {
            $errors[] = Tools::displayError('Archive cannot be downloaded from prestashop.com.');
        } elseif (!$lang_pack = Tools::jsonDecode($lang_pack_link)) {
            $errors[] = Tools::displayError('Error occurred when language was checked according to your Prestashop version.');
        } elseif (empty($lang_pack->error) && ($content = Tools::file_get_contents('http://translations.prestashop.com/download/lang_packs/gzip/'.$lang_pack->version.'/'.Tools::strtolower($lang_pack->iso_code.'.gzip')))) {
            if (!@file_put_contents($file, $content)) {
                if (is_writable(dirname($file))) {
                    @unlink($file);
                    @file_put_contents($file, $content);
                } elseif (!is_writable($file)) {
                    $errors[] = Tools::displayError('Server does not have permissions for writing.').' ('.$file.')';
                }
            }
        }

        if (!file_exists($file)) {
            $errors[] = Tools::displayError('No language pack is available for your version.');
        } elseif ($install) {
            require_once(_PS_TOOL_DIR_.'tar/Archive_Tar.php');
            $gz = new Archive_Tar($file, true);
            $files_list = AdminTranslationsController::filterTranslationFiles(Language::getLanguagePackListContent((string)$iso, $gz));
            $files_paths = AdminTranslationsController::filesListToPaths($files_list);

            $i = 0;
            $tmp_array = array();

            foreach ($files_paths as $files_path) {
                $path = dirname($files_path);
                if (is_dir(_PS_TRANSLATIONS_DIR_.'../'.$path) && !is_writable(_PS_TRANSLATIONS_DIR_.'../'.$path) && !in_array($path, $tmp_array)) {
                    $errors[] = (!$i++? Tools::displayError('The archive cannot be extracted.').' ' : '').Tools::displayError('The server does not have permissions for writing.').' '.sprintf(Tools::displayError('Please check rights for %s'), $path);
                    $tmp_array[] = $path;
                }
            }

            if (defined('_PS_HOST_MODE_')) {
                $mails_files = array();
                $other_files = array();

                foreach ($files_list as $key => $data) {
                    if (substr($data['filename'], 0, 5) == 'mails') {
                        $mails_files[] = $data;
                    } else {
                        $other_files[] = $data;
                    }
                }

                $files_list = $other_files;
            }

            if (!$gz->extractList(AdminTranslationsController::filesListToPaths($files_list), _PS_TRANSLATIONS_DIR_.'../')) {
                $errors[] = sprintf(Tools::displayError('Cannot decompress the translation file for the following language: %s'), (string)$iso);
            }

            // Clear smarty modules cache
            Tools::clearCache();

            if (!Language::checkAndAddLanguage((string)$iso, $lang_pack, false, $params)) {
                $errors[] = sprintf(Tools::displayError('An error occurred while creating the language: %s'), (string)$iso);
            } else {
                // Reset cache
                Language::loadLanguages();
                AdminTranslationsController::checkAndAddMailsFiles((string)$iso, $files_list);
                AdminTranslationsController::addNewTabs((string)$iso, $files_list);
            }
            //By webkul....Replace our customized mail files in the mail folder.....
            Language::replaceCustomizedMailFiles($iso);
        }

        return count($errors) ? $errors : true;
    }

    //By webkul....Replace our customized mail files in the mail folder.....
    public static function replaceCustomizedMailFiles($iso)
    {
        $directory = _PS_TRANSLATIONS_DIR_.'custom_mails/';
        // Open a directory, and read its contents
        if (is_dir($directory)){
            $mail_to_path = _PS_MAIL_DIR_.$iso.'/';
            if ($d_open = opendir($directory)){
                while (($file = readdir($d_open)) !== false){
                    if (($file == ".") || ($file == "..")) {
                        continue;
                    }
                    copy($directory.$file, $mail_to_path.$file);
                }
                closedir($d_open);  
            }
        }
    }

    /**
     * Check if more on than one language is activated
     *
     * @since 1.5.0
     * @return bool
     */
    public static function isMultiLanguageActivated($id_shop = null)
    {
        return (Language::countActiveLanguages($id_shop) > 1);
    }

    public static function getLanguagePackListContent($iso, $tar)
    {
        $key = 'Language::getLanguagePackListContent_'.$iso;
        if (!Cache::isStored($key)) {
            if (!$tar instanceof Archive_Tar) {
                return false;
            }
            $result = $tar->listContent();
            Cache::store($key, $result);
            return $result;
        }
        return Cache::retrieve($key);
    }

    public static function updateModulesTranslations(Array $modules_list)
    {
        require_once(_PS_TOOL_DIR_.'tar/Archive_Tar.php');

        $languages = Language::getLanguages(false);
        foreach ($languages as $lang) {
            $gz = false;
            $files_listing = array();
            foreach ($modules_list as $module_name) {
                $filegz = _PS_TRANSLATIONS_DIR_.$lang['iso_code'].'.gzip';

                clearstatcache();
                if (@filemtime($filegz) < (time() - (24 * 3600))) {
                    if (Language::downloadAndInstallLanguagePack($lang['iso_code'], null, null, false) !== true) {
                        break;
                    }
                }

                $gz = new Archive_Tar($filegz, true);
                $files_list = Language::getLanguagePackListContent($lang['iso_code'], $gz);
                foreach ($files_list as $i => $file) {
                    if (strpos($file['filename'], 'modules/'.$module_name.'/') !== 0) {
                        unset($files_list[$i]);
                    }
                }

                foreach ($files_list as $file) {
                    if (isset($file['filename']) && is_string($file['filename'])) {
                        $files_listing[] = $file['filename'];
                    }
                }
            }
            if ($gz) {
                $gz->extractList($files_listing, _PS_TRANSLATIONS_DIR_.'../', '');
            }
        }
    }
}
