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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

require_once(_PS_TOOL_DIR_.'smarty/Smarty.class.php');

class Smarty_CacheResource_Mysql extends Smarty_CacheResource_Custom
{
    /**
     * fetch cached content and its modification time from data source
     *
     * @param string $id unique cache content identifier
     * @param string $name template name
     * @param string $cache_id cache id
     * @param string $compile_id compile id
     * @param string $content cached content
     * @param int $mtime cache modification timestamp (epoch)
     * @return void
     */
    protected function fetch($id, $name, $cache_id, $compile_id, &$content, &$mtime)
    {
        $row = Db::getInstance()->getRow('SELECT modified, content FROM '._DB_PREFIX_.'smarty_cache WHERE id_smarty_cache = "'.pSQL($id, true).'"');
        if ($row) {
            $content = $row['content'];
            $mtime = strtotime($row['modified']);
        } else {
            $content = null;
            $mtime = null;
        }
    }

    /**
     * Fetch cached content's modification timestamp from data source
     *
     * @note implementing this method is optional. Only implement it if modification times can be accessed faster than loading the complete cached content.
     * @param string $id unique cache content identifier
     * @param string $name template name
     * @param string $cache_id cache id
     * @param string $compile_id compile id
     * @return int|boolean timestamp (epoch) the template was modified, or false if not found
     */
    protected function fetchTimestamp($id, $name, $cache_id, $compile_id)
    {
        $value = Db::getInstance()->getValue('SELECT modified FROM '._DB_PREFIX_.'smarty_cache WHERE id_smarty_cache = "'.pSQL($id, true).'"');
        $mtime = strtotime($value);
        return $mtime;
    }

    /**
     * Save content to cache
     *
     * @param string $id unique cache content identifier
     * @param string $name template name
     * @param string $cache_id cache id
     * @param string $compile_id compile id
     * @param int|null $exp_time seconds till expiration time in seconds or null
     * @param string $content content to cache
     * @return bool success
     */
    protected function save($id, $name, $cache_id, $compile_id, $exp_time, $content)
    {
        Db::getInstance()->execute('
		REPLACE INTO '._DB_PREFIX_.'smarty_cache (id_smarty_cache, name, cache_id, content)
		VALUES (
			"'.pSQL($id, true).'",
			"'.pSQL(sha1($name)).'",
			"'.pSQL($cache_id, true).'",
			"'.pSQL($content, true).'"
		)');

        return (bool)Db::getInstance()->Affected_Rows();
    }

    /**
     * Delete content from cache
     *
     * @param string $name template name
     * @param string $cache_id cache id
     * @param string $compile_id compile id
     * @param int|null $exp_time seconds till expiration or null
     * @return int number of deleted caches
     */
    protected function delete($name, $cache_id, $compile_id, $exp_time)
    {
        // delete the whole cache
        if ($name === null && $cache_id === null && $compile_id === null && $exp_time === null) {
            // returning the number of deleted caches would require a second query to count them
            Db::getInstance()->execute('TRUNCATE TABLE '._DB_PREFIX_.'smarty_cache');
            return -1;
        }

        $where = array();
        if ($name !== null) {
            $where[] = 'name = "'.pSQL(sha1($name)).'"';
        }
        if ($exp_time !== null) {
            $where[] = 'modified < DATE_SUB(NOW(), INTERVAL '.(int)$exp_time.' SECOND)';
        }
        if ($cache_id !== null) {
            $where[] = '(cache_id  = "'.pSQL($cache_id, true).'" OR cache_id LIKE "'.pSQL($cache_id .'|%', true).'")';
        }

        Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'smarty_cache WHERE '.implode(' AND ', $where));
        return Db::getInstance()->Affected_Rows();
    }
}
