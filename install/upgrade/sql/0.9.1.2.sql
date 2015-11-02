/* STRUCTURE */
CREATE TABLE `PREFIX_product_sale` (
`id_product` INT( 10 ) UNSIGNED NOT NULL ,
`quantity` INT( 10 ) UNSIGNED NOT NULL DEFAULT '0',
`nb_vente` INT( 10 ) UNSIGNED NOT NULL DEFAULT '0',
`date_upd` DATE NOT NULL ,
PRIMARY KEY ( `id_product` )
) ENGINE = MYISAM DEFAULT CHARSET=utf8;

ALTER TABLE `PREFIX_image_type`
	ADD `manufacturers` BOOL NOT NULL DEFAULT '1' AFTER `categories`;

ALTER TABLE `PREFIX_address`
	ADD `id_manufacturer` INT( 10 ) UNSIGNED NOT NULL AFTER `id_customer` ;
	
ALTER TABLE `PREFIX_address`
	ADD `id_supplier` INT( 10 ) UNSIGNED NOT NULL AFTER `id_manufacturer` ;
	
ALTER TABLE `PREFIX_order_discount`
	ADD `id_discount` INT( 10 ) UNSIGNED NOT NULL AFTER `id_order` ;
	
ALTER TABLE `PREFIX_discount`
	ADD `quantity_per_user` INT( 10 ) UNSIGNED NOT NULL DEFAULT '1' AFTER `quantity` ;

ALTER TABLE `PREFIX_contact` CHANGE `position` `position` TINYINT( 2 ) UNSIGNED NOT NULL DEFAULT '0';

/*  CONTENTS */

/* CONFIGURATION VARIABLE */

