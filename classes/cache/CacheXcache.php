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

/**
 * This class require Xcache extension
 *
 * @since 1.5.0
 */
class CacheXcacheCore extends Cache
{
	public function __construct()
	{
		$this->keys = xcache_get(self::KEYS_NAME);
		if (!is_array($this->keys))
			$this->keys = array();
	}

	/**
	 * @see Cache::_set()
	 */
	protected function _set($key, $value, $ttl = 0)
	{
		return xcache_set($key, $value, $ttl);
	}

	/**
	 * @see Cache::_get()
	 */
	protected function _get($key)
	{
		return xcache_isset($key) ? xcache_get($key) : false;
	}

	/**
	 * @see Cache::_exists()
	 */
	protected function _exists($key)
	{
		return xcache_isset($key);
	}

	/**
	 * @see Cache::_delete()
	 */
	protected function _delete($key)
	{
		return xcache_unset($key);
	}

	/**
	 * @see Cache::_writeKeys()
	 */
	protected function _writeKeys()
	{
		xcache_set(self::KEYS_NAME, $this->keys);
	}

	/**
	 * @see Cache::flush()
	 */
	public function flush()
	{
		$this->delete('*');
		return true;
	}
}
