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
 * Step 4 : configure the shop and admin access
 */
class InstallControllerHttpConfigure extends InstallControllerHttp
{
    public $list_countries = array();

    /**
     * @see InstallAbstractModel::processNextStep()
     */
    public function processNextStep()
    {
        if (Tools::isSubmit('shop_name')) {
            // Save shop configuration
            $this->session->shop_name = trim(Tools::getValue('shop_name'));
            $this->session->shop_activity = Tools::getValue('shop_activity');
            $this->session->install_type = Tools::getValue('db_mode');
            $this->session->shop_country = Tools::getValue('shop_country');
            $this->session->shop_timezone = Tools::getValue('shop_timezone');
            $this->session->enable_ssl = Tools::getValue('enable_ssl');

            // Save admin configuration
            $this->session->admin_firstname = trim(Tools::getValue('admin_firstname'));
            $this->session->admin_lastname = trim(Tools::getValue('admin_lastname'));
            $this->session->admin_email = trim(Tools::getValue('admin_email'));
            $this->session->send_informations = Tools::getValue('send_informations');
            $this->session->marketing_consent = Tools::getValue('marketing_consent');
            if ($this->session->send_informations) {
                // Qlo Notification
                $qloData = [
                    'shopDomain' => Tools::getHttpHost(),
                    'shopDomainSsl' => Tools::getHttpHost(),
                    'physicalUri' => __PS_BASE_URI__,
                    'email' => Tools::getValue('admin_email'),
                    'firstname' => Tools::ucfirst(Tools::getValue('admin_firstname')),
                    'lastname' => Tools::ucfirst(Tools::getValue('admin_lastname')),
                    'countryCode' => Tools::getValue('shop_country'),
                    'marketingConsent' => Tools::getValue('marketing_consent'),
                ];
                $objModelInstall = new InstallModelInstall();
                $objModelInstall->setNotification($qloData);
                // END
            }

            // If password fields are empty, but are already stored in session, do not fill them again
            if (!$this->session->admin_password || trim(Tools::getValue('admin_password'))) {
                $this->session->admin_password = trim(Tools::getValue('admin_password'));
            }

            if (!$this->session->admin_password_confirm || trim(Tools::getValue('admin_password_confirm'))) {
                $this->session->admin_password_confirm = trim(Tools::getValue('admin_password_confirm'));
            }
        }
    }

    /**
     * @see InstallAbstractModel::validate()
     */
    public function validate()
    {
        // List of required fields
        $required_fields = array('shop_name', 'shop_country', 'shop_timezone', 'admin_firstname', 'admin_lastname', 'admin_email', 'admin_password');
        foreach ($required_fields as $field) {
            if (!$this->session->$field) {
                $this->errors[$field] = $this->l('Field required');
            }
        }

        // Check shop name
        if ($this->session->shop_name && !Validate::isGenericName($this->session->shop_name)) {
            $this->errors['shop_name'] = $this->l('Invalid shop name');
        } elseif (strlen($this->session->shop_name) > 64) {
            $this->errors['shop_name'] = $this->l('The field %s is limited to %d characters', $this->l('shop name'), 64);
        }

        // Check admin name
        if ($this->session->admin_firstname && !Validate::isName($this->session->admin_firstname)) {
            $this->errors['admin_firstname'] = $this->l('Your firstname contains some invalid characters');
        } elseif (strlen($this->session->admin_firstname) > 32) {
            $this->errors['admin_firstname'] = $this->l('The field %s is limited to %d characters', $this->l('firstname'), 32);
        }

        if ($this->session->admin_lastname && !Validate::isName($this->session->admin_lastname)) {
            $this->errors['admin_lastname'] = $this->l('Your lastname contains some invalid characters');
        } elseif (strlen($this->session->admin_lastname) > 32) {
            $this->errors['admin_lastname'] = $this->l('The field %s is limited to %d characters', $this->l('lastname'), 32);
        }

        // Check passwords
        if ($this->session->admin_password) {
            if (!Validate::isPasswdAdmin($this->session->admin_password)) {
                $this->errors['admin_password'] = $this->l('The password is incorrect (alphanumeric string with at least 8 characters)');
            } elseif ($this->session->admin_password != $this->session->admin_password_confirm) {
                $this->errors['admin_password'] = $this->l('Password and its confirmation are different');
            }
        }

        // Check email
        if ($this->session->admin_email && !Validate::isEmail($this->session->admin_email)) {
            $this->errors['admin_email'] = $this->l('This e-mail address is invalid');
        }

        return count($this->errors) ? false : true;
    }

    public function process()
    {
        if (Tools::getValue('uploadLogo')) {
            $this->processUploadLogo();
        } elseif (Tools::getValue('timezoneByIso')) {
            $this->processTimezoneByIso();
        }
    }

    /**
     * Process the upload of new logo
     */
    public function processUploadLogo()
    {
        $error = '';
        if (isset($_FILES['fileToUpload']['tmp_name']) && $_FILES['fileToUpload']['tmp_name']) {
            $file = $_FILES['fileToUpload'];
            $error = ImageManager::validateUpload($file, 300000);
            if (!strlen($error)) {
                $tmp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS');
                if (!$tmp_name || !move_uploaded_file($file['tmp_name'], $tmp_name)) {
                    return false;
                }

                list($width, $height, $type) = getimagesize($tmp_name);

                $newheight = ($height > 500) ? 500 : $height;
                $percent = $newheight / $height;
                $newwidth = $width * $percent;
                $newheight = $height * $percent;

                if (!is_writable(_PS_ROOT_DIR_.'/img/')) {
                    $error = $this->l('Image folder %s is not writable', _PS_ROOT_DIR_.'/img/');
                }
                if (!$error) {
                    list($src_width, $src_height, $type) = getimagesize($tmp_name);
                    $src_image = ImageManager::create($type, $tmp_name);
                    $dest_image = imagecreatetruecolor($src_width, $src_height);
                    $white = imagecolorallocate($dest_image, 255, 255, 255);
                    imagefilledrectangle($dest_image, 0, 0, $src_width, $src_height, $white);
                    imagecopyresampled($dest_image, $src_image, 0, 0, 0, 0, $src_width, $src_height, $src_width, $src_height);
                    if (!imagejpeg($dest_image, _PS_ROOT_DIR_.'/img/logo.png', 95)) {
                        $error = $this->l('An error occurred during logo copy.');
                    } else {
                        imagedestroy($dest_image);
                        @chmod($filename, 0664);
                    }
                }
            } else {
                $error = $this->l('An error occurred during logo upload.');
            }
        }

        $this->ajaxJsonAnswer(($error) ? false : true, $error);
    }

    /**
     * Obtain the timezone associated to an iso
     */
    public function processTimezoneByIso()
    {
        $timezone = $this->getTimezoneByIso(Tools::getValue('iso'));
        $this->ajaxJsonAnswer(($timezone) ? true : false, $timezone);
    }

    /**
     * Get list of timezones
     *
     * @return array
     */
    public function getTimezones()
    {
        if (!is_null($this->cache_timezones)) {
            return;
        }

        if (!file_exists(_PS_INSTALL_DATA_PATH_.'xml/timezone.xml')) {
            return array();
        }

        $xml = @simplexml_load_file(_PS_INSTALL_DATA_PATH_.'xml/timezone.xml');
        $timezones = array();
        if ($xml) {
            foreach ($xml->entities->timezone as $timezone) {
                $timezones[] = (string)$timezone['name'];
            }
        }
        return $timezones;
    }

    /**
     * Get a timezone associated to an iso
     *
     * @param string $iso
     * @return string
     */
    public function getTimezoneByIso($iso)
    {
        if (!file_exists(_PS_INSTALL_DATA_PATH_.'iso_to_timezone.xml')) {
            return '';
        }

        $xml = @simplexml_load_file(_PS_INSTALL_DATA_PATH_.'iso_to_timezone.xml');
        $timezones = array();
        if ($xml) {
            foreach ($xml->relation as $relation) {
                $timezones[(string)$relation['iso']] = (string)$relation['zone'];
            }
        }
        return isset($timezones[$iso]) ? $timezones[$iso] : '';
    }

    /**
     * @see InstallAbstractModel::display()
     */
    public function display()
    {
        // List of activities
        $list_activities = array(
            1 => $this->l('Lingerie and Adult'),
            2 => $this->l('Animals and Pets'),
            3 => $this->l('Art and Culture'),
            4 => $this->l('Babies'),
            5 => $this->l('Beauty and Personal Care'),
            6 => $this->l('Cars'),
            7 => $this->l('Computer Hardware and Software'),
            8 => $this->l('Download'),
            9 => $this->l('Fashion and accessories'),
            10 => $this->l('Flowers, Gifts and Crafts'),
            11 => $this->l('Food and beverage'),
            12 => $this->l('HiFi, Photo and Video'),
            13 => $this->l('Home and Garden'),
            14 => $this->l('Home Appliances'),
            15 => $this->l('Jewelry'),
            16 => $this->l('Mobile and Telecom'),
            17 => $this->l('Services'),
            18 => $this->l('Shoes and accessories'),
            19 => $this->l('Sports and Entertainment'),
            20 => $this->l('Travel'),
        );

        asort($list_activities);
        $this->list_activities = $list_activities;

        // Countries list
        $this->list_countries = array();
        $countries = $this->language->getCountries();
        $top_countries = array(
            'fr', 'es', 'us',
            'gb', 'it', 'de',
            'nl', 'pl', 'id',
            'be', 'br', 'se',
            'ca', 'ru', 'cn',
        );

        foreach ($top_countries as $iso) {
            $this->list_countries[] = array('iso' => $iso, 'name' => $countries[$iso]);
        }
        $this->list_countries[] = array('iso' => 0, 'name' => '-----------------');

        foreach ($countries as $iso => $lang) {
            if (!in_array($iso, $top_countries)) {
                $this->list_countries[] = array('iso' => $iso, 'name' => $lang);
            }
        }

        // Try to detect default country
        if (!$this->session->shop_country) {
            $detect_language = $this->language->detectLanguage();
            if (isset($detect_language['primarytag'])) {
                $this->session->shop_country = strtolower(isset($detect_language['subtag']) ? $detect_language['subtag'] : $detect_language['primarytag']);
                $this->session->shop_timezone = $this->getTimezoneByIso($this->session->shop_country);
            }
        }

        // Install type
        $this->install_type = ($this->session->install_type) ? $this->session->install_type : 'full';

        if (!isset($this->session->allow_ssl) || $this->session->allow_ssl === false) {
            if (Tools::usingSecureMode()) {
                $this->session->enable_ssl = '1';
                $this->session->allow_ssl = true;
            } else {
                $this->session->enable_ssl = '0';
                $this->session->allow_ssl = false;
            }
        }

        $this->displayTemplate('configure');
    }

    /**
     * Helper to display error for a field
     *
     * @param string $field
     * @return string|void
     */
    public function displayError($field)
    {
        if (!isset($this->errors[$field])) {
            return;
        }

        return '<span class="result aligned errorTxt">'.$this->errors[$field].'</span>';
    }
}
