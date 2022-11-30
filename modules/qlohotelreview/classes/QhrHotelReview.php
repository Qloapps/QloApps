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

class QhrHotelReview extends ObjectModel
{
    public $id_hotel_review;
    public $id_hotel;
    public $id_order;
    public $rating;
    public $subject;
    public $description;
    public $status_abusive = self::QHR_STATUS_ABUSIVE_NOT_ABUSIVE;
    public $status;
    public $date_add;
    public $date_upd;

    const QHR_STATUS_ABUSIVE_REPORTED_ABUSE = 1;
    const QHR_STATUS_ABUSIVE_NOT_ABUSIVE = 2;

    const QHR_STATUS_PENDING = 1;
    const QHR_STATUS_DISAPPROVED = 2;
    const QHR_STATUS_APPROVED = 3;

    const QHR_SORT_BY_RELEVANCE = 1;
    const QHR_SORT_BY_TIME_NEW = 2;
    const QHR_SORT_BY_TIME_OLD = 3;
    const QHR_SORT_BY_RATING_HIGH = 4;
    const QHR_SORT_BY_RATING_LOW = 5;

    public static $definition = array(
        'table' => 'qhr_hotel_review',
        'primary' => 'id_hotel_review',
        'multilang' => false,
        'fields' => array(
            'id_hotel' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true),
            'id_order' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true),
            'rating' => array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat'),
            'subject' => array('type' => self::TYPE_STRING, 'validate' => 'isMessage', 'size' => 255, 'required' => true),
            'description' => array('type' => self::TYPE_STRING, 'validate' => 'isMessage', 'size' => 65535, 'required' => true),
            'status_abusive' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'status' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
        ),
    );

    public function __construct($id = null, $id_lang = null)
    {
        parent::__construct($id, $id_lang);
        if ($id) {
            $this->category_ratings = self::getCategoryRatings($id);
            $this->full_img_dir = _PS_MODULE_DIR_.'qlohotelreview/views/img/review/'.
            Image::getImgFolderStatic($this->id);
            $this->img_dir = _MODULE_DIR_.'qlohotelreview/views/img/review/'.
            Image::getImgFolderStatic($this->id);
        }
    }

    public function delete()
    {
        // tables to remove data from
        $tables = array(
            'qhr_review_category_rating',
            'qhr_review_usefulness',
            'qhr_review_report',
            'qhr_review_reply',
        );

        foreach ($tables as $table) {
            Db::getInstance()->delete($table, 'id_hotel_review = '.(int) $this->id);
        }

        Tools::deleteDirectory($this->full_img_dir);
        return parent::delete();
    }

    public static function getStatuses()
    {
        $objModule = Module::getInstanceByName('qlohotelreview');

        return array(
            self::QHR_STATUS_PENDING => $objModule->l('Pending', 'qlohotelreview'),
            self::QHR_STATUS_DISAPPROVED => $objModule->l('Disapproved', 'qlohotelreview'),
            self::QHR_STATUS_APPROVED => $objModule->l('Approved', 'qlohotelreview'),
        );
    }

    public static function cleanImagesDirectory()
    {
        $img_dir = _PS_MODULE_DIR_.'qlohotelreview/views/img/review/';
        $files = Tools::scandir($img_dir, '');
        foreach ($files as $file) {
            if (!in_array($file, array('.', '..', 'index.php'))) {
                if (!Tools::deleteDirectory($img_dir.$file)) {
                    return false;
                }
            }
        }
        return true;
    }

    public static function getAverageRatingByIdHotel($id_hotel)
    {
        $validate = Configuration::get('QHR_ADMIN_APPROVAL_ENABLED');
        $cache_id = 'QhrHotelReview::getAverageRatingByIdHotel_'.(int) $id_hotel.'-'.(int) $validate;
        if (!Cache::isStored($cache_id)) {
            $result = Db::getInstance()->getValue(
                'SELECT SUM(hr.`rating`) / COUNT(hr.`rating`) FROM `'._DB_PREFIX_.'qhr_hotel_review` hr
                WHERE hr.`id_hotel` = '.(int) ($id_hotel).
                ($validate == '1' ? ' AND hr.`status` = '. (int) self::QHR_STATUS_APPROVED : '')
            );
            Cache::store($cache_id, $result);
        }
        return Cache::retrieve($cache_id);
    }

    public static function getReviewCountByIdHotel($id_hotel)
    {
        $validate = Configuration::get('QHR_ADMIN_APPROVAL_ENABLED');
        $cache_id = 'QhrHotelReview::getCountByIdHotel_'.(int) $id_hotel.'-'.(int) $validate;
        if (!Cache::isStored($cache_id)) {
            $result = Db::getInstance()->getValue(
                'SELECT COUNT(*) FROM `'._DB_PREFIX_.'qhr_hotel_review` hr
                WHERE hr.`id_hotel` = '.(int) ($id_hotel).($validate == '1' ?
                ' AND hr.`status` = '. (int) self::QHR_STATUS_APPROVED : '')
            );
            Cache::store($cache_id, $result);
        }
        return Cache::retrieve($cache_id);
    }

    public function addCategoryRatings($categoryRatings)
    {
        if (!$categoryRatings) {
            return true;
        }
        // delete all first
        Db::getInstance()->delete('qhr_review_category_rating', 'id_hotel_review = '.(int) $this->id);

        // insert now
        $rows = array();
        foreach ($categoryRatings as $idCategory => $rating) {
            $rows[] = array(
                'id_hotel_review' => (int) $this->id,
                'id_category' => (int) $idCategory,
                'rating' => (float) $rating
            );
        }
        Db::getInstance()->insert('qhr_review_category_rating', $rows);
        return true;
    }

    public function addManagementReply($reply, $idEmployee = null)
    {
        if (!$reply) {
            return false;
        }

        if (!$idEmployee) {
            $idEmployee = (int) Context::getContext()->employee->id;
        }


        // delete all first
        Db::getInstance()->delete('qhr_review_reply', 'id_hotel_review = '.(int) $this->id);

        // insert now
        $row = array(
            'id_hotel_review' => (int) $this->id,
            'id_employee' => (int) $idEmployee,
            'message' => pSQL($reply),
            'date_add' => date('Y-m-d H:i:s'),
        );

        Db::getInstance()->insert('qhr_review_reply', $row);

        return true;
    }

    public function sendManagementReplyEmail()
    {
        if (!Validate::isLoadedObject($this)) {
            return false;
        }

        $objOrder = new Order($this->id_order);
        if (!Validate::isLoadedObject($objOrder)) {
            return false;
        }

        $objCustomer = new Customer($objOrder->id_customer);
        if (!Validate::isLoadedObject($objCustomer)) {
            return false;
        }

        $context = Context::getContext();
        $mailVars = array(
            '{firstname}' => $objCustomer->firstname,
            '{lastname}' => $objCustomer->lastname,
            '{hotel_name}' => QhrHotelReviewHelper::getHotelByOrder($this->id_order)['hotel_name'],
            '{review_subject}' => $this->subject,
            '{review_description}' => $this->description,
            '{management_reply}' => $this->getManagementReply()['message'],
        );

        $template = 'review_reply_without_link';
        if ($this->status == self::QHR_STATUS_APPROVED) {
            $template = 'review_reply_with_link';
            $mailVars['{review_view_url}'] = $this->getReviewViewLink();
        }

        return Mail::Send(
            $context->language->id,
            $template,
            Mail::l('Your review has a reply!', $context->language->id),
            $mailVars,
            $objCustomer->email,
            $objCustomer->firstname.' '.$objCustomer->lastname,
            null,
            null,
            null,
            null,
            _PS_MODULE_DIR_.'qlohotelreview/mails/',
            false,
            null,
            null
        );
    }

    public function getTotalReports()
    {
        $sql = new DbQuery();
        $sql->select('COUNT(*)');
        $sql->from('qhr_review_report', 'rr');
        $sql->where('rr.`id_hotel_review` = '.(int) $this->id);

        return Db::getInstance()->getValue($sql);
    }

    public function getCustomer()
    {
        if (!$this->id_order) {
            return false;
        }

        $objOrder = new Order($this->id_order);

        return $objOrder->getCustomer();
    }

    public function getManagementReply()
    {
        $sql = new DbQuery();
        $sql->select('*');
        $sql->from('qhr_review_reply', 'rr');
        $sql->where('rr.`id_hotel_review` = '.(int) $this->id);

        return Db::getInstance()->getRow($sql);
    }

    public function saveReviewImages()
    {
        $files = QhrHotelReviewHelper::fileAttachmentMultiple('images');
        if (is_array($files) && count($files)) {
            foreach ($files as $key => $file) {
                $ext = pathinfo($file['rename'], PATHINFO_EXTENSION);
                $dir = _PS_MODULE_DIR_.'qlohotelreview/views/img/review/'.Image::getImgFolderStatic($this->id);
                QhrHotelReviewHelper::createDirectory($dir);
                $useSameExt = false;
                if ($useSameExt) {
                    $imgPath = $dir.($key + 1).'.jpg';
                } else {
                    $imgPath = $dir.($key + 1).'.'.$ext;
                }
                ImageManager::resize($file['tmp_name'], $imgPath);
            }
        }
    }

    public function approveReview()
    {
        if (Validate::isLoadedObject($this)) {
            $this->status = self::QHR_STATUS_APPROVED;
            return $this->save();
        }

        return false;
    }

    public function sendApprovalEmail()
    {
        if (!Validate::isLoadedObject($this)) {
            return false;
        }

        $objOrder = new Order($this->id_order);
        if (!Validate::isLoadedObject($objOrder)) {
            return false;
        }

        $objCustomer = new Customer($objOrder->id_customer);
        if (!Validate::isLoadedObject($objCustomer)) {
            return false;
        }

        $context = Context::getContext();
        $mailVars = array(
            '{firstname}' => $objCustomer->firstname,
            '{lastname}' => $objCustomer->lastname,
            '{hotel_name}' => QhrHotelReviewHelper::getHotelByOrder($this->id_order)['hotel_name'],
            '{review_subject}' => $this->subject,
            '{review_description}' => $this->description,
            '{review_view_url}' => $this->getReviewViewLink(),
        );

        return Mail::Send(
            $context->language->id,
            'review_approve',
            Mail::l('Your review has been approved!', $context->language->id),
            $mailVars,
            $objCustomer->email,
            $objCustomer->firstname.' '.$objCustomer->lastname,
            null,
            null,
            null,
            null,
            _PS_MODULE_DIR_.'qlohotelreview/mails/',
            false,
            null,
            null
        );
    }

    public function disapproveReview()
    {
        if (Validate::isLoadedObject($this)) {
            $this->status = self::QHR_STATUS_DISAPPROVED;
            return $this->save();
        }

        return false;
    }

    public function markNotAbusive()
    {
        if (Validate::isLoadedObject($this)) {
            $this->status_abusive = self::QHR_STATUS_ABUSIVE_NOT_ABUSIVE;
            if ($this->save()) {
                return $this->removeAllReports();
            }
        }

        return false;
    }

    public function removeAllReports()
    {
        return Db::getInstance()->delete(
            'qhr_review_report',
            'id_hotel_review = '.(int) $this->id
        );
    }

    public static function getByCustomer($id_customer, $id_hotel = null, $id_order = null)
    {
        $cache_id = 'QhrHotelReview::getByCustomer_'.(int) $id_customer.'-'.(int) $id_hotel.'-'.(int) $id_order;
        if (!Cache::isStored($cache_id)) {
            $result = Db::getInstance()->executeS(
                'SELECT * FROM `'._DB_PREFIX_.'qhr_hotel_review` hr
                LEFT JOIN `'._DB_PREFIX_.'orders` o ON o.`id_order` = hr.`id_order`
                WHERE o.`id_customer` = '.(int) $id_customer.
                ($id_hotel ? ' AND hr.`id_hotel` = '.(int) $id_hotel : '').
                ($id_order ? ' AND hr.`id_order` = '.(int) $id_order : '').'
                ORDER BY hr.`date_add` DESC'
            );
            Cache::store($cache_id, $result);
        }
        return Cache::retrieve($cache_id);
    }

    public static function getByIdOrder($id_order)
    {
        $cache_id = 'QhrHotelReview::getByIdOrder_'.(int) $id_order;
        if (!Cache::isStored($cache_id)) {
            $result = (int) Db::getInstance()->getValue('
                SELECT `id_hotel_review`
                FROM `'._DB_PREFIX_.'qhr_hotel_review` hr
                WHERE hr.`id_order` = '.(int) $id_order
            );
            Cache::store($cache_id, $result);
        }
        return Cache::retrieve($cache_id);
    }

    public static function getByHotel(
        $id_hotel,
        $p = 1,
        $n = null,
        $sort_by = self::QHR_SORT_BY_TIME_NEW,
        $id_customer = null
    ) {
        if (!Validate::isUnsignedId($id_hotel)) {
            return false;
        }

        $id_lang = Context::getContext()->language->id;

        $validate = Configuration::get('QHR_ADMIN_APPROVAL_ENABLED');
        $p = (int) $p;
        $n = $n !== null ? (int) $n : $n; // n = null for no pagination
        if ($p <= 1) {
            $p = 1;
        }
        if ($n !== null && $n <= 0) {
            $n = (int) Configuration::get('QHR_REVIEWS_PER_PAGE');
        }

        $cache_id = 'QhrHotelReview::getByHotel_'.(int) $id_hotel.'-'.(int) $p.'-'.(int) $n.'-'.(int) $sort_by.
        (int) $id_customer.'-'.(bool) $validate;
        if (!Cache::isStored($cache_id)) {
            $sql = 'SELECT hr.*, CONCAT(c.`firstname`, " ", c.`lastname`) as `customer_name`,
            hbil.`hotel_name`, qrr.`id_employee`, qrr.`message`, qrr.`date_add` AS `reply_date`,
            (SELECT COUNT(*) FROM `'._DB_PREFIX_.'qhr_review_usefulness` ru
                WHERE ru.`id_hotel_review` = hr.`id_hotel_review`) AS `total_useful`,
            (SELECT COUNT(*) FROM `'._DB_PREFIX_.'qhr_review_report` rr
                WHERE rr.`id_hotel_review` = hr.`id_hotel_review`) AS `total_report`'.
            ((int) $id_customer ? ', (SELECT COUNT(*) FROM `'._DB_PREFIX_.'qhr_review_usefulness` ru
                WHERE ru.`id_hotel_review` = hr.`id_hotel_review`
                AND ru.id_customer = '.(int) $id_customer.') AS `response_helpful`' : '').
            ((int) $id_customer ? ', (SELECT COUNT(*) FROM `'._DB_PREFIX_.'qhr_review_report` rr
                WHERE rr.`id_hotel_review` = hr.`id_hotel_review`
                AND rr.id_customer = '.(int)$id_customer.') AS `response_report` ' : '').'
            FROM `'._DB_PREFIX_.'qhr_hotel_review` hr
            LEFT JOIN `'._DB_PREFIX_.'orders` o ON o.`id_order` = hr.`id_order`
            LEFT JOIN `'._DB_PREFIX_.'customer` c ON c.`id_customer` = o.`id_customer`
            LEFT JOIN `'._DB_PREFIX_.'qhr_review_reply` qrr ON qrr.`id_hotel_review` = hr.`id_hotel_review`
            LEFT JOIN `'._DB_PREFIX_.'htl_branch_info_lang` hbil
            ON hbil.`id` = hr.`id_hotel` AND hbil.`id_lang` = '.(int) $id_lang.'
            WHERE hr.`id_hotel` = '.(int) ($id_hotel).($validate == '1' ? ' AND
            hr.`status` = '.(int) self::QHR_STATUS_APPROVED : '');

            if ($sort_by == self::QHR_SORT_BY_RELEVANCE) {
                $sql .= ' ORDER BY `total_useful` DESC ';
            } elseif ($sort_by == self::QHR_SORT_BY_TIME_NEW) {
                $sql .= ' ORDER BY hr.`date_add` DESC ';
            } elseif ($sort_by == self::QHR_SORT_BY_TIME_OLD) {
                $sql .= ' ORDER BY hr.`date_add` ASC ';
            } elseif ($sort_by == self::QHR_SORT_BY_RATING_HIGH) {
                $sql .= ' ORDER BY hr.`rating` DESC ';
            } elseif ($sort_by == self::QHR_SORT_BY_RATING_LOW) {
                $sql .= ' ORDER BY hr.`rating` ASC ';
            }

            $sql .= ($n ? ' LIMIT '.(int) (($p - 1) * $n).', '.(int) ($n) : '');
            $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
            Cache::store($cache_id, $result);
        }
        return Cache::retrieve($cache_id);
    }

    public static function hasNextPage($id_hotel, $p, $n)
    {
        if (!Validate::isUnsignedId($id_hotel)) {
            return false;
        }

        $id_lang = Context::getContext()->language->id;

        $validate = Configuration::get('QHR_ADMIN_APPROVAL_ENABLED');
        $p = (int) $p + 1; // next page
        $n = (int) $n;

        $cache_id = 'QhrHotelReview::hasNextPage'.(int) $id_hotel.'-'.(int) $p.'-'.(int) $n.'-'.'-'.(bool) $validate;
        if (!Cache::isStored($cache_id)) {
            $sql = 'SELECT *
            FROM `'._DB_PREFIX_.'qhr_hotel_review` hr
            WHERE hr.`id_hotel` = '.(int) ($id_hotel).($validate == '1' ?
            ' AND hr.`status` = '. (int) self::QHR_STATUS_APPROVED : '');
            $sql .= ($n ? ' LIMIT '.(int) (($p - 1) * $n).', '.(int) ($n) : '');
            $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
            $result = (is_array($result) && count($result)) ? true : false;
            Cache::store($cache_id, $result);
        }

        return Cache::retrieve($cache_id);
    }

    public static function getSummaryByHotel($id_hotel)
    {
        $id_lang = Context::getContext()->language->id;
        $validate = Configuration::get('QHR_ADMIN_APPROVAL_ENABLED');
        $sql = 'SELECT (SUM(hr.`rating`) / COUNT(hr.`rating`)) AS `average`, MIN(hr.`rating`) AS `minimum`,
        MAX(hr.`rating`) AS `maximum`, COUNT(hr.`id_hotel_review`) AS `total_reviews`
        FROM `'._DB_PREFIX_.'qhr_hotel_review` hr
        WHERE hr.`id_hotel` = '.(int) ($id_hotel).($validate == '1' ?
        ' AND hr.`status` = '. (int) self::QHR_STATUS_APPROVED : '');
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);

        $sql2 = 'SELECT rcr.`id_category`, cl.`name`, SUM(rcr.`rating`) / COUNT(rcr.`rating`) AS `average`
        FROM `'._DB_PREFIX_.'qhr_review_category_rating` rcr
        LEFT JOIN `'._DB_PREFIX_.'qhr_hotel_review` hr
        ON hr.`id_hotel_review` = rcr.`id_hotel_review`
        LEFT JOIN `'._DB_PREFIX_.'qhr_category` c
        ON c.`id_category` = rcr.`id_category`
        LEFT JOIN `'._DB_PREFIX_.'qhr_category_lang` cl
        ON cl.`id_category` = rcr.`id_category` AND cl.`id_lang` = '.(int) $id_lang.'
        WHERE c.`active` = 1 AND hr.`id_hotel` = '.(int) ($id_hotel).
        ($validate == '1' ? ' AND hr.`status` = '. (int) self::QHR_STATUS_APPROVED : '').'
        GROUP BY hr.`id_hotel`, rcr.`id_category`';
        $result2 = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql2);
        $result['categories'] = $result2;

        return $result;
    }

    public function getImages()
    {
        if (!$this->id) {
            return false;
        }

        return self::getImagesById($this->id);
    }

    public static function getImagesById($id_hotel_review)
    {
        $full_img_dir = _PS_MODULE_DIR_.'qlohotelreview/views/img/review/'.Image::getImgFolderStatic($id_hotel_review);
        $files = glob($full_img_dir.'*');
        $files = array_filter($files, function ($item) {
            return !strpos($item, 'index.php');
        });

        foreach ($files as &$file) {
            $file = str_replace(_PS_ROOT_DIR_, '', $file);
            $file = ltrim($file, '/');
            $file = Context::getContext()->shop->getBaseUrl().$file;
        }
        return $files;
    }

    public static function getAllImages($id_hotel)
    {
        $images = array();
        $reviews = self::getByHotel($id_hotel);
        if (is_array($reviews) && count($reviews)) {
            foreach ($reviews as $review) {
                $images = array_merge($images, self::getImagesById($review['id_hotel_review']));
            }
        }
        return $images;
    }

    public static function getCategoryRatings($id_hotel_review)
    {
        if (!$id_hotel_review) {
            return false;
        }

        $id_lang = Context::getContext()->language->id;

        return Db::getInstance()->executeS(
            'SELECT * FROM `'._DB_PREFIX_.'qhr_category` qc
            LEFT JOIN `'._DB_PREFIX_.'qhr_category_lang` qcl
            ON qcl.`id_category` = qc.`id_category` AND qcl.`id_lang` = '.(int) $id_lang.'
            LEFT JOIN `'._DB_PREFIX_.'qhr_review_category_rating` qrcr
            ON qrcr.`id_category` = qc.`id_category`
            WHERE qc.`active` = 1 AND qrcr.`id_hotel_review` = '.(int) $id_hotel_review
        );
    }

    public static function isAlreadyMarkedHelpful($id_hotel_review, $id_customer)
    {
        return (bool) Db::getInstance()->getValue('
            SELECT COUNT(*)
            FROM `'._DB_PREFIX_.'qhr_review_usefulness`
            WHERE `id_customer` = '.(int) $id_customer.'
            AND `id_hotel_review` = '.(int) $id_hotel_review
        );
    }

    public static function markHelpful($id_hotel_review, $id_customer)
    {
        return Db::getInstance()->execute('
            INSERT INTO `'._DB_PREFIX_.'qhr_review_usefulness` (`id_hotel_review`, `id_customer`)
            VALUES ('.(int) $id_hotel_review.', '.(int) $id_customer.')'
        );
    }

    public static function isAlreadyReportedAbuse($id_hotel_review, $id_customer)
    {
        return (bool) Db::getInstance()->getValue('
            SELECT COUNT(*)
            FROM `'._DB_PREFIX_.'qhr_review_report`
            WHERE `id_customer` = '.(int) $id_customer.'
            AND `id_hotel_review` = '.(int) $id_hotel_review
        );
    }

    public static function reportAbuse($id_hotel_review, $id_customer)
    {
        return Db::getInstance()->execute('
            INSERT INTO `'._DB_PREFIX_.'qhr_review_report` (`id_hotel_review`, `id_customer`)
            VALUES ('.(int) $id_hotel_review.', '.(int) $id_customer.')'
        );
    }

    public function getReviewViewLink()
    {
        $objHotelBookingDetail = new HotelBookingDetail();
        $bookingData = $objHotelBookingDetail->getBookingDataByOrderId($this->id_order);
        if (!$bookingData) {
            return '#';
        }

        return Context::getContext()->link->getProductLink($bookingData[0]['id_product']);
    }
}
