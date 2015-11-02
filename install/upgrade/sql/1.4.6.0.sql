SET NAMES 'utf8';

/* PHP:update_order_canada(); */;

CREATE TABLE IF NOT EXISTS `PREFIX_compare` (
  `id_compare` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_customer` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id_compare`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

ALTER TABLE  `PREFIX_compare_product` DROP  `id_compare_product` , DROP  `id_guest` , DROP  `id_customer` ;

ALTER TABLE `PREFIX_compare_product`
	ADD `id_compare` int(10) unsigned NOT NULL,
   ADD PRIMARY KEY(
     `id_compare`,
     `id_product`);

ALTER TABLE `PREFIX_store` CHANGE `latitude` `latitude` DECIMAL(11, 8) NULL DEFAULT NULL;
ALTER TABLE `PREFIX_store` CHANGE `longitude` `longitude` DECIMAL(11, 8) NULL DEFAULT NULL;

ALTER TABLE `PREFIX_address_format` ADD PRIMARY KEY (`id_country`);
ALTER TABLE `PREFIX_address_format` DROP INDEX `country`;

/* PHP:hook_blocksearch_on_header(); */;
