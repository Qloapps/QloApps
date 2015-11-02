<?php
/*
* 2007-2015 PrestaShop
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
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class CMSCore extends ObjectModel
{
    /** @var string Name */
    public $meta_title;
    public $meta_description;
    public $meta_keywords;
    public $content;
    public $link_rewrite;
    public $id_cms_category;
    public $position;
    public $indexation;
    public $active;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'cms',
        'primary' => 'id_cms',
        'multilang' => true,
        'multilang_shop' => true,
        'fields' => array(
            'id_cms_category' =>    array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'position' =>            array('type' => self::TYPE_INT),
            'indexation' =>         array('type' => self::TYPE_BOOL),
            'active' =>            array('type' => self::TYPE_BOOL),

            /* Lang fields */
            'meta_description' =>    array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 255),
            'meta_keywords' =>        array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 255),
            'meta_title' =>            array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'required' => true, 'size' => 128),
            'link_rewrite' =>        array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isLinkRewrite', 'required' => true, 'size' => 128),
            'content' =>            array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml', 'size' => 3999999999999),
        ),
    );

    protected $webserviceParameters = array(
        'objectNodeName' => 'content',
        'objectsNodeName' => 'content_management_system',
    );

    public function add($autodate = true, $null_values = false)
    {
        $this->position = CMS::getLastPosition((int)$this->id_cms_category);
        return parent::add($autodate, true);
    }

    public function update($null_values = false)
    {
        if (parent::update($null_values)) {
            return $this->cleanPositions($this->id_cms_category);
        }
        return false;
    }

    public function delete()
    {
        if (parent::delete()) {
            return $this->cleanPositions($this->id_cms_category);
        }
        return false;
    }

    public static function getLinks($id_lang, $selection = null, $active = true, Link $link = null)
    {
        if (!$link) {
            $link = Context::getContext()->link;
        }
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT c.id_cms, cl.link_rewrite, cl.meta_title
		FROM '._DB_PREFIX_.'cms c
		LEFT JOIN '._DB_PREFIX_.'cms_lang cl ON (c.id_cms = cl.id_cms AND cl.id_lang = '.(int)$id_lang.')
		'.Shop::addSqlAssociation('cms', 'c').'
		WHERE 1
		'.(($selection !== null) ? ' AND c.id_cms IN ('.implode(',', array_map('intval', $selection)).')' : '').
        ($active ? ' AND c.`active` = 1 ' : '').
        'GROUP BY c.id_cms
		ORDER BY c.`position`');

        $links = array();
        if ($result) {
            foreach ($result as $row) {
                $row['link'] = $link->getCMSLink((int)$row['id_cms'], $row['link_rewrite']);
                $links[] = $row;
            }
        }
        return $links;
    }

    public static function listCms($id_lang = null, $id_block = false, $active = true)
    {
        if (empty($id_lang)) {
            $id_lang = (int)Configuration::get('PS_LANG_DEFAULT');
        }

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT c.id_cms, l.meta_title
		FROM  '._DB_PREFIX_.'cms c
		JOIN '._DB_PREFIX_.'cms_lang l ON (c.id_cms = l.id_cms)
		'.Shop::addSqlAssociation('cms', 'c').'
		'.(($id_block) ? 'JOIN '._DB_PREFIX_.'block_cms b ON (c.id_cms = b.id_cms)' : '').'
		WHERE l.id_lang = '.(int)$id_lang.(($id_block) ? ' AND b.id_block = '.(int)$id_block : '').($active ? ' AND c.`active` = 1 ' : '').'
		GROUP BY c.id_cms
		ORDER BY c.`position`');
    }

    public function updatePosition($way, $position)
    {
        if (!$res = Db::getInstance()->executeS('
			SELECT cp.`id_cms`, cp.`position`, cp.`id_cms_category`
			FROM `'._DB_PREFIX_.'cms` cp
			WHERE cp.`id_cms_category` = '.(int)$this->id_cms_category.'
			ORDER BY cp.`position` ASC'
        )) {
            return false;
        }

        foreach ($res as $cms) {
            if ((int)$cms['id_cms'] == (int)$this->id) {
                $moved_cms = $cms;
            }
        }

        if (!isset($moved_cms) || !isset($position)) {
            return false;
        }

        // < and > statements rather than BETWEEN operator
        // since BETWEEN is treated differently according to databases
        return (Db::getInstance()->execute('
			UPDATE `'._DB_PREFIX_.'cms`
			SET `position`= `position` '.($way ? '- 1' : '+ 1').'
			WHERE `position`
			'.($way
                ? '> '.(int)$moved_cms['position'].' AND `position` <= '.(int)$position
                : '< '.(int)$moved_cms['position'].' AND `position` >= '.(int)$position).'
			AND `id_cms_category`='.(int)$moved_cms['id_cms_category'])
        && Db::getInstance()->execute('
			UPDATE `'._DB_PREFIX_.'cms`
			SET `position` = '.(int)$position.'
			WHERE `id_cms` = '.(int)$moved_cms['id_cms'].'
			AND `id_cms_category`='.(int)$moved_cms['id_cms_category']));
    }

    public static function cleanPositions($id_category)
    {
        $sql = '
		SELECT `id_cms`
		FROM `'._DB_PREFIX_.'cms`
		WHERE `id_cms_category` = '.(int)$id_category.'
		ORDER BY `position`';

        $result = Db::getInstance()->executeS($sql);

        for ($i = 0, $total = count($result); $i < $total; ++$i) {
            $sql = 'UPDATE `'._DB_PREFIX_.'cms`
					SET `position` = '.(int)$i.'
					WHERE `id_cms_category` = '.(int)$id_category.'
						AND `id_cms` = '.(int)$result[$i]['id_cms'];
            Db::getInstance()->execute($sql);
        }
        return true;
    }

    public static function getLastPosition($id_category)
    {
        $sql = '
		SELECT MAX(position) + 1
		FROM `'._DB_PREFIX_.'cms`
		WHERE `id_cms_category` = '.(int)$id_category;

        return (Db::getInstance()->getValue($sql));
    }

    public static function getCMSPages($id_lang = null, $id_cms_category = null, $active = true, $id_shop = null)
    {
        $sql = new DbQuery();
        $sql->select('*');
        $sql->from('cms', 'c');

        if ($id_lang) {
            if ($id_shop) {
                $sql->innerJoin('cms_lang', 'l', 'c.id_cms = l.id_cms AND l.id_lang = '.(int)$id_lang.' AND l.id_shop = '.(int)$id_shop);
            } else {
                $sql->innerJoin('cms_lang', 'l', 'c.id_cms = l.id_cms AND l.id_lang = '.(int)$id_lang);
            }
        }

        if ($id_shop) {
            $sql->innerJoin('cms_shop', 'cs', 'c.id_cms = cs.id_cms AND cs.id_shop = '.(int)$id_shop);
        }

        if ($active) {
            $sql->where('c.active = 1');
        }

        if ($id_cms_category) {
            $sql->where('c.id_cms_category = '.(int)$id_cms_category);
        }

        $sql->orderBy('position');

        return Db::getInstance()->executeS($sql);
    }

    public static function getUrlRewriteInformations($id_cms)
    {
        $sql = 'SELECT l.`id_lang`, c.`link_rewrite`
				FROM `'._DB_PREFIX_.'cms_lang` AS c
				LEFT JOIN  `'._DB_PREFIX_.'lang` AS l ON c.`id_lang` = l.`id_lang`
				WHERE c.`id_cms` = '.(int)$id_cms.'
				AND l.`active` = 1';

        return Db::getInstance()->executeS($sql);
    }

    public static function getCMSContent($id_cms, $id_lang = null, $id_shop = null)
    {
        if (is_null($id_lang)) {
            $id_lang = (int)Configuration::get('PS_SHOP_DEFAULT');
        }
        if (is_null($id_shop)) {
            $id_shop = (int)Configuration::get('PS_LANG_DEFAULT');
        }

        $sql = '
			SELECT `content`
			FROM `'._DB_PREFIX_.'cms_lang`
			WHERE `id_cms` = '.(int)$id_cms.' AND `id_lang` = '.(int)$id_lang.' AND `id_shop` = '.(int)$id_shop;

        return Db::getInstance()->getRow($sql);
    }

    /* Method required for new PrestaShop Core */
    public static function getRepositoryClassName()
    {
        return 'Core_Business_CMS_CMSRepository';
    }
}
