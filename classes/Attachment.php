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

class AttachmentCore extends ObjectModel
{
    public $file;
    public $file_name;
    public $file_size;
    public $name;
    public $mime;
    public $description;

    /** @var int position */
    public $position;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'attachment',
        'primary' => 'id_attachment',
        'multilang' => true,
        'fields' => array(
            'file' =>            array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true, 'size' => 40),
            'mime' =>            array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'required' => true, 'size' => 128),
            'file_name' =>        array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 128),
            'file_size' =>        array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),

            /* Lang fields */
            'name' =>            array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'required' => true, 'size' => 32),
            'description' =>    array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCleanHtml'),
        ),
    );

    public function add($autodate = true, $null_values = false)
    {
        $this->file_size = filesize(_PS_DOWNLOAD_DIR_.$this->file);
        return parent::add($autodate, $null_values);
    }

    public function update($null_values = false)
    {
        $this->file_size = filesize(_PS_DOWNLOAD_DIR_.$this->file);
        return parent::update($null_values);
    }

    public function delete()
    {
        @unlink(_PS_DOWNLOAD_DIR_.$this->file);

        $products = Db::getInstance()->executeS('
		SELECT id_product
		FROM '._DB_PREFIX_.'product_attachment
		WHERE id_attachment = '.(int)$this->id);

        Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'product_attachment WHERE id_attachment = '.(int)$this->id);

        foreach ($products as $product) {
            Product::updateCacheAttachment((int)$product['id_product']);
        }

        return parent::delete();
    }

    public function deleteSelection($attachments)
    {
        $return = 1;
        foreach ($attachments as $id_attachment) {
            $attachment = new Attachment((int)$id_attachment);
            $return &= $attachment->delete();
        }
        return $return;
    }

    public static function getAttachments($id_lang, $id_product, $include = true)
    {
        return Db::getInstance()->executeS('
			SELECT *
			FROM '._DB_PREFIX_.'attachment a
			LEFT JOIN '._DB_PREFIX_.'attachment_lang al
				ON (a.id_attachment = al.id_attachment AND al.id_lang = '.(int)$id_lang.')
			WHERE a.id_attachment '.($include ? 'IN' : 'NOT IN').' (
				SELECT pa.id_attachment
				FROM '._DB_PREFIX_.'product_attachment pa
				WHERE id_product = '.(int)$id_product.'
			)'
        );
    }

    /**
     * Unassociate $id_product from the current object
     *
     * @param $id_product int
     * @return bool
     */
    public static function deleteProductAttachments($id_product)
    {
        $res = Db::getInstance()->execute('
		DELETE FROM '._DB_PREFIX_.'product_attachment
		WHERE id_product = '.(int)$id_product);

        Product::updateCacheAttachment((int)$id_product);

        return $res;
    }

    /**
     * associate $id_product to the current object.
     *
     * @param int $id_product id of the product to associate
     * @return bool true if succed
     */
    public function attachProduct($id_product)
    {
        $res = Db::getInstance()->execute('
			INSERT INTO '._DB_PREFIX_.'product_attachment
				(id_attachment, id_product) VALUES
				('.(int)$this->id.', '.(int)$id_product.')');

        Product::updateCacheAttachment((int)$id_product);

        return $res;
    }

    /**
     * Associate an array of id_attachment $array to the product $id_product
     * and remove eventual previous association
     *
     * @param $id_product
     * @param $array
     * @return bool
     */
    public static function attachToProduct($id_product, $array)
    {
        $result1 = Attachment::deleteProductAttachments($id_product);

        if (is_array($array)) {
            $ids = array();
            foreach ($array as $id_attachment) {
                if ((int)$id_attachment > 0) {
                    $ids[] = array('id_product' => (int)$id_product, 'id_attachment' => (int)$id_attachment);
                }
            }

            if (!empty($ids)) {
                $result2 = Db::getInstance()->insert('product_attachment', $ids);
            }
        }

        Product::updateCacheAttachment((int)$id_product);
        if (is_array($array)) {
            return ($result1 && (!isset($result2) || $result2));
        }

        return $result1;
    }

    public static function getProductAttached($id_lang, $list)
    {
        $ids_attachements = array();
        if (is_array($list)) {
            foreach ($list as $attachement) {
                $ids_attachements[] = $attachement['id_attachment'];
            }

            $sql = 'SELECT * FROM `'._DB_PREFIX_.'product_attachment` pa
					LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (pa.`id_product` = pl.`id_product`'.Shop::addSqlRestrictionOnLang('pl').')
					WHERE `id_attachment` IN ('.implode(',', array_map('intval', $ids_attachements)).')
						AND pl.`id_lang` = '.(int)$id_lang;
            $tmp = Db::getInstance()->executeS($sql);
            $product_attachements = array();
            foreach ($tmp as $t) {
                $product_attachements[$t['id_attachment']][] = $t['name'];
            }
            return $product_attachements;
        } else {
            return false;
        }
    }
}
