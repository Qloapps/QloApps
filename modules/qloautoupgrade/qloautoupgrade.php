<?php
/**
* 2010-2019 Webkul.
*
* NOTICE OF LICENSE
*
* All right is reserved,
* Please go through this link for complete license : https://store.webkul.com/license.html
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to https://store.webkul.com/customisation-guidelines/ for more information.
*
*  @author    Webkul IN <support@webkul.com>
*  @copyright 2010-2019 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

class QloAutoupgrade extends Module
{

    public function __construct()
    {
        $this->name = 'qloautoupgrade';
        $this->tab = 'administration';
        $this->author = 'Webkul';
        $this->version = '1.0.1';
        $this->need_instance = 1;

        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->l('QloApps 1-Click Upgrade');
        $this->description = $this->l('Update your QloApps version to latest version of QloApps.');
        $this->confirmUninstall = $this->l('Are you sure to uninstall this module?');
    }

    public function install()
    {
        if (50600 >  PHP_VERSION_ID) {
            $this->_errors[] = $this->trans('This version of 1-click upgrade requires PHP 5.6 to work properly. Please upgrade your server configuration.', array(), 'Modules.Autoupgrade.Admin');

            return false;
        }

        if (defined('_PS_HOST_MODE_') && _PS_HOST_MODE_) {
            return false;
        }

        // If the "AdminSelfUpgrade" tab does not exist yet, create it
        if (!$id_tab = Tab::getIdFromClassName('AdminSelfUpgrade')) {
            $tab = new Tab();
            $tab->class_name = 'AdminSelfUpgrade';
            $tab->module = $this->name;
            $tab->id_parent = (int) Tab::getIdFromClassName('AdminTools');
            foreach (Language::getLanguages(false) as $lang) {
                $tab->name[(int) $lang['id_lang']] = $this->displayName;
            }
            if (!$tab->save()) {
                return $this->_abortInstall($this->trans('Unable to create the "AdminSelfUpgrade" tab', array(), 'Modules.Autoupgrade.Admin'));
            }
            if (!@copy(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'logo.gif', _PS_ROOT_DIR_ . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . 't' . DIRECTORY_SEPARATOR . 'AdminSelfUpgrade.gif')) {
                return $this->_abortInstall($this->trans('Unable to copy logo.gif in %s', array(_PS_ROOT_DIR_ . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . 't' . DIRECTORY_SEPARATOR), 'Modules.Autoupgrade.Admin'));
            }
        } else {
            $tab = new Tab((int) $id_tab);
        }

        // Update the "AdminSelfUpgrade" tab id in database or exit
        if (Validate::isLoadedObject($tab)) {
            Configuration::updateValue('PS_AUTOUPDATE_MODULE_IDTAB', (int) $tab->id);
        } else {
            return $this->_abortInstall($this->trans('Unable to load the "AdminSelfUpgrade" tab', array(), 'Modules.Autoupgrade.Admin'));
        }

        // Check that the 1-click upgrade working directory is existing or create it
        $autoupgrade_dir = _PS_ADMIN_DIR_ . DIRECTORY_SEPARATOR . 'qloautoupgrade';
        if (!file_exists($autoupgrade_dir) && !@mkdir($autoupgrade_dir)) {
            return $this->_abortInstall($this->trans('Unable to create the directory "%s"', array($autoupgrade_dir), 'Modules.Autoupgrade.Admin'));
        }

        // Make sure that the 1-click upgrade working directory is writeable
        if (!is_writable($autoupgrade_dir)) {
            return $this->_abortInstall($this->trans('Unable to write in the directory "%s"', array($autoupgrade_dir), 'Modules.Autoupgrade.Admin'));
        }

        // If a previous version of ajax-upgradetab.php exists, delete it
        if (file_exists($autoupgrade_dir . DIRECTORY_SEPARATOR . 'ajax-upgradetab.php')) {
            @unlink($autoupgrade_dir . DIRECTORY_SEPARATOR . 'ajax-upgradetab.php');
        }

        // Then, try to copy the newest version from the module's directory
        if (!@copy(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'ajax-upgradetab.php', $autoupgrade_dir . DIRECTORY_SEPARATOR . 'ajax-upgradetab.php')) {
            return $this->_abortInstall($this->trans('Unable to copy ajax-upgradetab.php in %s', array($autoupgrade_dir), 'Modules.Autoupgrade.Admin'));
        }

        // Make sure that the XML config directory exists
        if (!file_exists(_PS_ROOT_DIR_ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'xml') &&
        !@mkdir(_PS_ROOT_DIR_ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'xml', 0775)) {
            return $this->_abortInstall($this->trans('Unable to create the directory "%s"', array(_PS_ROOT_DIR_ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'xml'), 'Modules.Autoupgrade.Admin'));
        } else {
            @chmod(_PS_ROOT_DIR_ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'xml', 0775);
        }

        // Create a dummy index.php file in the XML config directory to avoid directory listing
        if (!file_exists(_PS_ROOT_DIR_ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'xml' . DIRECTORY_SEPARATOR . 'index.php') &&
        (file_exists(_PS_ROOT_DIR_ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'index.php') &&
        !@copy(_PS_ROOT_DIR_ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'index.php', _PS_ROOT_DIR_ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'xml' . DIRECTORY_SEPARATOR . 'index.php'))) {
            return $this->_abortInstall($this->trans('Unable to create the directory "%s"', array(_PS_ROOT_DIR_ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'xml'), 'Modules.Autoupgrade.Admin'));
        }

        return parent::install();
    }

    public function uninstall()
    {
        if (!parent::uninstall()
            || !$this->uninstallTab()
        ) {
            return false;
        } else {

            // Remove the 1-click upgrade working directory
            self::_removeDirectory(_PS_ADMIN_DIR_ . DIRECTORY_SEPARATOR . 'qloautoupgrade');

            return true;
        }
    }

    public function uninstallTab()
    {
        $moduleTabs = Tab::getCollectionFromModule($this->name);
        if (!empty($moduleTabs)) {
            foreach ($moduleTabs as $moduleTab) {
                $moduleTab->delete();
            }
        }
        return true;
    }

    private static function _removeDirectory($dir)
    {
        if ($handle = @opendir($dir)) {
            while (false !== ($entry = @readdir($handle))) {
                if ($entry != '.' && $entry != '..') {
                    if (is_dir($dir . DIRECTORY_SEPARATOR . $entry) === true) {
                        self::_removeDirectory($dir . DIRECTORY_SEPARATOR . $entry);
                    } else {
                        @unlink($dir . DIRECTORY_SEPARATOR . $entry);
                    }
                }
            }

            @closedir($handle);
            @rmdir($dir);
        }
    }

    public function getContent()
    {
        global $cookie;
        header('Location: index.php?tab=AdminSelfUpgrade&token=' . md5(pSQL(_COOKIE_KEY_ . 'AdminSelfUpgrade' . (int) Tab::getIdFromClassName('AdminSelfUpgrade') . (int) $cookie->id_employee)));
        exit;
    }

    /**
     * Set installation errors and return false.
     *
     * @param string $error Installation abortion reason
     *
     * @return bool Always false
     */
    protected function _abortInstall($error)
    {
        $this->_errors[] = $error;

        return false;
    }

    /**
     * Adapter for trans calls, existing only on PS 1.7.
     * Making them available for PS 1.6 as well.
     *
     * @param string $id
     * @param array $parameters
     * @param string $domain
     * @param string $locale
     */
    public function trans($id, array $parameters = array(), $domain = null, $locale = null)
    {
        require_once _PS_ROOT_DIR_ . '/modules/qloautoupgrade/classes/UpgradeTools/Translator.php';

        $translator = new \PrestaShop\Module\AutoUpgrade\UpgradeTools\Translator(__CLASS__);

        return $translator->trans($id, $parameters, $domain, $locale);
    }
}