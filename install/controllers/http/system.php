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

/**
 * Step 2 : check system configuration (permissions on folders, PHP version, etc.)
 */
class InstallControllerHttpSystem extends InstallControllerHttp
{
    public $tests = array();

    /**
     * @var InstallModelSystem
     */
    public $model_system;

    /**
     * @see InstallAbstractModel::init()
     */
    public function init()
    {
        require_once _PS_INSTALL_MODELS_PATH_.'system.php';
        $this->model_system = new InstallModelSystem();
    }

    /**
     * @see InstallAbstractModel::processNextStep()
     */
    public function processNextStep()
    {
    }

    /**
     * Required tests must be passed to validate this step
     *
     * @see InstallAbstractModel::validate()
     */
    public function validate()
    {
        $this->tests['required'] = $this->model_system->checkRequiredTests();

        return $this->tests['required']['success'];
    }

    /**
     * Display system step
     */
    public function display()
    {
        if (!isset($this->tests['required'])) {
            $this->tests['required'] = $this->model_system->checkRequiredTests();
        }
        if (!isset($this->tests['optional'])) {
            $this->tests['optional'] = $this->model_system->checkOptionalTests();
        }

        if (!is_callable('getenv') || !($user = @getenv('APACHE_RUN_USER'))) {
            $user = 'Apache';
        }

        // Generate display array
        $this->tests_render = array(
            'required' => array(
                array(
                    'title' => $this->l('Required PHP parameters'),
                    'success' => 1,
                    'checks' => array(
                        'phpversion' => $this->l('Minimum PHP 5.4.0 or later is required'),
                        'upload' => $this->l('Cannot upload files'),
                        'system' => $this->l('Cannot create new files and folders'),
                        'gd' => $this->l('GD library is not installed'),
                        'pdo_mysql' => $this->l('PDO MySQL extension is not loaded'),
                        'curl' => $this->l('Curl extension is not loaded'),
                        'soap' => $this->l('SOAP extension is not loaded'),
                        'simplexml' => $this->l('SimpleXml extension is not loaded'),
                        'memory_limit' => $this->l('In the PHP configuration set memory_limit to minimum 128M'),
                        'max_execution_time' => $this->l('In the PHP configuration set max_execution_time to minimum 500'),
                        'upload_max_filesize' => $this->l('In the PHP configuration set upload_max_filesize to minimum 16M'),
                        'fopen' => $this->l('Cannot open external URLs (requires allow_url_fopen as On).'),
                    )
                ),
                array(
                    'title' => $this->l('Files'),
                    'success' => 1,
                    'checks' => array(
                        'files' => $this->l('Not all files were successfully uploaded on your server')
                    )
                ),
                array(
                    'title' => $this->l('Permissions on files and folders'),
                    'success' => 1,
                    'checks' => array(
                        'config_dir' => $this->l('Recursive write permissions for %1$s user on %2$s', $user, '~/config/'),
                        'cache_dir' => $this->l('Recursive write permissions for %1$s user on %2$s', $user, '~/cache/'),
                        'log_dir' => $this->l('Recursive write permissions for %1$s user on %2$s', $user, '~/log/'),
                        'img_dir' => $this->l('Recursive write permissions for %1$s user on %2$s', $user, '~/img/'),
                        'mails_dir' => $this->l('Recursive write permissions for %1$s user on %2$s', $user, '~/mails/'),
                        'module_dir' => $this->l('Recursive write permissions for %1$s user on %2$s', $user, '~/modules/'),
                        'theme_lang_dir' => $this->l('Recursive write permissions for %1$s user on %2$s', $user, '~/themes/hotel-reservation-theme/lang/'),
                        'theme_pdf_lang_dir' => $this->l('Recursive write permissions for %1$s user on %2$s', $user, '~/themes/hotel-reservation-theme/pdf/lang/'),
                        'theme_cache_dir' => $this->l('Recursive write permissions for %1$s user on %2$s', $user, '~/themes/hotel-reservation-theme/cache/'),
                        'translations_dir' => $this->l('Recursive write permissions for %1$s user on %2$s', $user, '~/translations/'),
                        'customizable_products_dir' => $this->l('Recursive write permissions for %1$s user on %2$s', $user, '~/upload/'),
                        'virtual_products_dir' => $this->l('Recursive write permissions for %1$s user on %2$s', $user, '~/download/')
                    )
                ),
            ),
            'optional' => array(
                array(
                    'title' => $this->l('Recommended PHP parameters'),
                    'success' => $this->tests['optional']['success'],
                    'checks' => array(
                        'new_phpversion' => sprintf($this->l('You are using PHP %s version. Soon, the latest PHP version supported by Qloapps will be PHP 5.4. To make sure you’re ready for the future, we recommend you to upgrade to PHP 5.4 now!'), phpversion()),
                        'register_globals' => $this->l('PHP register_globals option is enabled'),
                        'gz' => $this->l('GZIP compression is not activated'),
                        'mcrypt' => $this->l('Mcrypt extension is not enabled'),
                        'mbstring' => $this->l('Mbstring extension is not enabled'),
                        'magicquotes' => $this->l('PHP magic quotes option is enabled'),
                        'dom' => $this->l('Dom extension is not loaded'),
                    )
                ),
            ),
        );

        foreach ($this->tests_render['required'] as &$category) {
            foreach ($category['checks'] as $id => $check) {
                if ($this->tests['required']['checks'][$id] != 'ok') {
                    $category['success'] = 0;
                }
            }
        }

        // If required tests failed, disable next button
        if (!$this->tests['required']['success']) {
            $this->next_button = false;
        }

        $this->displayTemplate('system');
    }
}
