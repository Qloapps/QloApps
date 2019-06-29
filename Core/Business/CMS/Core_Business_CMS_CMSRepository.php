<?php
/**
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
 *  @author 	PrestaShop SA <contact@prestashop.com>
 *  @copyright  2007-2017 PrestaShop SA
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

class Core_Business_CMS_CMSRepository extends Core_Foundation_Database_EntityRepository
{
    /**
     * Return CMSRepository lang associative table name
     * @return string
     */
    private function getLanguageTableNameWithPrefix()
    {
        return $this->getTableNameWithPrefix() . '_lang';
    }

    /**
     * Return all CMSRepositories depending on $id_lang/$id_shop tuple
     * @param $id_lang
     * @param $id_shop
     * @return array|null
     */
    public function i10nFindAll($id_lang, $id_shop)
    {
        $sql = '
			SELECT *
			FROM `'.$this->getTableNameWithPrefix().'` c
			JOIN `'.$this->getPrefix().'cms_lang` cl ON c.`id_cms`= cl.`id_cms`
			WHERE cl.`id_lang` = '.(int)$id_lang.'
			AND cl.`id_shop` = '.(int)$id_shop.'

		';

        return $this->hydrateMany($this->db->select($sql));
    }

    /**
     * Return all CMSRepositories depending on $id_lang/$id_shop tuple
     * @param $id_cms
     * @param $id_lang
     * @param $id_shop
     * @return CMS|null
     * @throws Core_Foundation_Database_Exception
     */
    public function i10nFindOneById($id_cms, $id_lang, $id_shop)
    {
        $sql = '
			SELECT *
			FROM `'.$this->getTableNameWithPrefix().'` c
			JOIN `'.$this->getPrefix().'cms_lang` cl ON c.`id_cms`= cl.`id_cms`
			WHERE c.`id_cms` = '.(int)$id_cms.'
			AND cl.`id_lang` = '.(int)$id_lang.'
			AND cl.`id_shop` = '.(int)$id_shop.'
			LIMIT 0 , 1
		';

        return $this->hydrateOne($this->db->select($sql));
    }
}
