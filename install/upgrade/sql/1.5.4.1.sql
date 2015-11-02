SET NAMES 'utf8';

ALTER TABLE  `PREFIX_carrier` CHANGE  `max_weight`  `max_weight` DECIMAL( 20, 6 ) NULL DEFAULT  '0';

DELETE ms.*, hm.* FROM `PREFIX_module_shop` ms INNER JOIN `PREFIX_hook_module` hm USING (`id_module`) INNER JOIN `PREFIX_module` m USING (`id_module`) WHERE m.`name` LIKE 'backwardcompatibility';

UPDATE `PREFIX_module` SET `active` = 0 WHERE `name` LIKE 'backwardcompatibility';

ALTER TABLE `PREFIX_orders` CHANGE `reference` `reference` VARCHAR( 10 ) NULL DEFAULT NULL;

ALTER TABLE `PREFIX_order_payment` CHANGE `order_reference` `order_reference` VARCHAR( 10 ) NULL DEFAULT NULL;