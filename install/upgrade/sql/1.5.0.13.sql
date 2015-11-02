SET NAMES 'utf8';

DROP TABLE IF EXISTS `PREFIX_discount`;
DROP TABLE IF EXISTS `PREFIX_discount_type`;
DROP TABLE IF EXISTS `PREFIX_discount_type_lang`;

/* PHP:add_missing_image_key(); */;

-- Update order_payment structure for multishipping

-- Step 1: Add the table ps_order_invoice_payment and populate it
CREATE TABLE `PREFIX_order_invoice_payment` (
	`id_order_invoice` int(11) unsigned NOT NULL,
	`id_order_payment` int(11) unsigned NOT NULL,
	`id_order` int(11) unsigned NOT NULL,
	PRIMARY KEY (`id_order_invoice`,`id_order_payment`),
	KEY `order_payment` (`id_order_payment`),
	KEY `id_order` (`id_order`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

INSERT INTO `PREFIX_order_invoice_payment`
	(SELECT id_order_invoice, id_order_payment, id_order FROM `PREFIX_order_payment` WHERE id_order_invoice > 0);

-- Step 2: Add the collumn id_order_reference
ALTER TABLE `PREFIX_order_payment`
	ADD COLUMN `order_reference` VARCHAR(10)  AFTER `id_order`,
	ADD INDEX `order_reference`(`order_reference`);


-- Step 3: Fill in id_order_reference and merge duplicate lines
/* PHP:add_order_reference_in_order_payment(); */;

-- Step 4: Drop collumn id_order
ALTER TABLE `PREFIX_order_payment`
	DROP COLUMN `id_order`,
	DROP COLUMN `id_order_invoice`;

-- update for all employee the last ids for notifications
UPDATE `PREFIX_employee` 
	SET `id_last_order`= (SELECT IFNULL(MAX(`id_order`), 0) FROM `PREFIX_orders`),
	`id_last_customer_message`= (SELECT IFNULL(MAX(`id_customer_message`), 0) FROM `PREFIX_customer_message`),
	`id_last_customer`= (SELECT IFNULL(MAX(`id_customer`), 0) FROM `PREFIX_customer`);

UPDATE `PREFIX_category_shop` cs 
	SET `position` = (SELECT `position` FROM `PREFIX_category` c WHERE cs.`id_category` = c.`id_category`);

ALTER TABLE  `PREFIX_stock_available` CHANGE  `out_of_stock`  `out_of_stock` INT(2) UNSIGNED NOT NULL DEFAULT  '0';

/* PHP:p15013_add_missing_columns(); */;

