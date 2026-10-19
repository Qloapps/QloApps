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

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Stores homepage header image items for the image slider.
 * Video configuration is stored as Configuration keys (QLO_HEADER_VIDEO_SOURCE_TYPE,
 * QLO_HEADER_VIDEO_NAME) rather than as rows in this table, since only one video exists.
 */
class HotelHeaderImage extends ObjectModel
{
    const MEDIA_TYPE_IMAGE = 1;
    const MEDIA_TYPE_VIDEO = 2;

    const NAV_TYPE_DOTS   = 1;
    const NAV_TYPE_ARROWS = 2;
    const NAV_TYPE_BOTH   = 3;

    const ANIMATION_TYPE_SLIDE = 1;
    const ANIMATION_TYPE_FADE  = 2;
    const ANIMATION_TYPE_ZOOM  = 3;
    const ANIMATION_TYPE_BLUR  = 4;

    const CONTENT_ALIGN_LEFT   = 1;
    const CONTENT_ALIGN_CENTER = 2;
    const CONTENT_ALIGN_RIGHT  = 3;
    const TITLE_LIMIT = 128;

    public $name;
    public $position;
    public $title;
    public $description;
    public $description_color       = '#ffffff';
    public $description_font_size   = 16;
    public $description_font_weight = '400';
    public $active;
    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table'     => 'htl_header_image',
        'primary'   => 'id_header_image',
        'multilang' => true,
        'fields'    => array(
            'name'                     => array('type' => self::TYPE_STRING, 'size' => 512),
            'title'                    => array('type' => self::TYPE_STRING, 'lang' => true, 'size' => self::TITLE_LIMIT),
            'description'              => array('type' => self::TYPE_STRING, 'lang' => true, 'size' => 512),
            'description_color'       => array('type' => self::TYPE_STRING, 'validate' => 'isColor', 'size' => 7),
            'description_font_size'   => array('type' => self::TYPE_INT,    'validate' => 'isUnsignedInt'),
            'description_font_weight' => array('type' => self::TYPE_STRING, 'size' => 10),
            'position'                 => array('type' => self::TYPE_INT,    'validate' => 'isUnsignedInt'),
            'active'                   => array('type' => self::TYPE_BOOL,   'validate' => 'isBool'),
            'date_add'                 => array('type' => self::TYPE_DATE,   'validate' => 'isDate'),
            'date_upd'                 => array('type' => self::TYPE_DATE,   'validate' => 'isDate'),
        ),
    );

    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        $this->image_dir = _PS_IMG_DIR_.'hotel_header_media/';
        parent::__construct($id, $id_lang, $id_shop);
    }

    /**
     * @inheritdoc
     */
    public function delete()
    {
        if (!$this->deleteImageFile()
            || !parent::delete()
            || !$this->cleanPositions()
        ) {
            return false;
        }
        return true;
    }

    /**
     * Deletes the physical image file from disk.
     *
     * @return bool
     */
    public function deleteImageFile()
    {
        if (!$this->name) {
            return true;
        }
        $filePath = $this->image_dir.$this->name;
        if (file_exists($filePath) && !unlink($filePath)) {
            return false;
        }
        return true;
    }

    /**
     * Fetches image rows, optionally filtered by active status.
     *
     * @param int|null  $active       1 = active only, 0 = inactive only, null = all
     * @param int|null  $idLang       Language id for title/description; null = context default
     * @param bool      $withAllLangs true = also include titles[id_lang]/descriptions[id_lang] maps (admin edit forms)
     * @return array
     */
    public static function getItems($active = 1, $idLang = null, $withAllLangs = false)
    {
        if (!$idLang) {
            $idLang = (int)Context::getContext()->language->id;
        }

        if (!$withAllLangs) {
            $sql = 'SELECT m.*, IFNULL(ml.`title`, \'\') AS `title`, IFNULL(ml.`description`, \'\') AS `description`
                    FROM `'._DB_PREFIX_.'htl_header_image` m
                    LEFT JOIN `'._DB_PREFIX_.'htl_header_image_lang` ml
                        ON (m.`id_header_image` = ml.`id_header_image`
                            AND ml.`id_lang` = '.(int)$idLang.')';
            if ($active !== null) {
                $sql .= ' WHERE m.`active` = '.(int)$active;
            }
            $sql .= ' ORDER BY m.`position` ASC';
            return Db::getInstance()->executeS($sql) ?: array();
        }

        $sql = 'SELECT m.*, ml.`id_lang`, IFNULL(ml.`title`, \'\') AS `lang_title`, IFNULL(ml.`description`, \'\') AS `lang_description`
                FROM `'._DB_PREFIX_.'htl_header_image` m
                LEFT JOIN `'._DB_PREFIX_.'htl_header_image_lang` ml
                    ON m.`id_header_image` = ml.`id_header_image`';
        if ($active !== null) {
            $sql .= ' WHERE m.`active` = '.(int)$active;
        }
        $sql .= ' ORDER BY m.`position` ASC, ml.`id_lang` ASC';

        $rows = Db::getInstance()->executeS($sql);
        if (!$rows) {
            return array();
        }

        $itemsMap = array();
        foreach ($rows as $row) {
            $id = (int)$row['id_header_image'];
            if (!isset($itemsMap[$id])) {
                $itemsMap[$id] = $row;
                $itemsMap[$id]['title'] = '';
                $itemsMap[$id]['titles'] = array();
                $itemsMap[$id]['description'] = '';
                $itemsMap[$id]['descriptions'] = array();
                unset($itemsMap[$id]['id_lang'], $itemsMap[$id]['lang_title'], $itemsMap[$id]['lang_description']);
            }
            if (isset($row['id_lang'])) {
                $langId = (int)$row['id_lang'];
                $title = $row['lang_title'];
                $description = $row['lang_description'];
                $itemsMap[$id]['titles'][$langId] = $title;
                $itemsMap[$id]['descriptions'][$langId] = $description;
                if ($langId === (int)$idLang) {
                    $itemsMap[$id]['title'] = $title;
                    $itemsMap[$id]['description'] = $description;
                }
            }
        }

        return array_values($itemsMap);
    }

    /**
     * Returns the next available position value (MAX + 1).
     *
     * @return int
     */
    public function getHigherPosition()
    {
        $position = Db::getInstance()->getValue(
            'SELECT MAX(`position`) FROM `'._DB_PREFIX_.'htl_header_image`'
        );
        return (is_numeric($position) ? (int)$position : -1) + 1;
    }

    /**
     * Resequences position values to be contiguous (0-based) after a deletion.
     * Uses a single CASE...WHEN UPDATE instead of N individual queries.
     *
     * @return bool
     */
    public function cleanPositions()
    {
        $items = Db::getInstance()->executeS(
            'SELECT `id_header_image` FROM `'._DB_PREFIX_.'htl_header_image` ORDER BY `position` ASC'
        );
        if (!$items) {
            return true;
        }
        $cases = '';
        $ids   = array();
        foreach ($items as $i => $item) {
            $id     = (int)$item['id_header_image'];
            $cases .= ' WHEN '.$id.' THEN '.$i;
            $ids[]  = $id;
        }
        return (bool)Db::getInstance()->execute(
            'UPDATE `'._DB_PREFIX_.'htl_header_image`
            SET `position` = CASE `id_header_image`'.$cases.' END
            WHERE `id_header_image` IN ('.implode(',', $ids).')'
        );
    }

    /**
     * Returns the stored video configuration, or null when no video is set.
     *
     * @return array|null  ['source_type' => 'upload'|'url', 'name' => string]
     */
    public static function getVideoConfig()
    {
        $name = Configuration::get('QLO_HEADER_VIDEO_NAME');
        $sourceType = Configuration::get('QLO_HEADER_VIDEO_SOURCE_TYPE');
        if (!$sourceType || !$name) {
            return null;
        }
        return array('source_type' => $sourceType, 'name' => $name);
    }

    /**
     * Persists video source type and name/url to Configuration.
     *
     * @param string $sourceType  'upload' or 'url'
     * @param string $name        Filename (upload) or full URL (url)
     * @return bool
     */
    public static function saveVideoConfig($sourceType, $name)
    {
        return Configuration::updateValue('QLO_HEADER_VIDEO_SOURCE_TYPE', pSQL($sourceType))
            && Configuration::updateValue('QLO_HEADER_VIDEO_NAME', pSQL($name));
    }

    /**
     * Clears video configuration and deletes the uploaded file from disk if applicable.
     *
     * @return bool
     */
    public static function deleteVideoConfig()
    {
        $sourceType = Configuration::get('QLO_HEADER_VIDEO_SOURCE_TYPE');
        $name       = Configuration::get('QLO_HEADER_VIDEO_NAME');
        if ($sourceType === 'upload' && $name) {
            $filePath = _PS_IMG_DIR_.'hotel_header_media/'.$name;
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }
        Configuration::updateValue('QLO_HEADER_VIDEO_SOURCE_TYPE', '');
        Configuration::updateValue('QLO_HEADER_VIDEO_NAME', '');
        return true;
    }

    /**
     * Creates the upload directory with security controls if it does not exist.
     *
     * @return bool
     */
    public static function createMediaDirectory()
    {
        $dir = _PS_IMG_DIR_.'hotel_header_media/';
        if (!is_dir($dir) && !mkdir($dir, 0755, true)) {
            return false;
        }
        if (!file_exists($dir.'index.php') && file_exists(_PS_IMG_DIR_.'index.php')) {
            copy(_PS_IMG_DIR_.'index.php', $dir.'index.php');
        }
        if (!file_exists($dir.'.htaccess')) {
            file_put_contents(
                $dir.'.htaccess',
                "Options -ExecCGI\nAddHandler cgi-script .php .php3 .php4 .php5 .phtml .pl .py .jsp .asp .cgi\nphp_flag engine off\n"
            );
        }
        return true;
    }
}
