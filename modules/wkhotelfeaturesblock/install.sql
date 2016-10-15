CREATE TABLE IF NOT EXISTS `PREFIX_htl_features_block_data` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`feature_title` text NOT NULL,
	`feature_description` text NOT NULL, 
	`feature_image` text NOT NULL,
	`active` tinyint(1) NOT NULL,
	`position` int(11) NOT NULL,
	`date_add` datetime NOT NULL,
	`date_upd` datetime NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;