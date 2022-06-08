<?php
/**
* 2010-2022 Webkul.
*
* NOTICE OF LICENSE
*
* All right is reserved,
* Please go through this link for complete license : https://store.webkul.com/license.html
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to https://store.webkul.com/customisation-guidelines/ for more information.
*
*  @author    Webkul IN <support@webkul.com>
*  @copyright 2010-2022 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

if (!defined('_PS_VERSION_'))
	exit;

function upgrade_module_1_1_0($module)
{
	$objUpgrade = new upgradeBlockNavigationMenu110();
	return ($objUpgrade->createTables()
		&& $objUpgrade->registerModuleHooks($module)
		&& $module->installTab('AdminCustomNavigationLinkSetting', 'Manage Custom Navigation Links')
		&& Configuration::updateValue('WK_SHOW_FOOTER_NAVIGATION_BLOCK', 1)
		&& $objUpgrade->insertData($module)
	);
}

class upgradeBlockNavigationMenu110
{
	public function insertData($module)
	{
		//insert home link to the list
		$languages = Language::getLanguages(false);
		$objCustomNavigationLink = new WkCustomNavigationLink();
		foreach ($languages as $language) {
			$objCustomNavigationLink->name[$language['id_lang']] = 'Home';
		}
		$objCustomNavigationLink->position = $objCustomNavigationLink->getHigherPosition();
		$objCustomNavigationLink->id_cms = 0;
		$objCustomNavigationLink->show_at_navigation = 1;
		$objCustomNavigationLink->show_at_footer = 0;
		$objCustomNavigationLink->active = 1;
		$objCustomNavigationLink->is_custom_link = 0;
		$objCustomNavigationLink->link = 'index';
		$objCustomNavigationLink->save();

		$modsElems = array();
			if (Module::isEnabled('wkabouthotelblock')) {
				$modsElems['Interior'] = 'hotelInteriorBlock';
			}
			if (Module::isEnabled('wkhotelfeaturesblock')) {
				$modsElems['Amenities'] = 'hotelAmenitiesBlock';
			}
			if (Module::isEnabled('wkhotelroom')) {
				$modsElems['Rooms'] = 'hotelRoomsBlock';
			}
			if (Module::isEnabled('wktestimonialblock')) {
				$modsElems['Testimonials'] = 'hotelTestimonialBlock';
			}
		if ($modsElems) {
			$indexLink = Context::getContext()->shop->getBaseURI();
			foreach ($modsElems as $name => $modElm) {
				$objCustomNavigationLink = new WkCustomNavigationLink();
				foreach ($languages as $language) {
					$objCustomNavigationLink->name[$language['id_lang']] = $name;
				}
				$objCustomNavigationLink->position = $objCustomNavigationLink->getHigherPosition();
				$objCustomNavigationLink->id_cms = 0;
				$objCustomNavigationLink->show_at_navigation = 1;
				$objCustomNavigationLink->show_at_footer = 0;
				$objCustomNavigationLink->active = 1;
				$objCustomNavigationLink->link = $indexLink.'#'.$modElm;
				$objCustomNavigationLink->is_custom_link = 1;
				$objCustomNavigationLink->save();
			}
		}

		// get data from old navigation menu
		$exploreLinks = $this->getOldExploreLinks();
		if (!empty($exploreLinks)) {
			foreach ($exploreLinks as $link) {
				$objCustomNavigationLink = new WkCustomNavigationLink();
				foreach ($languages as $language) {
					if (isset($link['lang'][$language['id_lang']]['name']) && $link['lang'][$language['id_lang']]['name']) {
						$objCustomNavigationLink->name[$language['id_lang']] = $link['lang'][$language['id_lang']]['name'];
					} else {
						$objCustomNavigationLink->name[$language['id_lang']] = $link['name'];
					}
				}
				$objCustomNavigationLink->position = $objCustomNavigationLink->getHigherPosition();
				$objCustomNavigationLink->id_cms = $link['id_cms'];
				$objCustomNavigationLink->show_at_navigation = $link['show_at_navigation'];
				$objCustomNavigationLink->show_at_footer = $link['show_at_footer'];
				$objCustomNavigationLink->active = $link['active'];
				// $objCustomNavigationLink->is_custom_link = 1;
				$objCustomNavigationLink->save();
			}
		}

		//insert contact link to the list
		$objCustomNavigationLink = new WkCustomNavigationLink();
		foreach ($languages as $language) {
			$objCustomNavigationLink->name[$language['id_lang']] = 'Contact';
		}
		$objCustomNavigationLink->position = $objCustomNavigationLink->getHigherPosition();
		$objCustomNavigationLink->id_cms = 0;
		$objCustomNavigationLink->show_at_navigation = 1;
		$objCustomNavigationLink->show_at_footer = 1;
		$objCustomNavigationLink->active = 1;
		$objCustomNavigationLink->is_custom_link = 0;
		$objCustomNavigationLink->link = 'contact';
		$objCustomNavigationLink->save();
		return true;
	}

	public function getOldExploreLinks()
	{
		$links =  Db::getInstance()->executeS(
			'SELECT * FROM `'._DB_PREFIX_.'htl_custom_explore_link`'
		);
		foreach ($links as &$link) {
            $link_lang = Db::getInstance()->executeS(
                'SELECT * FROM `'._DB_PREFIX_.'htl_custom_explore_link_lang`
				WHERE `id_explore_link` = '.$link['id_explore_link']
			);
			// set default name
			if (!isset($link['name'])) {
                $link['name'] = $link_lang[0]['name'];
			}
            foreach ($link_lang as $lang) {
                $link['lang'][$lang['id_lang']]['name'] = $lang['name'];
			}

		}

		return $links;
	}

	public function registerModuleHooks($module)
	{
		return $module->registerHook(
			array (
				'footer',
				'displayAddModuleSettingLink',
				'actionObjectLanguageAddAfter',
				'displayDefaultNavigationHook'
			)
		);
	}

	public function getModuleSql()
	{
		return array (
			"CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."htl_custom_navigation_link` (
				`id_navigation_link` int(11) NOT NULL AUTO_INCREMENT,
				`link` text NOT NULL,
				`is_custom_link` tinyint(1) NOT NULL,
				`id_cms` int(11) NOT NULL DEFAULT '0',
				`position` int(11) unsigned NOT NULL DEFAULT '0',
				`show_at_navigation` tinyint(1) NOT NULL DEFAULT '0',
				`show_at_footer` tinyint(1) NOT NULL DEFAULT '0',
				`active` tinyint(1) NOT NULL,
				`date_add` datetime NOT NULL,
				`date_upd` datetime NOT NULL,
				PRIMARY KEY (`id_navigation_link`)
			) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;",
			"CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."htl_custom_navigation_link_lang` (
				`id_navigation_link` int(11) NOT NULL,
				`id_lang` int(11) NOT NULL,
				`name` varchar(255) NOT NULL,
				PRIMARY KEY (`id_navigation_link`, `id_lang`)
				) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8 ;",
		);
	}

	public function createTables()
	{
		if ($sql = $this->getModuleSql()) {
			foreach ($sql as $query) {
				if ($query) {
					if (!Db::getInstance()->execute(trim($query))) {
						return false;
					}
				}
			}
		}
		return true;
	}
}
