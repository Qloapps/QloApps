/**
* 2010-2018 Webkul.
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
*  @copyright 2010-2018 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

CREATE TABLE IF NOT EXISTS `PREFIX_htl_testimonials_block_data` (
	`id_testimonial_block` int(11) NOT NULL AUTO_INCREMENT,
	`name` varchar(255) NOT NULL,
	`designation` text NOT NULL,
	`active` tinyint(1) NOT NULL,
  	`position` int(10) unsigned NOT NULL DEFAULT '0',
	`date_add` datetime NOT NULL,
	`date_upd` datetime NOT NULL,
	PRIMARY KEY (`id_testimonial_block`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `PREFIX_htl_testimonials_block_data_lang` (
  `id_testimonial_block` int(11) NOT NULL,
  `id_lang` int(11) NOT NULL,
  `testimonial_content` text NOT NULL,
  PRIMARY KEY (`id_testimonial_block`, `id_lang`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 ;
