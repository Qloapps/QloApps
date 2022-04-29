<?php
/**
* 2010-2022 Webkul.
*
* NOTICE OF LICENSE
*
* All right is reserved,
* Please go through LICENSE.txt file inside our module
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to CustomizationPolicy.txt file inside our module for more information.
*
* @author Webkul IN
* @copyright 2010-2022 Webkul IN
* @license LICENSE.txt
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once 'classes/RequiredFiles.php';

class QloHotelReview extends Module
{
    public function __construct()
    {
        $this->name = 'qlohotelreview';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => '1.6');
        $this->author = 'Webkul';
        $this->bootstrap = true;
        parent::__construct();
        $this->secure_key = Tools::encrypt($this->name);
        $this->displayName = $this->l('QloApps Hotel Reviews');
        $this->description = $this->l('This module allows guests to review hotels on specific categories.');
        $this->confirmUnsinstall = $this->l('Are you sure you want to uninstall?');
    }

    public function install()
    {
        if (!parent::install()
            || !$this->registerModuleHooks()
            || !$this->installModuleTabs()
            || !QhrHotelReviewDb::createTables()
            || !$this->saveModuleDefaultConfig()
        ) {
            return false;
        }
        return true;
    }

    public function getContent()
    {
        Tools::redirectAdmin($this->context->link->getAdminLink('AdminHotelReviewHotelReview'));
    }

    public function registerModuleHooks()
    {
        return $this->registerHook(
            array(
                'actionFrontControllerSetMedia',
                'displayProductTab',
                'displayProductTabContent',
                'displayFooterBefore',
                'actionRoomBookingStatusUpdateAfter',
                'displayBookingAction',
                'displayBackOfficeHeader',
                'displayAdminAfterHeader',
            )
        );
    }

    public function saveModuleDefaultConfig()
    {
        $config = QhrHotelReviewDb::getModuleDefaultConfig();

        foreach ($config as $key => $value) {
            if (!Configuration::updateValue($key, $value)) {
                return false;
            }
        }
        return true;
    }

    public function hookActionFrontControllerSetMedia()
    {
        // review popup resources
        $this->reviewPopupResources();

        // review list resources
        $this->reviewListResources();
    }

    public function reviewPopupResources()
    {
        $controllers = array('history', 'guesttracking');
        $controller = Tools::getValue('controller');
        if (!in_array($controller, $controllers)) {
            return;
        }

        $idOrder = 0;
        if ($controller == 'history') {
            $idOrder = (int) Tools::getValue('id_order');
        }

        $this->loadMedia($idOrder);
    }

    public function reviewListResources()
    {
        if (Tools::getValue('controller') == 'product') {
            $idProduct = Tools::getValue('id_product');
            $objHotelRoomType = new HotelRoomType();
            $roomTypeInfo = $objHotelRoomType->getRoomTypeInfoByIdProduct($idProduct);
            $idHotel = $roomTypeInfo['id_hotel'];
            $reviewImages = QhrHotelReview::getAllImages($idHotel);

            Media::addJsDef(array('qlo_hotel_review_js_vars' => array(
                'review_ajax_link' => $this->context->link->getModuleLink($this->name),
                'review_ajax_token' => $this->secure_key,
                'raty_path' => $this->getPathUri().'views/img/raty',
                'review_images' => $reviewImages,
            )));

            $this->context->controller->addCSS($this->getPathUri().'libs/js/raty/jquery.raty.css');
            $this->context->controller->addJS($this->getPathUri().'libs/js/raty/jquery.raty.js');
            $this->context->controller->addJS(
                $this->getPathUri().'libs/js/jquery-circle-progress/circle-progress.min-1.2.2.js'
            );
            $this->context->controller->addCSS($this->getPathUri().'views/css/front/review-list.css');
            $this->context->controller->addJS($this->getPathUri().'views/js/front/review-list.js');
        }
    }

    public function loadMedia($idOrder)
    {
        Media::addJsDef(array('qlo_hotel_review_js_vars' => array(
            'id_order' => (int) $idOrder, // 0 for guesttracking
            'link' => $this->context->link->getPageLink('order-detail', true),
            'review_ajax_link' => $this->context->link->getModuleLink($this->name),
            'review_ajax_token' => $this->secure_key,
            'raty_path' => $this->getPathUri().'views/img/raty',
            'num_images_max' => (int) Configuration::get('QHR_MAX_IMAGES_PER_REVIEW'),
            'admin_approval_enabled' => (int) Configuration::get('QHR_ADMIN_APPROVAL_ENABLED'),
            'texts' => array(
                'num_files' => sprintf(
                    $this->l('You can upload a maximum of %d images.'),
                    (int) Configuration::get('QHR_MAX_IMAGES_PER_REVIEW')
                ),
            ),
        )));

        $this->context->controller->addCSS($this->getPathUri().'libs/js/raty/jquery.raty.css');
        $this->context->controller->addJS($this->getPathUri().'libs/js/raty/jquery.raty.js');
        $this->context->controller->addCSS($this->getPathUri().'views/css/front/review.css');
        $this->context->controller->addJS($this->getPathUri().'views/js/hook/review.js');
    }

    public function hookDisplayBookingAction($params)
    {
        $idOrder = $params['id_order'];
        if (QhrHotelReviewHelper::getIsReviewable($idOrder)
            && !QhrHotelReview::getByIdOrder($idOrder)
        ) {
            $hotel = QhrHotelReviewHelper::getHotelByOrder($idOrder);
            $this->smarty->assign(array(
                'id_order' => (int) $idOrder,
                'id_hotel' => $hotel['id_hotel'],
                'hotel_name' => $hotel['hotel_name'],
            ));
            return $this->display(__FILE__, 'booking-action.tpl');
        }
    }

    public function hookDisplayFooterBefore()
    {
        $controllers = array('history', 'guesttracking');
        $controller = Tools::getValue('controller');
        if (in_array($controller, $controllers)) {
            $categories = QhrCategory::getAll();
            $this->smarty->assign(array(
                'categories' => $categories,
                'action' => $this->context->link->getModuleLink($this->name),
            ));
            return $this->display(__FILE__, 'add-review-popup.tpl');
        }
    }

    public function hookActionRoomBookingStatusUpdateAfter($params)
    {
        $idOrder = $params['id_order'];
        if (QhrHotelReviewHelper::getIsOrderCheckedOut($idOrder)) {
            QhrHotelReviewHelper::sendReviewRequestMail($idOrder);
        }
    }

    public function hookDisplayProductTab()
    {
        return $this->display(__FILE__, 'product-tab.tpl');
    }

    public function hookDisplayProductTabContent()
    {
        $idProduct = Tools::getValue('id_product');
        $objHotelRoomType = new HotelRoomType();
        $roomTypeInfo = $objHotelRoomType->getRoomTypeInfoByIdProduct($idProduct);
        $idHotel = $roomTypeInfo['id_hotel'];
        $reviewsPerPage = (int) Configuration::get('QHR_REVIEWS_PER_PAGE');
        $reviews = QhrHotelReview::getByHotel(
            $idHotel,
            1,
            $reviewsPerPage,
            QhrHotelReview::QHR_SORT_BY_TIME_NEW,
            $this->context->cookie->id_customer
        );
        if (is_array($reviews) && count($reviews)) {
            foreach ($reviews as &$review) {
                $review['images'] = QhrHotelReview::getImagesById($review['id_hotel_review']);
            }
        }

        $summary = QhrHotelReview::getSummaryByHotel($idHotel);
        if (is_array($summary['categories']) && count($summary['categories'])) {
            $summary = QhrHotelReviewHelper::prepareCategoriesData($summary);
        }

        $hasNextPage = QhrHotelReview::hasNextPage($idHotel, 1, $reviewsPerPage);

        $this->smarty->assign(array(
            'id_hotel' => $idHotel,
            'reviews' => $reviews,
            'summary' => $summary,
            'images' => QhrHotelReview::getAllImages($idHotel),
            'logged' => $this->context->customer->isLogged(true),
            'show_load_more_btn' => $hasNextPage,
        ));
        return $this->display(__FILE__, 'product-tab-content.tpl');
    }

    public function installModuleTabs()
    {
        $tabs = array(
            array('AdminParentHotelReview', 'Hotel Reviews', false, true),
            array('AdminHotelReviewCategory', 'Configuration', 'AdminParentHotelReview', false),
            array('AdminHotelReviewHotelReview', 'Reviews', 'AdminParentHotelReview', false),
        );

        foreach ($tabs as $tab) {
            $this->installTab($tab[0], $tab[1], $tab[2], $tab[3]);
        }
        return true;
    }

    public function installTab($className, $tabName, $tabParentName = false, $hidden = false)
    {
        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = $className;
        $tab->name = array();
        foreach (Language::getLanguages(false) as $lang) {
            $tab->name[$lang['id_lang']] = $tabName;
        }
        if ($tabParentName) {
            $tab->id_parent = (int) Tab::getIdFromClassName($tabParentName);
        } elseif ($hidden) {
            $tab->id_parent = -1;
        } else {
            $tab->id_parent = 0;
        }
        $tab->module = $this->name;
        return $tab->add();
    }

    public function hookDisplayBackOfficeHeader()
    {
        $this->context->controller->addCSS($this->getPathUri().'views/css/hook/global.css');
    }

    public function hookDisplayAdminAfterHeader()
    {
        if ($currentController = Tools::getValue('controller')) {
            $controllers = array(
                'AdminHotelReviewHotelReview',
                'AdminHotelReviewCategory',
            );
            if (in_array($currentController, $controllers)) {
                return $this->display(__FILE__, 'admin-after-header.tpl');
            }
        }
    }

    public function deleteModuleConfigKeys()
    {
        $config = QhrHotelReviewDb::getModuleDefaultConfig();
        foreach ($config as $key => $value) {
            Configuration::deleteByName($key);
        }
        return true;
    }

    public function uninstallModuleTabs()
    {
        $moduleTabs = Tab::getCollectionFromModule($this->name);
        if (!empty($moduleTabs)) {
            foreach ($moduleTabs as $moduleTab) {
                $moduleTab->delete();
            }
        }
        return true;
    }

    public function reset()
    {
        if (!$this->uninstall(false)) {
            return false;
        }
        if (!$this->install(false)) {
            return false;
        }
        return true;
    }

    public function uninstall($keep = true)
    {
        if (!parent::uninstall()
            || ($keep && !QhrHotelReviewDb::deleteTables())
            || ($keep && !QhrHotelReview::cleanImagesDirectory())
            || !$this->uninstallModuleTabs()
            || !$this->deleteModuleConfigKeys()
        ) {
            return false;
        }
        return true;
    }
}
