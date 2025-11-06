SET NAMES 'utf8';

ALTER TABLE `PREFIX_address` ADD `id_hotel` int(10) unsigned NOT NULL DEFAULT '0' AFTER `id_warehouse`;

CREATE TABLE `PREFIX_order_customer_guest_detail` (
  `id_order_customer_guest_detail` int(10) unsigned NOT NULL auto_increment,
  `id_order` int(10) unsigned NOT NULL,
  `id_gender` int(10) unsigned NOT NULL,
  `firstname` varchar(32) NOT NULL,
  `lastname` varchar(32) NOT NULL,
  `email` varchar(128) NOT NULL,
  `phone` varchar(32) DEFAULT NULL,
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
	PRIMARY KEY (`id_order_customer_guest_detail`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE `PREFIX_cart_customer_guest_detail` (
  `id_customer_guest_detail` int(10) unsigned NOT NULL auto_increment,
  `id_cart` int(10) unsigned NOT NULL,
  `id_gender` int(10) unsigned NOT NULL,
  `firstname` varchar(32) NOT NULL,
  `lastname` varchar(32) NOT NULL,
  `email` varchar(128) NOT NULL,
  `phone` varchar(32) DEFAULT NULL,
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
	PRIMARY KEY (`id_customer_guest_detail`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

ALTER TABLE `PREFIX_image_type` ADD `hotels` tinyint(1) NOT NULL DEFAULT '1' AFTER `categories`;
UPDATE `PREFIX_image_type` SET `hotels` = '1' WHERE `name` IN ("small_default", "medium_default", "large_default");

ALTER TABLE `PREFIX_orders`
ADD `id_address_tax` int(10) unsigned NOT NULL AFTER `id_address_invoice`,
ADD `payment_type` int(10) unsigned NOT NULL AFTER `payment`,
ADD `with_occupancy` tinyint(1) NOT NULL DEFAULT '0' AFTER `advance_paid_amount`;

UPDATE `PREFIX_orders`
set `id_address_tax` = `id_address_invoice`;

ALTER TABLE `PREFIX_order_detail`
ADD `is_booking_product` tinyint(1) NOT NULL DEFAULT '0' AFTER `product_quantity_discount`,
ADD `selling_preference_type` tinyint(1) NOT NULL DEFAULT '1' AFTER `is_booking_product`,
ADD `product_auto_add` tinyint(1) NOT NULL DEFAULT '0' AFTER `selling_preference_type`,
ADD `product_price_addition_type` tinyint(1) NOT NULL DEFAULT '0' AFTER `product_auto_add`,
ADD `product_allow_multiple_quantity` tinyint(1) unsigned NOT NULL DEFAULT '1' AFTER `product_price_addition_type`,
ADD `product_price_calculation_method` tinyint(1) unsigned NOT NULL DEFAULT '1' AFTER `product_allow_multiple_quantity`;

UPDATE `PREFIX_order_detail`
set `is_booking_product` = 1;

ALTER TABLE `PREFIX_order_return`
ADD`id_return_type` tinyint(1) unsigned NOT NULL DEFAULT '0' AFTER `by_admin`,
ADD`return_type` tinyint(1) unsigned NOT NULL DEFAULT '0' AFTER `id_return_type`;

ALTER TABLE `PREFIX_order_slip`
ADD `redeem_status` TINYINT(1) NOT NULL DEFAULT '0' AFTER `partial`,
ADD `id_cart_rule` int(10) unsigned NOT NULL DEFAULT '0' AFTER `redeem_status`;
UPDATE `PREFIX_order_slip`
set `redeem_status` = 1;

ALTER TABLE `PREFIX_order_payment` ADD `payment_type` INT UNSIGNED NOT NULL AFTER `payment_method`;
UPDATE `PREFIX_order_payment` op
INNER JOIN `PREFIX_orders` o ON (op.`order_reference` = o.`reference`)
SET o.`payment_type` = "1", op.`payment_type` = "1"
WHERE o.`module` IS NOT NULL;
UPDATE `PREFIX_order_payment` op
INNER JOIN `PREFIX_orders` o ON (op.`order_reference` = o.`reference`)
SET o.`payment_type` = "1", op.`payment_type` = "2"
WHERE o.`module` IS NULL;

CREATE TABLE `PREFIX_order_payment_detail` (
	`id_order_payment_detail` INT(10) unsigned NOT NULL auto_increment,
	`id_order_payment` INT(10) unsigned NOT NULL,
	`id_order` INT(10) unsigned NOT NULL,
	`amount` DECIMAL(10,2) NOT NULL,
	`date_add` DATETIME NOT NULL,
	PRIMARY KEY (`id_order_payment_detail`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

ALTER TABLE `PREFIX_product`
ADD `allow_multiple_quantity` tinyint(1) unsigned NOT NULL DEFAULT '1' AFTER `minimal_quantity`,
ADD `max_quantity` int(10) unsigned NOT NULL DEFAULT '1' AFTER `allow_multiple_quantity`,
ADD `price_calculation_method` tinyint(1) unsigned NOT NULL DEFAULT '1' AFTER `max_quantity`,
ADD `auto_add_to_cart` tinyint(1) NOT NULL DEFAULT '0' AFTER `available_for_order`,
ADD `price_addition_type` tinyint(1) NOT NULL DEFAULT '1' AFTER `auto_add_to_cart`,
ADD `show_at_front` tinyint(1) NOT NULL DEFAULT '1' AFTER `price_addition_type`,
ADD `selling_preference_type` tinyint(1) NOT NULL DEFAULT '1' AFTER `show_at_front`,
ADD `price_display_method` tinyint(1) NOT NULL DEFAULT '1' AFTER `selling_preference_type`,
ADD `booking_product` tinyint(1) NOT NULL DEFAULT '1' AFTER `is_virtual`;

ALTER TABLE `PREFIX_product_shop`
ADD `allow_multiple_quantity` tinyint(1) unsigned NOT NULL DEFAULT '1' AFTER `minimal_quantity`,
ADD `max_quantity` int(10) unsigned NOT NULL DEFAULT '1' AFTER `allow_multiple_quantity`,
ADD `price_calculation_method` tinyint(1) unsigned NOT NULL DEFAULT '1' AFTER `max_quantity`,
ADD `auto_add_to_cart` tinyint(1) NOT NULL DEFAULT '0' AFTER `available_for_order`,
ADD `price_addition_type` tinyint(1) NOT NULL DEFAULT '1' AFTER `auto_add_to_cart`,
ADD `show_at_front` tinyint(1) NOT NULL DEFAULT '1' AFTER `price_addition_type`,
ADD `price_display_method` tinyint(1) NOT NULL DEFAULT '1' AFTER `show_at_front`;

ALTER TABLE `PREFIX_order_invoice_payment` ADD `id_order_payment_detail` int(11) unsigned NOT NULL AFTER `id_order_payment`;

CREATE TABLE `PREFIX_maintenance_access` (
  `id_maintenance_access` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(50) NOT NULL,
  `email` varchar(128) NOT NULL,
  `date_add` datetime NOT NULL,
  PRIMARY KEY (`id_maintenance_access`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

INSERT INTO `PREFIX_configuration` (`name`, `value`, `date_add`, `date_upd`) VALUES
	('PS_HOTEL_IMAGES_PER_PAGE', '9', NOW(), NOW()),
	('PS_COOKIE_SAMESITE', 'Lax', NOW(), NOW()),
  ('HOTEL_CHECKUP_DESCRIPTIONS_LT', '100', NOW(), NOW()),
  ('HOTEL_CHECKUP_DESCRIPTIONS_GT', '400', NOW(), NOW()),
  ('HOTEL_CHECKUP_IMAGES_LT', '1', NOW(), NOW()),
  ('HOTEL_CHECKUP_IMAGES_GT', '2', NOW(), NOW()),
  ('HOTEL_CHECKUP_ORDERS_LT', '1', NOW(), NOW()),
  ('HOTEL_CHECKUP_ORDERS_GT', '2', NOW(), NOW()),
  ('HOTEL_CHECKUP_TOTAL_ROOMS_LT', '1', NOW(), NOW()),
  ('HOTEL_CHECKUP_TOTAL_ROOMS_GT', '3', NOW(), NOW()),
  ('ROOM_TYPE_CHECKUP_DESCRIPTIONS_LT', '100', NOW(), NOW()),
  ('ROOM_TYPE_CHECKUP_DESCRIPTIONS_GT', '400', NOW(), NOW()),
  ('ROOM_TYPE_CHECKUP_IMAGES_LT', '1', NOW(), NOW()),
  ('ROOM_TYPE_CHECKUP_IMAGES_GT', '2', NOW(), NOW()),
  ('ROOM_TYPE_CHECKUP_ORDERS_LT', '1', NOW(), NOW()),
  ('ROOM_TYPE_CHECKUP_ORDERS_GT', '2', NOW(), NOW()),
  ('ROOM_TYPE_CHECKUP_TOTAL_ROOMS_LT', '1', NOW(), NOW()),
  ('ROOM_TYPE_CHECKUP_TOTAL_ROOMS_GT', '3', NOW(), NOW()),
  ('SERVICE_CHECKUP_DESCRIPTIONS_SHORT_LT', '50', NOW(), NOW()),
  ('SERVICE_CHECKUP_DESCRIPTIONS_SHORT_GT', '150', NOW(), NOW()),
  ('SERVICE_CHECKUP_IMAGES_LT', '1', NOW(), NOW()),
  ('SERVICE_CHECKUP_IMAGES_GT', '2', NOW(), NOW()),
  ('SERVICE_CHECKUP_ORDERS_LT', '1', NOW(), NOW()),
  ('SERVICE_CHECKUP_ORDERS_GT', '2', NOW(), NOW()),
  ('PS_SERVICE_PRODUCT_CATEGORY_FILTER', '1', NOW(), NOW());


CREATE PROCEDURE qlo_move_product_comment_to_review()
BEGIN
  IF (SELECT COUNT(table_name)
    FROM
      information_schema.TABLES as table_name
    WHERE
      TABLE_SCHEMA LIKE (SELECT DATABASE()) AND
      TABLE_TYPE LIKE 'BASE TABLE' AND
      TABLE_NAME = 'qlo_product_comment')
  THEN
    CREATE TABLE IF NOT EXISTS `PREFIX_qhr_hotel_review` (
        `id_hotel_review` INT(10) NOT NULL AUTO_INCREMENT,
        `id_hotel` INT(10) NOT NULL,
        `id_order` INT(10) NOT NULL,
        `rating` FLOAT UNSIGNED NOT NULL,
        `subject` VARCHAR(255) NOT NULL,
        `description` TEXT NOT NULL,
        `status_abusive` TINYINT(1) DEFAULT 0,
        `status` TINYINT(1) DEFAULT 0,
        `date_add` DATETIME NOT NULL,
        `date_upd` DATETIME NOT NULL,
        PRIMARY KEY (`id_hotel_review`)
    ) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;  CREATE TABLE IF NOT EXISTS `PREFIX_qhr_category` (
        `id_category` INT(10) NOT NULL AUTO_INCREMENT,
        `active` TINYINT(1) DEFAULT 1,
        `date_add` DATETIME NOT NULL,
        `date_upd` DATETIME NOT NULL,
        PRIMARY KEY (`id_category`)
    ) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8; CREATE TABLE IF NOT EXISTS `PREFIX_qhr_category_lang` (
        `id_category` INT(10) NOT NULL,
        `id_lang` INT(10) NOT NULL,
        `name` VARCHAR(255) NOT NULL,
        PRIMARY KEY (`id_category`, `id_lang`)
    ) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;  CREATE TABLE IF NOT EXISTS `PREFIX_qhr_review_category_rating` (
        `id_hotel_review` INT(10) NOT NULL,
        `id_category` INT(10) NOT NULL,
        `rating` FLOAT UNSIGNED NOT NULL,
        PRIMARY KEY (`id_hotel_review`, `id_category`)
    ) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;  CREATE TABLE IF NOT EXISTS `PREFIX_qhr_review_usefulness` (
        `id_hotel_review` INT(10) NOT NULL,
        `id_customer` INT(10) NOT NULL,
        PRIMARY KEY (`id_hotel_review`, `id_customer`)
    ) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;  CREATE TABLE IF NOT EXISTS `PREFIX_qhr_review_report` (
        `id_hotel_review` INT(10) NOT NULL,
        `id_customer` INT(10) NOT NULL,
        PRIMARY KEY (`id_hotel_review`, `id_customer`)
    ) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8; CREATE TABLE IF NOT EXISTS `PREFIX_qhr_review_reply` (
        `id_review_reply` INT(10) NOT NULL AUTO_INCREMENT,
        `id_hotel_review` INT(10) NOT NULL,
        `id_employee` INT(10) NOT NULL DEFAULT 0,
        `message` TEXT NOT NULL,
        `date_add` DATETIME NOT NULL,
        PRIMARY KEY (`id_review_reply`)
    ) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;  INSERT INTO `PREFIX_qhr_hotel_review` (`id_hotel_review`, `id_hotel`, `id_order`, `rating`, `subject`, `description`, `status_abusive`, `status`, `date_add`, `date_upd`)
      SELECT pc.`id_product_comment`, hrt.`id_hotel`, 0, pc.`grade`, pc.`title`, pc.`content`, 0, IF(pc.`deleted`, 2, IF(pc.`validate`, 3, 1)), NOW(), NOW()
      FROM `PREFIX_product_comment` pc
      INNER JOIN `PREFIX_htl_room_type` hrt ON (pc.`id_product` = hrt.`id_product`);  INSERT INTO `PREFIX_qhr_category` (`id_category`, `active`, `date_add`, `date_upd`)
      SELECT `id_product_comment_criterion`, `active`, NOW(), NOW()
      FROM `PREFIX_product_comment_criterion`;  INSERT INTO `PREFIX_qhr_category_lang` (`id_category`, `id_lang`, `name`)
      SELECT pcc.`id_product_comment_criterion`, `id_lang`, `name`
      FROM `PREFIX_product_comment_criterion` pcc
      INNER JOIN `PREFIX_product_comment_criterion_lang` pccl ON (pccl.`id_product_comment_criterion` = pcc.`id_product_comment_criterion`);  INSERT INTO `PREFIX_qhr_review_category_rating` (`id_hotel_review`, `id_category`, `rating`)
      SELECT `id_product_comment`, `id_product_comment_criterion`, `grade`
      FROM `PREFIX_product_comment_grade`;  INSERT INTO `PREFIX_qhr_review_usefulness` (`id_hotel_review`, `id_customer`)
      SELECT `id_product_comment`, `id_customer`
      FROM `PREFIX_product_comment_usefulness` WHERE `usefulness` = 1;  INSERT INTO `PREFIX_qhr_review_report` (`id_hotel_review`, `id_customer`)
      SELECT `id_product_comment`, `id_customer`
      FROM `PREFIX_product_comment_report`;  END IF;  END;

call qlo_move_product_comment_to_review;

DROP PROCEDURE qlo_move_product_comment_to_review;


/* PHP:add_new_tabs_160(); */;
/* PHP:add_new_categores_160(); */;
/* PHP:move_hotel_categoryes_to_location(); */;
/* PHP:move_hotel_address_to_address_table(); */;
/* PHP:add_new_order_refund_states_160(); */;
/* PHP:update_order_payment_detail_160(); */;



