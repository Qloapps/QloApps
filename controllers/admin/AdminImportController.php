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

@ini_set('max_execution_time', 0);
/** No max line limit since the lines can be more than 4096. Performance impact is not significant. */
define('MAX_LINE_SIZE', 0);

/** Used for validatefields diying without user friendly error or not */
define('UNFRIENDLY_ERROR', false);

/** this value set the number of columns visible on each page */
define('MAX_COLUMNS', 6);

/** correct Mac error on eof */
@ini_set('auto_detect_line_endings', '1');

class AdminImportControllerCore extends AdminController
{
    public static $column_mask;

    public $entities = array();

    public $available_fields = array();

    public $required_fields = array();

    public $cache_image_deleted = array();

    public static $default_values = array();

    public static $validators = array(
        'active' => array('AdminImportController', 'getBoolean'),
        'tax_rate' => array('AdminImportController', 'getPrice'),
        'reduction_price' => array('AdminImportController', 'getPrice'),
        'reduction_percent' => array('AdminImportController', 'getPrice'),
        'wholesale_price' => array('AdminImportController', 'getPrice'),
        'ecotax' => array('AdminImportController', 'getPrice'),
        'name' => array('AdminImportController', 'createMultiLangField'),
        'description' => array('AdminImportController', 'createMultiLangField'),
        'description_short' => array('AdminImportController', 'createMultiLangField'),
        'meta_title' => array('AdminImportController', 'createMultiLangField'),
        'meta_keywords' => array('AdminImportController', 'createMultiLangField'),
        'meta_description' => array('AdminImportController', 'createMultiLangField'),
        'link_rewrite' => array('AdminImportController', 'createMultiLangField'),
        'available_now' => array('AdminImportController', 'createMultiLangField'),
        'available_later' => array('AdminImportController', 'createMultiLangField'),
        'category' => array('AdminImportController', 'split'),
        'online_only' => array('AdminImportController', 'getBoolean'),
    );

    public $separator;
    public $multiple_value_separator;

    public function __construct()
    {
        $this->bootstrap = true;
        $this->entities = array(
            $this->l('Hotels'),
            $this->l('Room Types'),
            $this->l('Rooms'),
            $this->l('Categories'),
            $this->l('Service Products'),
            $this->l('Bookings'),
            $this->l('Customers'),
        );

        $this->entities = array_flip($this->entities);
        switch ((int)Tools::getValue('entity')) {
            case $this->entities[$this->l('Categories')]:
                $this->required_fields = array(
                    'name'
                );
                $this->available_fields = array(
                    'no' => array('label' => $this->l('Ignore this column')),
                    'id' => array('label' => $this->l('ID'),
                    'help' => $this->l('Please note: Default categories will not be overridden even with force ids.Ex: Home, Root, Service, Location')),
                    'active' => array('label' => $this->l('Active (0/1)')),
                    'name' => array('label' => $this->l('Name *')),
                    'parent' => array('label' => $this->l('Parent category')),
                    'image' => array('label' => $this->l('Image URL')),
                );

                self::$default_values = array(
                    'active' => '1',
                    'parent' => Configuration::get('PS_HOME_CATEGORY'),
                );
            break;
            case $this->entities[$this->l('Hotels')]:
                $this->required_fields = array(
                    'hotel_name',
                    'phone',
                    'email',
                    'address',
                    'rating',
                    'check_in',
                    'check_out',
                    'id_country',
                    'id_state',
                    'city',
                    'postcode'
                );
                self::$validators['short_description'] = array('AdminImportController', 'createMultiLangField');
                self::$validators['policies'] = array('AdminImportController', 'createMultiLangField');
                self::$validators['hotel_name'] = array('AdminImportController', 'createMultiLangField');
                self::$validators['image'] = array('AdminImportController', 'split');
                self::$validators['refund_ids'] = array('AdminImportController', 'split');

                $this->available_fields = array(
                    'no' => array('label' => $this->l('Ignore this column')),
                    'id' => array('label' => $this->l('ID')),
                    'active' => array('label' => $this->l('Active (0/1)')),
                    'hotel_name' => array('label' => $this->l('Hotel Name *')),
                    'short_description' => array('label' => $this->l('Short Description')),
                    'description' => array('label' => $this->l('Description')),
                    'phone' => array('label' => $this->l('Mobile phone *')),
                    'email' => array('label' => $this->l('Email *')),
                    'address' => array('label' => $this->l('Address *')),
                    'rating' => array('label' => $this->l('Rating *')),
                    'check_in' => array('label' => $this->l('Check-In *')),
                    'check_out' => array('label' => $this->l('Check-Out *')),
                    'id_country' => array('label' => $this->l('Country ID*')),
                    'id_state' => array('label' => $this->l('State ID')),
                    'city' => array('label' => $this->l('City *')),
                    'postcode' => array('label' => $this->l('Zip Code *')),
                    'fax' => array('label' => $this->l('Fax')),
                    'policies' => array('label' => $this->l('Hotel Policies')),
                    'active_refund' => array('label' => $this->l('Allow Refund (0 = No, 1 = Yes)')),
                    'refund_ids' => array('label' => $this->l('Refund IDs (x,y,z...)')),
                    'max_checkout_offset' => array('label' => $this->l('Maximum checkout offset')),
                    'min_booking_offset' => array('label' => $this->l('Minimum booking offset')),
                    'image' => array('label' => $this->l('Image URLs (x,y,z...)')),
                    'delete_existing_images' => array(
                        'label' => $this->l('Delete existing images (0 = No, 1 = Yes)')
                    ),
                );
            break;
            case $this->entities[$this->l('Room Types')]:
                $this->required_fields = array('id_hotel', 'name');

                self::$validators['image'] = array('AdminImportController', 'split');
                self::$validators['id_additional_facilities'] = array('AdminImportController', 'split');
                self::$validators['id_service_products'] = array('AdminImportController', 'split');
                self::$validators['id_features'] = array('AdminImportController', 'split');
                $this->available_fields = array(
                    'no' => array('label' => $this->l('Ignore this column')),
                    'id' => array('label' => $this->l('ID')),
                    'active' => array('label' => $this->l('Active (0/1)')),
                    'name' => array('label' => $this->l('Name *')),
                    'id_hotel' => array('label' => $this->l('Hotel ID *')),
                    'price' => array('label' => $this->l('Pre-tax retail price')),
                    'wholesale_price' => array('label' => $this->l('Pre-tax operating cost')),
                    'id_tax_rules_group' => array('label' => $this->l('Tax rule ID')),
                    'advance_payment' => array('label' => $this->l('Allow advance payment(0 = Yes, 1 = No)')),
                    'payment_type' => array(
                        'label' => $this->l('Room type advance payment (0/1/2)'),
                        'help' => $this->l('0 = use global, 1 = Percentage, 2 = Fixed amount')
                    ),
                    'payment_value' => array(
                        'label' => $this->l('Value for the advance payment'),
                        'help' => $this->l('Required if advance payment allowed.')
                    ),
                    'tax_included' => array(
                        'label' => $this->l('Include tax with advance payment'),
                        'help' => $this->l('Required if advance payment allowed.')
                    ),
                    'min_len_stay' => array('label' => $this->l('Minimum length of stay (1 = No Limit)')),
                    'max_len_stay' => array('label' => $this->l('Maximum lenght of stay (0 = No Limit)')),
                    'base_adults' => array('label' => $this->l('Base adults')),
                    'base_children' => array('label' => $this->l('Base children')),
                    'max_adults' => array('label' => $this->l('Maximum adults')),
                    'max_children' => array('label' => $this->l('Maximum children')),
                    'max_room_occupancy' => array('label' => $this->l('Maximum room occupancy')),
                    'show_at_front' => array('label' => $this->l('Show at front (0/1)')),
                    'id_additional_facilities' => array('label' => $this->l('Additional facilities IDs (x,y,z...)')),
                    'id_service_products' => array('label' => $this->l('Service products IDs (x, y, z...)')),
                    'id_features' => array('label' => $this->l('Feature IDs (x, y, z...)')),
                    'description_short' => array('label' => $this->l('Short description')),
                    'description' => array('label' => $this->l('Description')),
                    'meta_title' => array('label' => $this->l('Meta title')),
                    'meta_keywords' => array('label' => $this->l('Meta keywords')),
                    'meta_description' => array('label' => $this->l('Meta description')),
                    'link_rewrite' => array('label' => $this->l('URL rewritten')),
                    'image' => array('label' => $this->l('Image URLs (x,y,z...)')),
                    'delete_existing_images' => array(
                        'label' => $this->l('Delete existing images (0 = No, 1 = Yes)')
                    ),
                );

                self::$default_values = array(
                    'id_category' => array((int) Configuration::get('PS_HOME_CATEGORY')),
                    'id_category_default' => null,
                    'active' => '1',
                    'minimal_quantity' => 1,
                    'price' => 0,
                    'id_tax_rules_group' => 0,
                    'description_short' => array((int) Configuration::get('PS_LANG_DEFAULT') => ''),
                    'show_at_front' => true,
                    'available_date' => date('Y-m-d'),
                    'date_add' => date('Y-m-d H:i:s'),
                    'date_upd' => date('Y-m-d H:i:s'),
                );
            break;
            case $this->entities[$this->l('Rooms')]:
                $this->required_fields = array('room_num', 'id_status', 'id_product');

                self::$validators['dates'] = array('AdminImportController', 'split');
                $this->available_fields = array(
                    'no' => array('label' => $this->l('Ignore this column')),
                    'room_num' => array('label' => $this->l('Room No *'),),
                    'floor' => array('label' => $this->l('Floor')),
                    'id_product' => array('label' => $this->l('Room Type ID *')),
                    'id_status' => array(
                        'label' => $this->l('Room status (1/2/3)'),
                        'help' => $this->l('1 = Active, 2 = Inactive, 3 = Temporarily Inactive')),
                    'comment' => array('label' => $this->l('Extra Information')),
                    'dates' => array('label' => $this->l('Inactive date ranges and Reason(yyyy-mm-dd)'),
                        'help' => $this->l('If Temporarily Inactive (date_from:date_to:reason, date_from:date_to:reason,...)')
                    ),
                );
            break;
            case $this->entities[$this->l('Service Products')]:
                self::$validators['image'] = array('AdminImportController', 'split');
                self::$validators['id_room_types'] = array('AdminImportController', 'split');

                $this->available_fields = array(
                    'no' => array('label' => $this->l('Ignore this column')),
                    'id' => array('label' => $this->l('ID')),
                    'active' => array('label' => $this->l('Active (0/1)')),
                    'name' => array('label' => $this->l('Name *')),
                    'category' => array('label' => $this->l('Categories (x,y,z...)')),
                    'id_room_types' => array('label' => $this->l('Associated room types (x,y,z...) *')),
                    'price' => array('label' => $this->l('Pre-tax retail price')),
                    'wholesale_price' => array('label' => $this->l('Pre-tax operating cost')),
                    'id_tax_rules_group' => array('label' => $this->l('Tax rule ID')),
                    'auto_add_to_cart' => array('label' => $this->l('Auto add to cart (0 = No, 1 = Yes)')),
                    'price_addition_type' => array('label' => $this->l('Price display preference'),
                        'help' => $this->l('1 = With room price, 2 = As convenience fee')),
                    'show_at_front' => array('label' => $this->l('Show at front office (0 = No, 1 = Yes)')),
                    'price_calculation_method' => array('label' => $this->l('Price calculation method'),
                        'help' => $this->l('1 = Once per booking, 2 = Each day')),
                    'description_short' => array('label' => $this->l('Short description')),
                    'meta_title' => array('label' => $this->l('Meta title')),
                    'meta_keywords' => array('label' => $this->l('Meta keywords')),
                    'meta_description' => array('label' => $this->l('Meta description')),
                    'image' => array('label' => $this->l('Image URLs (x,y,z...)')),
                    'delete_existing_images' => array(
                        'label' => $this->l('Delete existing images (0 = No, 1 = Yes)')
                    ),
                );

                self::$default_values = array(
                    'id_category' => array((int) Configuration::get('PS_SERVICE_CATEGORY')),
                    'id_category_default' => Configuration::get('PS_SERVICE_CATEGORY'),
                    'active' => '1',
                    'price' => 0,
                    'id_tax_rules_group' => 0,
                    'description_short' => array((int) Configuration::get('PS_LANG_DEFAULT') => ''),
                    'date_add' => date('Y-m-d H:i:s'),
                    'date_upd' => date('Y-m-d H:i:s'),
                );
            break;
            case $this->entities[$this->l('Bookings')]:
                $this->required_fields = array('id_customer', 'duration_dates', 'num_rooms', 'id_product');
                self::$validators['duration_dates'] = array('AdminImportController', 'split');
                self::$validators['id_additional_facilities'] = array('AdminImportController', 'split');
                self::$validators['id_service_products'] = array('AdminImportController', 'split');

                $this->available_fields = array(
                    'no' => array('label' => $this->l('Ignore this column')),
                    'id_order' => array(
                        'label' => $this->l('Order Reference ID'),
                        'help' => $this->l('This is only used to group together orders from multiple rows as a single order.')
                    ),
                    'id_customer' => array(
                        'label' => $this->l('Customer ID *'),
                        'help' => $this->l('Orders with same Order Reference ID will use Customer ID from the first row only.')
                    ),
                    'id_product' => array('label'=> 'Room Type ID'),
                    'duration_dates' => array(
                        'label' => $this->l('Duration * (yyyy-mm-dd)'),
                        'help' => $this->l('Check_in, Check_out')
                    ),
                    'num_rooms' => array('label' => $this->l('Number Of Rooms')),
                    'amount' => array('label' => $this->l('Order Price')),
                    'due_amount' => array('label' => $this->l('Due Amount')),
                    'id_currency' => array('label' => $this->l('Currency ID')),
                    'id_order_status' => array('label' => $this->l('Order Status ID')),
                    'id_service_products' => array(
                        'label' => $this->l('Service Product IDs (x:n, y:n, z:n,..)'),
                        'help' => $this->l('id_service_product:quantity')
                    ),
                    'id_additional_facilities' => array(
                        'label' => $this->l('Additional Facilities IDs (x:a, y:b, z:c,...)'),
                        'help' => $this->l('id_additional_facility:id_option')
                    ),
                );
            break;
            case $this->entities[$this->l('Customers')]:
                $this->required_fields = array('email', 'passwd', 'lastname', 'firstname');
                if (Configuration::get('PS_ONE_PHONE_AT_LEAST')) {
                    $this->required_fields[] = 'phone';
                }

                $this->available_fields = array(
                    'no' => array('label' => $this->l('Ignore this column')),
                    'id' => array('label' => $this->l('ID')),
                    'active' => array('label' => $this->l('Active  (0/1)')),
                    'id_gender' => array('label' => $this->l('Titles ID (Mr = 1, Ms = 2, else 0)')),
                    'email' => array('label' => $this->l('Email *')),
                    'passwd' => array('label' => $this->l('Password *')),
                    'birthday' => array('label' => $this->l('Birthday (yyyy-mm-dd)')),
                    'lastname' => array('label' => $this->l('Last Name *')),
                    'firstname' => array('label' => $this->l('First Name *')),
                    'phone' => array('label' => $this->l('Phone'). (Configuration::get('PS_ONE_PHONE_AT_LEAST') ? ' *' : '' )),
                    'newsletter' => array('label' => $this->l('Newsletter (0/1)')),
                    'optin' => array('label' => $this->l('Opt-in (0/1)')),
                    'group' => array('label' => $this->l('Groups (x,y,z...)')),
                    'id_default_group' => array('label' => $this->l('Default group ID')),
                );

                self::$default_values = array('active' => '1');
            break;
        }

        $this->separator = ($separator = Tools::substr(strval(trim(Tools::getValue('separator'))), 0, 1)) ? $separator :  ';';
        $this->multiple_value_separator = ($separator = Tools::substr(strval(trim(Tools::getValue('multiple_value_separator'))), 0, 1)) ? $separator :  ',';
        parent::__construct();
    }

    public function setMedia()
    {
        $bo_theme = ((Validate::isLoadedObject($this->context->employee)
            && $this->context->employee->bo_theme) ? $this->context->employee->bo_theme : 'default');

        if (!file_exists(_PS_BO_ALL_THEMES_DIR_.$bo_theme.DIRECTORY_SEPARATOR
            .'template')) {
            $bo_theme = 'default';
        }

        // We need to set parent media first, so that jQuery is loaded before the dependant plugins
        parent::setMedia();

        $this->addJs(__PS_BASE_URI__.$this->admin_webpath.'/themes/'.$bo_theme.'/js/jquery.iframe-transport.js');
        $this->addJs(__PS_BASE_URI__.$this->admin_webpath.'/themes/'.$bo_theme.'/js/jquery.fileupload.js');
        $this->addJs(__PS_BASE_URI__.$this->admin_webpath.'/themes/'.$bo_theme.'/js/jquery.fileupload-process.js');
        $this->addJs(__PS_BASE_URI__.$this->admin_webpath.'/themes/'.$bo_theme.'/js/jquery.fileupload-validate.js');
        $this->addJs(__PS_BASE_URI__.'js/vendor/spin.js');
        $this->addJs(__PS_BASE_URI__.'js/vendor/ladda.js');
    }

    public function renderForm()
    {
        if (!is_dir(AdminImportController::getPath())) {
            return !($this->errors[] = Tools::displayError('The import directory does not exist.'));
        }

        if (!is_writable(AdminImportController::getPath())) {
            $this->displayWarning($this->l('The import directory must be writable (CHMOD 755 / 777).'));
        }

        if (isset($this->warnings) && count($this->warnings)) {
            $warnings = array();
            foreach ($this->warnings as $warning) {
                $warnings[] = $warning;
            }
        }

        $files_to_import = scandir(AdminImportController::getPath());
        uasort($files_to_import, array('AdminImportController', 'usortFiles'));
        foreach ($files_to_import as $k => &$filename) {
            //exclude .  ..  .svn and index.php and all hidden files
            if (preg_match('/^\..*|index\.php/i', $filename)) {
                unset($files_to_import[$k]);
            }
        }
        unset($filename);

        $this->fields_form = array('');

        $this->toolbar_scroll = false;
        $this->toolbar_btn = array();

        // adds fancybox
        $this->addJqueryPlugin(array('fancybox'));

        $entity_selected = 0;
        if (isset($this->entities[$this->l(Tools::ucfirst(Tools::getValue('import_type')))])) {
            $entity_selected = $this->entities[$this->l(Tools::ucfirst(Tools::getValue('import_type')))];
            $this->context->cookie->entity_selected = (int)$entity_selected;
        } elseif (isset($this->context->cookie->entity_selected)) {
            $entity_selected = (int)$this->context->cookie->entity_selected;
        }

        $csv_selected = '';
        if (isset($this->context->cookie->csv_selected) && @filemtime(AdminImportController::getPath(
            urldecode($this->context->cookie->csv_selected)))) {
            $csv_selected = urldecode($this->context->cookie->csv_selected);
        } else {
            $this->context->cookie->csv_selected = $csv_selected;
        }

        $id_lang_selected = '';
        if (isset($this->context->cookie->iso_lang_selected) && $this->context->cookie->iso_lang_selected) {
            $id_lang_selected = (int)Language::getIdByIso(urldecode($this->context->cookie->iso_lang_selected));
        }

        $separator_selected = $this->separator;
        if (isset($this->context->cookie->separator_selected) && $this->context->cookie->separator_selected) {
            $separator_selected = urldecode($this->context->cookie->separator_selected);
        }

        $multiple_value_separator_selected = $this->multiple_value_separator;
        if (isset($this->context->cookie->multiple_value_separator_selected) && $this->context->cookie->multiple_value_separator_selected) {
            $multiple_value_separator_selected = urldecode($this->context->cookie->multiple_value_separator_selected);
        }

        //get post max size
        $post_max_size = ini_get('post_max_size');
        $bytes         = (int)trim($post_max_size);
        $last          = strtolower($post_max_size[strlen($post_max_size) - 1]);

        switch ($last) {
            case 'g': $bytes *= 1024;
            case 'm': $bytes *= 1024;
            case 'k': $bytes *= 1024;
        }

        if (!isset($bytes) || $bytes == '') {
            $bytes = 20971520;
        } // 20Mb

        $this->tpl_form_vars = array(
            'post_max_size' => (int)$bytes,
            'module_confirmation' => Tools::isSubmit('import') && (isset($this->warnings) && !count($this->warnings)),
            'path_import' => AdminImportController::getPath(),
            'entities' => $this->entities,
            'entity_selected' => $entity_selected,
            'csv_selected' => $csv_selected,
            'separator_selected' => $separator_selected,
            'multiple_value_separator_selected' => $multiple_value_separator_selected,
            'files_to_import' => $files_to_import,
            'languages' => Language::getLanguages(false),
            'id_language' => ($id_lang_selected) ? $id_lang_selected : $this->context->language->id,
            'available_fields' => $this->getAvailableFields(),
            'truncateAuthorized' => (Shop::isFeatureActive() && $this->context->employee->isSuperAdmin()) || !Shop::isFeatureActive(),
            'PS_ADVANCED_STOCK_MANAGEMENT' => Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT'),
        );

        return parent::renderForm();
    }

    public function ajaxProcessuploadCsv()
    {
        $filename_prefix = date('YmdHis').'-';

        if (isset($_FILES['file']) && !empty($_FILES['file']['error'])) {
            switch ($_FILES['file']['error']) {
                case UPLOAD_ERR_INI_SIZE:
                    $_FILES['file']['error'] = Tools::displayError('The uploaded file exceeds the upload_max_filesize directive in php.ini. If your server configuration allows it, you may add a directive in your .htaccess.');
                    break;
                case UPLOAD_ERR_FORM_SIZE:
                    $_FILES['file']['error'] = Tools::displayError('The uploaded file exceeds the post_max_size directive in php.ini.
                        If your server configuration allows it, you may add a directive in your .htaccess, for example:')
                    .'<br/><a href="'.$this->context->link->getAdminLink('AdminMeta').'" >
                    <code>php_value post_max_size 20M</code> '.
                    Tools::displayError('(click to open "Generators" page)').'</a>';
                    break;
                break;
                case UPLOAD_ERR_PARTIAL:
                    $_FILES['file']['error'] = Tools::displayError('The uploaded file was only partially uploaded.');
                    break;
                break;
                case UPLOAD_ERR_NO_FILE:
                    $_FILES['file']['error'] = Tools::displayError('No file was uploaded.');
                    break;
                break;
            }
        } elseif (!preg_match('/.*\.csv$/i', $_FILES['file']['name'])) {
            $_FILES['file']['error'] = Tools::displayError('The extension of your file should be .csv.');
        } elseif (!@filemtime($_FILES['file']['tmp_name']) ||
            !@move_uploaded_file($_FILES['file']['tmp_name'], AdminImportController::getPath().$filename_prefix.str_replace("\0", '', $_FILES['file']['name']))) {
            $_FILES['file']['error'] = $this->l('An error occurred while uploading / copying the file.');
        } else {
            @chmod(AdminImportController::getPath().$filename_prefix.$_FILES['file']['name'], 0664);
            $_FILES['file']['filename'] = $filename_prefix.str_replace('\0', '', $_FILES['file']['name']);
        }

        die(json_encode($_FILES));
    }

    public function renderView()
    {
        $this->addJS(_PS_JS_DIR_.'admin/import.js');

        $handle = $this->openCsvFile();
        $nb_column = $this->getNbrColumn($handle, $this->separator);
        $nb_table = ceil($nb_column / MAX_COLUMNS);

        $res = array();
        foreach ($this->required_fields as $elem) {
            $res[] = '\''.$elem.'\'';
        }

        $data = array();
        for ($i = 0; $i < $nb_table; $i++) {
            $data[$i] = $this->generateContentTable($i, $nb_column, $handle, $this->separator);
        }

        $this->context->cookie->entity_selected = (int)Tools::getValue('entity');
        $this->context->cookie->iso_lang_selected = urlencode(Tools::getValue('iso_lang'));
        $this->context->cookie->separator_selected = urlencode($this->separator);
        $this->context->cookie->multiple_value_separator_selected = urlencode($this->multiple_value_separator);
        $this->context->cookie->csv_selected = urlencode(Tools::getValue('csv'));

        $this->tpl_view_vars = array(
            'import_matchs' => Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'import_match', true, false),
            'fields_value' => array(
                'csv' => Tools::getValue('csv'),
                'convert' => Tools::getValue('convert'),
                'entity' => (int)Tools::getValue('entity'),
                'iso_lang' => Tools::getValue('iso_lang'),
                'truncate' => Tools::getValue('truncate'),
                'forceIDs' => Tools::getValue('forceIDs'),
                'regenerate' => Tools::getValue('regenerate'),
                'match_ref' => Tools::getValue('match_ref'),
                'separator' => $this->separator,
                'multiple_value_separator' => $this->multiple_value_separator
            ),
            'nb_table' => $nb_table,
            'nb_column' => $nb_column,
            'res' => implode(',', $res),
            'max_columns' => MAX_COLUMNS,
            'no_pre_select' => array('price_tin', 'feature'),
            'available_fields' => $this->available_fields,
            'data' => $data
        );

        return parent::renderView();
    }

    public function initToolbar()
    {
        switch ($this->display) {
            case 'import':
                // Default cancel button - like old back link
                $back = Tools::safeOutput(Tools::getValue('back', ''));
                if (empty($back)) {
                    $back = self::$currentIndex.'&token='.$this->token;
                }

                $this->toolbar_btn['cancel'] = array(
                    'href' => $back,
                    'desc' => $this->l('Cancel')
                );
                // Default save button - action dynamically handled in javascript
                $this->toolbar_btn['save-import'] = array(
                    'href' => '#',
                    'desc' => $this->l('Import .CSV data')
                );
                break;
        }
    }

    protected function generateContentTable($current_table, $nb_column, $handle, $glue)
    {
        $html = '<table id="table'.$current_table.'" style="display: none;" class="table table-bordered"><thead><tr>';
        // Header
        for ($i = 0; $i < $nb_column; $i++) {
            if (MAX_COLUMNS * (int)$current_table <= $i && (int)$i < MAX_COLUMNS * ((int)$current_table + 1)) {
                $html .= '<th>
                            <select id="type_value['.$i.']"
                                name="type_value['.$i.']"
                                class="type_value">
                                '.$this->getTypeValuesOptions($i).'
                            </select>
                        </th>';
            }
        }
        $html .= '</tr></thead><tbody>';

        AdminImportController::setLocale();
        for ($current_line = 0; $current_line < 10 && $line = fgetcsv($handle, MAX_LINE_SIZE, $glue, escape: ""); $current_line++) {
            /* UTF-8 conversion */
            if (Tools::getValue('convert')) {
                $line = $this->utf8EncodeArray($line);
            }
            $html .= '<tr id="table_'.$current_table.'_line_'.$current_line.'">';
            foreach ($line as $nb_c => $column) {
                if ((MAX_COLUMNS * (int)$current_table <= $nb_c) && ((int)$nb_c < MAX_COLUMNS * ((int)$current_table + 1))) {
                    $html .= '<td>'.htmlentities(Tools::substr($column, 0, 200), ENT_QUOTES, 'UTF-8').'</td>';
                }
            }
            $html .= '</tr>';
        }
        $html .= '</tbody></table>';
        AdminImportController::rewindBomAware($handle);
        return $html;
    }

    public function init()
    {
        parent::init();
        if (Tools::isSubmit('submitImportFile')) {
            $this->display = 'import';
        }
    }

    public function initContent()
    {
        $this->initTabModuleList();
        // toolbar (save, cancel, new, ..)
        $this->initToolbar();
        $this->initPageHeaderToolbar();
        if ($this->display == 'import') {
            if (Tools::getValue('csv')) {
                $this->content .= $this->renderView();
            } else {
                $this->errors[] = $this->l('You must upload a file in order to proceed to the next step');
                $this->content .= $this->renderForm();
            }
        } else {
            $this->content .= $this->renderForm();
        }

        $this->context->smarty->assign(array(
            'content' => $this->content,
            'url_post' => self::$currentIndex.'&token='.$this->token,
            'show_page_header_toolbar' => $this->show_page_header_toolbar,
            'page_header_toolbar_title' => $this->page_header_toolbar_title,
            'page_header_toolbar_btn' => $this->page_header_toolbar_btn
        ));
    }

    protected static function rewindBomAware($handle)
    {
        // A rewind wrapper that skips BOM signature wrongly
        if (!is_resource($handle)) {
            return false;
        }
        rewind($handle);
        if (($bom = fread($handle, 3)) != "\xEF\xBB\xBF") {
            rewind($handle);
        }
    }

    protected static function getBoolean($field)
    {
        return (bool)$field;
    }

    protected static function getPrice($field)
    {
        $field = ((float)str_replace(',', '.', $field));
        $field = ((float)str_replace('%', '', $field));
        return $field;
    }

    protected static function split($field)
    {
        if (empty($field)) {
            return array();
        }

        $separator = Tools::getValue('multiple_value_separator');
        if (is_null($separator) || trim($separator) == '') {
            $separator = ',';
        }

        do {
            $uniqid_path = _PS_UPLOAD_DIR_.uniqid();
        } while (file_exists($uniqid_path));
        file_put_contents($uniqid_path, $field);
        $tab = '';
        if (!empty($uniqid_path)) {
            $fd = fopen($uniqid_path, 'r');
            $tab = fgetcsv($fd, MAX_LINE_SIZE, $separator, escape: "");
            fclose($fd);
            if (file_exists($uniqid_path)) {
                @unlink($uniqid_path);
            }
        }

        if (empty($tab) || (!is_array($tab))) {
            return array();
        }
        return $tab;
    }

    protected static function createMultiLangField($field)
    {
        $res = array();
        foreach (Language::getIDs(false) as $id_lang) {
            $res[$id_lang] = $field;
        }

        return $res;
    }

    protected function getTypeValuesOptions($nb_c)
    {
        $i = 0;
        $no_pre_select = array('price_tin', 'feature');

        $options = '';
        foreach ($this->available_fields as $k => $field) {
            $options .= '<option value="'.$k.'"';
            if ($k === 'price_tin') {
                ++$nb_c;
            }
            if ($i === ($nb_c + 1) && (!in_array($k, $no_pre_select))) {
                $options .= ' selected="selected"';
            }
            $options .= '>'.$field['label'].'</option>';
            ++$i;
        }
        return $options;
    }

    /*
    * Return fields to be display AS piece of advise
    *
    * @param $in_array boolean
    * @return string or return array
    */
    public function getAvailableFields($in_array = false)
    {
        $i = 0;
        $fields = array();
        $keys = array_keys($this->available_fields);
        array_shift($keys);
        foreach ($this->available_fields as $k => $field) {
            if ($k === 'no') {
                continue;
            }
            if ($k === 'price_tin') {
                $fields[$i - 1] = '<div>'.$this->available_fields[$keys[$i - 1]]['label'].' '.$this->l('or').' '.$field['label'].'</div>';
            } else {
                if (isset($field['help'])) {
                    $html = '&nbsp;<a href="#" class="help-tooltip" data-toggle="tooltip" title="'.$field['help'].'"><i class="icon-info-sign"></i></a>';
                } else {
                    $html = '';
                }
                $fields[] = '<div>'.$field['label'].$html.'</div>';
            }
            ++$i;
        }
        if ($in_array) {
            return $fields;
        } else {
            return implode("\n\r", $fields);
        }
    }

    protected function receiveTab()
    {
        $type_value = Tools::getValue('type_value') ? Tools::getValue('type_value') : array();
        foreach ($type_value as $nb => $type) {
            if ($type != 'no') {
                self::$column_mask[$type] = $nb;
            }
        }
    }

    public static function getMaskedRow($row)
    {
        $res = array();
        if (is_array(self::$column_mask)) {
            foreach (self::$column_mask as $type => $nb) {
                $res[$type] = isset($row[$nb]) ? $row[$nb] : null;
            }
        }

        return $res;
    }

    protected static function setDefaultValues(&$info)
    {
        foreach (self::$default_values as $k => $v) {
            if (!isset($info[$k]) || $info[$k] == '') {
                $info[$k] = $v;
            }
        }
    }

    protected static function setEntityDefaultValues(&$entity)
    {
        $members = get_object_vars($entity);
        foreach (self::$default_values as $k => $v) {
            if ((array_key_exists($k, $members) && $entity->$k === null) || !array_key_exists($k, $members)) {
                $entity->$k = $v;
            }
        }
    }

    protected static function fillInfo($infos, $key, &$entity)
    {
        $infos = trim($infos);
        if (isset(self::$validators[$key][1]) && self::$validators[$key][1] == 'createMultiLangField' && Tools::getValue('iso_lang')) {
            $id_lang = Language::getIdByIso(Tools::getValue('iso_lang'));
            $tmp = call_user_func(self::$validators[$key], $infos);
            foreach ($tmp as $id_lang_tmp => $value) {
                if (empty($entity->{$key}[$id_lang_tmp]) || $id_lang_tmp == $id_lang) {
                    $entity->{$key}[$id_lang_tmp] = $value;
                }
            }
        } elseif (!empty($infos) || $infos == '0') { // ($infos == '0') => if you want to disable a product by using "0" in active because empty('0') return true
                $entity->{$key} = isset(self::$validators[$key]) ? call_user_func(self::$validators[$key], $infos) : $infos;
        }

        return true;
    }

    /**
     * @param $array
     * @param $funcname
     * @param mixed $user_data
     * @return bool
     */
    public static function arrayWalk(&$array, $funcname, &$user_data = false)
    {
        if (!is_callable($funcname)) {
            return false;
        }

        foreach ($array as $k => $row) {
            if (!call_user_func_array($funcname, array($row, $k, &$user_data))) {
                return false;
            }
        }
        return true;
    }

    /**
     * copyImg copy an image located in $url and save it in a path
     * according to $entity->$id_entity .
     * $id_image is used if we need to add a watermark
     *
     * @param int $id_entity id of product or category (set in entity)
     * @param int $id_image (default null) id of the image if watermark enabled.
     * @param string $url path or url to use
     * @param string $entity 'products' or 'categories'
     * @param bool $regenerate
     * @return bool
     */
    protected static function copyImg($id_entity, $id_image = null, $url = '', $entity = 'products', $regenerate = true)
    {
        $tmpfile = tempnam(_PS_TMP_IMG_DIR_, 'ps_import');
        $watermark_types = explode(',', Configuration::get('WATERMARK_TYPES'));

        switch ($entity) {
            default:
            case 'products':
                $image_obj = new Image($id_image);
                $path = $image_obj->getPathForCreation();
            break;
            case 'hotels':
                $image_obj = new HotelImage($id_image);
                $path = $image_obj->getPathForCreation().$id_image;
            break;
            case 'categories':
                $path = _PS_CAT_IMG_DIR_.(int)$id_entity;
            break;
        }

        $url = urldecode(trim($url));
        $parced_url = parse_url($url);

        if (isset($parced_url['path'])) {
            $uri = ltrim($parced_url['path'], '/');
            $parts = explode('/', $uri);
            foreach ($parts as &$part) {
                $part = rawurlencode($part);
            }
            unset($part);
            $parced_url['path'] = '/'.implode('/', $parts);
        }

        if (isset($parced_url['query'])) {
            $query_parts = array();
            parse_str($parced_url['query'], $query_parts);
            $parced_url['query'] = http_build_query($query_parts);
        }

        if (!function_exists('http_build_url')) {
            require_once(_PS_TOOL_DIR_.'http_build_url/http_build_url.php');
        }

        $url = http_build_url('', $parced_url);

        $orig_tmpfile = $tmpfile;

        if (Tools::copy($url, $tmpfile)) {
            // Evaluate the memory required to resize the image: if it's too much, you can't resize it.
            if (!ImageManager::checkImageMemoryLimit($tmpfile)) {
                @unlink($tmpfile);
                return false;
            }

            $tgt_width = $tgt_height = 0;
            $src_width = $src_height = 0;
            $error = 0;
            ImageManager::resize($tmpfile, $path.'.jpg', null, null, 'jpg', false, $error, $tgt_width, $tgt_height, 5,
                                 $src_width, $src_height);
            $images_types = ImageType::getImagesTypes($entity, true);

            if ($regenerate) {
                $previous_path = null;
                $path_infos = array();
                $path_infos[] = array($tgt_width, $tgt_height, $path.'.jpg');
                foreach ($images_types as $image_type) {
                    $tmpfile = self::get_best_path($image_type['width'], $image_type['height'], $path_infos);

                    if (ImageManager::resize($tmpfile, $path.'-'.stripslashes($image_type['name']).'.jpg', $image_type['width'],
                                         $image_type['height'], 'jpg', false, $error, $tgt_width, $tgt_height, 5,
                                         $src_width, $src_height)) {
                        // the last image should not be added in the candidate list if it's bigger than the original image
                        if ($tgt_width <= $src_width && $tgt_height <= $src_height) {
                            $path_infos[] = array($tgt_width, $tgt_height, $path.'-'.stripslashes($image_type['name']).'.jpg');
                        }
                        if ($entity == 'products') {
                            if (is_file(_PS_TMP_IMG_DIR_.'product_mini_'.(int)$id_entity.'.jpg')) {
                                unlink(_PS_TMP_IMG_DIR_.'product_mini_'.(int)$id_entity.'.jpg');
                            }
                            if (is_file(_PS_TMP_IMG_DIR_.'product_mini_'.(int)$id_entity.'_'.(int)Context::getContext()->shop->id.'.jpg')) {
                                unlink(_PS_TMP_IMG_DIR_.'product_mini_'.(int)$id_entity.'_'.(int)Context::getContext()->shop->id.'.jpg');
                            }
                        }
                    }
                    if (in_array($image_type['id_image_type'], $watermark_types)) {
                        Hook::exec('actionWatermark', array('id_image' => $id_image, 'id_product' => $id_entity));
                    }
                }
            }
        } else {
            @unlink($orig_tmpfile);
            return false;
        }
        unlink($orig_tmpfile);
        return true;
    }

    private static function get_best_path($tgt_width, $tgt_height, $path_infos)
    {
        $path_infos = array_reverse($path_infos);
        $path = '';
        foreach ($path_infos as $path_info) {
            list($width, $height, $path) = $path_info;
            if ($width >= $tgt_width && $height >= $tgt_height) {
                return $path;
            }
        }
        return $path;
    }

    public function categoryImport()
    {
        $cat_moved = array();
        $this->receiveTab();
        $handle = $this->openCsvFile();
        $default_language_id = (int)Configuration::get('PS_LANG_DEFAULT');
        $id_lang = Language::getIdByIso(Tools::getValue('iso_lang'));
        if (!Validate::isUnsignedId($id_lang)) {
            $id_lang = $default_language_id;
        }

        AdminImportController::setLocale();
        $convert = Tools::getValue('convert');
        $force_ids = Tools::getValue('forceIDs');
        $regenerate = Tools::getValue('regenerate');
        $shop_is_feature_active = Shop::isFeatureActive();
        $core_categories = array(
            Configuration::get('PS_HOME_CATEGORY'),
            Configuration::get('PS_ROOT_CATEGORY'),
            Configuration::get('PS_SERVICE_CATEGORY'),
            Configuration::get('PS_LOCATIONS_CATEGORY'),
        );

        for ($current_line = 0; $line = fgetcsv($handle, MAX_LINE_SIZE, $this->separator, escape: ""); $current_line++) {
            if ($convert) {
                $line = $this->utf8EncodeArray($line);
            }

            $info = AdminImportController::getMaskedRow($line);

            $tab_categ = array(Configuration::get('PS_HOME_CATEGORY'), Configuration::get('PS_ROOT_CATEGORY'));
            if (isset($info['id']) && in_array((int)$info['id'], $tab_categ)) {
                $this->errors[] = Tools::displayError('The category ID cannot be the same as the Root category ID or the Home category ID.');
                continue;
            }

            AdminImportController::setDefaultValues($info);

            $createNew = true;
            if (isset($info['id'])
                && (int) $info['id']
                && Category::existsInDatabase((int)$info['id'], 'category')
                && !in_array($info['id'], $core_categories)
            ) {
                $createNew = false;
            }

            if ($createNew) {
                $category = new Category();
            } else {
                $category = new Category((int) $info['id']);
                if ($force_ids) {
                    $category->force_id = $info['id'];
                }
            }

            AdminImportController::arrayWalk($info, array('AdminImportController', 'fillInfo'), $category);

            if (isset($category->parent) && is_numeric($category->parent)) {
                $category->id_parent = $category->parent;
            } else if (isset($category->parent) && is_string($category->parent)) {
                $category_parent = Category::searchByName($id_lang, $category->parent, true);
                if ($category_parent['id_category']) {
                    $category->id_parent = (int)$category_parent['id_category'];
                    $category->level_depth = (int)$category_parent['level_depth'] + 1;
                } else {
                    $category_to_create = new Category();
                    $category_to_create->name = AdminImportController::createMultiLangField($category->parent);
                    $category_to_create->active = 1;
                    $category_link_rewrite = Tools::link_rewrite($category_to_create->name[$id_lang]);
                    $category_to_create->link_rewrite = AdminImportController::createMultiLangField($category_link_rewrite);
                    $category_to_create->id_parent = Configuration::get('PS_HOME_CATEGORY'); // Default parent is home for unknown category to create
                    if (($field_error = $category_to_create->validateFields(UNFRIENDLY_ERROR, true)) === true &&
                        ($lang_field_error = $category_to_create->validateFieldsLang(UNFRIENDLY_ERROR, true)) === true && $category_to_create->add()) {
                        $category->id_parent = $category_to_create->id;
                        Cache::clean('Category::searchByName_'.$category->parent);
                    } else {
                        $this->errors[] = sprintf(
                            Tools::displayError('%1$s (ID: %2$s) cannot be saved'),
                            $category_to_create->name[$id_lang],
                            (isset($category_to_create->id) && !empty($category_to_create->id))? $category_to_create->id : 'null'
                        );
                        $this->errors[] = ($field_error !== true ? $field_error : '').(isset($lang_field_error) && $lang_field_error !== true ? $lang_field_error : '').
                            Db::getInstance()->getMsgError();
                    }
                }
            }

            $category->id_shop_default = (int) Context::getContext()->shop->id;
            $category->link_rewrite[$default_language_id] = Tools::link_rewrite($category->name[$default_language_id]);
            if (($field_error = $category->validateFields(UNFRIENDLY_ERROR, true)) === true
                && ($lang_field_error = $category->validateFieldsLang(UNFRIENDLY_ERROR, true)) === true
                && empty($this->errors)
            ) {
                $category_already_created = Category::searchByNameAndParentCategoryId(
                    $id_lang,
                    $category->name[$id_lang],
                    $category->id_parent
                );
                // If category already in base, get id category back
                if (isset($category_already_created['id_category'])
                    && $category_already_created['id_category']
                ) {
                    $cat_moved[$category->id] = (int) $category_already_created['id_category'];
                    $category->id = (int)$category_already_created['id_category'];
                    if ($category_already_created['date_add'] &&  Validate::isDate($category_already_created['date_add'])) {
                        $category->date_add = $category_already_created['date_add'];
                    }
                }

                if ($category->id && $category->id == $category->id_parent) {
                    $this->errors[] = Tools::displayError('A category cannot be its own parent');
                    continue;
                }

                /* No automatic nTree regeneration for import */
                $category->doNotRegenerateNTree = true;

                $res = false;
                // If id category AND id category already in base, trying to update
                if ($category->id && $category->categoryExists($category->id) ) {
                    $res = $category->update();
                }

                // If no id_category or update failed
                if (!$res) {
                    $res = $category->add();
                }
            }
            //copying images of categories
            if (isset($category->image) && !empty($category->image)) {
                if (!(AdminImportController::copyImg($category->id, null, $category->image, 'categories', !$regenerate))) {
                    $this->warnings[] = $category->image.' '.Tools::displayError('cannot be copied.');
                }
            }
            // If both failed, mysql error
            if (!$res) {
                $this->errors[] = sprintf(
                    Tools::displayError('%1$s (ID: %2$s) cannot be saved'),
                    (isset($info['name']) && !empty($info['name']))? Tools::safeOutput($info['name']) : 'No Name',
                    (isset($info['id']) && !empty($info['id']))? Tools::safeOutput($info['id']) : 'No ID'
                );
                $error_tmp = ($field_error !== true ? $field_error : '').(isset($lang_field_error) && $lang_field_error !== true ? $lang_field_error : '').Db::getInstance()->getMsgError();
                if ($error_tmp != '') {
                    $this->errors[] = $error_tmp;
                }
            } else {
                // Associate category to shop
                if ($shop_is_feature_active) {
                    Db::getInstance()->execute('
                        DELETE FROM '._DB_PREFIX_.'category_shop
                        WHERE id_category = '.(int)$category->id
                    );

                    if (!$shop_is_feature_active) {
                        $info['shop'] = 1;
                    } elseif (!isset($info['shop']) || empty($info['shop'])) {
                        $info['shop'] = implode($this->multiple_value_separator, Shop::getContextListShopID());
                    }

                    // Get shops for each attributes
                    $info['shop'] = explode($this->multiple_value_separator, $info['shop']);

                    foreach ($info['shop'] as $shop) {
                        if (!empty($shop) && !is_numeric($shop)) {
                            $category->addShop(Shop::getIdByName($shop));
                        } elseif (!empty($shop)) {
                            $category->addShop($shop);
                        }
                    }
                }
            }
        }

        /* Import has finished, we can regenerate the categories nested tree */
        Category::regenerateEntireNtree();
        $this->closeCsvFile($handle);
    }

    public function hotelImport()
    {
        $this->receiveTab();
        $handle = $this->openCsvFile();
        $idLangDefault = (int) Configuration::get('PS_LANG_DEFAULT');
        AdminImportController::setLocale();
        $convert = Tools::getValue('convert');
        $force_ids = Tools::getValue('forceIDs');
        $regenerate = Tools::getValue('regenerate');
        $objHotelImage = new HotelImage();
        for ($current_line = 0; $line = fgetcsv($handle, MAX_LINE_SIZE, $this->separator, escape: ""); $current_line++) {
            if ($convert) {
                $line = $this->utf8EncodeArray($line);
            }

            $info = AdminImportController::getMaskedRow($line);
            if ($this->checkRequiredFields($info, 'hotel_name')) {
                $hotelExists = false;
                if (isset($info['id']) && Validate::isLoadedObject(new HotelBranchInformation((int) $info['id']))) {
                    $hotelExists = true;
                }

                if ($hotelExists) {
                    $objHotelBranch = new HotelBranchInformation((int) $info['id']);
                } else {
                    $objHotelBranch = new HotelBranchInformation();
                }

                $objCountry = new Country((int) $info['id_country'], $this->context->language->id);
                if (!Validate::isLoadedObject($objCountry)) {
                    $this->errors[] = sprintf(
                        $this->l('ID Country is invalid for (ID: %1$s)'),
                        (isset($info['id']) && !empty($info['id']))? $info['id'] : 'null'
                    );
                } else if (!isset($info['city']) || !$info['city']) {
                    $this->errors[] = sprintf(
                        $this->l('City is required for (ID: %1$s)'),
                        (isset($info['id']) && !empty($info['id']))? $info['id'] : 'null'
                    );
                } else if (!isset($info['postcode'])
                    || !Validate::isPostCode($info['postcode'])
                    || !$objCountry->checkZipCode($info['postcode'])
                ) {
                    $this->errors[] = sprintf(
                        $this->l('Zip code is invalid for (ID: %1$s)'),
                        (isset($info['id']) && !empty($info['id']))? $info['id'] : 'null'
                    );
                } else if (isset($info['fax']) && !Validate::isGenericName($info['fax'])) {
                    $this->errors[] = sprintf(
                        $this->l('Fax is invalid for (ID: %1$s)'),
                        (isset($info['id']) && !empty($info['id']))? $info['id'] : 'null'
                    );
                } else if ($objCountry->active) {
                    if ($objCountry->contains_states) {
                        if (!isset($info['id_state'])
                            || !Validate::isLoadedObject(new State($info['id_state']))
                        ) {
                            $this->warnings[] = sprintf(
                                $this->l('ID state is invalid for (ID: %1$s)'),
                                (isset($info['id']) && !empty($info['id']))? $info['id'] : 'null'
                            );
                        }
                    }

                    AdminImportController::arrayWalk($info, array('AdminImportController', 'fillInfo'), $objHotelBranch);
                    $fieldError = $objHotelBranch->validateFields(UNFRIENDLY_ERROR, true);
                    $langFieldError = $objHotelBranch->validateFieldsLang(UNFRIENDLY_ERROR, true);
                    $res = false;
                    if ($fieldError === true && $langFieldError === true) {
                        if ($hotelExists) {
                            $res = $objHotelBranch->save();
                        } else {
                            if ($force_ids) {
                                $objHotelBranch->force_id = $info['id'];
                                $res = $objHotelBranch->add();
                            } else {
                                $res = $objHotelBranch->add();
                            }
                        }

                        $existingHotelCategories = $objHotelBranch->getAllHotelCategories();
                        if ($objHotelBranch->id) {
                            if ($primaryHotelId = Configuration::get('WK_PRIMARY_HOTEL')) {
                                if ($primaryHotelId == $objHotelBranch->id && !$objHotelBranch->active) {
                                    $hotels = $objHotelBranch->hotelBranchesInfo(false, 1);
                                    if (($hotel = array_shift($hotels)) && isset($hotel['id'])) {
                                        Configuration::updateValue('WK_PRIMARY_HOTEL', $hotel['id']);
                                    } else {
                                        Configuration::updateValue('WK_PRIMARY_HOTEL', 0);
                                    }
                                }
                            } else if ($objHotelBranch->active) {
                                Configuration::updateValue('WK_PRIMARY_HOTEL', $objHotelBranch->id);
                            }

                            if ($idHotelAddress = $objHotelBranch->getHotelIdAddress()) {
                                $objAddress = new Address($idHotelAddress);
                            } else {
                                $objAddress = new Address();
                            }

                            $objAddress->id_hotel = $objHotelBranch->id;
                            $objAddress->id_country = $info['id_country'];
                            $objAddress->id_state = $info['id_state'];
                            $objAddress->city = $info['city'];
                            $objAddress->postcode = $info['postcode'];
                            $objHotelBranch->fax = $info['fax'];
                            $hotelName = $objHotelBranch->hotel_name[$idLangDefault];
                            $objAddress->alias = $hotelName;
                            $hotelName = trim(preg_replace('/[0-9!<>,;?=+()@#"°{}_$%:]*$/u', '', $hotelName));
                            $addressFirstName = $hotelName;
                            $addressLastName = $hotelName;
                            // If hotel name is length is greater than 32 then we split it into two
                            if (Tools::strlen($hotelName) > 32) {
                                // Slicing and removing the extra spaces after slicing
                                $addressFirstName = trim(substr($hotelName, 0, 32));
                                // To remove the excess space from last name
                                if (!$addressLastName = trim(substr($hotelName, 32, 32))) {
                                    // since the last name can also be an empty space we will then use first name as last name
                                    $addressLastName = $addressFirstName;
                                }
                            }

                            $objAddress->firstname = $addressFirstName;
                            $objAddress->lastname = $addressLastName;
                            $objAddress->address1 = $info['address'];
                            $objAddress->phone = $info['phone'];
                            $objAddress->save();
                            $groupIds = array();
                            if ($dataGroupIds = Group::getGroups($this->context->language->id)) {
                                foreach ($dataGroupIds as $key => $value) {
                                    $groupIds[] = $value['id_group'];
                                }
                            }

                            // refund_ids will be set by array walk and does not exists the hotel information table
                            if ($hotelRefundRules = $objHotelBranch->refund_ids) {
                                foreach ($hotelRefundRules as $key => $idRefundRule) {
                                    $objBranchRefundRules = new HotelBranchRefundRules();
                                    if (!$objBranchRefundRules->getHotelRefundRules(
                                        $objHotelBranch->id,
                                        $idRefundRule
                                    )) {
                                        $objBranchRefundRules->id_hotel = $objHotelBranch->id;
                                        $objBranchRefundRules->id_refund_rule = $idRefundRule;
                                        $objBranchRefundRules->position = $key + 1;
                                        $objBranchRefundRules->save();
                                    }
                                }
                            }

                            if (isset($objHotelBranch->delete_existing_images)
                                && (bool) $objHotelBranch->delete_existing_images
                            ) {
                                $hotelAllImages = $objHotelImage->getImagesByHotelId($objHotelBranch->id);
                                if ($hotelAllImages) {
                                    foreach ($hotelAllImages as $value_img) {
                                        if (Validate::isLoadedObject($objHotelImage = new HotelImage((int) $value_img['id']))) {
                                            $objHotelImage->deleteImage();
                                        }
                                    }
                                }
                            }

                            if (isset($objHotelBranch->image) && $objHotelBranch->image) {
                                // adding new hotel images
                                $hotelImageExists = (bool) $objHotelImage->getImagesByHotelId($objHotelBranch->id);
                                foreach ($objHotelBranch->image as $key => $url) {
                                    $url = trim($url);
                                    $error = false;
                                    if (!empty($url)) {
                                        $url = str_replace(' ', '%20', $url);
                                        $objHotelImage = new HotelImage();
                                        $objHotelImage->id_hotel = (int) $objHotelBranch->id;
                                        $objHotelImage->cover = (!$key && !$hotelImageExists) ? true : false;
                                        if ($objHotelImage->add()) {
                                            if (!AdminImportController::copyImg($objHotelBranch->id, $objHotelImage->id, $url, 'hotels', !$regenerate)) {
                                                $objHotelImage->delete();
                                                $this->warnings[] = sprintf(Tools::displayError('Error copying image: %s'), $url);
                                            }
                                        } else {
                                            $error = true;
                                        }
                                    } else {
                                        $error = true;
                                    }

                                    if ($error) {
                                        $this->warnings[] = sprintf(Tools::displayError('hotel #%1$d: the picture (%2$s) cannot be saved.'), $objHotelImage->id_hotel, $url);
                                    }
                                }
                            }

                            $linkRewriteArray = array();
                            foreach (Language::getLanguages() as $lang) {
                                $linkRewriteArray[$lang['id_lang']] = Tools::link_rewrite($objHotelBranch->hotel_name[$lang['id_lang']]);
                                if (!$linkRewriteArray[$lang['id_lang']]) {
                                    $linkRewriteArray[$lang['id_lang']] = Tools::link_rewrite($objHotelBranch->hotel_name[$idLangDefault]);
                                }
                            }

                            $country = Country::getNameById($idLangDefault, $info['id_country']);
                             if ($catCountry = $objHotelBranch->addCategory(
                                array (
                                    'name' => $country,
                                    'group_ids' => $groupIds,
                                    'parent_category' => false
                                )
                            )) {
                                if ($info['id_state']) {
                                    $objState = new State();
                                    $stateName = $objState->getNameById($info['id_state']);
                                } else {
                                    $stateName = $info['city'];
                                }

                                if ($catState = $objHotelBranch->addCategory(
                                    array (
                                        'name' => $stateName,
                                        'group_ids' => $groupIds,
                                        'parent_category' => $catCountry
                                    )
                                )) {
                                     if ($catCity = $objHotelBranch->addCategory(
                                        array (
                                            'name' => $info['city'],
                                            'group_ids' => $groupIds,
                                            'parent_category' => $catState
                                        )
                                    )) {
                                        if ($objHotelBranch->id_category) {
                                            $objCategory = new Category($objHotelBranch->id_category);
                                            $objCategory->name = $objHotelBranch->hotel_name;
                                            $objCategory->id_parent = $catCity;
                                            $objCategory->save();
                                            foreach (Language::getLanguages() as $lang) {
                                                $objCategory->link_rewrite[$lang['id_lang']] = $linkRewriteArray[$lang['id_lang']];
                                            }

                                            Category::regenerateEntireNtree();
                                        } else {
                                            if ($catHotel = $objHotelBranch->addCategory(
                                                array (
                                                    'name' => $objHotelBranch->hotel_name,
                                                    'group_ids' => $groupIds,
                                                    'parent_category' => $catCity,
                                                    'is_hotel' => 1,
                                                    'id_hotel' => $objHotelBranch->id,
                                                    'link_rewrite' => $linkRewriteArray
                                                )
                                            )) {
                                                $objHotelBranch = new HotelBranchInformation($objHotelBranch->id);
                                                $objHotelBranch->id_category = $catHotel;
                                                $objHotelBranch->save();
                                            }
                                        }
                                    }
                                }
                            }
                        }

                        $newHotelCategories = $objHotelBranch->getAllHotelCategories();
                        if (($unusedCategories = array_diff($existingHotelCategories, $newHotelCategories))
                            && ($hotelCategories = $objHotelBranch->getAllHotelCategories())
                        ) {
                            foreach ($unusedCategories as $idCategory) {
                                if (!in_array($idCategory, $hotelCategories)
                                    && $idCategory != Configuration::get('PS_HOME_CATEGORY')
                                    && $idCategory != Configuration::get('PS_LOCATIONS_CATEGORY')
                                ) {
                                    $objCategory = new Category($idCategory);
                                    $objCategory->delete();
                                }
                            }
                        }

                        // Updating the room type categories if exists.
                        $objHotelBranch->updateRoomTypeCategories();
                        $objHotelOrderRestrictDate = new HotelOrderRestrictDate();
                        $restrictDateInfo = HotelOrderRestrictDate::getDataByHotelId($objHotelBranch->id);
                        if ($restrictDateInfo) {
                            $objHotelOrderRestrictDate = new HotelOrderRestrictDate($restrictDateInfo['id']);
                        } else {
                            $objHotelOrderRestrictDate = new HotelOrderRestrictDate();
                        }

                        $objHotelOrderRestrictDate->id_hotel = $objHotelBranch->id;
                        $objHotelOrderRestrictDate->use_global_max_checkout_offset = true;
                        $objHotelOrderRestrictDate->use_global_min_booking_offset = true;

                        if (isset($info['max_checkout_offset'])
                            && ((Configuration::get('PS_MIN_BOOKING_OFFSET') < $info['max_checkout_offset'])
                                || (isset($info['min_booking_offset']) && ($info['min_booking_offset'] < $info['max_checkout_offset']))
                            )
                        ) {
                            $objHotelOrderRestrictDate->use_global_max_checkout_offset = false;
                            $objHotelOrderRestrictDate->max_checkout_offset = $info['max_checkout_offset'];
                        }

                        if (isset($info['min_booking_offset'])
                            && ((Configuration::get('PS_MAX_CHECKOUT_OFFSET') > $info['min_booking_offset'])
                                || (isset($info['max_checkout_offset']) && ($info['max_checkout_offset'] > $info['min_booking_offset']))
                            )
                        ) {
                            $objHotelOrderRestrictDate->use_global_min_booking_offset = false;
                            $objHotelOrderRestrictDate->min_booking_offset = $info['min_booking_offset'];
                        }

                        $objHotelOrderRestrictDate->save();
                    }

                    if (!$res) {
                        $this->errors[] = sprintf(
                            Tools::displayError('%1$s (ID: %2$s) cannot be saved'),
                            (isset($info['hotel_name']) && !empty($info['hotel_name']))? Tools::safeOutput($info['name']) : 'No Name',
                            (isset($info['id']) && !empty($info['id']))? Tools::safeOutput($info['id']) : 'No ID'
                        );
                        if ($field_error !== true
                            || $lang_field_error !== true
                        ) {
                            $this->errors[] = ($field_error !== true ? $field_error : '').(isset($lang_field_error) && $lang_field_error !== true ? $lang_field_error : '').
                                Db::getInstance()->getMsgError();
                        }
                    }
                } else {
                    $this->warnings[] = $this->l('Hotel creation for the ').$objCountry->name.
                    $this->l(' is currently unavailable. Kindly activate the country for hotel creation.');
                }
            }
        }

        $this->closeCsvFile($handle);
    }

    public function roomTypeImport()
    {
        $this->receiveTab();
        $handle = $this->openCsvFile();
        $idLangDefault = (int) Configuration::get('PS_LANG_DEFAULT');
        $id_lang = Language::getIdByIso(Tools::getValue('iso_lang'));
        if (!Validate::isUnsignedId($id_lang)) {
            $id_lang = $idLangDefault;
        }

        AdminImportController::setLocale();
        $shop_ids = Shop::getCompleteListOfShopsID();
        $convert = Tools::getValue('convert');
        $force_ids = Tools::getValue('forceIDs');
        $regenerate = Tools::getValue('regenerate');
        $shop_is_feature_active = Shop::isFeatureActive();
        Module::setBatchMode(true);
        $objRoomType = new HotelRoomType();
        $objAdvancePayment = new HotelAdvancedPayment();
        for ($current_line = 0; $line = fgetcsv($handle, MAX_LINE_SIZE, $this->separator, escape: ""); $current_line++) {
            if ($convert) {
                $line = $this->utf8EncodeArray($line);
            }

            $info = AdminImportController::getMaskedRow($line);
            if ($this->checkRequiredFields($info)) {
                if (!isset($info['id_hotel'])
                    || !Validate::isLoadedObject(new HotelBranchInformation($info['id_hotel'])
                )) {
                    $this->errors[] = sprintf(
                        $this->l('ID Hotel is invalid for (ID: %1$s)'),
                        (isset($info['id']) && !empty($info['id']))? $info['id'] : 'null'
                    );
                } else {
                    $roomTypeExists = false;
                    if (isset($info['id']) && (int) $info['id'] && Product::existsInDatabase((int) $info['id'], 'product')) {
                        $product = new Product((int) $info['id']);
                        if ($product->booking_product || $force_ids) {
                            $roomTypeExists = true;
                        }
                    }

                    if ($roomTypeExists) {
                        $product = new Product((int) $info['id']);
                    } else {
                        $product = new Product();
                    }

                    $update_advanced_stock_management_value = false;
                    if ($roomTypeExists) {
                        $product->loadStockData();
                        $update_advanced_stock_management_value = true;
                        $category_data = Product::getProductCategories((int) $product->id);
                        if (is_array($category_data)) {
                            foreach ($category_data as $tmp) {
                                if (!isset($product->category) || !$product->category || is_array($product->category)) {
                                    $product->category[] = $tmp;
                                }
                            }
                        }
                    }

                    AdminImportController::setEntityDefaultValues($product);
                    AdminImportController::arrayWalk($info, array('AdminImportController', 'fillInfo'), $product);
                    $product->booking_product = 1;
                    $product->is_virtual = 1;
                    if (!$shop_is_feature_active) {
                        $product->shop = (int)Configuration::get('PS_SHOP_DEFAULT');
                    } elseif (!isset($product->shop) || empty($product->shop)) {
                        $product->shop = implode($this->multiple_value_separator, Shop::getContextListShopID());
                    }

                    if (!$shop_is_feature_active) {
                        $product->id_shop_default = (int)Configuration::get('PS_SHOP_DEFAULT');
                    } else {
                        $product->id_shop_default = (int)Context::getContext()->shop->id;
                    }

                    // link product to shops
                    $product->id_shop_list = array();
                    foreach (explode($this->multiple_value_separator, $product->shop) as $shop) {
                        if (!empty($shop) && !is_numeric($shop)) {
                            $product->id_shop_list[] = Shop::getIdByName($shop);
                        } elseif (!empty($shop)) {
                            $product->id_shop_list[] = $shop;
                        }
                    }

                    if ((int) $product->id_tax_rules_group != 0) {
                        if (Validate::isLoadedObject(new TaxRulesGroup($product->id_tax_rules_group))) {
                            $address = $this->context->shop->getAddress();
                            $tax_manager = TaxManagerFactory::getManager($address, $product->id_tax_rules_group);
                            $product_tax_calculator = $tax_manager->getTaxCalculator();
                            $product->tax_rate = $product_tax_calculator->getTotalRate();
                        } else {
                            $this->addProductWarning(
                                'id_tax_rules_group',
                                $product->id_tax_rules_group,
                                Tools::displayError('Invalid tax rule group ID. You first need to create a group with this ID.')
                            );
                        }
                    }

                    if (!Configuration::get('PS_USE_ECOTAX')) {
                        $product->ecotax = 0;
                    }
                    // Will update default category if there is none set here. Home if no category at all.
                    if (!isset($product->id_category_default) || !$product->id_category_default) {
                        // this if will avoid ereasing default category if category column is not present in the CSV file (or ignored)
                        if (isset($product->id_category[0])) {
                            $product->id_category_default = (int)$product->id_category[0];
                        } else {
                            $defaultProductShop = new Shop($product->id_shop_default);
                            $product->id_category_default = Category::getRootCategory(null, Validate::isLoadedObject($defaultProductShop)?$defaultProductShop:null)->id;
                        }
                    }

                    if (!(is_array($product->link_rewrite) && count($product->link_rewrite))) {
                        $link_rewrite = isset($product->link_rewrite[$id_lang]) ? trim($product->link_rewrite[$id_lang]) : '';
                        $product->link_rewrite = AdminImportController::createMultiLangField($link_rewrite);
                    } else {
                        $product->link_rewrite[(int) $id_lang] = Tools::link_rewrite($product->name[$id_lang]);
                    }

                    // replace the value of separator by coma
                    if ($this->multiple_value_separator != ',') {
                        if (is_array($product->meta_keywords)) {
                            foreach ($product->meta_keywords as &$meta_keyword) {
                                if (!empty($meta_keyword)) {
                                    $meta_keyword = str_replace($this->multiple_value_separator, ',', $meta_keyword);
                                }
                            }
                        }
                    }

                    // Convert comma into dot for all floating values
                    foreach (Product::$definition['fields'] as $key => $array) {
                        if ($array['type'] == Product::TYPE_FLOAT) {
                            $product->{$key} = str_replace(',', '.', $product->{$key});
                        }
                    }

                    $fieldError = $product->validateFields(UNFRIENDLY_ERROR, true);
                    $langFieldError = $product->validateFieldsLang(UNFRIENDLY_ERROR, true);
                    $res = false;
                    if ($fieldError === true && $langFieldError === true) {
                        // check quantity
                        if ($product->quantity == null) {
                            $product->quantity = 0;
                        }

                        // If no id_product or update failed
                        $product->force_id = (bool) $force_ids;
                        if (!$roomTypeExists) {
                            if (isset($product->date_add) && $product->date_add != '') {
                                $res = $product->add(false);
                            } else {
                                $res = $product->add();
                            }
                        } else {
                            $res = $product->save();
                        }

                        if ($product->getType() == Product::PTYPE_VIRTUAL) {
                            StockAvailable::setProductOutOfStock((int)$product->id, 1);
                        } else {
                            StockAvailable::setProductOutOfStock((int)$product->id, (int)$product->out_of_stock);
                        }
                    }

                    $shops = array();
                    $product_shop = explode($this->multiple_value_separator, $product->shop);
                    foreach ($product_shop as $shop) {
                        if (empty($shop)) {
                            continue;
                        }
                        $shop = trim($shop);
                        if (!empty($shop) && !is_numeric($shop)) {
                            $shop = Shop::getIdByName($shop);
                        }

                        if (in_array($shop, $shop_ids)) {
                            $shops[] = $shop;
                        } else {
                            $this->addProductWarning(Tools::safeOutput($info['name']), $product->id, $this->l('Shop is not valid'));
                        }
                    }
                    if (empty($shops)) {
                        $shops = Shop::getContextListShopID();
                    }
                    // If both failed, mysql error
                    if (!$res) {
                        $this->errors[] = sprintf(
                            Tools::displayError('%1$s (ID: %2$s) cannot be saved'),
                            (isset($info['name']) && !empty($info['name']))? Tools::safeOutput($info['name']) : 'No Name',
                            (isset($info['id']) && !empty($info['id']))? Tools::safeOutput($info['id']) : 'No ID'
                        );
                        if ($field_error !== true
                            || $lang_field_error !== true
                        ) {
                            $this->errors[] = ($field_error !== true ? $field_error : '').(isset($lang_field_error) && $lang_field_error !== true ? $lang_field_error : '').
                                Db::getInstance()->getMsgError();
                        }
                    } else {
                        // SpecificPrice (only the basic reduction feature is supported by the import)
                        if (!$shop_is_feature_active) {
                            $info['shop'] = 1;
                        } elseif (!isset($info['shop']) || empty($info['shop'])) {
                            $info['shop'] = implode($this->multiple_value_separator, Shop::getContextListShopID());
                        }

                        // Get shops for each attributes
                        $info['shop'] = explode($this->multiple_value_separator, $info['shop']);

                        $id_shop_list = array();
                        foreach ($info['shop'] as $shop) {
                            if (!empty($shop) && !is_numeric($shop)) {
                                $id_shop_list[] = (int)Shop::getIdByName($shop);
                            } elseif (!empty($shop)) {
                                $id_shop_list[] = $shop;
                            }
                        }

                        if ($advance_payment_info = $objAdvancePayment->getIdAdvPaymentByIdProduct($product->id)) {
                            $objHotelAdvancePayment = new HotelAdvancedPayment($advance_payment_info['id']);
                        } else {
                            $objHotelAdvancePayment = new HotelAdvancedPayment();
                        }

                        $objHotelAdvancePayment->id_product = $product->id;
                        $objHotelAdvancePayment->active = (int) false;
                        $objHotelAdvancePayment->payment_type = '';
                        $objHotelAdvancePayment->value = '';
                        $objHotelAdvancePayment->id_currency = '';
                        $objHotelAdvancePayment->tax_include = false;
                        $objHotelAdvancePayment->calculate_from = 0;
                        if (isset($product->advance_payment) && $product->advance_payment) {
                            $payment_type = 0;
                            if (isset($product->payment_type)) {
                                $payment_type = $product->payment_type;
                            }

                            $payment_value = 0;
                            if (isset($product->payment_value)) {
                                $payment_value = $product->payment_value;
                            }

                            $objHotelAdvancePayment->active = 1;
                            $objHotelAdvancePayment->payment_type = $payment_type;
                            $objHotelAdvancePayment->calculate_from = $payment_type ? 1 : 0;
                            $objHotelAdvancePayment->value = $payment_value;
                            $objHotelAdvancePayment->tax_include = isset($product->tax_include) ? $product->tax_included : 0;
                            if ($payment_type == 2) {
                                $objHotelAdvancePayment->id_currency = (int) Configuration::get('PS_CURRENCY_DEFAULT');
                            }
                        }

                        $objHotelAdvancePayment->save();
                        if (isset($product->id_additional_facilities) && $product->id_additional_facilities) {
                            $objRoomTypeDemand = new HotelRoomTypeDemand();
                            $objRoomTypeDemand->deleteRoomTypeDemands($product->id);
                            foreach ($product->id_additional_facilities as $idGlobalDemand) {
                                if (Validate::isLoadedObject(new HotelRoomTypeGlobalDemand((int) $idGlobalDemand))) {
                                    $objRoomTypeDemand = new HotelRoomTypeDemand();
                                    $objRoomTypeDemand->id_product = $product->id;
                                    $objRoomTypeDemand->id_global_demand = $idGlobalDemand;
                                    $objRoomTypeDemand->save();
                                }
                            }
                        }

                        //delete existing images if "delete_existing_images" is set to 1
                        if (isset($product->delete_existing_images)) {
                            if ((bool)$product->delete_existing_images) {
                                $product->deleteImages();
                            }
                        }

                        if (isset($product->image) && is_array($product->image) && count($product->image)) {
                            $product_has_images = (bool)Image::getImages($this->context->language->id, (int)$product->id);
                            foreach ($product->image as $key => $url) {
                                $url = trim($url);
                                $error = false;
                                if (!empty($url)) {
                                    $url = str_replace(' ', '%20', $url);

                                    $image = new Image();
                                    $image->id_product = (int)$product->id;
                                    $image->position = Image::getHighestPosition($product->id) + 1;
                                    $image->cover = (!$key && !$product_has_images) ? true : false;
                                    // file_exists doesn't work with HTTP protocol
                                    if (($field_error = $image->validateFields(UNFRIENDLY_ERROR, true)) === true &&
                                        ($lang_field_error = $image->validateFieldsLang(UNFRIENDLY_ERROR, true)) === true && $image->add()) {
                                        // associate image to selected shops
                                        $image->associateTo($shops);
                                        if (!AdminImportController::copyImg($product->id, $image->id, $url, 'products', !$regenerate)) {
                                            $image->delete();
                                            $this->warnings[] = sprintf(Tools::displayError('Error copying image: %s'), $url);
                                        }
                                    } else {
                                        $error = true;
                                    }
                                } else {
                                    $error = true;
                                }

                                if ($error) {
                                    $this->warnings[] = sprintf(Tools::displayError('Product #%1$d: the picture (%2$s) cannot be saved.'), $image->id_product, $url);
                                }
                            }
                        }


                        $product->checkDefaultAttributes();
                        if (!$product->cache_default_attribute) {
                            Product::updateDefaultAttribute($product->id);
                        }

                        // Features import
                        if (isset($product->id_features) && !empty($product->id_features)) {
                            foreach ($product->id_features as $id_feature) {
                                if ($feature = FeatureValue::getFeatureValues((int) $id_feature)) {
                                    Product::addFeatureProductImport(
                                        $product->id,
                                        $feature[0]['id_feature'],
                                        $feature[0]['id_feature_value']
                                    );
                                }
                            }
                        }
                        // clean feature positions to avoid conflict
                        Feature::cleanPositions();
                        if (Validate::isLoadedObject($product)) {
                            if ($id_hotel = $info['id_hotel']) {
                                if ($roomTypeInfo = $objRoomType->getRoomTypeInfoByIdProduct($product->id)) {
                                    $objRoomType = new HotelRoomType($roomTypeInfo['id']);
                                } else {
                                    $objRoomType = new HotelRoomType();
                                }

                                $objRoomType->min_los = 1;
                                $objRoomType->max_los = 0;
                                if (isset($info['min_len_stay']) && $info['min_len_stay'] > 1) {
                                    $objRoomType->min_los = $info['min_len_stay'];
                                }

                                if (isset($info['max_len_stay']) && $info['max_len_stay'] != 0) {
                                    if ($info['max_len_stay'] > $info['min_len_stay']) {
                                        $objRoomType->max_los = $info['max_len_stay'];
                                    } else {
                                        $this->warnings[] = Tools::displayError('Minimum length of stay cannot be large than Maximum length of stay ');
                                        $objRoomType->min_los = 1;
                                        $objRoomType->max_los = 0;
                                    }
                                }

                                if (isset($product->base_adults) && $product->base_adults) {
                                    $objRoomType->adults = $product->base_adults;
                                }

                                if (isset($product->base_children) && $product->base_children) {
                                    $objRoomType->children = $product->base_children;
                                }

                                if (isset($product->max_adults) && $product->max_adults > $product->base_adults) {
                                    $objRoomType->max_adults = $product->max_adults;
                                } else {
                                    $objRoomType->max_adults = $objRoomType->adults;
                                }

                                if (isset($product->max_children) && $product->max_children > $product->base_children) {
                                    $objRoomType->max_children = $product->max_children;
                                } else {
                                    $objRoomType->max_children = $objRoomType->children;
                                }

                                if (isset($products->max_room_occupancy)
                                    && $product->max_room_occupancy > ($objRoomType->adults + $objRoomType->children)
                                    && $product->max_room_occupancy <= ($objRoomType->max_adults + $objRoomType->max_children)
                                ) {
                                    $objRoomType->max_guests = $product->max_room_occupancy;
                                } else {
                                    $objRoomType->max_guests = $objRoomType->max_adults + $objRoomType->max_children;
                                }

                                $objRoomType->id_product = $product->id;
                                $objRoomType->id_hotel = $id_hotel;
                                $objRoomType->save();
                                $objHotel = new HotelBranchInformation($id_hotel);
                                $product->id_category_default = $objHotel->id_category;
                                $relatedCategories = array();
                                if (Validate::isLoadedObject($objCategory = new Category($objHotel->id_category))) {
                                    foreach($objCategory->getParentsCategories() as $category) {
                                        $relatedCategories[] = $category['id_category'];
                                    }
                                }

                                $product->updateCategories($relatedCategories);
                            }
                        }

                        if (isset($product->id_service_products) && count($product->id_service_products)) {
                            $objRoomTypeServiceProduct = new RoomTypeServiceProduct();
                            foreach ($product->id_service_products as $idServiceProduct) {
                                if ($idRoomTypeServiceProduct = $objRoomTypeServiceProduct->isRoomTypeLinkedWithProduct(
                                    $product->id,
                                    $idServiceProduct)
                                ){
                                    $objOlderRTserviceProduct = new RoomTypeServiceProduct($idRoomTypeServiceProduct);
                                    $objOlderRTserviceProduct->delete();
                                }

                                $objServiceProduct = new Product($idServiceProduct);
                                if (Product::SELLING_PREFERENCE_WITH_ROOM_TYPE == $objServiceProduct->selling_preference_type) {
                                    $objRoomTypeServiceProduct->addRoomProductLink(
                                        $objServiceProduct->id,
                                        $product->id,
                                        RoomTypeServiceProduct::WK_ELEMENT_TYPE_ROOM_TYPE
                                    );
                                }
                            }
                        }

                        // set advanced stock managment
                        if (isset($product->advanced_stock_management)) {
                            if ($product->advanced_stock_management != 1 && $product->advanced_stock_management != 0) {
                                $this->warnings[] = sprintf(Tools::displayError('Advanced stock management has incorrect value. Not set for product %1$s '), $product->name[$idLangDefault]);
                            } elseif (!Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT') && $product->advanced_stock_management == 1) {
                                $this->warnings[] = sprintf(Tools::displayError('Advanced stock management is not enabled, cannot enable on product %1$s '), $product->name[$idLangDefault]);
                            } elseif ($update_advanced_stock_management_value) {
                                $product->setAdvancedStockManagement($product->advanced_stock_management);
                            }
                            // automaticly disable depends on stock, if a_s_m set to disabled
                            if (StockAvailable::dependsOnStock($product->id) == 1 && $product->advanced_stock_management == 0) {
                                StockAvailable::setProductDependsOnStock($product->id, 0);
                            }
                        }

                        // stock available
                        if (isset($product->depends_on_stock)) {
                            if ($product->depends_on_stock != 0 && $product->depends_on_stock != 1) {
                                $this->warnings[] = sprintf(Tools::displayError('Incorrect value for "depends on stock" for product %1$s '), $product->name[$idLangDefault]);
                            } elseif ((!$product->advanced_stock_management || $product->advanced_stock_management == 0) && $product->depends_on_stock == 1) {
                                $this->warnings[] = sprintf(Tools::displayError('Advanced stock management not enabled, cannot set "depends on stock" for product %1$s '), $product->name[$idLangDefault]);
                            } else {
                                StockAvailable::setProductDependsOnStock($product->id, $product->depends_on_stock);
                            }

                            // This code allows us to set qty and disable depends on stock
                            if (isset($product->quantity) && (int)$product->quantity) {
                                // if depends on stock and quantity, add quantity to stock
                                if ($product->depends_on_stock == 1) {
                                    $stock_manager = StockManagerFactory::getManager();
                                    $price = str_replace(',', '.', $product->wholesale_price);
                                    if ($price == 0) {
                                        $price = 0.000001;
                                    }
                                    $price = round(floatval($price), 6);
                                    $warehouse = new Warehouse($product->warehouse);
                                    if ($stock_manager->addProduct((int)$product->id, 0, $warehouse, (int)$product->quantity, 1, $price, true)) {
                                        StockAvailable::synchronize((int)$product->id);
                                    }
                                } else {
                                    if ($shop_is_feature_active) {
                                        foreach ($shops as $shop) {
                                            StockAvailable::setQuantity((int)$product->id, 0, (int)$product->quantity, (int)$shop);
                                        }
                                    } else {
                                        StockAvailable::setQuantity((int)$product->id, 0, (int)$product->quantity, (int)$this->context->shop->id);
                                    }
                                }
                            }
                        } else {
                            // if not depends_on_stock set, use normal qty
                            if ($shop_is_feature_active) {
                                foreach ($shops as $shop) {
                                    StockAvailable::setQuantity((int)$product->id, 0, (int)$product->quantity, (int)$shop);
                                }
                            } else {
                                StockAvailable::setQuantity((int)$product->id, 0, (int)$product->quantity, (int)$this->context->shop->id);
                            }
                        }
                    }
                }
            }
        }

        $this->closeCsvFile($handle);
        Module::processDeferedFuncCall();
        Module::processDeferedClearCache();
        Tag::updateTagCount();
    }

    public function roomImport()
    {
        $this->receiveTab();
        $handle = $this->openCsvFile();
        AdminImportController::setLocale();
        $convert = Tools::getValue('convert');
        $force_ids = Tools::getValue('forceIDs');
        $objHotelRoomType = new HotelRoomType();
        $objHotelRoomInformation = new HotelRoomInformation();
        $statuses = array_column($objHotelRoomInformation->getAllRoomStatus(), 'id');
        for ($current_line = 0; $line = fgetcsv($handle, MAX_LINE_SIZE, $this->separator, escape: ""); $current_line++) {
            if ($convert) {
                $line = $this->utf8EncodeArray($line);
            }

            $info = AdminImportController::getMaskedRow($line);
            $objHotelRoomInfo = new HotelRoomInformation();
            if ($roomTypeInfo = $objHotelRoomType->getRoomTypeInfoByIdProduct($info['id_product'])) {
                $info['id_hotel'] = $roomTypeInfo['id_hotel'];
            }

            AdminImportController::arrayWalk($info, array('AdminImportController', 'fillInfo'), $objHotelRoomInfo);
            if (!isset($info['id_status'])
                || !$info['id_status']
                || !in_array($info['id_status'], $statuses)
            ) {
                $objHotelRoomInfo->id_status = HotelRoomInformation::STATUS_ACTIVE;
                $info['id_status'] = HotelRoomInformation::STATUS_ACTIVE;
            }

            $field_error = $objHotelRoomInfo->validateFields(UNFRIENDLY_ERROR, true);
            $lang_field_error = $objHotelRoomInfo->validateFieldsLang(UNFRIENDLY_ERROR, true);
            $res = false;
            if ($this->checkRequiredFields($info, 'room_num')) {
                if ($objHotelRoomInfo->id_status == HotelRoomInformation::STATUS_TEMPORARY_INACTIVE
                    && (!isset($objHotelRoomInfo->dates) || !$objHotelRoomInfo->dates)
                ) {
                    $this->errors[] = sprintf(
                        Tools::displayError('%1$s (ID: %2$s) cannot be saved due to missing disable dates.'),
                        (isset($info['room_num']) && !empty($info['room_num']))? Tools::safeOutput($info['room_num']) : 'No Name',
                        (isset($info['id']) && !empty($info['id']))? Tools::safeOutput($info['id']) : 'No ID'
                    );
                } else if (Validate::isLoadedObject($objProduct = new Product((int) $info['id_product']))
                    && Product::isBookingProduct($objProduct->id)
                ) {
                    if ($field_error === true && $lang_field_error === true) {
                        if ($res = $objHotelRoomInfo->add()) {
                            if ($idRoom = $objHotelRoomInfo->id) {
                                if ($objHotelRoomInfo->id_status == HotelRoomInformation::STATUS_TEMPORARY_INACTIVE) {
                                    $objHotelRoomDisableDates = new HotelRoomDisableDates();
                                    if (isset($objHotelRoomInfo->dates)
                                        && $objHotelRoomInfo->dates
                                    ) {
                                        foreach ($objHotelRoomInfo->dates as $disableDate) {
                                            $datesData = explode(':', $disableDate);
                                            $reason = '';
                                            if(isset($datesData[2])) {
                                                $reason = $datesData[2];
                                            }

                                            if (isset($datesData[0]) && isset($datesData[1])
                                                && strtotime($datesData[1]) > strtotime($datesData[0])
                                            ) {
                                                $objHotelRoomDisableDates = new HotelRoomDisableDates();
                                                $objHotelRoomDisableDates->id_room_type = $objHotelRoomInfo->id_product;
                                                $objHotelRoomDisableDates->id_room = $idRoom;
                                                $objHotelRoomDisableDates->date_from = date('Y-m-d', strtotime($datesData[0]));
                                                $objHotelRoomDisableDates->date_to = date('Y-m-d', strtotime($datesData[1]));
                                                $objHotelRoomDisableDates->reason = $reason;
                                                $objHotelRoomDisableDates->add();
                                            }
                                        }
                                    } else {
                                        $this->warnings[] = Tools::displayError('Please set date from and date to incase of temporary inactive status.');
                                    }
                                }
                            }
                        }
                    }

                    if (!$res) {
                        $this->errors[] = sprintf(
                            Tools::displayError('%1$s (ID: %2$s) cannot be saved'),
                            (isset($info['room_num']) && !empty($info['room_num']))? Tools::safeOutput($info['room_num']) : 'No Name',
                            (isset($info['id']) && !empty($info['id']))? Tools::safeOutput($info['id']) : 'No ID'
                        );
                        if ($field_error !== true
                            || $lang_field_error !== true
                        ) {
                            $this->errors[] = ($field_error !== true ? $field_error : '').(isset($lang_field_error) && $lang_field_error !== true ? $lang_field_error : '').
                                Db::getInstance()->getMsgError();
                        }
                    }
                } else {
                    $this->errors[] = sprintf(
                        Tools::displayError('Invalid ID Room Type for %1$s (ID: %2$s).'),
                        (isset($info['room_num']) && !empty($info['room_num']))? Tools::safeOutput($info['room_num']) : 'No Name',
                        (isset($info['id']) && !empty($info['id']))? Tools::safeOutput($info['id']) : 'No ID'
                    );
                }
            }
        }

        $this->closeCsvFile($handle);
    }

    public function serviceProductImport()
    {
        $this->receiveTab();
        $handle = $this->openCsvFile();
        $idLangDefault = (int) Configuration::get('PS_LANG_DEFAULT');
        $id_lang = Language::getIdByIso(Tools::getValue('iso_lang'));
        if (!Validate::isUnsignedId($id_lang)) {
            $id_lang = $idLangDefault;
        }

        AdminImportController::setLocale();
        $shop_ids = Shop::getCompleteListOfShopsID();

        $convert = Tools::getValue('convert');
        $force_ids = Tools::getValue('forceIDs');
        $regenerate = Tools::getValue('regenerate');
        $shop_is_feature_active = Shop::isFeatureActive();
        Module::setBatchMode(true);
        for ($current_line = 0; $line = fgetcsv($handle, MAX_LINE_SIZE, $this->separator, escape: ""); $current_line++) {
            if ($convert) {
                $line = $this->utf8EncodeArray($line);
            }

            $info = AdminImportController::getMaskedRow($line);
            if ($this->checkRequiredFields($info)) {
                $serviceProductExists = false;
                if (isset($info['id']) && (int) $info['id'] && Product::existsInDatabase((int) $info['id'], 'product')) {
                    $product = new Product((int) $info['id']);
                    if (!$product->booking_product || $force_ids) {
                        $serviceProductExists = true;
                    }
                }

                if ($serviceProductExists) {
                    $product = new Product((int) $info['id']);
                } else {
                    $product = new Product();
                }

                $update_advanced_stock_management_value = false;
                if ($serviceProductExists) {
                    $product->loadStockData();
                    $update_advanced_stock_management_value = true;
                    $category_data = Product::getProductCategories((int) $product->id);
                    if (is_array($category_data)) {
                        foreach ($category_data as $tmp) {
                            if (!isset($product->category) || !$product->category || is_array($product->category)) {
                                $product->category[] = $tmp;
                            }
                        }
                    }
                }

                AdminImportController::setEntityDefaultValues($product);
                AdminImportController::arrayWalk($info, array('AdminImportController', 'fillInfo'), $product);
                $product->booking_product = false;
                $product->visibility = 'none';
                $product->is_virtual = 1;
                if (!$shop_is_feature_active) {
                    $product->shop = (int)Configuration::get('PS_SHOP_DEFAULT');
                } elseif (!isset($product->shop) || empty($product->shop)) {
                    $product->shop = implode($this->multiple_value_separator, Shop::getContextListShopID());
                }

                if (!$shop_is_feature_active) {
                    $product->id_shop_default = (int)Configuration::get('PS_SHOP_DEFAULT');
                } else {
                    $product->id_shop_default = (int)Context::getContext()->shop->id;
                }

                if ((int)$product->id_tax_rules_group != 0) {
                    if (Validate::isLoadedObject(new TaxRulesGroup($product->id_tax_rules_group))) {
                        $address = $this->context->shop->getAddress();
                        $tax_manager = TaxManagerFactory::getManager($address, $product->id_tax_rules_group);
                        $product_tax_calculator = $tax_manager->getTaxCalculator();
                        $product->tax_rate = $product_tax_calculator->getTotalRate();
                    } else {
                        $this->addProductWarning(
                            'id_tax_rules_group',
                            $product->id_tax_rules_group,
                            Tools::displayError('Invalid tax rule group ID. You first need to create a group with this ID.')
                        );
                    }
                }

                if (isset($product->price_tex) && !isset($product->price_tin)) {
                    $product->price = $product->price_tex;
                } elseif (isset($product->price_tin) && !isset($product->price_tex)) {
                    $product->price = $product->price_tin;
                    // If a tax is already included in price, withdraw it from price
                    if ($product->tax_rate) {
                        $product->price = (float)number_format($product->price / (1 + $product->tax_rate / 100), 6, '.', '');
                    }
                } elseif (isset($product->price_tin) && isset($product->price_tex)) {
                    $product->price = $product->price_tex;
                }

                if (!Configuration::get('PS_USE_ECOTAX')) {
                    $product->ecotax = 0;
                }

                if (isset($product->category) && is_array($product->category) && count($product->category)) {
                    $product->id_category = array(); // Reset default values array
                    foreach ($product->category as $value) {
                        if (is_numeric($value)) {
                            if (Category::categoryExists((int)$value)) {
                                $product->id_category[] = (int)$value;
                            } else {
                                $category_to_create = new Category();
                                $category_to_create->id = (int)$value;
                                $category_to_create->name = AdminImportController::createMultiLangField($value);
                                $category_to_create->active = 1;
                                $category_to_create->id_parent = Configuration::get('PS_HOME_CATEGORY'); // Default parent is home for unknown category to create
                                $category_link_rewrite = Tools::link_rewrite($category_to_create->name[$idLangDefault]);
                                $category_to_create->link_rewrite = AdminImportController::createMultiLangField($category_link_rewrite);
                                if (($field_error = $category_to_create->validateFields(UNFRIENDLY_ERROR, true)) === true &&
                                    ($lang_field_error = $category_to_create->validateFieldsLang(UNFRIENDLY_ERROR, true)) === true && $category_to_create->add()) {
                                    $product->id_category[] = (int)$category_to_create->id;
                                } else {
                                    $this->errors[] = sprintf(
                                        Tools::displayError('%1$s (ID: %2$s) cannot be saved'),
                                        $category_to_create->name[$idLangDefault],
                                        (isset($category_to_create->id) && !empty($category_to_create->id))? $category_to_create->id : 'null'
                                    );
                                    $this->errors[] = ($field_error !== true ? $field_error : '').(isset($lang_field_error) && $lang_field_error !== true ? $lang_field_error : '').
                                        Db::getInstance()->getMsgError();
                                }
                            }
                        } elseif (is_string($value) && !empty($value)) {
                            $category = Category::searchByPath($idLangDefault, trim($value), $this, 'productImportCreateCat');
                            if ($category['id_category']) {
                                $product->id_category[] = (int)$category['id_category'];
                            } else {
                                $this->errors[] = sprintf(Tools::displayError('%1$s cannot be saved'), trim($value));
                            }
                        }
                    }
                    $product->id_category = array_values(array_unique($product->id_category));
                }

                // Will update default category if there is none set here. Home if no category at all.
                if (!isset($product->id_category_default) || !$product->id_category_default) {
                    // this if will avoid ereasing default category if category column is not present in the CSV file (or ignored)
                    if (isset($product->id_category[0])) {
                        $product->id_category_default = (int) $product->id_category[0];
                    } else {
                        $defaultProductShop = new Shop($product->id_shop_default);
                        $product->id_category_default = Category::getRootCategory(null, Validate::isLoadedObject($defaultProductShop)?$defaultProductShop:null)->id;
                    }
                }

                $product->link_rewrite[(int)$id_lang] = Tools::link_rewrite($product->name[$id_lang]);

                // Convert comma into dot for all floating values
                foreach (Product::$definition['fields'] as $key => $array) {
                    if ($array['type'] == Product::TYPE_FLOAT) {
                        $product->{$key} = str_replace(',', '.', $product->{$key});
                    }
                }

                // Indexation is already 0 if it's a new product, but not if it's an update
                $product->indexed = 0;
                $field_error = $product->validateFields(UNFRIENDLY_ERROR, true);
                $lang_field_error = $product->validateFieldsLang(UNFRIENDLY_ERROR, true);
                $res = false;
                if ($field_error === true && $lang_field_error === true) {
                    // check quantity
                    if ($product->quantity == null) {
                        $product->quantity = 0;
                    }

                    // If no id_product or update failed
                    $product->force_id = (bool) $force_ids;
                    if ($product->getType() == Product::PTYPE_VIRTUAL) {
                        StockAvailable::setProductOutOfStock((int)$product->id, 1);
                    } else {
                        StockAvailable::setProductOutOfStock((int)$product->id, (int)$product->out_of_stock);
                    }

                    if (!$product->auto_add_to_cart){
                        $product->auto_add_to_cart = (int) false;
                        $product->price_addition_type = Product::PRICE_ADDITION_TYPE_WITH_ROOM;
                    } else if (!$product->price_addition_type) {
                        $product->price_addition_type = Product::PRICE_ADDITION_TYPE_WITH_ROOM;
                    }

                    $product->selling_preference_type = Product::SELLING_PREFERENCE_WITH_ROOM_TYPE;
                    if (!$serviceProductExists) {
                        if (isset($product->date_add) && $product->date_add != '') {
                            $res = $product->add(false);
                        } else {
                            $res = $product->add();
                        }
                    } else {
                        $res = $product->save();
                    }

                    $shops = array();
                    $product_shop = explode($this->multiple_value_separator, $product->shop);
                    foreach ($product_shop as $shop) {
                        if (empty($shop)) {
                            continue;
                        }
                        $shop = trim($shop);
                        if (!empty($shop) && !is_numeric($shop)) {
                            $shop = Shop::getIdByName($shop);
                        }

                        if (in_array($shop, $shop_ids)) {
                            $shops[] = $shop;
                        } else {
                            $this->addProductWarning(Tools::safeOutput($info['name']), $product->id, $this->l('Shop is not valid'));
                        }
                    }
                    if (empty($shops)) {
                        $shops = Shop::getContextListShopID();
                    }
                }

                // If both failed, mysql error
                if (!$res) {
                    $this->errors[] = sprintf(
                        Tools::displayError('%1$s (ID: %2$s) cannot be saved'),
                        (isset($info['name']) && !empty($info['name']))? Tools::safeOutput($info['name']) : 'No Name',
                        (isset($info['id']) && !empty($info['id']))? Tools::safeOutput($info['id']) : 'No ID'
                    );
                    if ($field_error !== true
                        || $lang_field_error !== true
                    ) {
                        $this->errors[] = ($field_error !== true ? $field_error : '').(isset($lang_field_error) && $lang_field_error !== true ? $lang_field_error : '').
                            Db::getInstance()->getMsgError();
                    }
                } else {
                    if (!$shop_is_feature_active) {
                        $info['shop'] = 1;
                    } elseif (!isset($info['shop']) || empty($info['shop'])) {
                        $info['shop'] = implode($this->multiple_value_separator, Shop::getContextListShopID());
                    }

                    $info['shop'] = explode($this->multiple_value_separator, $info['shop']);

                    RoomTypeServiceProduct::deleteRoomProductLink($product->id);
                    if (Product::SELLING_PREFERENCE_WITH_ROOM_TYPE == $product->selling_preference_type) {
                        $objRoomTypeServiceProduct = new RoomTypeServiceProduct();
                        if (isset($info['id_room_types']) && $info['id_room_types']) {
                            $objRoomTypeServiceProduct->addRoomProductLink(
                                $product->id,
                                $product->id_room_types,
                                RoomTypeServiceProduct::WK_ELEMENT_TYPE_ROOM_TYPE
                            );
                        }
                    }

                    if (Configuration::get('PS_FORCE_ASM_NEW_PRODUCT') && Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT') && $product->getType() != Product::PTYPE_VIRTUAL) {
                        $product->advanced_stock_management = 1;
                        $product->save();
                        $id_shops = Shop::getContextListShopID();
                        foreach ($id_shops as $id_shop) {
                            StockAvailable::setProductDependsOnStock($product->id, true, (int)$id_shop, 0);
                        }
                    }

                    StockAvailable::setQuantity($product->id, 0, 999999999);
                    if (Configuration::get('PS_DEFAULT_WAREHOUSE_NEW_PRODUCT') != 0 && Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT')) {
                        $warehouse_location_entity = new WarehouseProductLocation();
                        $warehouse_location_entity->id_product = $product->id;
                        $warehouse_location_entity->id_product_attribute = 0;
                        $warehouse_location_entity->id_warehouse = Configuration::get('PS_DEFAULT_WAREHOUSE_NEW_PRODUCT');
                        $warehouse_location_entity->location = pSQL('');
                        $warehouse_location_entity->save();
                    }
                    // Apply groups reductions
                    $product->setGroupReduction();
                    //delete existing images if "delete_existing_images" is set to 1
                    if (isset($product->delete_existing_images)) {
                        if ((bool)$product->delete_existing_images) {
                            $product->deleteImages();
                        }
                    }

                    if (isset($product->image) && is_array($product->image) && count($product->image)) {
                        $product_has_images = (bool)Image::getImages($this->context->language->id, (int)$product->id);
                        foreach ($product->image as $key => $url) {
                            $url = trim($url);
                            $error = false;
                            if (!empty($url)) {
                                $url = str_replace(' ', '%20', $url);
                                $image = new Image();
                                $image->id_product = (int)$product->id;
                                $image->position = Image::getHighestPosition($product->id) + 1;
                                $image->cover = (!$key && !$product_has_images) ? true : false;
                                // file_exists doesn't work with HTTP protocol
                                if (($field_error = $image->validateFields(UNFRIENDLY_ERROR, true)) === true &&
                                    ($lang_field_error = $image->validateFieldsLang(UNFRIENDLY_ERROR, true)) === true && $image->add()) {
                                    // associate image to selected shops
                                    $image->associateTo($shops);
                                    if (!AdminImportController::copyImg($product->id, $image->id, $url, 'products', !$regenerate)) {
                                        $image->delete();
                                        $this->warnings[] = sprintf(Tools::displayError('Error copying image: %s'), $url);
                                    }
                                } else {
                                    $error = true;
                                }
                            } else {
                                $error = true;
                            }

                            if ($error) {
                                $this->warnings[] = sprintf(Tools::displayError('Product #%1$d: the picture (%2$s) cannot be saved.'), $image->id_product, $url);
                            }
                        }
                    }

                    if (isset($product->id_category) && is_array($product->id_category)) {
                        $product->updateCategories(array_map('intval', $product->id_category));
                    }

                    $product->checkDefaultAttributes();
                    if (!$product->cache_default_attribute) {
                        Product::updateDefaultAttribute($product->id);
                    }

                    // clean feature positions to avoid conflict
                    Feature::cleanPositions();
                    // set advanced stock managment
                    if (isset($product->advanced_stock_management)) {
                        if ($product->advanced_stock_management != 1 && $product->advanced_stock_management != 0) {
                            $this->warnings[] = sprintf(Tools::displayError('Advanced stock management has incorrect value. Not set for product %1$s '), $product->name[$idLangDefault]);
                        } elseif (!Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT') && $product->advanced_stock_management == 1) {
                            $this->warnings[] = sprintf(Tools::displayError('Advanced stock management is not enabled, cannot enable on product %1$s '), $product->name[$idLangDefault]);
                        } elseif ($update_advanced_stock_management_value) {
                            $product->setAdvancedStockManagement($product->advanced_stock_management);
                        }
                        // automaticly disable depends on stock, if a_s_m set to disabled
                        if (StockAvailable::dependsOnStock($product->id) == 1 && $product->advanced_stock_management == 0) {
                            StockAvailable::setProductDependsOnStock($product->id, 0);
                        }
                    }
                }
            }
        }

        $this->closeCsvFile($handle);
        Module::processDeferedFuncCall();
        Module::processDeferedClearCache();
        Tag::updateTagCount();
    }

    public function bookingsImport()
    {
        $this->receiveTab();
        $handle = $this->openCsvFile();
        AdminImportController::setLocale();
        $convert = Tools::getValue('convert');
        $objHotelRoomType = new HotelRoomType();
        $ordersRow = array();
        $hotelRoomTypeInfo = array();
        $orderInfo = array();
        for ($current_line = 0; $line = fgetcsv($handle, MAX_LINE_SIZE, $this->separator, escape: ""); $current_line++) {
            if ($convert) {
                $line = $this->utf8EncodeArray($line);
            }

            $singleRow = (object) array();
            $info = AdminImportController::getMaskedRow($line);
            AdminImportController::arrayWalk($info, array('AdminImportController', 'fillInfo'), $singleRow);
            $has_required_fields = $this->checkRequiredFields($info, 'id_order');
            if ($has_required_fields) {
                $idHotel = 0;
                $objCustomer = new Customer($info['id_customer']);
                $objProduct = new Product($info['id_product']);
                if (Validate::isLoadedObject($objCustomer)) {
                    if (Validate::isLoadedObject($objProduct) || $objProduct->booking_product) {
                        if (!isset($hotelRoomTypeInfo[$info['id_product']])) {
                            if ($roomInfo = $objHotelRoomType->getRoomTypeInfoByIdProduct($singleRow->id_product)) {
                                $singleRow->id_hotel = $roomInfo['id_hotel'];
                                $idHotel = $roomInfo['id_hotel'];
                                $singleRow->adults = $roomInfo['adults'];
                                $singleRow->children = $roomInfo['children'];
                                $hotelRoomTypeInfo[$info['id_product']] = $roomInfo;
                            }
                        } else {
                            $roomInfo = $hotelRoomTypeInfo[$info['id_product']];
                            $singleRow->id_hotel = $roomInfo['id_hotel'];
                            $idHotel = $roomInfo['id_hotel'];
                            $singleRow->adults = $roomInfo['adults'];
                            $singleRow->children = $roomInfo['children'];
                        }

                        $ordersRow[$info['id_order']][$singleRow->id_hotel][] = (array) $singleRow;
                        if (!isset($orderInfo[$info['id_order']])) {
                            $orderInfo[$info['id_order']]['id_customer'] = $singleRow->id_customer;
                            if (isset($singleRow->id_order_status)) {
                                $orderInfo[$info['id_order']]['id_order_status'] = $singleRow->id_order_status;
                            } else {
                                $orderInfo[$info['id_order']]['id_order_status'] = Configuration::get('PS_OS_AWAITING_REMOTE_PAYMENT');
                            }

                            if (isset($singleRow->id_currency)) {
                                $orderInfo[$info['id_order']]['id_currency'] = $singleRow->id_currency   ;
                            } else {
                                $orderInfo[$info['id_order']]['id_currency'] = Configuration::get('PS_CURRENCY_DEFAULT');
                            }
                        }
                    } else {
                        $this->warnings[] = $this->l('Invalid room type Id: ').$info['id_product'];
                    }
                }
            }
        }

        foreach ($ordersRow as $orderRefKey => $orderRow) {
            $idCustomer = $orderInfo[$orderRefKey]['id_customer'];
            if (Validate::isLoadedObject($objCustomer = new Customer((int) $idCustomer))) {
                $idGuest = Guest::getFromCustomer($objCustomer->id);
                $id_order_state = (int) $orderInfo[$orderRefKey]['id_order_status'];
                $objCartBooking = new HotelCartBookingData();
                $this->context->cart = new Cart();
                $this->context->customer = $objCustomer;
                $this->context->cart->id_customer = $idCustomer;
                $this->context->cart->id_guest = $idGuest;
                if (Validate::isLoadedObject($this->context->cart) && $this->context->cart->OrderExists()) {
                    continue;
                }

                if (isset($orderRow['id_currency']) && Validate::isLoadedObject($objCurrency = new Currency($orderRow['id_currency']))) {
                    $this->context->cart->id_currency = $objCurrency->id;
                }

                if (!$this->context->cart->secure_key) {
                    $this->context->cart->secure_key = $this->context->customer->secure_key;
                }

                if (!$this->context->cart->id_shop) {
                    $this->context->cart->id_shop = Configuration::get('PS_SHOP_DEFAULT');
                }

                if (!$this->context->cart->id_lang) {
                    $this->context->cart->id_lang = Configuration::get('PS_LANG_DEFAULT');
                }

                if (!$this->context->cart->id_currency) {
                    $this->context->cart->id_currency = Configuration::get('PS_CURRENCY_DEFAULT');
                }

                $objHotelBookingDetails = new HotelBookingDetail();
                $amount = 0;
                $dueAmount = 0;
                $this->context->cart->setNoMultishipping();
                $this->context->cart->save();
                $occupancy = $featurePrices = array();
                foreach ($orderRow as $idHotel => $orderByHotel) {
                    foreach($orderByHotel as $key => $orderProduct) {
                        $dateFrom = $orderProduct['duration_dates'][0];
                        $dateTo  = $orderProduct['duration_dates'][1];
                        $bookingParams = array(
                            'date_from' => $dateFrom,
                            'date_to' => $dateTo,
                            'hotel_id' => $idHotel,
                            'id_room_type' => $orderProduct['id_product'],
                            'only_search_data' => 1,
                        );
                        $dueAmount += isset($orderProduct['due_amount']) ? $orderProduct['due_amount'] : 0;
                        $data = $objHotelBookingDetails->getBookingData($bookingParams);
                        if ($data['stats']['num_avail'] >= $orderProduct['num_rooms']) {
                            for ($i = 0; $i < $orderProduct['num_rooms']; $i++) {
                                $occupancy[$i]['adults'] = $orderProduct['adults'];
                                $occupancy[$i]['children'] = 0;
                            }

                            $serviceProducts = array();
                            $globalDemands = array();
                            if (isset($orderProduct['id_service_products']) && count($orderProduct['id_service_products'])) {
                                foreach ($orderProduct['id_service_products'] as $serviceProdKey =>  $serviceProd) {
                                    $serviceProd = explode(':', $serviceProd);
                                    if (Validate::isLoadedObject($objServiceProduct = new Product((int) $serviceProd[0]))) {
                                        $serviceProducts[$serviceProdKey]['id_product'] = $serviceProd[0];
                                        $serviceProducts[$serviceProdKey]['quantity'] = 1;
                                        if ($objServiceProduct->allow_multiple_quantity
                                            && isset($serviceProd[1])
                                            && $serviceProd[1]
                                        ) {
                                            if ($serviceProd[1] < $objServiceProduct->max_quantity) {
                                                $serviceProducts[$serviceProdKey]['quantity'] = $serviceProd[1];
                                            } else {
                                                $serviceProducts[$serviceProdKey]['quantity'] = $objServiceProduct->max_quantity;
                                            }
                                        }
                                    }
                                }
                            }

                            if (isset($orderProduct['id_additional_facilities']) && count($orderProduct['id_additional_facilities'])) {
                                foreach ($orderProduct['id_additional_facilities'] as $globalDemandKey =>  $globalDemand) {
                                    $globalDemand = explode(':', $globalDemand);
                                    $objGlobalDemand = new HotelRoomTypeGlobalDemand($globalDemand[0], $this->context->language->id);
                                    if (Validate::isLoadedObject($objGlobalDemand)) {
                                        $objAdvOption = new HotelRoomTypeGlobalDemandAdvanceOption();
                                        // incase no option is provided or if the provided id is not valid or the id is not connected to the diffrent option, we will set the first option as default.
                                        if ((!isset($globalDemand[1])
                                            || !Validate::isLoadedObject($objAdvOption = new HotelRoomTypeGlobalDemandAdvanceOption($globalDemand[1])
                                            || $objAdvOption->id_global_demand != $globalDemand[0]))
                                            && ($advOptions = $objAdvOption->getGlobalDemandAdvanceOptions($objGlobalDemand->id))
                                        ) {
                                            $globalDemand[1] = $advOptions[0]['id'];
                                        }

                                        $globalDemands[$globalDemandKey]['id_global_demand'] = $globalDemand[0];
                                        $globalDemands[$globalDemandKey]['id_option'] = isset($globalDemand[1]) ? $globalDemand[1] : 0;
                                    }
                                }
                            }

                            $globalDemands = json_encode($globalDemands);
                            $objCartBooking->updateCartBooking(
                                $orderProduct['id_product'],
                                $occupancy,
                                'up',
                                $idHotel,
                                0,
                                date('Y-m-d', strtotime($dateFrom)),
                                date('Y-m-d', strtotime($dateTo)),
                                $globalDemands,
                                $serviceProducts,
                                $this->context->cart->id,
                                $this->context->cart->id_guest
                            );
                            $objHotelCartBookingData = new HotelCartBookingData();
                            if ($bookingCartData = $objHotelCartBookingData->getCustomerIdRoomsByIdCartIdProduct(
                                $this->context->cart->id,
                                $orderProduct['id_product'],
                                date('Y-m-d', strtotime($dateFrom)),
                                date('Y-m-d', strtotime($dateTo))
                            )) {
                                $productPriceTI = Product::getPriceStatic((int) $orderProduct['id_product'], true);
                                $productPriceTE = Product::getPriceStatic((int) $orderProduct['id_product'], false);
                                if ($productPriceTE) {
                                    $taxRate = (($productPriceTI-$productPriceTE)/$productPriceTE)*100;
                                } else {
                                    $taxRate = 0;
                                }

                                $taxRateM =  $taxRate/100;
                                if (isset($orderProduct['amount'])) {
                                    $orderProduct['amount'] = (float)$orderProduct['amount']/(1+$taxRateM);
                                    foreach ($bookingCartData as $bookingCart) {
                                        $featurePrices[] = HotelRoomTypeFeaturePricing::createRoomTypeFeaturePrice(
                                            array(
                                                'name' => 'csvprice',
                                                'id_product' => (int) $orderProduct['id_product'],
                                                'id_cart' => (int) $this->context->cart->id,
                                                'id_guest' => (int) $this->context->cart->id_guest,
                                                'is_special_days_exists' => 0,
                                                'id_room' => $bookingCart['id_room'],
                                                'impact_way' => HotelRoomTypeFeaturePricing::IMPACT_WAY_FIX_PRICE,
                                                'impact_type' => HotelRoomTypeFeaturePricing::IMPACT_TYPE_FIXED_PRICE,
                                                'impact_value' => $orderProduct['amount'],
                                                'restrictions' => array(
                                                    array(
                                                        'date_from' => date('Y-m-d', strtotime($dateFrom)),
                                                        'date_to' => date('Y-m-d', strtotime($dateTo)),
                                                    )
                                                )
                                            )
                                        );
                                    }
                                }
                            }
                        }
                    }
                }

                $amount = $this->context->cart->getOrderTotal(true);
                $amount -= $dueAmount;
                if ($this->context->cart->getProducts()) {
                    $objPayment = new BoOrder();
                    if (!$objPayment->validateOrder(
                        (int) $this->context->cart->id,
                        $id_order_state,
                        $amount,
                        $this->l('CSV Import -- Admin'),
                        null,
                        array(),
                        (int) $orderInfo[$orderRefKey]['id_currency'],
                        false,
                        $objCustomer->secure_key,
                        null,
                        false
                    )) {
                        $this->errors[] = $this->l('Failed to create booking for reference').' '.$orderRefKey;
                    }

                    foreach ($featurePrices as $idPrice) {
                        $objRoomFeaturePrice = new HotelRoomTypeFeaturePricing($idPrice);
                        $objRoomFeaturePrice->delete();
                    }
                }
            } else {
                $this->warnings[] = sprintf(
                    Tools::displayError('No customer found for Customer ID: %1$s'),
                    (isset($idCustomer) && !empty($idCustomer))? $idCustomer : 'null'
                );
            }
        }

        $this->closeCsvFile($handle);
    }

    public function productImportCreateCat($default_language_id, $category_name, $id_parent_category = null)
    {
        $category_to_create = new Category();
        $shop_is_feature_active = Shop::isFeatureActive();
        if (!$shop_is_feature_active) {
            $category_to_create->id_shop_default = 1;
        } else {
            $category_to_create->id_shop_default = (int)Context::getContext()->shop->id;
        }
        $category_to_create->name = AdminImportController::createMultiLangField(trim($category_name));
        $category_to_create->active = 1;
        $category_to_create->id_parent = (int)$id_parent_category ? (int)$id_parent_category : (int)Configuration::get('PS_HOME_CATEGORY'); // Default parent is home for unknown category to create
        $category_link_rewrite = Tools::link_rewrite($category_to_create->name[$default_language_id]);
        $category_to_create->link_rewrite = AdminImportController::createMultiLangField($category_link_rewrite);

        if (($field_error = $category_to_create->validateFields(UNFRIENDLY_ERROR, true)) === true &&
            ($lang_field_error = $category_to_create->validateFieldsLang(UNFRIENDLY_ERROR, true)) === true && $category_to_create->add()) {
            /**
             * @see AdminImportController::productImport() @ Line 1480
             * @TODO Refactor if statement
             */
            // $product->id_category[] = (int)$category_to_create->id;
        } else {
            $this->errors[] = sprintf(
                Tools::displayError('%1$s (ID: %2$s) cannot be saved'),
                $category_to_create->name[$default_language_id],
                (isset($category_to_create->id) && !empty($category_to_create->id))? $category_to_create->id : 'null'
            );
            $this->errors[] = ($field_error !== true ? $field_error : '').(isset($lang_field_error) && $lang_field_error !== true ? $lang_field_error : '').
                Db::getInstance()->getMsgError();
        }
    }

    public function customerImport()
    {
        $this->receiveTab();
        $handle = $this->openCsvFile();
        $default_language_id = (int)Configuration::get('PS_LANG_DEFAULT');
        $id_lang = Language::getIdByIso(Tools::getValue('iso_lang'));
        if (!Validate::isUnsignedId($id_lang)) {
            $id_lang = $default_language_id;
        }
        AdminImportController::setLocale();

        $convert = Tools::getValue('convert');
        $force_ids = Tools::getValue('forceIDs');
        $phoneRequired = false;
        if (Configuration::get('PS_ONE_PHONE_AT_LEAST')) {
            $phoneRequired = true;
        }

        for ($current_line = 0; $line = fgetcsv($handle, MAX_LINE_SIZE, $this->separator, escape: ""); $current_line++) {
            if ($convert) {
                $line = $this->utf8EncodeArray($line);
            }

            $info = AdminImportController::getMaskedRow($line);
            $has_required_fields = $this->checkRequiredFields($info, 'firstname');
            if ($phoneRequired && !Validate::isPhoneNumber(trim($info['phone']))) {
                $this->errors[] = sprintf(
                    Tools::displayError('Invalid phone for %1$s (ID: %2$s).'),
                    $info['email'],
                    (isset($info['id']) && !empty($info['id']))? $info['id'] : 'null'
                );
            } else if ($has_required_fields) {
                AdminImportController::setDefaultValues($info);
                $customerExists = false;
                if (isset($info['id'])
                    && (int) $info['id']
                    && Customer::customerIdExistsStatic((int) $info['id'])
                ){
                    $customerExists = true;
                }

                if ($customerExists) {
                    $customer = new Customer((int)$info['id']);
                    if ($force_ids) {
                        $customer->force_id = $info['id'];
                    }
                } else {
                    $customer = new Customer();
                }

                $customer_exist = false;

                if ($customerExists && Validate::isLoadedObject($customer)) {
                    $current_id_customer = (int) $customer->id;
                    $current_id_shop = (int) $customer->id_shop;
                    $current_id_shop_group = (int) $customer->id_shop_group;
                    $customer_exist = true;
                    $customer_groups = $customer->getGroups();
                    $addresses = $customer->getAddresses((int)Configuration::get('PS_LANG_DEFAULT'));
                }

                // Group Importation
                if (isset($info['group']) && !empty($info['group'])) {
                    foreach (explode($this->multiple_value_separator, $info['group']) as $key => $group) {
                        $group = trim($group);
                        if (empty($group)) {
                            continue;
                        }
                        $id_group = false;
                        if (is_numeric($group) && $group) {
                            $my_group = new Group((int)$group);
                            if (Validate::isLoadedObject($my_group)) {
                                $customer_groups[] = (int)$group;
                            }

                            continue;
                        }

                        $my_group = Group::searchByName($group);
                        if (isset($my_group['id_group']) && $my_group['id_group']) {
                            $id_group = (int)$my_group['id_group'];
                        }

                        if (!$id_group) {
                            $my_group = new Group();
                            $my_group->name = array($id_lang => $group);
                            if ($id_lang != $default_language_id) {
                                $my_group->name = $my_group->name + array($default_language_id => $group);
                            }

                            $my_group->price_display_method = 1;
                            $my_group->add();
                            if (Validate::isLoadedObject($my_group)) {
                                $id_group = (int)$my_group->id;
                            }
                        }

                        if ($id_group) {
                            $customer_groups[] = (int)$id_group;
                        }
                    }
                } elseif (empty($info['group']) && isset($customer->id) && $customer->id) {
                    $customer_groups = array(0 => Configuration::get('PS_CUSTOMER_GROUP'));
                }

                AdminImportController::arrayWalk($info, array('AdminImportController', 'fillInfo'), $customer);
                if ($customer->passwd) {
                    $customer->passwd = Tools::encrypt($customer->passwd);
                }

                $customers_shop = array();
                $default_shop = new Shop((int)Configuration::get('PS_SHOP_DEFAULT'));
                $customers_shop[$default_shop->id] = $default_shop->getGroup()->id;

                //set temporally for validate field
                $customer->id_shop = $default_shop->id;
                $customer->id_shop_group = $default_shop->getGroup()->id;
                if (isset($info['id_default_group']) && !empty($info['id_default_group']) && !is_numeric($info['id_default_group'])) {
                    $info['id_default_group'] = trim($info['id_default_group']);
                    $my_group = Group::searchByName($info['id_default_group']);
                    if (isset($my_group['id_group']) && $my_group['id_group']) {
                        $info['id_default_group'] = (int)$my_group['id_group'];
                    }
                }

                $my_group = new Group($customer->id_default_group);
                if (!Validate::isLoadedObject($my_group)) {
                    $customer->id_default_group = (int)Configuration::get('PS_CUSTOMER_GROUP');
                }

                $customer_groups[] = (int)$customer->id_default_group;
                $customer_groups = array_flip(array_flip($customer_groups));
                $res = false;
                if (($field_error = $customer->validateFields(UNFRIENDLY_ERROR, true)) === true &&
                    ($lang_field_error = $customer->validateFieldsLang(UNFRIENDLY_ERROR, true)) === true
                ) {
                    $res = true;
                    foreach ($customers_shop as $id_shop => $id_group) {
                        $customer->force_id = (bool)$force_ids;
                        $customer->id_shop = $id_shop;
                        $customer->id_shop_group = $id_group;
                        if ($customer_exist && (int)$id_shop == (int)$current_id_shop) {
                            $customer->id = (int)$current_id_customer;
                            $res &= $customer->update();
                        } else {
                            $res &= $customer->add();
                            if (isset($addresses)) {
                                foreach ($addresses as $address) {
                                    $address['id_customer'] = $customer->id;
                                    unset($address['country'], $address['state'], $address['state_iso'], $address['id_address']);
                                    Db::getInstance()->insert('address', $address, false, false);
                                }
                            }
                        }

                        if ($res && isset($customer_groups)) {
                            $customer->updateGroup($customer_groups);
                        }
                    }
                }

                if (isset($customer_groups)) {
                    unset($customer_groups);
                }
                if (isset($current_id_customer)) {
                    unset($current_id_customer);
                }
                if (isset($current_id_shop)) {
                    unset($current_id_shop);
                }
                if (isset($current_id_shop_group)) {
                    unset($current_id_shop_group);
                }
                if (isset($addresses)) {
                    unset($addresses);
                }

                if (!$res) {
                    $this->errors[] = sprintf(
                        Tools::displayError('%1$s (ID: %2$s) cannot be saved'),
                        $info['email'],
                        (isset($info['id']) && !empty($info['id']))? $info['id'] : 'null'
                    );
                    if ($field_error !== true
                        || $lang_field_error !== true
                    ) {
                        $this->errors[] = ($field_error !== true ? $field_error : '').(isset($lang_field_error) && $lang_field_error !== true ? $lang_field_error : '').
                            Db::getInstance()->getMsgError();
                    }
                }
            }
        }
        $this->closeCsvFile($handle);
    }

    public function aliasImport()
    {
        $this->receiveTab();
        $handle = $this->openCsvFile();
        AdminImportController::setLocale();

        $convert = Tools::getValue('convert');
        $force_ids = Tools::getValue('forceIDs');

        for ($current_line = 0; $line = fgetcsv($handle, MAX_LINE_SIZE, $this->separator, escape: ""); $current_line++) {
            if ($convert) {
                $line = $this->utf8EncodeArray($line);
            }
            $info = AdminImportController::getMaskedRow($line);

            AdminImportController::setDefaultValues($info);

            if ($force_ids && isset($info['id']) && (int)$info['id']) {
                $alias = new Alias((int)$info['id']);
            } else {
                if (array_key_exists('id', $info) && (int)$info['id'] && Alias::existsInDatabase((int)$info['id'], 'alias')) {
                    $alias = new Alias((int)$info['id']);
                } else {
                    $alias = new Alias();
                }
            }

            AdminImportController::arrayWalk($info, array('AdminImportController', 'fillInfo'), $alias);

            $res = false;
            if (($field_error = $alias->validateFields(UNFRIENDLY_ERROR, true)) === true &&
                ($lang_field_error = $alias->validateFieldsLang(UNFRIENDLY_ERROR, true)) === true) {
                if ($alias->id && $alias->aliasExists($alias->id)) {
                    $res = $alias->update();
                }

                $alias->force_id = (bool)$force_ids;
                if (!$res) {
                    $res = $alias->add();
                }

                if (!$res) {
                    $this->errors[] = Db::getInstance()->getMsgError().' '.sprintf(
                        Tools::displayError('%1$s (ID: %2$s) cannot be saved'),
                        $info['name'],
                        (isset($info['id']) ? $info['id'] : 'null')
                    );
                    if ($field_error !== true
                        || $lang_field_error !== true
                    ) {
                        $this->errors[] = ($field_error !== true ? $field_error : '').(isset($lang_field_error) && $lang_field_error !== true ? $lang_field_error : '').
                            Db::getInstance()->getMsgError();
                    }
                }
            } else {
                $this->errors[] = $this->l('Alias is invalid').' ('.$alias->name.')';
                $this->errors[] = ($field_error !== true ? $field_error : '').(isset($lang_field_error) && $lang_field_error !== true ? $lang_field_error : '');
            }
        }
        $this->closeCsvFile($handle);
    }

    /**
     * @since 1.5.0
     */
    public function supplyOrdersImport()
    {
        // opens CSV & sets locale
        $this->receiveTab();
        $handle = $this->openCsvFile();
        AdminImportController::setLocale();

        $convert = Tools::getValue('convert');
        $force_ids = Tools::getValue('forceIDs');

        // main loop, for each supply orders to import
        for ($current_line = 0; $line = fgetcsv($handle, MAX_LINE_SIZE, $this->separator, escape: ""); ++$current_line) {
            // if convert requested
            if ($convert) {
                $line = $this->utf8EncodeArray($line);
            }
            $info = AdminImportController::getMaskedRow($line);

            // sets default values if needed
            AdminImportController::setDefaultValues($info);

            // if an id is set, instanciates a supply order with this id if possible
            if (array_key_exists('id', $info) && (int)$info['id'] && SupplyOrder::exists((int)$info['id'])) {
                $supply_order = new SupplyOrder((int)$info['id']);
            }
            // if a reference is set, instanciates a supply order with this reference if possible
            elseif (array_key_exists('reference', $info) && $info['reference'] && SupplyOrder::exists(pSQL($info['reference']))) {
                $supply_order = SupplyOrder::getSupplyOrderByReference(pSQL($info['reference']));
            } else { // new supply order
                $supply_order = new SupplyOrder();
            }

            // gets parameters
            $id_supplier = (int)$info['id_supplier'];
            $id_lang = (int)$info['id_lang'];
            $id_warehouse = (int)$info['id_warehouse'];
            $id_currency = (int)$info['id_currency'];
            $reference = pSQL($info['reference']);
            $date_delivery_expected = pSQL($info['date_delivery_expected']);
            $discount_rate = (float)$info['discount_rate'];
            $is_template = (bool)$info['is_template'];

            $error = '';
            // checks parameters
            if (!Supplier::supplierExists($id_supplier)) {
                $error = sprintf($this->l('Supplier ID (%d) is not valid (at line %d).'), $id_supplier, $current_line + 1);
            }
            if (!Language::getLanguage($id_lang)) {
                $error = sprintf($this->l('Lang ID (%d) is not valid (at line %d).'), $id_lang, $current_line + 1);
            }
            if (!Warehouse::exists($id_warehouse)) {
                $error = sprintf($this->l('Warehouse ID (%d) is not valid (at line %d).'), $id_warehouse, $current_line + 1);
            }
            if (!Currency::getCurrency($id_currency)) {
                $error = sprintf($this->l('Currency ID (%d) is not valid (at line %d).'), $id_currency, $current_line + 1);
            }
            if (empty($supply_order->reference) && SupplyOrder::exists($reference)) {
                $error = sprintf($this->l('Reference (%s) already exists (at line %d).'), $reference, $current_line + 1);
            }
            if (!empty($supply_order->reference) && ($supply_order->reference != $reference && SupplyOrder::exists($reference))) {
                $error = sprintf($this->l('Reference (%s) already exists (at line %d).'), $reference, $current_line + 1);
            }
            if (!Validate::isDateFormat($date_delivery_expected)) {
                $error = sprintf($this->l('Date (%s) is not valid (at line %d). Format: %s.'), $date_delivery_expected, $current_line + 1, $this->l('YYYY-MM-DD'));
            } elseif (new DateTime($date_delivery_expected) <= new DateTime('yesterday')) {
                $error = sprintf($this->l('Date (%s) cannot be in the past (at line %d). Format: %s.'), $date_delivery_expected, $current_line + 1, $this->l('YYYY-MM-DD'));
            }
            if ($discount_rate < 0 || $discount_rate > 100) {
                $error = sprintf($this->l('Discount rate (%d) is not valid (at line %d). %s.'), $discount_rate, $current_line + 1, $this->l('Format: Between 0 and 100'));
            }
            if ($supply_order->id > 0 && !$supply_order->isEditable()) {
                $error = sprintf($this->l('Supply Order (%d) is not editable (at line %d).'), $supply_order->id, $current_line + 1);
            }

            // if no errors, sets supply order
            if (empty($error)) {
                // adds parameters
                $info['id_ref_currency'] = (int)Currency::getDefaultCurrency()->id;
                $info['supplier_name'] = pSQL(Supplier::getNameById($id_supplier));
                if ($supply_order->id > 0) {
                    $info['id_supply_order_state'] = (int)$supply_order->id_supply_order_state;
                    $info['id'] = (int)$supply_order->id;
                } else {
                    $info['id_supply_order_state'] = 1;
                }

                // sets parameters
                AdminImportController::arrayWalk($info, array('AdminImportController', 'fillInfo'), $supply_order);

                // updatesd($supply_order);

                $res = false;

                if ((int)$supply_order->id && ($supply_order->exists((int)$supply_order->id) || $supply_order->exists($supply_order->reference))) {
                    $res = $supply_order->update();
                } else {
                    $supply_order->force_id = (bool)$force_ids;
                    $res = $supply_order->add();
                }

                // errors
                if (!$res) {
                    $this->errors[] = sprintf($this->l('Supply Order could not be saved (at line %d).'), $current_line + 1);
                }
            } else {
                $this->errors[] = $error;
            }
        }

        // closes
        $this->closeCsvFile($handle);
    }

    public function supplyOrdersDetailsImport()
    {
        // opens CSV & sets locale
        $this->receiveTab();
        $handle = $this->openCsvFile();
        AdminImportController::setLocale();

        $products = array();
        $reset = true;

        $convert = Tools::getValue('convert');
        $force_ids = Tools::getValue('forceIDs');

        // main loop, for each supply orders details to import
        for ($current_line = 0; $line = fgetcsv($handle, MAX_LINE_SIZE, $this->separator, escape: ""); ++$current_line) {
            // if convert requested
            if ($convert) {
                $line = $this->utf8EncodeArray($line);
            }
            $info = AdminImportController::getMaskedRow($line);

            // sets default values if needed
            AdminImportController::setDefaultValues($info);

            // gets the supply order
            if (array_key_exists('supply_order_reference', $info) && pSQL($info['supply_order_reference']) && SupplyOrder::exists(pSQL($info['supply_order_reference']))) {
                $supply_order = SupplyOrder::getSupplyOrderByReference(pSQL($info['supply_order_reference']));
            } else {
                $this->errors[] = sprintf($this->l('Supply Order (%s) could not be loaded (at line %d).'), (int)$info['supply_order_reference'], $current_line + 1);
            }

            if (empty($this->errors)) {
                // sets parameters
                $id_product = (int)$info['id_product'];
                if (!$info['id_product_attribute']) {
                    $info['id_product_attribute'] = 0;
                }
                $id_product_attribute = (int)$info['id_product_attribute'];
                $unit_price_te = (float)$info['unit_price_te'];
                $quantity_expected = (int)$info['quantity_expected'];
                $discount_rate = (float)$info['discount_rate'];
                $tax_rate = (float)$info['tax_rate'];

                // checks if one product/attribute is there only once
                if (isset($products[$id_product][$id_product_attribute])) {
                    $this->errors[] = sprintf($this->l('Product/Attribute (%d/%d) cannot be added twice (at line %d).'), $id_product,
                        $id_product_attribute, $current_line + 1);
                } else {
                    $products[$id_product][$id_product_attribute] = $quantity_expected;
                }

                // checks parameters
                if (false === ($supplier_reference = ProductSupplier::getProductSupplierReference($id_product, $id_product_attribute, $supply_order->id_supplier))) {
                    $this->errors[] = sprintf($this->l('Product (%d/%d) is not available for this order (at line %d).'), $id_product,
                        $id_product_attribute, $current_line + 1);
                }
                if ($unit_price_te < 0) {
                    $this->errors[] = sprintf($this->l('Unit Price (tax excl.) (%d) is not valid (at line %d).'), $unit_price_te, $current_line + 1);
                }
                if ($quantity_expected < 0) {
                    $this->errors[] = sprintf($this->l('Quantity Expected (%d) is not valid (at line %d).'), $quantity_expected, $current_line + 1);
                }
                if ($discount_rate < 0 || $discount_rate > 100) {
                    $this->errors[] = sprintf($this->l('Discount rate (%d) is not valid (at line %d). %s.'), $discount_rate,
                        $current_line + 1, $this->l('Format: Between 0 and 100'));
                }
                if ($tax_rate < 0 || $tax_rate > 100) {
                    $this->errors[] = sprintf($this->l('Quantity Expected (%d) is not valid (at line %d).'), $tax_rate,
                        $current_line + 1, $this->l('Format: Between 0 and 100'));
                }

                // if no errors, sets supply order details
                if (empty($this->errors)) {
                    // resets order if needed
                    if ($reset) {
                        $supply_order->resetProducts();
                        $reset = false;
                    }

                    // creates new product
                    $supply_order_detail = new SupplyOrderDetail();
                    AdminImportController::arrayWalk($info, array('AdminImportController', 'fillInfo'), $supply_order_detail);

                    // sets parameters
                    $supply_order_detail->id_supply_order = $supply_order->id;
                    $currency = new Currency($supply_order->id_ref_currency);
                    $supply_order_detail->id_currency = $currency->id;
                    $supply_order_detail->exchange_rate = $currency->conversion_rate;
                    $supply_order_detail->supplier_reference = $supplier_reference;
                    $supply_order_detail->name = Product::getProductName($id_product, $id_product_attribute, $supply_order->id_lang);

                    // gets ean13 / ref / upc
                    $query = new DbQuery();
                    $query->select('
                        IFNULL(pa.reference, IFNULL(p.reference, \'\')) as reference,
                        IFNULL(pa.ean13, IFNULL(p.ean13, \'\')) as ean13,
                        IFNULL(pa.upc, IFNULL(p.upc, \'\')) as upc
                    ');
                    $query->from('product', 'p');
                    $query->leftJoin('product_attribute', 'pa', 'pa.id_product = p.id_product AND id_product_attribute = '.(int)$id_product_attribute);
                    $query->where('p.id_product = '.(int)$id_product);
                    $query->where('p.is_virtual = 0 AND p.cache_is_pack = 0');
                    $res = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
                    $product_infos = $res['0'];

                    $supply_order_detail->reference = $product_infos['reference'];
                    $supply_order_detail->ean13 = $product_infos['ean13'];
                    $supply_order_detail->upc = $product_infos['upc'];
                    $supply_order_detail->force_id = (bool)$force_ids;
                    $supply_order_detail->add();
                    $supply_order->update();
                    unset($supply_order_detail);
                }
            }
        }

        // closes
        $this->closeCsvFile($handle);
    }

    public function utf8EncodeArray($array)
    {
        return (is_array($array) ? array_map('utf8_encode', $array) : mb_convert_encoding($array, 'UTF-8', 'ISO-8859-1'));
    }

    protected function getNbrColumn($handle, $glue)
    {
        if (!is_resource($handle)) {
            return false;
        }
        $tmp = fgetcsv($handle, MAX_LINE_SIZE, $glue, escape: "");
        AdminImportController::rewindBomAware($handle);
        return count($tmp);
    }

    protected static function usortFiles($a, $b)
    {
        if ($a == $b) {
            return 0;
        }
        return ($b < $a) ? 1 : - 1;
    }

    protected function openCsvFile()
    {
        $file = AdminImportController::getPath(strval(preg_replace('/\.{2,}/', '.', Tools::getValue('csv'))));
        $handle = false;
        if (is_file($file) && is_readable($file)) {
            $handle = fopen($file, 'r');
        }

        if (!$handle) {
            $this->errors[] = Tools::displayError('Cannot read the .CSV file');
        }

        AdminImportController::rewindBomAware($handle);

        for ($i = 0; $i < (int)Tools::getValue('skip'); ++$i) {
            $line = fgetcsv($handle, MAX_LINE_SIZE, $this->separator, escape: "");
        }
        return $handle;
    }

    protected function closeCsvFile($handle)
    {
        fclose($handle);
    }

    protected function truncateTables($case)
    {
        switch ((int) $case) {
            case $this->entities[$this->l('Categories')]:
                $core_categories = array(
                    Configuration::get('PS_HOME_CATEGORY'),
                    Configuration::get('PS_ROOT_CATEGORY'),
                    Configuration::get('PS_SERVICE_CATEGORY'),
                    Configuration::get('PS_LOCATIONS_CATEGORY')
                );
                $exclCategories = implode(',', $core_categories);

                Db::getInstance()->execute('
                    DELETE FROM `'._DB_PREFIX_.'category`
                    WHERE id_category NOT IN ('.$exclCategories.')');
                Db::getInstance()->execute('
                    DELETE FROM `'._DB_PREFIX_.'category_lang`
                    WHERE id_category NOT IN ('.$exclCategories.')');
                Db::getInstance()->execute('
                    DELETE FROM `'._DB_PREFIX_.'category_shop`
                    WHERE `id_category` NOT IN ('.$exclCategories.')');
                Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'category` AUTO_INCREMENT = '.(count($core_categories) + 1));
                foreach (scandir(_PS_CAT_IMG_DIR_) as $d) {
                    if (preg_match('/^[0-9]+(\-(.*))?\.jpg$/', $d)) {
                        unlink(_PS_CAT_IMG_DIR_.$d);
                    }
                }
                break;
            case $this->entities[$this->l('Hotels')]:
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'htl_branch_info_lang`');
                Db::getInstance()->execute('DELETE c, cl FROM `'._DB_PREFIX_.'category` c
                    LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON c.id_category = cl.id_category
                    INNER JOIN `'._DB_PREFIX_.'htl_branch_info` hbi ON hbi.id_category = c.id_category');
                Db::getInstance()->execute('DELETE a FROM `'._DB_PREFIX_.'address` a
                    LEFT JOIN `'._DB_PREFIX_.'htl_branch_info` hbi ON hbi.id = a.id_hotel');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'htl_image`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'htl_access`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'htl_booking_detail`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'htl_branch_features`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'htl_cart_booking_data`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'htl_branch_refund_rules`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'htl_order_restrict_date`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'htl_branch_info`');
                $objHotelReservation = Module::getInstanceByName('hotelreservationsystem');
                $hotelImages = $objHotelReservation->getLocalPath().'views/img/hotel_img/';
                Tools::deleteDirectory($hotelImages, false);
                $prodImages = $objHotelReservation->getLocalPath().'views/img/prod_imgs/';
                Tools::deleteDirectory($prodImages, false);
            case $this->entities[$this->l('Room Types')]:
                $images = Db::getInstance()->executeS('SELECT id_image FROM `'._DB_PREFIX_.'image` img
                    LEFT JOIN `'._DB_PREFIX_.'product` p ON p.id_product = img.id_product
                    WHERE p.booking_product=1');
                if ($images && count($images)) {
                    $image_types = ImageType::getImagesTypes();
                    $files_to_delete = array();
                    foreach ($images as $image) {
                        $path = _PS_PROD_IMG_DIR_.Image::getImgFolderStatic($image['id_image']);
                        foreach ($image_types as $image_type) {
                            $files_to_delete[] = $path.$image['id_image'].'-'.$image_type['name'].'.jpg';
                            if (Configuration::get('WATERMARK_HASH')) {
                                $files_to_delete[] = $path.'-'.$image['id_image'].$image_type['name'].'-'.Configuration::get('WATERMARK_HASH').'.jpg';
                            }
                        }
                    }

                    foreach ($files_to_delete as $file) {
                        if (file_exists($file) && !@unlink($file)) {
                            return false;
                        }
                    }
                }

                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'htl_room_type`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'htl_room_type_feature_pricing`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'htl_room_type_feature_pricing_lang`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'htl_room_type_feature_pricing_group`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'htl_room_type_global_demand`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'htl_room_type_global_demand_lang`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'htl_room_type_global_demand_advance_option`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'htl_room_type_global_demand_advance_option_lang`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'htl_room_type_demand_price`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'htl_room_type_demand`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'htl_room_type_service_product_price`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'service_product_cart_detail`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'htl_room_type_restriction_date_range`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'feature_product`');

                Db::getInstance()->execute('DELETE pl, cp, ps, img, sp, spp, crtp, st, sta, sod
                    FROM `'._DB_PREFIX_.'product_lang` pl
                    LEFT JOIN `'._DB_PREFIX_.'product` p ON p.id_product = pl.id_product
                    LEFT JOIN `'._DB_PREFIX_.'category_product` cp ON p.id_product = cp.id_product
                    LEFT JOIN `'._DB_PREFIX_.'product_shop` ps ON p.id_product = ps.id_product
                    LEFT JOIN `'._DB_PREFIX_.'image` img ON p.id_product = img.id_product
                    LEFT JOIN `'._DB_PREFIX_.'specific_price` sp ON p.id_product = sp.id_product
                    LEFT JOIN `'._DB_PREFIX_.'specific_price_priority` spp ON p.id_product = spp.id_product
                    LEFT JOIN `'._DB_PREFIX_.'cart_product` crtp ON p.id_product = cp.id_product
                    LEFT JOIN `'._DB_PREFIX_.'stock` st ON p.id_product = st.id_product
                    LEFT JOIN `'._DB_PREFIX_.'stock_available` sta ON p.id_product = sta.id_product
                    LEFT JOIN `'._DB_PREFIX_.'supply_order_detail` sod ON p.id_product = sod.id_product
                    WHERE p.booking_product=1');

                Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'product` WHERE booking_product=1');
                if (!file_exists(_PS_PROD_IMG_DIR_)) {
                    mkdir(_PS_PROD_IMG_DIR_);
                }
            case $this->entities[$this->l('Rooms')]:
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'htl_room_information`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'htl_room_disable_dates`');
            break;
            case $this->entities[$this->l('Service Products')]:
                $images = Db::getInstance()->executeS('SELECT id_image FROM `'._DB_PREFIX_.'image` img
                    LEFT JOIN `'._DB_PREFIX_.'product` p ON p.id_product = img.id_product
                    WHERE p.booking_product=0');
                if ($images && count($images)) {
                    $image_types = ImageType::getImagesTypes();
                    $files_to_delete = array();
                    foreach ($images as $image) {
                        $path = _PS_PROD_IMG_DIR_.Image::getImgFolderStatic($image['id_image']);
                        foreach ($image_types as $image_type) {
                            $files_to_delete[] = $path.$image['id_image'].'-'.$image_type['name'].'.jpg';
                            if (Configuration::get('WATERMARK_HASH')) {
                                $files_to_delete[] = $path.'-'.$image['id_image'].$image_type['name'].'-'.Configuration::get('WATERMARK_HASH').'.jpg';
                            }
                        }
                    }

                    foreach ($files_to_delete as $file) {
                        if (file_exists($file) && !@unlink($file)) {
                            return false;
                        }
                    }
                }

                Db::getInstance()->execute('DELETE pl, cp, ps, img, sp, spp, crtp, st, sta, sod
                    FROM `'._DB_PREFIX_.'product_lang` pl
                    LEFT JOIN `'._DB_PREFIX_.'product` p ON p.id_product = pl.id_product
                    LEFT JOIN `'._DB_PREFIX_.'category_product` cp ON p.id_product = cp.id_product
                    LEFT JOIN `'._DB_PREFIX_.'product_shop` ps ON p.id_product = ps.id_product
                    LEFT JOIN `'._DB_PREFIX_.'image` img ON p.id_product = img.id_product
                    LEFT JOIN `'._DB_PREFIX_.'specific_price` sp ON p.id_product = sp.id_product
                    LEFT JOIN `'._DB_PREFIX_.'specific_price_priority` spp ON p.id_product = spp.id_product
                    LEFT JOIN `'._DB_PREFIX_.'cart_product` crtp ON p.id_product = cp.id_product
                    LEFT JOIN `'._DB_PREFIX_.'stock` st ON p.id_product = st.id_product
                    LEFT JOIN `'._DB_PREFIX_.'stock_available` sta ON p.id_product = sta.id_product
                    LEFT JOIN `'._DB_PREFIX_.'supply_order_detail` sod ON p.id_product = sod.id_product
                    WHERE p.booking_product=0');

                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'htl_room_type_service_product`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'htl_room_type_service_product_price`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'service_product_cart_detail`');
                Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'product` WHERE booking_product=0');
            break;
            case $this->entities[$this->l('Customers')]:
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'customer`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'customer_group`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'customer_message`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'customer_message_sync_imap`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'customer_thread`');
            break;
            case $this->entities[$this->l('Bookings')]:
                $orderRelatedTables = array(
                    'cart',
                    'cart_product',
                    'orders',
                    'order_carrier',
                    'order_cart_rule',
                    'order_detail',
                    'order_detail_tax',
                    'order_history',
                    'order_invoice',
                    'order_invoice_payment',
                    'order_invoice_tax',
                    'order_message',
                    'order_message_lang',
                    'order_payment',
                    'order_payment_detail',
                    'order_return',
                    'order_return_detail',
                    'order_slip',
                    'order_slip_detail',
                    'product_sale',
                    'referrer_cache',
                    'htl_cart_booking_data',
                    'htl_booking_detail',
                    'htl_booking_demands',
                    'htl_booking_demands_tax',
                    'service_product_order_detail',
                    'service_product_cart_detail',
                );
                foreach($orderRelatedTables as $table) {
                    Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.$table.'`');
                }

            break;
        }
        Image::clearTmpDir();
        return true;
    }

    public function clearSmartyCache()
    {
        Tools::enableCache();
        Tools::clearCache($this->context->smarty);
        Tools::restoreCacheSettings();
    }

    public function postProcess()
    {
        /* PrestaShop demo mode */
        if (_PS_MODE_DEMO_) {
            $this->errors[] = Tools::displayError('This functionality has been disabled.');
            return;
        }

        if (Tools::isSubmit('import')) {
            // Check if the CSV file exist
            if (Tools::getValue('csv')) {
                $shop_is_feature_active = Shop::isFeatureActive();
                // If i am a superadmin, i can truncate table
                if ((($shop_is_feature_active && $this->context->employee->isSuperAdmin()) || !$shop_is_feature_active) && Tools::getValue('truncate')) {
                    $this->truncateTables((int) Tools::getValue('entity'));
                }
                $import_type = false;
                Db::getInstance()->disableCache();
                switch ((int)Tools::getValue('entity')) {
                    case $this->entities[$import_type = $this->l('Hotels')]:
                        $this->hotelImport();
                        $this->clearSmartyCache();
                        break;
                    case $this->entities[$import_type = $this->l('Room Types')]:
                        $this->roomTypeImport();
                        $this->clearSmartyCache();
                        break;
                    case $this->entities[$import_type = $this->l('Rooms')]:
                        $this->roomImport();
                        $this->clearSmartyCache();
                        break;
                    case $this->entities[$import_type = $this->l('Service Products')]:
                        $this->serviceProductImport();
                        $this->clearSmartyCache();
                        break;
                    case $this->entities[$import_type = $this->l('Categories')]:
                        $this->categoryImport();
                        $this->clearSmartyCache();
                        break;
                    case $this->entities[$import_type = $this->l('Bookings')]:
                        $this->bookingsImport();
                        $this->clearSmartyCache();
                        break;
                    case $this->entities[$import_type = $this->l('Customers')]:
                        $this->customerImport();
                        $this->clearSmartyCache();
                        break;
                    case $this->entities[$import_type = $this->l('Alias')]:
                        $this->aliasImport();
                        break;
                }

                if ($import_type !== false) {
                    $log_message = sprintf($this->l('%s import', 'AdminTab', false, false), $import_type);
                    if (Tools::getValue('truncate')) {
                        $log_message .= ' '.$this->l('with truncate', 'AdminTab', false, false);
                    }
                    PrestaShopLogger::addLog($log_message, 1, null, $import_type, null, true, (int)$this->context->employee->id);
                }
            } else {
                $this->errors[] = $this->l('You must upload a file in order to proceed to the next step');
            }
        } elseif ($filename = Tools::getValue('csvfilename')) {
            $filename = urldecode($filename);
            $file = AdminImportController::getPath(basename($filename));
            if (realpath(dirname($file)) != realpath(AdminImportController::getPath())) {
                exit();
            }
            if (!empty($filename)) {
                $b_name = basename($filename);
                if (Tools::getValue('delete') && file_exists($file)) {
                    @unlink($file);
                } elseif (file_exists($file)) {
                    $b_name = explode('.', $b_name);
                    $b_name = strtolower($b_name[count($b_name) - 1]);
                    $mime_types = array('csv' => 'text/csv');

                    if (isset($mime_types[$b_name])) {
                        $mime_type = $mime_types[$b_name];
                    } else {
                        $mime_type = 'application/octet-stream';
                    }

                    if (ob_get_level() && ob_get_length() > 0) {
                        ob_end_clean();
                    }

                    header('Content-Transfer-Encoding: binary');
                    header('Content-Type: '.$mime_type);
                    header('Content-Length: '.filesize($file));
                    header('Content-Disposition: attachment; filename="'.$filename.'"');
                    $fp = fopen($file, 'rb');
                    while (is_resource($fp) && !feof($fp)) {
                        echo fgets($fp, 16384);
                    }
                    exit;
                }
            }
        }
        Db::getInstance()->enableCache();
        return parent::postProcess();
    }

    public static function setLocale()
    {
        $iso_lang  = trim(Tools::getValue('iso_lang'));
        setlocale(LC_COLLATE, strtolower($iso_lang).'_'.strtoupper($iso_lang).'.UTF-8');
        setlocale(LC_CTYPE, strtolower($iso_lang).'_'.strtoupper($iso_lang).'.UTF-8');
    }

    protected function addProductWarning($product_name, $product_id = null, $message = '')
    {
        $this->warnings[] = $product_name.(isset($product_id) ? ' (ID '.$product_id.')' : '').' '
            .Tools::displayError($message);
    }

    public function ajaxProcessSaveImportMatchs()
    {
        if ($this->tabAccess['edit'] === 1) {
            $match = implode('|', Tools::getValue('type_value'));
            Db::getInstance()->execute('INSERT IGNORE INTO  `'._DB_PREFIX_.'import_match` (
                                        `id_import_match` ,
                                        `name` ,
                                        `match`,
                                        `skip`
                                        )
                                        VALUES (
                                        NULL ,
                                        \''.pSQL(Tools::getValue('newImportMatchs')).'\',
                                        \''.pSQL($match).'\',
                                        \''.pSQL(Tools::getValue('skip')).'\'
                                        )', false);

            die('{"id" : "'.Db::getInstance()->Insert_ID().'"}');
        }
    }

    public function ajaxProcessLoadImportMatchs()
    {
        if ($this->tabAccess['edit'] === 1) {
            $return = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'import_match` WHERE `id_import_match` = '
                .(int)Tools::getValue('idImportMatchs'), true, false);
            die('{"id" : "'.$return[0]['id_import_match'].'", "matchs" : "'.$return[0]['match'].'", "skip" : "'
                .$return[0]['skip'].'"}');
        }
    }

    public function ajaxProcessDeleteImportMatchs()
    {
        if ($this->tabAccess['edit'] === 1) {
            Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'import_match` WHERE `id_import_match` = '
                .(int)Tools::getValue('idImportMatchs'), false);
            die;
        }
    }

    public static function getPath($file = '')
    {
        return (defined('_PS_HOST_MODE_') ? _PS_ROOT_DIR_ : _PS_ADMIN_DIR_).DIRECTORY_SEPARATOR.'import'
            .DIRECTORY_SEPARATOR.$file;
    }

    public function checkRequiredFields($fields = array(), $nameKey = 'name')
    {
        $res = true;
        if (is_array($this->required_fields) && count($this->required_fields)) {
            $res = false;
            if (is_array($fields) && count($fields)) {
                $res = true;
                foreach ($this->required_fields as $field) {
                    if (!isset($fields[$field])
                        || empty($fields[$field])
                    ) {
                        $this->errors[] = sprintf(
                            Tools::displayError('%1$s (ID: %2$s) cannot be saved ').
                            $this->available_fields[$field]['label'].$this->l(' is invalid.'),
                            (isset($fields[$nameKey]) && !empty($fields[$nameKey]))? Tools::safeOutput($fields[$nameKey]) : 'No Name',
                            (isset($fields['id']) && !empty($fields['id']))? Tools::safeOutput($fields['id']) : 'No ID'
                        );

                        $res = false;
                    }
                }
            }

        }

        return $res;
    }
}
