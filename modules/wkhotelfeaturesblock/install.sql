/**
* 2010-2020 Webkul.
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
*  @copyright 2010-2020 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

CREATE TABLE IF NOT EXISTS `PREFIX_htl_features_block_data` (
	`id_features_block` int(11) NOT NULL AUTO_INCREMENT,
	`active` tinyint(1) NOT NULL,
	`position` int(11) NOT NULL,
	`date_add` datetime NOT NULL,
	`date_upd` datetime NOT NULL,
	PRIMARY KEY (`id_features_block`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `PREFIX_htl_features_block_data_lang` (
  `id_features_block` int(11) NOT NULL,
  `id_lang` int(11) NOT NULL,
  `feature_title` text NOT NULL,
  `feature_description` text NOT NULL,
  PRIMARY KEY (`id_features_block`, `id_lang`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 ;
