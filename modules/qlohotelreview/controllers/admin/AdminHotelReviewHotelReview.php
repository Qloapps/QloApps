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

class AdminHotelReviewHotelReviewController extends ModuleAdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->className = 'QhrHotelReview';
        $this->table = 'qhr_hotel_review';
        $this->identifier = 'id_hotel_review';
        $this->context = Context::getContext();

        // add profile access restriction
        $this->access_select = 'SELECT a.`id_hotel_review`
        FROM `'._DB_PREFIX_.'qhr_hotel_review` a';

        $accessibleHotels = HotelBranchInformation::getProfileAccessedHotels(
            $this->context->employee->id_profile,
            1,
            1
        );

        if ($accessibleHotels) {
            $this->access_where = ' WHERE a.`id_hotel` IN ('.implode(',', $accessibleHotels).')';
        }

        parent::__construct();

        $this->_select .= ' hbl.`hotel_name`, c.`id_customer`,
        CONCAT(c.`firstname`, " ", c.`lastname`) as `customer_name`,
        CONVERT(a.`rating`, DECIMAL(10, 1)) AS `rating`,
        (SELECT COUNT(*) FROM `'._DB_PREFIX_.'qhr_review_report` rr
        WHERE rr.`id_hotel_review` = a.`id_hotel_review`) AS `total_report`';
        $this->_join .= ' LEFT JOIN `'._DB_PREFIX_.'orders` o ON (o.`id_order` = a.`id_order`)';
        $this->_join .= ' LEFT JOIN `'._DB_PREFIX_.'customer` c ON (c.`id_customer` = o.`id_customer`)';
        $this->_join .= ' LEFT JOIN `'._DB_PREFIX_.'htl_branch_info_lang` hbl
        ON (hbl.`id` = a.`id_hotel` AND hbl.`id_lang` = '.(int) $this->context->language->id.')';
        $this->_orderBy .= 'a.date_add';
        $this->_orderWay .= 'DESC';

        $this->addRowAction('view');
        $this->addRowAction('approve');
        $this->addRowAction('disapprove');
        $this->addRowAction('delete');

        $this->fields_list = array(
            'id_hotel_review' => array(
                'title' => $this->l('ID'),
                'hint' => $this->l('ID of the review.'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
            ),
            'hotel_name' => array(
                'title' => $this->l('Hotel'),
                'hint' => $this->l('Name of the hotel for which this review has been added.'),
                'align' => 'left',
                'callback' => 'getHotelLink',
            ),
            'rating' => array(
                'title' => $this->l('Overall Rating'),
                'hint' => $this->l('Overall rating of the review.'),
                'align' => 'center',
                'havingFilter' => true,
                'callback' => 'getOverallRating',
            ),
            'subject' => array(
                'title' => $this->l('Subject'),
                'hint' => $this->l('Subject of the review.'),
                'align' => 'left',
                'callback' => 'getSubject',
            ),
            'customer_name' => array(
                'title' => $this->l('Customer'),
                'hint' => $this->l('Name of the customer who added this review.'),
                'align' => 'left',
                'callback' => 'getCustomerLink',
                'havingFilter' => true,
            ),
            'total_report' => array(
                'title' => $this->l('Total Reports'),
                'hint' => $this->l('Number of times this review has been reported.'),
                'align' => 'center',
                'havingFilter' => true,
                'class' => 'fixed-width-md',
                'callback' => 'getTotalReports',
            ),
            'status' => array(
                'title' => $this->l('Approval Status'),
                'hint' => $this->l('Approval status of the review.'),
                'align' => 'center',
                'callback' => 'getStatus',
                'type' => 'select',
                'filter_key' => 'a!status',
                'list' => QhrHotelReview::getStatuses(),
            ),
            'date_add' => array(
                'title' => $this->l('Date'),
                'hint' => $this->l('Date and time when this review was added.'),
                'align' => 'center',
                'type' => 'datetime',
                'filter_key' => 'a!date_add',
            ),
        );

        $this->list_no_link = true;

        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'confirm' => $this->l('Delete selected items?'),
                'icon' => 'icon-trash'
            )
        );

        $this->_conf[101] = $this->l('Reply has been added successfully.');
        $this->_conf[102] = $this->l('Review has been approved successfully.');
        $this->_conf[103] = $this->l('Review has been disapproved successfully.');
        $this->_conf[104] = $this->l('Review has been marked not abusive successfully.');

        $this->cacheStatus = array();

        $this->tpls_base_dir = $this->module->getLocalPath().'views/templates/admin/'.$this->tpl_folder;
    }

    public function getOverallRating($rating)
    {
        $tpl = $this->context->smarty->createTemplate(
            $this->tpls_base_dir.'helpers/list/overall-rating.tpl'
        );

        $tpl->assign(array(
            'rating' => $rating,
        ));

        return $tpl->fetch();
    }

    public function getSubject($subject)
    {
        return Tools::substr($subject, 0, 50).(Tools::strlen($subject) > 50 ? '...' : '');
    }

    public function getCustomerLink($fullname, $tr)
    {
        $tpl = $this->context->smarty->createTemplate(
            $this->tpls_base_dir.'helpers/list/customer-link.tpl'
        );

        $tpl->assign(array(
            'fullname' => $fullname,
            'id_customer' => $tr['id_customer'],
        ));

        return $tpl->fetch();
    }

    public function getHotelLink($hotelName, $tr)
    {
        $tpl = $this->context->smarty->createTemplate(
            $this->tpls_base_dir.'helpers/list/hotel-link.tpl'
        );

        $tpl->assign(array(
            'hotel_name' => $hotelName,
            'id_hotel' => $tr['id_hotel'],
        ));

        return $tpl->fetch();
    }

    public function getTotalReports($totalReports)
    {
        $tpl = $this->context->smarty->createTemplate(
            $this->tpls_base_dir.'helpers/list/total-reports.tpl'
        );

        $tpl->assign(array(
            'total_reports' => $totalReports,
        ));

        return $tpl->fetch();
    }

    public function getStatus($status)
    {
        $tpl = $this->context->smarty->createTemplate(
            $this->tpls_base_dir.'helpers/list/status.tpl'
        );

        $tpl->assign(array(
            'status' => $status,
        ));

        return $tpl->fetch();
    }

    public function displayApproveLink($token = null, $id, $name = null)
    {
        $status = null;
        if (array_key_exists($id, $this->cacheStatus)) {
            $status = $this->cacheStatus[$id];
        } else {
            $objHotelReview = new QhrHotelReview((int) $id);
            $status = $objHotelReview->status;
            $this->cacheStatus[$id] = $status;
        }

        if ($status != QhrHotelReview::QHR_STATUS_APPROVED) {
            $tpl = $this->context->smarty->createTemplate(
                $this->tpls_base_dir.'helpers/list/approve-link.tpl'
            );

            $link = self::$currentIndex.'&'.$this->identifier.'='.$id.'&action=approveHotelReview'.'&token='.
            ($token != null ? $token : $this->token);

            $tpl->assign(array(
                'link' => $link,
            ));

            return $tpl->fetch();
        } else {
            $this->addRowActionSkipList('approve', $id);
        }
    }

    public function displayDisapproveLink($token = null, $id, $name = null)
    {
        $status = null;
        if (array_key_exists($id, $this->cacheStatus)) {
            $status = $this->cacheStatus[$id];
        } else {
            $objHotelReview = new QhrHotelReview((int) $id);
            $status = $objHotelReview->status;
            $this->cacheStatus[$id] = $status;
        }

        if ($status != QhrHotelReview::QHR_STATUS_DISAPPROVED) {
            $tpl = $this->context->smarty->createTemplate(
                $this->tpls_base_dir.'helpers/list/disapprove-link.tpl'
            );

            $link = self::$currentIndex.'&'.$this->identifier.'='.$id.'&action=disapproveHotelReview'.'&token='.
            ($token != null ? $token : $this->token);

            $tpl->assign(array(
                'link' => $link,
            ));

            return $tpl->fetch();
        } else {
            $this->addRowActionSkipList('disapprove', $id);
        }
    }

    public function initToolbarTitle()
    {
        parent::initToolbarTitle();
        if ($this->display == 'view') {
            $this->toolbar_title = $this->l('Viewing');
        } elseif ($this->display == '') {
            $this->toolbar_title = $this->l('Reviews');
        }
    }

    public function renderList()
    {
        unset($this->toolbar_btn['new']);
        $this->context->smarty->assign(array('icon' => 'icon-list'));
        return parent::renderList();
    }

    public function renderView()
    {
        $this->loadObject();
        if (!count($this->errors)) {
            $smartyVars = array();

            $objCustomer = $this->object->getCustomer();
            $objHotel = new HotelBranchInformation($this->object->id_hotel, (int) $this->context->language->id);
            $smartyVars['id_hotel_review'] = (int) $this->id_object;
            $smartyVars['current_iso_code'] = $this->context->language->iso_code;
            $smartyVars['currentTab'] = $this;
            $smartyVars['currentObject'] = $this->object;
            $smartyVars['images'] = $this->object->getImages();
            $smartyVars['reply'] = $this->object->getManagementReply();
            $smartyVars['total_reports'] = $this->object->getTotalReports();
            $smartyVars['obj_customer'] = $objCustomer;
            $smartyVars['obj_hotel'] = $objHotel;

            $this->context->smarty->assign($smartyVars);
            return parent::renderView();
        }
    }

    public function postProcess()
    {
        if (Tools::isSubmit('submitReply')) {
            $reply = Tools::getValue('management_reply');
            if ($reply == '') {
                $this->errors[] = $this->l('Management reply can not be empty.');
            } elseif (!Validate::isCleanHtml($reply)) {
                $this->errors[] = $this->l('Management reply is invalid.');
            }

            $this->loadObject(false);
            if (!count($this->errors)) {
                if ($this->object->addManagementReply($reply)) {
                    if (Configuration::get('QHR_REVIEW_MGMT_REPLY_EMAIL_ENABLED')) {
                        $this->object->sendManagementReplyEmail();
                    }
                    Tools::redirectAdmin(self::$currentIndex.'&conf=101&token='.$this->token.
                    '&viewqhr_hotel_review&id_hotel_review='.$this->id_object);
                } else {
                    $this->display = 'view';
                }
            }
        } elseif (Tools::isSubmit('submitApprove')) {
            $this->loadObject(false);
            if (!count($this->errors)) {
                if ($this->object->approveReview()) {
                    if (Configuration::get('QHR_REVIEW_APPROVAL_EMAIL_ENABLED')) {
                        $this->object->sendApprovalEmail();
                    }
                    Tools::redirectAdmin(self::$currentIndex.'&conf=102&token='.$this->token.
                    '&viewqhr_hotel_review&id_hotel_review='.$this->id_object);
                } else {
                    $this->display = 'view';
                }
            }
        } elseif (Tools::isSubmit('submitDisapprove')) {
            $this->loadObject(false);
            if (!count($this->errors)) {
                if ($this->object->disapproveReview()) {
                    Tools::redirectAdmin(self::$currentIndex.'&conf=103&token='.$this->token.
                    '&viewqhr_hotel_review&id_hotel_review='.$this->id_object);
                } else {
                    $this->display = 'view';
                }
            }
        } elseif (Tools::isSubmit('submitDelete')) {
            $this->loadObject(false);
            if (!count($this->errors)) {
                if ($this->object->delete()) {
                    Tools::redirectAdmin(self::$currentIndex.'&conf=1&token='.$this->token);
                }
            }
        }

        parent::postProcess();
    }

    public function processApproveHotelReview()
    {
        $this->loadObject(false);
        if (!count($this->errors)) {
            if ($this->object->approveReview()) {
                if (Configuration::get('QHR_REVIEW_APPROVAL_EMAIL_ENABLED')) {
                    $this->object->sendApprovalEmail();
                }
                Tools::redirectAdmin(self::$currentIndex.'&conf=102&token='.$this->token);
            }
        }
    }

    public function processDisapproveHotelReview()
    {
        $this->loadObject(false);
        if (!count($this->errors)) {
            if ($this->object->disapproveReview()) {
                Tools::redirectAdmin(self::$currentIndex.'&conf=103&token='.$this->token);
            }
        }
    }

    public function processMarkNotAbusive()
    {
        $this->loadObject(false);
        if (!count($this->errors)) {
            if ($this->object->markNotAbusive()) {
                Tools::redirectAdmin(self::$currentIndex.'&conf=104&token='.$this->token);
            }
        }
    }

    public function setMedia()
    {
        parent::setMedia();
        $this->addCSS($this->module->getPathUri().'views/css/admin/hotel-review.css');
        $this->addJS($this->module->getPathUri().'views/js/admin/hotel-review.js');
    }
}
