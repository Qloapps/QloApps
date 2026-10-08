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

/**
 * Queries for the pages added to the sitemap, and helpers for the sitemap files.
 */
class QgsHelper
{
    const QGS_MAX_URLS_PER_FILE = 25000;

    const QGS_INDEX_FILE_NAME = 'sitemap.xml';

    const QGS_DEFAULT_FREQUENCY = 'weekly';

    public static function getRoomTypeIds($idShop)
    {
        $groupJoin = '';
        if (Group::isFeatureActive() && Configuration::get('PS_UNIDENTIFIED_GROUP')) {
            $groupJoin = ' INNER JOIN (
                SELECT DISTINCT cp.`id_product` FROM `'._DB_PREFIX_.'category_product` cp
                INNER JOIN `'._DB_PREFIX_.'category_group` cg ON (cg.`id_category` = cp.`id_category`)
                WHERE cg.`id_group` = '.(int) Configuration::get('PS_UNIDENTIFIED_GROUP').'
            ) g ON ps.`id_product` = g.`id_product`';
        }

        return Db::getInstance()->executeS(
            'SELECT ps.`id_product` FROM `'._DB_PREFIX_.'product_shop` ps
            INNER JOIN `'._DB_PREFIX_.'product` p ON (p.`id_product` = ps.`id_product`)'.$groupJoin.'
            WHERE ps.`id_shop` = '.(int) $idShop.' AND ps.`active` = 1 AND ps.`visibility` <> \'none\' AND p.`booking_product` = 1
            ORDER BY ps.`id_product`'
        );
    }

    public static function getCategoryIds($idShop)
    {
        $groupJoin = '';
        if (Group::isFeatureActive() && Configuration::get('PS_UNIDENTIFIED_GROUP')) {
            $groupJoin = ' INNER JOIN `'._DB_PREFIX_.'category_group` cg ON (cg.`id_category` = c.`id_category`
                AND cg.`id_group` = '.(int) Configuration::get('PS_UNIDENTIFIED_GROUP').')';
        }

        return Db::getInstance()->executeS(
            'SELECT c.`id_category` FROM `'._DB_PREFIX_.'category` c
            INNER JOIN `'._DB_PREFIX_.'category_shop` cs ON (cs.`id_category` = c.`id_category`)
            INNER JOIN `'._DB_PREFIX_.'htl_branch_info` hbi ON (hbi.`id_category` = c.`id_category` AND hbi.`active` = 1)'.$groupJoin.'
            WHERE c.`active` = 1 AND cs.`id_shop` = '.(int) $idShop.'
            ORDER BY c.`id_category`'
        );
    }

    public static function getCmsIds($idLang, $idShop)
    {
        return Db::getInstance()->executeS(
            'SELECT c.`id_cms` FROM `'._DB_PREFIX_.'cms` c
            INNER JOIN `'._DB_PREFIX_.'cms_lang` cl ON (cl.`id_cms` = c.`id_cms` AND cl.`id_lang` = '.(int) $idLang.')
            INNER JOIN `'._DB_PREFIX_.'cms_shop` cs ON (cs.`id_cms` = c.`id_cms` AND cs.`id_shop` = '.(int) $idShop.')
            INNER JOIN `'._DB_PREFIX_.'cms_category` cc ON (cc.`id_cms_category` = c.`id_cms_category` AND cc.`active` = 1)
            WHERE c.`active` = 1 AND c.`indexation` = 1
            GROUP BY c.`id_cms` ORDER BY c.`id_cms`'
        );
    }

    public static function getSitemapFiles()
    {
        $files = array();
        foreach ((array) glob(_PS_ROOT_DIR_.'/*_sitemap.xml') as $path) {
            $name = basename($path);
            if (preg_match('/^[a-z]{2,3}(-[a-z]{2,4})?_\d+_sitemap\.xml$/i', $name)) {
                $files[] = array('link' => $name);
            }
        }
        sort($files);

        return $files;
    }

    public static function deleteSitemapFiles()
    {
        foreach (self::getSitemapFiles() as $row) {
            @unlink(_PS_ROOT_DIR_.'/'.$row['link']);
        }
        @unlink(_PS_ROOT_DIR_.'/'.self::QGS_INDEX_FILE_NAME);
    }

    public static function createIndexFile()
    {
        $sitemaps = self::getSitemapFiles();
        if (!$sitemaps) {
            return false;
        }

        $baseLink = Context::getContext()->link->getBaseLink();
        $xml = '<?xml version="1.0" encoding="UTF-8"?>'.PHP_EOL.'<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'.PHP_EOL;
        foreach ($sitemaps as $sitemap) {
            $xml .= '<sitemap><loc>'.self::escapeXml($baseLink.$sitemap['link']).'</loc><lastmod>'.date('c').'</lastmod></sitemap>'.PHP_EOL;
        }
        $xml .= '</sitemapindex>'.PHP_EOL;

        return (bool) file_put_contents(_PS_ROOT_DIR_.'/'.self::QGS_INDEX_FILE_NAME, $xml);
    }

    public static function escapeXml($string)
    {
        return htmlspecialchars($string, ENT_QUOTES | ENT_XML1, 'UTF-8');
    }
}
