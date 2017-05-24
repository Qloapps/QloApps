<?php
/**
* 2007-2016 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
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
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2016 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

class CronJobsForms
{
    protected static $module = false;

    public static function init($module)
    {
        if (self::$module == false) {
            self::$module = $module;
        }

        return self::$module;
    }

    public static function getJobForm($title = 'New cron task', $update = false)
    {
        $form = array(
            array(
                'form' => array(
                    'legend' => array(
                        'title' => self::$module->l($title),
                        'icon' => 'icon-plus',
                    ),
                    'input' => array(),
                    'submit' => array('title' => self::$module->l('Save', 'CronJobsForms'), 'type' => 'submit', 'class' => 'btn btn-default pull-right'),
                ),
            ),
        );

        $id_shop = (int)Context::getContext()->shop->id;
        $id_shop_group = (int)Context::getContext()->shop->id_shop_group;

        $currencies_cron_url = Tools::getShopDomain(true, true).__PS_BASE_URI__.basename(_PS_ADMIN_DIR_);
        $currencies_cron_url .= '/cron_currency_rates.php?secure_key='.md5(_COOKIE_KEY_.Configuration::get('PS_SHOP_NAME'));

        if (($update == true) && (Tools::isSubmit('id_cronjob'))) {
            $id_cronjob = (int)Tools::getValue('id_cronjob');
            $id_module = (int)Db::getInstance()->getValue('SELECT `id_module` FROM `'._DB_PREFIX_.bqSQL(self::$module->name).'`
                WHERE `id_cronjob` = \''.(int)$id_cronjob.'\'
                    AND `id_shop` = \''.$id_shop.'\' AND `id_shop_group` = \''.$id_shop_group.'\'');

            if ((bool)$id_module == true) {
                $form[0]['form']['input'][] = array(
                    'type' => 'free',
                    'name' => 'description',
                    'label' => self::$module->l('Task description', 'CronJobsForms'),
                    'placeholder' => self::$module->l('Update my currencies', 'CronJobsForms'),
                );

                $form[0]['form']['input'][] = array(
                    'type' => 'free',
                    'name' => 'task',
                    'label' => self::$module->l('Target link', 'CronJobsForms'),
                );
            } else {
                $form[0]['form']['input'][] = array(
                    'type' => 'text',
                    'name' => 'description',
                    'label' => self::$module->l('Task description', 'CronJobsForms'),
                    'desc' => self::$module->l('Enter a description for this task.', 'CronJobsForms'),
                    'placeholder' => self::$module->l('Update my currencies', 'CronJobsForms'),
                );

                $form[0]['form']['input'][] = array(
                    'type' => 'text',
                    'name' => 'task',
                    'label' => self::$module->l('Target link', 'CronJobsForms'),
                    'desc' => self::$module->l('Set the link of your cron task.', 'CronJobsForms'),
                    'placeholder' => $currencies_cron_url,
                );
            }
        } else {
            $form[0]['form']['input'][] = array(
                'type' => 'text',
                'name' => 'description',
                'label' => self::$module->l('Task description', 'CronJobsForms'),
                'desc' => self::$module->l('Enter a description for this task.', 'CronJobsForms'),
                'placeholder' => self::$module->l('Update my currencies', 'CronJobsForms'),
            );

            $form[0]['form']['input'][] = array(
                'type' => 'text',
                'name' => 'task',
                'label' => self::$module->l('Target link', 'CronJobsForms'),
                'desc' => self::$module->l('Do not forget to use an absolute URL to make it valid! The link also has to be on the same domain as the shop.', 'CronJobsForms'),
                'placeholder' => $currencies_cron_url,
            );
        }

        $form[0]['form']['input'][] = array(
            'type' => 'select',
            'name' => 'hour',
            'label' => self::$module->l('Task frequency', 'CronJobsForms'),
            'desc' => self::$module->l('At what time should this task be executed?', 'CronJobsForms'),
            'options' => array(
                'query' => self::getHoursFormOptions(),
                'id' => 'id', 'name' => 'name'
            ),
        );
        $form[0]['form']['input'][] = array(
            'type' => 'select',
            'name' => 'day',
            'desc' => self::$module->l('On which day of the month should this task be executed?', 'CronJobsForms'),
            'options' => array(
                'query' => self::getDaysFormOptions(),
                'id' => 'id', 'name' => 'name'
            ),
        );
        $form[0]['form']['input'][] = array(
            'type' => 'select',
            'name' => 'month',
            'desc' => self::$module->l('On what month should this task be executed?', 'CronJobsForms'),
            'options' => array(
                'query' => self::getMonthsFormOptions(),
                'id' => 'id', 'name' => 'name'
            ),
        );
        $form[0]['form']['input'][] = array(
            'type' => 'select',
            'name' => 'day_of_week',
            'desc' => self::$module->l('On which day of the week should this task be executed?', 'CronJobsForms'),
            'options' => array(
                'query' => self::getDaysofWeekFormOptions(),
                'id' => 'id', 'name' => 'name'
            ),
        );

        return $form;
    }

    public static function getForm()
    {
        $form = array(
            'form' => array(
                'legend' => array(
                    'title' => self::$module->l('Settings', 'CronJobsForms'),
                    'icon' => 'icon-cog',
                ),
                'input' => array(
                    array(
                        'type' => 'radio',
                        'name' => 'cron_mode',
                        'label' => self::$module->l('Cron mode', 'CronJobsForms'),
                        'values' => array(
                            array('id' => 'webservice', 'value' => 'webservice', 'label' => self::$module->l('Basic', 'CronJobsForms'),
                                'p' => self::$module->l('Use the PrestaShop cron tasks webservice to execute your tasks.', 'CronJobsForms')),
                            array('id' => 'advanced', 'value' => 'advanced', 'label' => self::$module->l('Advanced', 'CronJobsForms'),
                                'p' => self::$module->l('For advanced users only: use your own crontab manager instead of PrestaShop cron tasks service.', 'CronJobsForms'))
                        ),
                    ),
                ),
                'submit' => array('title' => self::$module->l('Save', 'CronJobsForms'), 'type' => 'submit', 'class' => 'btn btn-default pull-right'),
            ),
        );

        if (Configuration::get('CRONJOBS_MODE') == 'advanced') {
            $form['form']['input'][] = array('type' => 'free', 'name' => 'advanced_help', 'col' => 9, 'offset' => 0);
        }

        return array($form);
    }

    public static function getFormValues()
    {
        $token = Configuration::get('CRONJOBS_EXECUTION_TOKEN', null, 0, 0);
        $admin_folder = str_replace(_PS_ROOT_DIR_.'/', null, basename(_PS_ADMIN_DIR_));
        if (version_compare(_PS_VERSION_, '1.7', '<') == true) {
            $path = Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.$admin_folder.'/';
            $curl_url = $path.Context::getContext()->link->getAdminLink('AdminCronJobs', false);
            $curl_url .= '&token='.$token;
        } else {
            $curl_url = Context::getContext()->link->getAdminLink('AdminCronJobs', false);
            $curl_url .= '&token='.$token;
        }

        return array(
            'cron_mode' => Configuration::get('CRONJOBS_MODE'),
            'advanced_help' =>
                '<div class="alert alert-info">
                    <p>'
                        .self::$module->l('The Advanced mode enables you to use your own cron tasks manager instead of PrestaShop cron tasks webservice.', 'CronJobsForms').' '
                        .self::$module->l('First of all, make sure the \'curl\' library is installed on your server.', 'CronJobsForms')
                        .'<br />'.self::$module->l('To execute your cron tasks, please insert the following line in your cron tasks manager:', 'CronJobsForms').'
                    </p>
                    <br />
                    <ul class="list-unstyled">
                        <li><code>0 * * * * curl '.(Configuration::get('PS_SSL_ENABLED') ? '-k ' : null).'"'.$curl_url.'"</code></li>
                    </ul>
                </div>'
        );
    }

    public static function getTasksList()
    {
        return array(
            'description' => array('title' => self::$module->l('Task description', 'CronJobsForms'), 'type' => 'text', 'orderby' => false),
            'task' => array('title' => self::$module->l('Target link', 'CronJobsForms'), 'type' => 'text', 'orderby' => false),
            'hour' => array('title' => self::$module->l('Hour', 'CronJobsForms'), 'type' => 'text', 'orderby' => false),
            'day' => array('title' => self::$module->l('Day', 'CronJobsForms'), 'type' => 'text', 'orderby' => false),
            'month' => array('title' => self::$module->l('Month', 'CronJobsForms'), 'type' => 'text', 'orderby' => false),
            'day_of_week' => array('title' => self::$module->l('Day of week', 'CronJobsForms'), 'type' => 'text', 'orderby' => false),
            'updated_at' => array('title' => self::$module->l('Last execution', 'CronJobsForms'), 'type' => 'text', 'orderby' => false),
            'one_shot' => array('title' => self::$module->l('One shot', 'CronJobsForms'), 'active' => 'oneshot', 'type' => 'bool', 'align' => 'center'),
            'active' => array('title' => self::$module->l('Active', 'CronJobsForms'), 'active' => 'status', 'type' => 'bool', 'align' => 'center', 'orderby' => false),
        );
    }

    public static function getNewJobFormValues()
    {
        return array(
            'description' => Tools::safeOutput(Tools::getValue('description', null)),
            'task' => Tools::safeOutput(Tools::getValue('task', null)),
            'hour' => (int)Tools::getValue('hour', -1),
            'day' => (int)Tools::getValue('day', -1),
            'month' => (int)Tools::getValue('month', -1),
            'day_of_week' => (int)Tools::getValue('day_of_week', -1),
        );
    }

    public static function getUpdateJobFormValues()
    {
        $id_shop = (int)Context::getContext()->shop->id;
        $id_shop_group = (int)Context::getContext()->shop->id_shop_group;

        $id_cronjob = (int)Tools::getValue('id_cronjob');
        $cron = Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.bqSQL(self::$module->name).'`
            WHERE `id_cronjob` = \''.$id_cronjob.'\'
            AND `id_shop` = \''.$id_shop.'\' AND `id_shop_group` = \''.$id_shop_group.'\'');

        if ((bool)$cron['id_module'] == false) {
            $description = Tools::safeOutput(Tools::getValue('description', $cron['description']));
            $task = urldecode(Tools::getValue('task', $cron['task']));
        } else {
            $module_name = Db::getInstance()->getValue('SELECT `name` FROM `'._DB_PREFIX_.'module` WHERE `id_module` = \''.(int)$cron['id_module'].'\'');
            $description = '<p class="form-control-static"><strong>'.Tools::safeOutput(Module::getModuleName($module_name)).'</strong></p>';
            $task = '<p class="form-control-static"><strong>'.self::$module->l('Module - Hook', 'CronJobsForms').'</strong></p>';
        }

        return array(
            'description' => $description,
            'task' => $task,
            'hour' => (int)Tools::getValue('hour', $cron['hour']),
            'day' => (int)Tools::getValue('day', $cron['day']),
            'month' => (int)Tools::getValue('month', $cron['month']),
            'day_of_week' => (int)Tools::getValue('day_of_week', $cron['day_of_week']),
        );
    }

    public static function getTasksListValues()
    {
        $id_shop = (int)Context::getContext()->shop->id;
        $id_shop_group = (int)Context::getContext()->shop->id_shop_group;

        self::$module->addNewModulesTasks();
        $crons = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.bqSQL(self::$module->name).'` WHERE `id_shop` = \''.$id_shop.'\' AND `id_shop_group` = \''.$id_shop_group.'\'');

        foreach ($crons as $key => &$cron) {
            if (empty($cron['id_module']) == false) {
                $module = Module::getInstanceById((int)$cron['id_module']);

                if ($module == false) {
                    Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.bqSQL(self::$module->name).' WHERE `id_cronjob` = \''.(int)$cron['id_cronjob'].'\'');
                    unset($crons[$key]);
                    break;
                }

                $query = 'SELECT `name` FROM `'._DB_PREFIX_.'module` WHERE `id_module` = \''.(int)$cron['id_module'].'\'';
                $module_name = Db::getInstance()->getValue($query);

                $cron['description'] = Tools::safeOutput(Module::getModuleName($module_name));
                $cron['task'] = self::$module->l('Module - Hook', 'CronJobsForms');
            } else {
                $cron['task'] = urldecode($cron['task']);
            }

            $cron['hour'] = ($cron['hour'] == -1) ? self::$module->l('Every hour', 'CronJobsForms') : date('H:i', mktime((int)$cron['hour'], 0, 0, 0, 1));
            $cron['day'] = ($cron['day'] == -1) ? self::$module->l('Every day', 'CronJobsForms') : (int)$cron['day'];
            $cron['month'] = ($cron['month'] == -1) ? self::$module->l('Every month', 'CronJobsForms') : self::$module->l(date('F', mktime(0, 0, 0, (int)$cron['month'], 1)));
            $cron['day_of_week'] = ($cron['day_of_week'] == -1) ? self::$module->l('Every day of the week', 'CronJobsForms') : self::$module->l(date('l', mktime(0, 0, 0, 0, (int)$cron['day_of_week'])));
            $cron['updated_at'] = ($cron['updated_at'] == 0) ? self::$module->l('Never', 'CronJobsForms') : date('Y-m-d H:i:s', strtotime($cron['updated_at']));
            $cron['one_shot'] = (bool)$cron['one_shot'];
            $cron['active'] = (bool)$cron['active'];
        }

        return $crons;
    }

    protected static function getHoursFormOptions()
    {
        $data = array(array('id' => '-1', 'name' => self::$module->l('Every hour', 'CronJobsForms')));

        for ($hour = 0; $hour < 24; $hour += 1) {
            $data[] = array('id' => $hour, 'name' => date('H:i', mktime($hour, 0, 0, 0, 1)));
        }

        return $data;
    }

    protected static function getDaysFormOptions()
    {
        $data = array(array('id' => '-1', 'name' => self::$module->l('Every day of the month', 'CronJobsForms')));

        for ($day = 1; $day <= 31; $day += 1) {
            $data[] = array('id' => $day, 'name' => $day);
        }

        return $data;
    }

    protected static function getMonthsFormOptions()
    {
        $data = array(array('id' => '-1', 'name' => self::$module->l('Every month', 'CronJobsForms')));

        for ($month = 1; $month <= 12; $month += 1) {
            $data[] = array('id' => $month, 'name' => self::$module->l(date('F', mktime(0, 0, 0, $month, 1))));
        }

        return $data;
    }

    protected static function getDaysofWeekFormOptions()
    {
        $data = array(array('id' => '-1', 'name' => self::$module->l('Every day of the week', 'CronJobsForms')));

        for ($day = 1; $day <= 7; $day += 1) {
            $data[] = array('id' => $day, 'name' => self::$module->l(date('l', strtotime('Sunday +' . $day . ' days'))));
        }

        return $data;
    }
}
