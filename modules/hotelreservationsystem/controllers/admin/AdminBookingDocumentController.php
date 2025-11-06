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

class AdminBookingDocumentController extends ModuleAdminController
{
    public function processGetDocument()
    {
        $idHtlBookingDocument = (int) Tools::getValue('id_document');
        $objHotelBookingDocument = new HotelBookingDocument($idHtlBookingDocument);
        if (Validate::isLoadedObject($objHotelBookingDocument)) {
            Hook::exec('actionDownloadBookingDocument', array('attachment' => &$objHotelBookingDocument));

            $contentType = $objHotelBookingDocument->getContentType();
            $contentLength = $objHotelBookingDocument->getContentLength();
            $filePath = $objHotelBookingDocument->getPhysicalPath();
            $downloadFileName = $objHotelBookingDocument->getDownloadFileName();
            $contentDisposition = Tools::getValue('is_preview') ? 'inline' : 'attachment';

            if (Tools::file_exists_cache($filePath)) {
                if (ob_get_level() && ob_get_length() > 0) {
                    ob_end_clean();
                }

                header('Content-Transfer-Encoding: binary');
                header('Content-Type: '.$contentType);
                header('Content-Length: '.$contentLength);
                header('Content-Disposition: '.$contentDisposition.'; filename="'.$downloadFileName.'"');
                @set_time_limit(0);
                readfile($filePath);
            }

            exit;
        }

        header('HTTP/1.1 404 Not Found');
        header('Status: 404 Not Found');
    }

    public function postProcess()
    {
        parent::postProcess();
        exit;
    }
}
