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

include_once('../init.php');
include_once(_PS_ROOT_DIR_.'/config/settings.inc.php');
include_once(_PS_INSTALL_PATH_.'classes/controllerHttp.php');

class SynchronizeController extends InstallControllerHttp
{
    public function validate()
    {
    }
    public function display()
    {
    }
    public function processNextStep()
    {
    }

    /**
     * @var InstallXmlLoader
     */
    protected $loader;

    public function displayTemplate($template, $get_output = false, $path = null)
    {
        parent::displayTemplate($template, false, _PS_INSTALL_PATH_.'dev/');
    }

    public function init()
    {
        $this->type = Tools::getValue('type');
        $this->loader = new InstallXmlLoader();
        $languages = array();
        foreach (Language::getLanguages(false) as $language) {
            $languages[$language['id_lang']] = $language['iso_code'];
        }
        $this->loader->setLanguages($languages);

        if (Tools::getValue('submit')) {
            $this->generateSchemas();
        } elseif (Tools::getValue('synchronize')) {
            $this->synchronizeEntities();
        }

        if ($this->type == 'demo') {
            $this->loader->setFixturesPath();
        } else {
            $this->loader->setDefaultPath();
        }
        $this->displayTemplate('index');
    }

    public function generateSchemas()
    {
        if ($this->type == 'demo') {
            $this->loader->setFixturesPath();
        }

        $tables = isset($_POST['tables']) ? (array)$_POST['tables'] : array();
        $columns = isset($_POST['columns']) ? (array)$_POST['columns'] : array();
        $relations = isset($_POST['relations']) ? (array)$_POST['relations'] : array();
        $ids = isset($_POST['id']) ? (array)$_POST['id'] : array();
        $primaries = isset($_POST['primary']) ? (array)$_POST['primary'] : array();
        $classes = isset($_POST['class']) ? (array)$_POST['class'] : array();
        $sqls = isset($_POST['sql']) ? (array)$_POST['sql'] : array();
        $orders = isset($_POST['order']) ? (array)$_POST['order'] : array();
        $images = isset($_POST['image']) ? (array)$_POST['image'] : array();
        $nulls = isset($_POST['null']) ? (array)$_POST['null'] : array();

        $entities = array();
        foreach ($tables as $table) {
            $config = array();
            if (isset($ids[$table]) && $ids[$table]) {
                $config['id'] = $ids[$table];
            }

            if (isset($primaries[$table]) && $primaries[$table]) {
                $config['primary'] = $primaries[$table];
            }

            if (isset($classes[$table]) && $classes[$table]) {
                $config['class'] = $classes[$table];
            }

            if (isset($sqls[$table]) && $sqls[$table]) {
                $config['sql'] = $sqls[$table];
            }

            if (isset($orders[$table]) && $orders[$table]) {
                $config['ordersql'] = $orders[$table];
            }

            if (isset($images[$table]) && $images[$table]) {
                $config['image'] = $images[$table];
            }

            if (isset($nulls[$table]) && $nulls[$table]) {
                $config['null'] = $nulls[$table];
            }

            $fields = array();
            if (isset($columns[$table])) {
                foreach ($columns[$table] as $column) {
                    $fields[$column] = array();
                    if (isset($relations[$table][$column]['check'])) {
                        $fields[$column]['relation'] = $relations[$table][$column];
                    }
                }
            }

            $entities[$table] = array(
                'config' => $config,
                'fields' => $fields,
            );
        }

        foreach ($entities as $entity => $info) {
            $this->loader->generateEntitySchema($entity, $info['fields'], $info['config']);
        }

        $this->errors = $this->loader->getErrors();
    }

    public function synchronizeEntities()
    {
        $entities = Tools::getValue('entities');
        if (isset($entities['common'])) {
            $this->loader->setDefaultPath();
            $this->loader->generateEntityFiles($entities['common']);
        }

        if (isset($entities['fixture'])) {
            $this->loader->setFixturesPath();
            $this->loader->generateEntityFiles($entities['fixture']);
        }

        $this->errors = $this->loader->getErrors();
        $this->loader->setDefaultPath();
    }
}

new SynchronizeController('synchronize');
