ALTER TABLE `PREFIX_customer_message` CHANGE `ip_address` `ip_address` VARCHAR( 15 ) NULL DEFAULT NULL;

UPDATE `PREFIX_currency` SET `conversion_rate` = 1 WHERE `conversion_rate` = 0;

ALTER TABLE `PREFIX_currency` CHANGE `conversion_rate` `conversion_rate` DECIMAL( 13, 6 ) NOT NULL DEFAULT '1';

UPDATE `PREFIX_orders` SET conversion_rate = 1 WHERE conversion_rate = 0;

ALTER TABLE `PREFIX_cms` ADD `indexation` tinyint(1) UNSIGNED NULL DEFAULT '1' AFTER `active`;

/* PHP:update_order_messages(); */;

ALTER TABLE `PREFIX_group_reduction` CHANGE `reduction` `reduction` DECIMAL( 4, 4 ) NOT NULL;