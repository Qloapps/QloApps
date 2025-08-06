<?php
/**
 * 2010-2023 Webkul.
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
 * @copyright 2010-2023 Webkul IN
 * @license LICENSE.txt
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
