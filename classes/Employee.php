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

class EmployeeCore extends ObjectModel
{
    public $id;

    /** @var string Determine employee profile */
    public $id_profile;

    /** @var string employee language */
    public $id_lang;

    /** @var string Lastname */
    public $lastname;

    /** @var string Firstname */
    public $firstname;

    /** @var string e-mail */
    public $email;

    /** @var string Password */
    public $passwd;

    /** @var datetime Password */
    public $last_passwd_gen;

    public $stats_date_from;
    public $stats_date_to;

    public $stats_compare_from;
    public $stats_compare_to;
    public $stats_compare_option = 1;

    public $preselect_date_range;

    /** @var string Display back office background in the specified color */
    public $bo_color;

    public $default_tab;

    /** @var string employee's chosen theme */
    public $bo_theme;

    /** @var string employee's chosen css file */
    public $bo_css = 'admin-theme.css';

    /** @var int employee desired screen width */
    public $bo_width;

    /** @var bool, false */
    public $bo_menu = 1;

    /* Deprecated */
    public $bo_show_screencast = false;

    /** @var bool Status */
    public $active = 1;

    /** @var bool Optin status */
    public $optin = 1;

    public $remote_addr;

    /* employee notifications */
    public $id_last_order;
    public $id_last_customer_message;
    public $id_last_customer;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'employee',
        'primary' => 'id_employee',
        'fields' => array(
            'lastname' =>                    array('type' => self::TYPE_STRING, 'validate' => 'isName', 'required' => true, 'size' => 32),
            'firstname' =>                    array('type' => self::TYPE_STRING, 'validate' => 'isName', 'required' => true, 'size' => 32),
            'email' =>                        array('type' => self::TYPE_STRING, 'validate' => 'isEmail', 'required' => true, 'size' => 128),
            'id_lang' =>                    array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true),
            'passwd' =>                    array('type' => self::TYPE_STRING, 'validate' => 'isPasswdAdmin', 'required' => true, 'size' => 32),
            'last_passwd_gen' =>            array('type' => self::TYPE_STRING),
            'active' =>                    array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'optin' =>                        array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'id_profile' =>                array('type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true),
            'bo_color' =>                    array('type' => self::TYPE_STRING, 'validate' => 'isColor', 'size' => 32),
            'default_tab' =>                array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'bo_theme' =>                    array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 32),
            'bo_css' =>                    array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 64),
            'bo_width' =>                    array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'bo_menu' =>                    array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'stats_date_from' =>            array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'stats_date_to' =>                array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'stats_compare_from' =>            array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'stats_compare_to' =>            array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'stats_compare_option' =>        array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'preselect_date_range' =>        array('type' => self::TYPE_STRING, 'size' => 32),
            'id_last_order' =>                array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'id_last_customer_message' =>    array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'id_last_customer' =>            array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
        ),
    );

    protected $webserviceParameters = array(
        'fields' => array(
            'id_lang' => array('xlink_resource' => 'languages'),
            'last_passwd_gen' => array('setter' => null),
            'stats_date_from' => array('setter' => null),
            'stats_date_to' => array('setter' => null),
            'stats_compare_from' => array('setter' => null),
            'stats_compare_to' => array('setter' => null),
            'passwd' => array('setter' => 'setWsPasswd'),
        ),
    );

    protected $associated_shops = array();

    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        parent::__construct($id, null, $id_shop);

        if (!is_null($id_lang)) {
            $this->id_lang = (int)(Language::getLanguage($id_lang) !== false) ? $id_lang : Configuration::get('PS_LANG_DEFAULT');
        }

        if ($this->id) {
            $this->associated_shops = $this->getAssociatedShops();
        }

        $this->image_dir = _PS_EMPLOYEE_IMG_DIR_;
    }

    /**
     * @see ObjectModel::getFields()
     * @return array
     */
    public function getFields()
    {
        if (empty($this->stats_date_from) || $this->stats_date_from == '0000-00-00') {
            $this->stats_date_from = date('Y-m-d', strtotime('-1 month'));
        }

        if (empty($this->stats_compare_from) || $this->stats_compare_from == '0000-00-00') {
            $this->stats_compare_from = null;
        }

        if (empty($this->stats_date_to) || $this->stats_date_to == '0000-00-00') {
            $this->stats_date_to = date('Y-m-d');
        }

        if (empty($this->stats_compare_to) || $this->stats_compare_to == '0000-00-00') {
            $this->stats_compare_to = null;
        }

        return parent::getFields();
    }

    public function add($autodate = true, $null_values = true)
    {
        $this->last_passwd_gen = date('Y-m-d H:i:s', strtotime('-'.Configuration::get('PS_PASSWD_TIME_BACK').'minutes'));
        $this->saveOptin();
        $this->updateTextDirection();
        return parent::add($autodate, $null_values);
    }

    public function update($null_values = false)
    {
        if (empty($this->stats_date_from) || $this->stats_date_from == '0000-00-00') {
            $this->stats_date_from = date('Y-m-d');
        }

        if (empty($this->stats_date_to) || $this->stats_date_to == '0000-00-00') {
            $this->stats_date_to = date('Y-m-d');
        }

        $currentEmployee = new Employee((int)$this->id);

        if ($currentEmployee->optin != $this->optin) {
            $this->saveOptin();
        }

        $this->updateTextDirection();
        return parent::update($null_values);
    }

    protected function updateTextDirection()
    {
        if (!defined('_PS_ADMIN_DIR_')) {
            return;
        }

        $path = _PS_ADMIN_DIR_.DIRECTORY_SEPARATOR.'themes'.DIRECTORY_SEPARATOR.$this->bo_theme.DIRECTORY_SEPARATOR.'css'.DIRECTORY_SEPARATOR;
        $language = new Language($this->id_lang);

        if ($language->is_rtl && !strpos($this->bo_css, '_rtl')) {
            $bo_css = preg_replace('/^(.*)\.css$/', '$1_rtl.css', $this->bo_css);

            if (file_exists($path.$bo_css)) {
                $this->bo_css = $bo_css;
            }
        } elseif (!$language->is_rtl && strpos($this->bo_css, '_rtl')) {
            $bo_css = preg_replace('/^(.*)_rtl\.css$/', '$1.css', $this->bo_css);

            if (file_exists($path.$bo_css)) {
                $this->bo_css = $bo_css;
            }
        }
    }

    protected function saveOptin()
    {
        if ($this->optin && !defined('PS_INSTALLATION_IN_PROGRESS')) {
            $language = new Language($this->id_lang);
            $params = http_build_query(array(
                'email' => $this->email,
                'method' => 'addMemberToNewsletter',
                'language' => $language->iso_code,
                'visitorType' => 1,
                'source' => 'backoffice'
            ));
            Tools::file_get_contents('https://www.prestashop.com/ajax/controller.php?'.$params);
        }
    }

    /**
     * Return list of employees
     *
     * @param bool $active_only Filter employee by active status
     * @return array|false Employees or false
     */
    public static function getEmployees($active_only = true)
    {
        return Db::getInstance()->executeS('
			SELECT `id_employee`, `firstname`, `lastname`
			FROM `'._DB_PREFIX_.'employee`
			'.($active_only ? ' WHERE `active` = 1' : '').'
			ORDER BY `lastname` ASC
		');
    }

    /**
     * Return employee instance from its e-mail (optionnaly check password)
     *
     * @param string $email e-mail
     * @param string $passwd Password is also checked if specified
     * @param bool $active_only Filter employee by active status
     * @return Employee instance
     */
    public function getByEmail($email, $passwd = null, $active_only = true)
    {
        if (!Validate::isEmail($email) || ($passwd != null && !Validate::isPasswd($passwd))) {
            die(Tools::displayError());
        }

        $result = Db::getInstance()->getRow('
		SELECT *
		FROM `'._DB_PREFIX_.'employee`
		WHERE `email` = \''.pSQL($email).'\'
		'.($active_only ? ' AND `active` = 1' : '')
        .($passwd !== null ? ' AND `passwd` = \''.Tools::encrypt($passwd).'\'' : ''));
        if (!$result) {
            return false;
        }
        $this->id = $result['id_employee'];
        $this->id_profile = $result['id_profile'];
        foreach ($result as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
        return $this;
    }

    public static function employeeExists($email)
    {
        if (!Validate::isEmail($email)) {
            die(Tools::displayError());
        }

        return (bool)Db::getInstance()->getValue('
		SELECT `id_employee`
		FROM `'._DB_PREFIX_.'employee`
		WHERE `email` = \''.pSQL($email).'\'');
    }

    /**
     * Check if employee password is the right one
     *
     * @param string $passwd Password
     * @return bool result
     */
    public static function checkPassword($id_employee, $passwd)
    {
        if (!Validate::isUnsignedId($id_employee) || !Validate::isPasswd($passwd, 8)) {
            die(Tools::displayError());
        }

        return Db::getInstance()->getValue('
		SELECT `id_employee`
		FROM `'._DB_PREFIX_.'employee`
		WHERE `id_employee` = '.(int)$id_employee.'
		AND `passwd` = \''.pSQL($passwd).'\'
		AND `active` = 1');
    }

    public static function countProfile($id_profile, $active_only = false)
    {
        return Db::getInstance()->getValue('
		SELECT COUNT(*)
		FROM `'._DB_PREFIX_.'employee`
		WHERE `id_profile` = '.(int)$id_profile.'
		'.($active_only ? ' AND `active` = 1' : ''));
    }

    public function isLastAdmin()
    {
        return ($this->isSuperAdmin()
            && Employee::countProfile($this->id_profile, true) == 1
            && $this->active
        );
    }

    public function setWsPasswd($passwd)
    {
        if ($this->id != 0) {
            if ($this->passwd != $passwd) {
                $this->passwd = Tools::encrypt($passwd);
            }
        } else {
            $this->passwd = Tools::encrypt($passwd);
        }
        return true;
    }

    /**
     * Check employee informations saved into cookie and return employee validity
     *
     * @return bool employee validity
     */
    public function isLoggedBack()
    {
        if (!Cache::isStored('isLoggedBack'.$this->id)) {
            /* Employee is valid only if it can be load and if cookie password is the same as database one */
            $result = (
                            $this->id && Validate::isUnsignedId($this->id) && Employee::checkPassword($this->id, Context::getContext()->cookie->passwd)
                            && (!isset(Context::getContext()->cookie->remote_addr) || Context::getContext()->cookie->remote_addr == ip2long(Tools::getRemoteAddr()) || !Configuration::get('PS_COOKIE_CHECKIP'))
                        );
            Cache::store('isLoggedBack'.$this->id, $result);
            return $result;
        }
        return Cache::retrieve('isLoggedBack'.$this->id);
    }

    /**
     * Logout
     */
    public function logout()
    {
        if (isset(Context::getContext()->cookie)) {
            Context::getContext()->cookie->logout();
            Context::getContext()->cookie->write();
        }
        $this->id = null;
    }

    public function favoriteModulesList()
    {
        return Db::getInstance()->executeS('
			SELECT `module`
			FROM `'._DB_PREFIX_.'module_preference`
			WHERE `id_employee` = '.(int)$this->id.' AND `favorite` = 1 AND (`interest` = 1 OR `interest` IS NULL)'
        );
    }

    /**
     * Check if the employee is associated to a specific shop
     *
     * @since 1.5.0
     * @param int $id_shop
     * @return bool
     */
    public function hasAuthOnShop($id_shop)
    {
        return $this->isSuperAdmin() || in_array($id_shop, $this->associated_shops);
    }

    /**
     * Check if the employee is associated to a specific shop group
     *
     * @since 1.5.0
     * @param int $id_shop_shop
     * @return bool
     */
    public function hasAuthOnShopGroup($id_shop_group)
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        foreach ($this->associated_shops as $id_shop) {
            if ($id_shop_group == Shop::getGroupFromShop($id_shop, true)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get default id_shop with auth for current employee
     *
     * @since 1.5.0
     * @return int
     */
    public function getDefaultShopID()
    {
        if ($this->isSuperAdmin() || in_array(Configuration::get('PS_SHOP_DEFAULT'), $this->associated_shops)) {
            return Configuration::get('PS_SHOP_DEFAULT');
        }
        return $this->associated_shops[0];
    }

    public static function getEmployeesByProfile($id_profile, $active_only = false)
    {
        return Db::getInstance()->executeS('
		SELECT *
		FROM `'._DB_PREFIX_.'employee`
		WHERE `id_profile` = '.(int)$id_profile.'
		'.($active_only ? ' AND `active` = 1' : ''));
    }

    /**
     * Check if current employee is super administrator
     *
     * @return bool
     */
    public function isSuperAdmin()
    {
        return $this->id_profile == _PS_ADMIN_PROFILE_;
    }

    public function getImage()
    {
        if (!Validate::isLoadedObject($this)) {
            return Tools::getAdminImageUrl('prestashop-avatar.png');
        }
        return Tools::getShopProtocol().'profile.prestashop.com/'.urlencode($this->email).'.jpg';
    }

    public function getLastElementsForNotify($element)
    {
        $element = bqSQL($element);
        $max = Db::getInstance()->getValue('
			SELECT MAX(`id_'.$element.'`) as `id_'.$element.'`
			FROM `'._DB_PREFIX_.$element.($element == 'order' ? 's': '').'`');

        // if no rows in table, set max to 0
        if ((int)$max < 1) {
            $max = 0;
        }

        return (int)$max;
    }

    public static function setLastConnectionDate($id_employee)
    {
        return Db::getInstance()->execute('
			UPDATE `'._DB_PREFIX_.'employee`
			SET `last_connection_date` = CURRENT_DATE()
			WHERE `id_employee` = '.(int)$id_employee.' AND `last_connection_date`< CURRENT_DATE()
		');
    }
}
