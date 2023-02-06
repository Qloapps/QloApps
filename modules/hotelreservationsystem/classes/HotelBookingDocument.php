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

class HotelBookingDocument extends ObjectModel
{
    public $id_htl_booking_document;
    public $id_htl_booking;
    public $file_type;
    public $file_name;
    public $date_add;
    public $date_upd;

    const FILE_TYPE_IMAGE = 1;
    const FILE_TYPE_PDF = 2;

    const DOWNLOAD_FILE_PREFIX = 'checkin_document_';

    protected static $accessRights = 0775;

    public static $definition = array(
        'table' => 'htl_booking_document',
        'primary' => 'id_htl_booking_document',
        'fields' => array(
            'id_htl_booking' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'file_type' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'file_name' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
        ),
    );

    public function __construct($id = null)
    {
        $this->file_name = '';
        parent::__construct($id);

        $this->documentsBaseDir = _PS_MODULE_DIR_.'hotelreservationsystem/documents/booking_guests/';
        $this->sourceIndexFile = $this->documentsBaseDir.'index.php';
    }

    public function save($null_values = false, $auto_date = true)
    {
        parent::save($null_values, $auto_date);

        $this->documentsBaseDir = _PS_MODULE_DIR_.'hotelreservationsystem/documents/booking_guests/';
        $this->sourceIndexFile = $this->documentsBaseDir.'index.php';

        if ($this->id) {
            $this->setFileName();
            return $this->update();
        }

        return false;
    }

    public static function getDocumentsByIdHtlBooking($idHtlBooking)
    {
        return Db::getInstance()->executeS(
            'SELECT *
            FROM `'._DB_PREFIX_.'htl_booking_document`
            WHERE `id_htl_booking` = '.(int) $idHtlBooking
        );
    }

    public static function getDocumentsByIdOrder($idOrder)
    {
        return Db::getInstance()->executeS(
            'SELECT hbdo.*
            FROM `'._DB_PREFIX_.'htl_booking_document` hbdo
            INNER JOIN `'._DB_PREFIX_.'htl_booking_detail` hbde
            ON (hbde.`id` = hbdo.`id_htl_booking`)
            WHERE hbde.`id_order` = '.(int) $idOrder
        );
    }

    public function setFileInfoForUploadedDocument($fileName)
    {
        $this->fileInfo = Tools::fileAttachment($fileName, false);
    }

    public function saveDocumentFile()
    {
        if (!$this->id) {
            return false;
        }

        $this->setDocumentFolder();
        $this->createDocumentFolder();

        @move_uploaded_file($this->fileInfo['tmp_name'], $this->getDestinationFilePath());
    }

    public function setDocumentFolder()
    {
        if (!isset($this->documentFolder)) {
            $this->documentFolder = $this->documentsBaseDir.Image::getImgFolderStatic($this->id);
        }
    }

    public function createDocumentFolder()
    {
        if (!file_exists($this->documentFolder)) {
            // Trying both methods for setting access rights
            $mkdir = @mkdir($this->documentFolder, self::$accessRights, true);
            $chmod = @chmod($this->documentFolder, self::$accessRights);

            // Copy index.php file in the new folder
            if (($mkdir || $chmod)
                && !file_exists($this->documentFolder.'index.php')
                && file_exists($this->sourceIndexFile)
            ) {
                return @copy($this->sourceIndexFile, $this->documentFolder.'index.php');
            }
        }

        return true;
    }

    public function getDestinationFilePath()
    {
        $fileExtension = pathinfo($this->fileInfo['rename'], PATHINFO_EXTENSION);

        return $this->documentFolder.$this->id.'.'.$fileExtension;
    }

    public function setFileType()
    {
        if (ImageManager::isRealImage($this->fileInfo['tmp_name'])) {
            $this->file_type = self::FILE_TYPE_IMAGE;
        } elseif ($this->fileInfo['mime'] == 'application/pdf') {
            $this->file_type = self::FILE_TYPE_PDF;
        } else {
            $this->file_type = 0;
        }
    }

    public function setFileName()
    {
        $fileExtension = pathinfo($this->fileInfo['rename'], PATHINFO_EXTENSION);
        $this->file_name = $this->id.'.'.$fileExtension;
    }

    public function getContentType()
    {
        $file = $this->getPhysicalPath();

        $contentType = 'application/octet-stream';
        if (function_exists('finfo_open')) {
            $fileInfo = @finfo_open(FILEINFO_MIME);
            $contentType = @finfo_file($fileInfo, $file);
            @finfo_close($fileInfo);
        } elseif (function_exists('mime_content_type')) {
            $contentType = @mime_content_type($file);
        } elseif (function_exists('exec')) {
            $contentType = trim(@exec('file -b --mime-type '.escapeshellarg($file)));
            if (!$contentType) {
                $contentType = trim(@exec('file --mime '.escapeshellarg($file)));
            }
            if (!$contentType) {
                $contentType = trim(@exec('file -bi '.escapeshellarg($file)));
            }
        }

        return $contentType;
    }

    public function getContentLength()
    {
        $file = $this->getPhysicalPath();

        if (Tools::file_exists_cache($file)) {
            return filesize($file);
        }

        return 0;
    }

    public function getPhysicalPath()
    {
        $this->setDocumentFolder();

        return $this->documentFolder.$this->file_name;
    }

    public function getDownloadFileName()
    {
        $objHotelBookingDetail = new HotelBookingDetail($this->id_htl_booking);
        $fileExtension = pathinfo($this->file_name, PATHINFO_EXTENSION);

        return self::DOWNLOAD_FILE_PREFIX.$this->id_htl_booking_document.'_'.date('YmdHis').'.'.$fileExtension;
    }

    public function deleteDocumentFolder()
    {
        $this->setDocumentFolder();

        return Tools::deleteDirectory($this->documentFolder);
    }

    public function delete()
    {
        $this->deleteDocumentFolder();

        return parent::delete();
    }
}
