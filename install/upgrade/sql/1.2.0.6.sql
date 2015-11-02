SET NAMES 'utf8';

/* ##################################### */
/* 				STRUCTURE			 		 */
/* ##################################### */

ALTER TABLE `PREFIX_configuration` DROP INDEX `configuration_name`;
ALTER TABLE `PREFIX_order_detail` ADD `product_quantity_in_stock` INT(10) NOT NULL DEFAULT 0 AFTER `product_quantity`;
ALTER TABLE `PREFIX_order_detail` ADD `product_quantity_reinjected` INT(10) UNSIGNED NOT NULL DEFAULT 0 AFTER `product_quantity_return`;

/* ##################################### */
/* 					CONTENTS					 */
/* ##################################### */
UPDATE `PREFIX_product` SET `out_of_stock` = 0 WHERE `id_product` IN ((SELECT `id_product` FROM `PREFIX_product_download`));
