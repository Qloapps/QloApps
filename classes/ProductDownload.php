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

class ProductDownloadCore extends ObjectModel
{
    /** @var int Product id which download belongs */
    public $id_product;

    /** @var string DisplayFilename the name which appear */
    public $display_filename;

    /** @var string PhysicallyFilename the name of the file on hard disk */
    public $filename;

    /** @var string DateDeposit when the file is upload */
    public $date_add;

    /** @var string DateExpiration deadline of the file */
    public $date_expiration;

    /** @var string NbDaysAccessible how many days the customer can access to file */
    public $nb_days_accessible;

    /** @var string NbDownloadable how many time the customer can download the file */
    public $nb_downloadable;

    /** @var bool Active if file is accessible or not */
    public $active = 1;

    /** @var bool is_shareable indicates whether the product can be shared */
    public $is_shareable = 0;

    protected static $_productIds = array();

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'product_download',
        'primary' => 'id_product_download',
        'fields' => array(
            'id_product' =>            array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'display_filename' =>        array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 255),
            'filename' =>                array('type' => self::TYPE_STRING, 'validate' => 'isSha1', 'size' => 255),
            'date_add' =>                array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_expiration' =>        array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'nb_days_accessible' =>    array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'size' => 10),
            'nb_downloadable' =>        array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'size' => 10),
            'active' =>                array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'is_shareable' =>            array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
        ),
    );

    /**
     * Build a virtual product
     *
     * @param int $id_product_download Existing productDownload id in order to load object (optional)
     */
    public function __construct($id_product_download = null)
    {
        parent::__construct($id_product_download);
        // @TODO check if the file is present on hard drive
    }

    /**
     * @see ObjectModel::getFields()
     * @return array
     */
    public function getFields()
    {
        $fields = parent::getFields();
        if (!$fields['date_expiration']) {
            $fields['date_expiration'] = '0000-00-00 00:00:00';
        }

        return $fields;
    }

    public function add($autodate = true, $null_values = false)
    {
        return (bool)parent::add($autodate, $null_values);
    }

    public function update($null_values = false)
    {
        if (parent::update($null_values)) {
            // Refresh cache of feature detachable because the row can be deactive
            Configuration::updateGlobalValue('PS_VIRTUAL_PROD_FEATURE_ACTIVE', ProductDownload::isCurrentlyUsed($this->def['table'], true));
            return true;
        }
        return false;
    }

    public function delete($delete_file = false)
    {
        $result = parent::delete();
        if ($result && $delete_file) {
            return $this->deleteFile();
        }
        return $result;
    }

    /**
     * Delete the file
     * @param int $id_product_download : if we need to delete a specific product attribute file
     *
     * @return bool
     */
    public function deleteFile($id_product_download = null)
    {
        if (!$this->checkFile()) {
            return false;
        }

        return unlink(_PS_DOWNLOAD_DIR_.$this->filename)
            && Db::getInstance()->delete('product_download', 'id_product_download = '.(int)$id_product_download);
    }

    /**
     * Check if file exists
     *
     * @return bool
     */
    public function checkFile()
    {
        if (!$this->filename) {
            return false;
        }
        return file_exists(_PS_DOWNLOAD_DIR_.$this->filename);
    }

    /**
     * Check if download repository is writable
     *
     * @return bool
     */
    public static function checkWritableDir()
    {
        return is_writable(_PS_DOWNLOAD_DIR_);
    }

    /**
     * Return the id_product_download from an id_product
     *
     * @param int $id_product Product the id
     * @return int Product the id for this virtual product
     */
    public static function getIdFromIdProduct($id_product, $active = true)
    {
        if (!ProductDownload::isFeatureActive()) {
            return false;
        }
        if (array_key_exists((int)$id_product, self::$_productIds)) {
            return self::$_productIds[$id_product];
        }
        self::$_productIds[$id_product] = (int)Db::getInstance()->getValue('
		SELECT `id_product_download`
		FROM `'._DB_PREFIX_.'product_download`
		WHERE `id_product` = '.(int)$id_product.'
		'.($active ? ' AND `active` = 1' : '').'
		ORDER BY `id_product_download` DESC');

        return self::$_productIds[$id_product];
    }

    /**
     * Return the display filename from a physical filename
     *
     * @since 1.5.0.1
     *
     * @param string $filename Filename physically
     * @return int Product the id for this virtual product
     *
     */
    public static function getIdFromFilename($filename)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
		SELECT `id_product_download`
		FROM `'._DB_PREFIX_.'product_download`
		WHERE `filename` = \''.pSQL($filename).'\'');
    }

    /**
     * Return the filename from an id_product
     *
     * @param int $id_product Product the id
     * @return string Filename the filename for this virtual product
     */
    public static function getFilenameFromIdProduct($id_product)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
			SELECT `filename`
			FROM `'._DB_PREFIX_.'product_download`
			WHERE `id_product` = '.(int)$id_product.'
				AND `active` = 1
		');
    }

    /**
     * Return the display filename from a physical filename
     *
     * @param string $filename Filename physically
     * @return string Filename the display filename for this virtual product
     */
    public static function getFilenameFromFilename($filename)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
		SELECT `display_filename`
		FROM `'._DB_PREFIX_.'product_download`
		WHERE `filename` = \''.pSQL($filename).'\'');
    }

    /**
     * Return html link
     *
     * @param string $class CSS selector (optionnal)
     * @param bool $admin specific to backend (optionnal)
     * @param string $hash hash code in table order detail (optionnal)
     * @return string Html all the code for print a link to the file
     */
    public function getTextLink($admin = true, $hash = false)
    {
        $key = $this->filename.'-'.($hash ? $hash : 'orderdetail');
        $link = ($admin) ? 'get-file-admin.php?' : _PS_BASE_URL_.__PS_BASE_URI__.'index.php?controller=get-file&';
        $link .= ($admin) ? 'file='.$this->filename : 'key='.$key;
        return $link;
    }

    /**
     * Return html link
     *
     * @param string $class CSS selector
     * @param bool $admin specific to backend
     * @param bool $hash hash code in table order detail
     * @return string Html all the code for print a link to the file
     */
    public function getHtmlLink($class = false, $admin = true, $hash = false)
    {
        $link = $this->getTextLink($admin, $hash);
        $html = '<a href="'.$link.'" title=""';
        if ($class) {
            $html .= ' class="'.$class.'"';
        }
        $html .= '>'.$this->display_filename.'</a>';
        return $html;
    }

    /**
     * Return a deadline
     *
     * @return string Datetime in SQL format
     */
    public function getDeadline()
    {
        if (!(int)$this->nb_days_accessible) {
            return '0000-00-00 00:00:00';
        }
        $timestamp = strtotime('+'.(int)$this->nb_days_accessible.' day');
        return date('Y-m-d H:i:s', $timestamp);
    }

    /**
     * Return a hash for control download access
     *
     * @return string Hash ready to insert in database
     */
    public function getHash()
    {
        // TODO check if this hash not already in database
        return sha1(microtime().$this->id);
    }

    /**
     * Return a sha1 filename
     *
     * @return string Sha1 unique filename
     */
    public static function getNewFilename()
    {
        do {
            $filename = sha1(microtime());
        } while (file_exists(_PS_DOWNLOAD_DIR_.$filename));
        return $filename;
    }

    /**
     * This method is allow to know if a feature is used or active
     * @since 1.5.0.1
     * @return bool
     */
    public static function isFeatureActive()
    {
        return Configuration::get('PS_VIRTUAL_PROD_FEATURE_ACTIVE');
    }
}
