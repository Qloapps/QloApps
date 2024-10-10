<?php
/**
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License version 3.0
* that is bundled with this package in the file LICENSE.md
* It is also available through the world-wide-web at this URL:
* https://opensource.org/license/osl-3-0-php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to support@qloapps.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to a newer
* versions in the future. If you wish to customize this module for your needs
* please refer to https://store.webkul.com/customisation-guidelines for more information.
*
* @author Webkul IN
* @copyright Since 2010 Webkul
* @license https://opensource.org/license/osl-3-0-php Open Software License version 3.0
*/

class QhrHotelReviewHelper
{
    public static function getIsOrderCheckedOut($idOrder)
    {
        if (!$idOrder) {
            return false;
        }

        return (bool) !Db::getInstance()->getValue(
            'SELECT COUNT(*) FROM `'._DB_PREFIX_.'htl_booking_detail` hbd
            WHERE hbd.`id_status` != '.(int) HotelBookingDetail::STATUS_CHECKED_OUT.'
            AND hbd.`is_refunded` != 1
            AND hbd.`is_cancelled` != 1
            AND hbd.`id_order` = '.(int) $idOrder
        );
    }

    public static function prepareCategoriesData($summary)
    {
        if (!$summary) {
            return false;
        }

        foreach ($summary['categories'] as &$category) {
            if ($category['average'] <= (float) 2) {
                $category['color'] = '#EC4040';
            } elseif ($category['average'] <= (float) 3.5) {
                $category['color'] = '#FFBD37';
            } else {
                $category['color'] = '#449F3A';
            }
        }
        return $summary;
    }

    public static function getIsReviewable($idOrder)
    {
        if (!$idOrder) {
            return false;
        }

        $objOrder = new Order($idOrder);
        if ($objOrder->hasCompletelyRefunded()) {
            return false;
        }

        if (self::getIsOrderCheckedOut($idOrder)) {
            return true;
        }

        $maxDate = Db::getInstance()->getValue(
            'SELECT MAX(DATE(hbd.`date_to`)) FROM `'._DB_PREFIX_.'htl_booking_detail` hbd
            WHERE hbd.`id_order` = '.(int) $idOrder
        );
        return date('Y-m-d') >= $maxDate;
    }

    public static function sendReviewRequestMail($idOrder)
    {
        $objOrder = new Order((int)$idOrder);
        if (Validate::isLoadedObject($objOrder)) {
            $context = Context::getContext();
            $mailVars = array();
            $objCustomer = new Customer((int)$objOrder->id_customer);
            $mailVars = array(
                '{firstname}' => $objCustomer->firstname,
                '{lastname}' => $objCustomer->lastname,
                '{hotel_name}' => self::getHotelByOrder($idOrder)['hotel_name'],
                '{review_link}' => self::generateReviewLink($idOrder),
            );

            return Mail::Send(
                $context->language->id,
                'review_request',
                Mail::l('Please review us!', $context->language->id),
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
        return false;
    }

    public static function generateReviewLink($idOrder)
    {
        $objOrder = new Order($idOrder);
        $objCustomer = new Customer($objOrder->id_customer);
        if ($objCustomer->isGuest()) {
            return Context::getContext()->link->getPageLink('guest-tracking', true, null);
        }
        return Context::getContext()->link->getPageLink('history', true, null, array('id_order' => $idOrder));
    }

    public static function getHotelByOrder($idOrder)
    {
        return Db::getInstance()->getRow(
            'SELECT `id_hotel`, `hotel_name` FROM `'._DB_PREFIX_.'htl_booking_detail`
            WHERE `id_order` = '.(int) $idOrder
        );
    }

    public static function createDirectory($dir)
    {
        if (!file_exists($dir)) {
            if (!mkdir($dir)) {
                return false;
            }
            $src = _PS_MODULE_DIR_.'qlohotelreview/index.php';
            $dest = $dir.'index.php';
            copy($src, $dest);
        }
        return true;
    }

    public static function fileAttachmentMultiple($input)
    {
        $file_attachments = null;
        if (isset($_FILES[$input]['tmp_name']) && !empty($_FILES[$input]['tmp_name'])) {
            foreach ($_FILES[$input]['tmp_name'] as $key => $tmp_name) {
                $file_attachment = array();
                $file_attachment['rename'] = uniqid().
                Tools::strtolower(Tools::substr($_FILES[$input]['name'][$key], -5));
                $file_attachment['tmp_name'] = $tmp_name;
                $file_attachment['name'] = $_FILES[$input]['name'][$key];
                $file_attachment['mime'] = $_FILES[$input]['type'][$key];
                $file_attachment['error'] = $_FILES[$input]['error'][$key];
                $file_attachment['size'] = $_FILES[$input]['size'][$key];
                $file_attachments[] = $file_attachment;
            }
        }
        return $file_attachments;
    }
}
