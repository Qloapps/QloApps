SET NAMES 'utf8';

CREATE TABLE `PREFIX_specific_price_priority` (
`id_specific_price_priority` INT NOT NULL AUTO_INCREMENT ,
`id_product` INT NOT NULL ,
`priority` VARCHAR( 80 ) NOT NULL ,
PRIMARY KEY ( `id_specific_price_priority` , `id_product` )
)  ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;

ALTER TABLE `PREFIX_product` ADD `unit_price_ratio` DECIMAL(20, 6) NOT NULL default '0.00' AFTER `unit_price`;

UPDATE `PREFIX_product` SET `unit_price_ratio` =  IF (`unit_price` != 0, `price` / `unit_price`, 0);

ALTER TABLE `PREFIX_product` DROP `unit_price`;

ALTER TABLE `PREFIX_discount` ADD `behavior_not_exhausted` TINYINT(3) DEFAULT '1' AFTER `id_discount_type`;
