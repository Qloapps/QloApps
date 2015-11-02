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

class StatisticsControllerCore extends FrontController
{
    public $display_header = false;
    public $display_footer = false;

    protected $param_token;

    public function postProcess()
    {
        $this->param_token = Tools::getValue('token');
        if (!$this->param_token) {
            die;
        }

        if ($_POST['type'] == 'navinfo') {
            $this->processNavigationStats();
        } elseif ($_POST['type'] == 'pagetime') {
            $this->processPageTime();
        } else {
            exit;
        }
    }

    /**
     * Log statistics on navigation (resolution, plugins, etc.)
     */
    protected function processNavigationStats()
    {
        $id_guest = (int)Tools::getValue('id_guest');
        if (sha1($id_guest._COOKIE_KEY_) != $this->param_token) {
            die;
        }

        $guest = new Guest((int)substr($_POST['id_guest'], 0, 10));
        $guest->javascript = true;
        $guest->screen_resolution_x = (int)substr($_POST['screen_resolution_x'], 0, 5);
        $guest->screen_resolution_y = (int)substr($_POST['screen_resolution_y'], 0, 5);
        $guest->screen_color = (int)substr($_POST['screen_color'], 0, 3);
        $guest->sun_java = (int)substr($_POST['sun_java'], 0, 1);
        $guest->adobe_flash = (int)substr($_POST['adobe_flash'], 0, 1);
        $guest->adobe_director = (int)substr($_POST['adobe_director'], 0, 1);
        $guest->apple_quicktime = (int)substr($_POST['apple_quicktime'], 0, 1);
        $guest->real_player = (int)substr($_POST['real_player'], 0, 1);
        $guest->windows_media = (int)substr($_POST['windows_media'], 0, 1);
        $guest->update();
    }

    /**
     * Log statistics on time spend on pages
     */
    protected function processPageTime()
    {
        $id_connection = (int)Tools::getValue('id_connections');
        $time = (int)Tools::getValue('time');
        $time_start = Tools::getValue('time_start');
        $id_page = (int)Tools::getValue('id_page');

        if (sha1($id_connection.$id_page.$time_start._COOKIE_KEY_) != $this->param_token) {
            die;
        }

        if ($time <= 0) {
            die;
        }

        Connection::setPageTime($id_connection, $id_page, substr($time_start, 0, 19), $time);
    }
}
