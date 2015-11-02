CREATE TABLE IF NOT EXISTS `PREFIX_htl_features_block_data` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`blog_heading` text NOT NULL,
	`blog_description` text NOT NULL,
	`feature_image` text NOT NULL,
	`feature_title` text NOT NULL,
	`feature_description` text NOT NULL, 
	`is_blog` int(2) unsigned NOT NULL,
	`date_add` datetime NOT NULL,
	`date_upd` datetime NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
