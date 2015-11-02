SET NAMES 'utf8';

ALTER TABLE `PREFIX_delivery` ADD `id_shop` INT UNSIGNED NULL DEFAULT NULL AFTER `id_delivery`;
ALTER TABLE `PREFIX_delivery` ADD `id_group_shop` INT UNSIGNED NULL DEFAULT NULL AFTER `id_shop`;

CREATE TABLE IF NOT EXISTS `PREFIX_module_access` (
  `id_profile` int(10) unsigned NOT NULL,
  `id_module` int(10) unsigned NOT NULL,
  `view` tinyint(1) NOT NULL,
  `configure` tinyint(1) NOT NULL,
  PRIMARY KEY (`id_profile`,`id_module`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

/* Create SuperAdmin */
UPDATE `PREFIX_profile_lang` SET `name` = 'SuperAdmin' WHERE `id_profile` = 1;

CREATE TABLE IF NOT EXISTS `PREFIX_accounting_zone_shop` (
  `id_accounting_zone_shop` int(11) NOT NULL AUTO_INCREMENT,
  `id_zone` int(11) NOT NULL,
  `id_shop` int(11) NOT NULL,
  `account_number` varchar(64) NOT NULL,
  PRIMARY KEY (`id_accounting_zone_shop`),
  UNIQUE KEY `id_zone` (`id_zone`,`id_shop`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_accounting_product_zone_shop` (
  `id_accounting_product_zone_shop` int(11) NOT NULL AUTO_INCREMENT,
  `id_product` int(11) NOT NULL,
  `id_shop` int(11) NOT NULL,
  `id_zone` int(11) NOT NULL,
  `account_number` varchar(64) NOT NULL,
  PRIMARY KEY (`id_accounting_product_zone_shop`),
  UNIQUE KEY `id_product` (`id_product`,`id_shop`,`id_zone`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;

/* PHP:add_accounting_tab(); */;


INSERT INTO `PREFIX_module_access` (`id_profile`, `id_module`, `configure`, `view`) (
	SELECT `id_profile`, `id_module`, 0, 1
	FROM `PREFIX_access` a, PREFIX_module m
	WHERE `id_tab` = (SELECT `id_tab` FROM `PREFIX_tab` WHERE `class_name` != "" LIMIT 1)
	AND a.`view` = 0
);

INSERT INTO `PREFIX_module_access` (`id_profile`, `id_module`, `configure`, `view`) (
	SELECT `id_profile`, `id_module`, 1, 1
	FROM `PREFIX_access` a, PREFIX_module m
	WHERE `id_tab` = (SELECT `id_tab` FROM `PREFIX_tab` WHERE `class_name` != "" LIMIT 1)
	AND a.`view` = 1
);


UPDATE `PREFIX_tab` SET `class_name` = 'AdminThemes' WHERE `class_name` = 'AdminAppearance';

INSERT INTO `PREFIX_hook` (
`name` ,
`title` ,
`description` ,
`position` ,
`live_edit`
)
VALUES ('taxmanager', 'taxmanager', NULL , '1', '0');

ALTER TABLE `PREFIX_tax_rule`
	ADD `zipcode_from` INT NOT NULL AFTER `id_state` ,
	ADD `zipcode_to` INT NOT NULL AFTER `zipcode_from` ,
	ADD `behavior` INT NOT NULL AFTER `zipcode_to`,
	ADD `description` VARCHAR( 100 ) NOT NULL AFTER `id_tax`;

INSERT INTO `PREFIX_tax_rule` 
	(`id_tax_rules_group`, `id_country`, `id_state`, `id_tax`, 
	`behavior`, `zipcode_from`, `zipcode_to`, `id_county`, 
	`description`, `state_behavior`, `county_behavior`)
	SELECT r.`id_tax_rules_group`, r.`id_country`, r.`id_state`, r.`id_tax`, 
	0, z.`from_zip_code`, z.`to_zip_code`, r.`id_county`, 
	r.`description`, r.`state_behavior`, r.county_behavior
	FROM 
		`PREFIX_tax_rule` r 
		INNER JOIN 
		`PREFIX_county_zip_code` z 
	ON (z.`id_county` = r.`id_county`);

UPDATE `PREFIX_tax_rule` SET `behavior` = GREATEST(`state_behavior`, `county_behavior`);

DELETE FROM `PREFIX_tax_rule`
WHERE `id_county` != 0
AND `zipcode_from` = 0;

ALTER TABLE `PREFIX_tax_rule`
  DROP `id_county`,
  DROP `state_behavior`,
  DROP `county_behavior`;

/* PHP:remove_tab(AdminCounty); */;
DROP TABLE `PREFIX_county_zip_code`;
DROP TABLE `PREFIX_county`;

ALTER TABLE `PREFIX_employee`
	ADD `id_last_order` tinyint(1) unsigned NOT NULL default '0',
	ADD `id_last_customer_message` tinyint(1) unsigned NOT NULL default '0',
	ADD `id_last_customer` tinyint(1) unsigned NOT NULL default '0';

INSERT INTO `PREFIX_configuration` (`name`, `value`, `date_add`, `date_upd`) VALUES
('PS_SHOW_NEW_ORDERS', '1', NOW(), NOW()),
('PS_SHOW_NEW_CUSTOMERS', '1', NOW(), NOW()),
('PS_SHOW_NEW_MESSAGES', '1', NOW(), NOW()),
('PS_FEATURE_FEATURE_ACTIVE', '1', NOW(), NOW()),
('PS_COMBINATION_FEATURE_ACTIVE', '1', NOW(), NOW()),
('PS_ADMINREFRESH_NOTIFICATION', '1', NOW(), NOW());

/* PHP:update_feature_detachable_cache(); */;

ALTER TABLE `PREFIX_product` ADD `available_date` DATE NOT NULL AFTER `available_for_order`;

ALTER TABLE `PREFIX_product_attribute` ADD `available_date` DATE NOT NULL;

/* Index was only used by deprecated function Image::positionImage() */
ALTER TABLE `PREFIX_image` DROP INDEX `product_position`;

CREATE TABLE IF NOT EXISTS `PREFIX_gender` (
  `id_gender` int(11) NOT NULL AUTO_INCREMENT,
  `type` tinyint(1) NOT NULL,
  PRIMARY KEY (`id_gender`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_gender_lang` (
  `id_gender` int(10) unsigned NOT NULL,
  `id_lang` int(10) unsigned NOT NULL,
  `name` varchar(20) NOT NULL,
  PRIMARY KEY (`id_gender`,`id_lang`),
  KEY `id_gender` (`id_gender`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

INSERT INTO `PREFIX_gender` (`id_gender`, `type`) VALUES
(1, 0),
(2, 1),
(3, 1);

INSERT INTO `PREFIX_gender_lang` (`id_gender`, `id_lang`, `name`) VALUES
(1, 1, 'Mr.'),
(1, 2, 'M.'),
(1, 3, 'Sr.'),
(1, 4, 'Herr'),
(1, 5, 'Sig.'),
(2, 1, 'Ms.'),
(2, 2, 'Mme'),
(2, 3, 'Sra.'),
(2, 4, 'Frau'),
(2, 5, 'Sig.ra'),
(3, 1, 'Miss'),
(3, 2, 'Mlle'),
(3, 3, 'Miss'),
(3, 4, 'Miss'),
(3, 5, 'Miss');

DELETE FROM `PREFIX_configuration` WHERE `name` = 'PS_FORCE_SMARTY_2';

CREATE TABLE IF NOT EXISTS `PREFIX_order_detail_tax` (
`id_order_detail` INT NOT NULL ,
`id_tax` INT NOT NULL
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

ALTER TABLE `PREFIX_tax` ADD `deleted` INT NOT NULL AFTER `active`;

/* PHP:update_order_detail_taxes(); */;

CREATE TABLE `PREFIX_customer_message_sync_imap` (
  `md5_header` varbinary(32) NOT NULL,
  KEY `md5_header_index` (`md5_header`(4))
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

ALTER TABLE  `PREFIX_customer_message` ADD  `private` TINYINT NOT NULL DEFAULT  '0' AFTER  `user_agent`;

/* PHP:add_new_tab(AdminGenders, fr:Genres|es:Genders|en:Genders|de:Genders|it:Genders, 2); */;

ALTER TABLE `PREFIX_attribute` ADD `position` INT( 10 ) UNSIGNED NOT NULL DEFAULT '0';

/* PHP:add_attribute_position(); */;

ALTER TABLE `PREFIX_product_download` CHANGE `date_deposit` `date_add` DATETIME NOT NULL ;
ALTER TABLE `PREFIX_product_download` CHANGE `physically_filename` `filename` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
ALTER TABLE `PREFIX_product_download` ADD `id_product_attribute` INT( 10 ) UNSIGNED NOT NULL AFTER `id_product` , ADD INDEX ( `id_product_attribute` );
ALTER TABLE `PREFIX_product_download` ADD `is_shareable` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT '0' AFTER `active`;

ALTER TABLE `PREFIX_attribute_group` ADD `position` INT( 10 ) UNSIGNED NOT NULL DEFAULT '0';

ALTER TABLE  `PREFIX_attribute_group` ADD  `group_type` ENUM('select', 'radio', 'color') NOT NULL DEFAULT  'select';
UPDATE `PREFIX_attribute_group` SET  `group_type`='color' WHERE `is_color_group` = 1;
ALTER TABLE `PREFIX_product` DROP `id_color_default`;

/* PHP:add_group_attribute_position(); */;

ALTER TABLE `PREFIX_feature` ADD `position` INT( 10 ) UNSIGNED NOT NULL DEFAULT '0';

/* PHP:add_feature_position(); */;

CREATE TABLE IF NOT EXISTS `PREFIX_request_sql` (
  `id_request_sql` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `sql` text NOT NULL,
  PRIMARY KEY (`id_request_sql`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;

/* PHP:add_new_tab(AdminRequestSql, fr:SQL Manager|es:SQL Manager|en:SQL Manager|de:Wunsh|it:SQL Manager, 9); */;

ALTER TABLE `PREFIX_carrier` ADD COLUMN `id_reference` int(10)  NOT NULL AFTER `id_carrier`;
UPDATE `PREFIX_carrier` SET id_reference = id_carrier;

ALTER TABLE `PREFIX_product` ADD `is_virtual` TINYINT( 1 ) NOT NULL DEFAULT '0' AFTER `cache_has_attachments`;

/* PHP:add_new_tab(AdminProducts, fr:Produits|es:Productos|en:Products|de:Produkten|it:Prodotti, 1); */;
/* PHP:add_new_tab(AdminCategories, fr:Catégories|es:Categoria|en:Categories|de:Kategorien|it:Categories, 1); */;

CREATE TABLE IF NOT EXISTS `PREFIX_employee_shop` (
`id_employee` INT( 11 ) UNSIGNED NOT NULL ,
`id_shop` INT( 11 ) UNSIGNED NOT NULL ,
PRIMARY KEY ( `id_employee` , `id_shop` )
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

INSERT INTO `PREFIX_employee_shop` (`id_employee`, `id_shop`) (SELECT `id_employee`, 1 FROM `PREFIX_employee`);

UPDATE `PREFIX_access` SET `view` = 0, `add` = 0, `edit` = 0, `delete` = 0 WHERE `id_tab` = (SELECT `id_tab` FROM `PREFIX_tab` t WHERE t.`class_name` = 'AdminShop' LIMIT 1) AND `id_profile` != 1;

ALTER TABLE `PREFIX_carrier` ADD `position` INT( 10 ) UNSIGNED NOT NULL DEFAULT '0';

/* PHP:add_carrier_position(); */;

ALTER TABLE `PREFIX_order_state` ADD COLUMN `shipped` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 AFTER `delivery`;
UPDATE `PREFIX_order_state` SET `shipped` = 1 WHERE id_order_state IN (4, 5);

CREATE TABLE `PREFIX_order_invoice` (
  `id_order_invoice` int(11) NOT NULL AUTO_INCREMENT,
  `id_order` int(11) NOT NULL,
  `number` int(11) NOT NULL,
  `total_discount_tax_excl` decimal(17,2) NOT NULL DEFAULT '0.00',
  `total_discount_tax_incl` decimal(17,2) NOT NULL DEFAULT '0.00',
  `total_paid_tax_excl` decimal(17,2) NOT NULL DEFAULT '0.00',
  `total_paid_tax_incl` decimal(17,2) NOT NULL DEFAULT '0.00',
  `total_products` decimal(17,2) NOT NULL DEFAULT '0.00',
  `total_products_wt` decimal(17,2) NOT NULL DEFAULT '0.00',
  `total_shipping_tax_excl` decimal(17,2) NOT NULL DEFAULT '0.00',
  `total_shipping_tax_incl` decimal(17,2) NOT NULL DEFAULT '0.00',
  `total_wrapping_tax_excl` decimal(17,2) NOT NULL DEFAULT '0.00',
  `total_wrapping_tax_incl` decimal(17,2) NOT NULL DEFAULT '0.00',
  `date_add` datetime NOT NULL,
  PRIMARY KEY (`id_order_invoice`),
  KEY `id_order` (`id_order`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

ALTER TABLE `PREFIX_order_detail` ADD `id_order_invoice` int(11) NOT NULL DEFAULT 0 AFTER `id_order`;

ALTER TABLE `PREFIX_payment_cc` CHANGE `id_payment_cc` `id_order_payment` INT NOT NULL auto_increment;
ALTER TABLE `PREFIX_payment_cc` ADD `id_order_invoice` varchar(255) NOT NULL AFTER `id_order_payment`;
ALTER TABLE `PREFIX_payment_cc` ADD `payment_method` varchar(255) NOT NULL AFTER `amount`;
ALTER TABLE `PREFIX_payment_cc` ADD `conversion_rate` decimal(13,6) NOT NULL DEFAULT 1 AFTER `payment_method`;

RENAME TABLE `PREFIX_payment_cc` TO `PREFIX_order_payment`;

ALTER TABLE `PREFIX_carrier`
	ADD COLUMN `max_width` int(10) DEFAULT 0 AFTER `position`,
	ADD COLUMN `max_height` int(10) DEFAULT 0 AFTER `max_width`,
	ADD COLUMN `max_depth` int(10) DEFAULT 0 AFTER `max_height`,
	ADD COLUMN `max_weight` int(10) DEFAULT 0 AFTER `max_depth`,
	ADD COLUMN `grade` int(10)  DEFAULT 0 AFTER `max_weight`;

ALTER TABLE `PREFIX_cart_product`
	ADD COLUMN `id_address_delivery` int(10) UNSIGNED DEFAULT 0 AFTER `date_add`;
	
UPDATE `PREFIX_cart_product` SET id_address_delivery = 0;

ALTER TABLE `PREFIX_cart` ADD COLUMN `allow_seperated_package` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 AFTER `gift_message`;
CREATE TABLE `PREFIX_product_carrier` (
  `id_product` int(10) unsigned NOT NULL,
  `id_carrier_reference` int(10) unsigned NOT NULL,
  `id_shop` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id_product`, `id_carrier_reference`, `id_shop`)
) ENGINE = ENGINE_TYPE DEFAULT CHARSET=utf8;

ALTER TABLE `PREFIX_customization` ADD COLUMN `id_address_delivery` int(10) UNSIGNED NOT NULL DEFAULT 0 AFTER `id_product_attribute`,
  DROP PRIMARY KEY,
  ADD PRIMARY KEY (`id_customization`, `id_cart`, `id_product`, `id_address_delivery`);

CREATE TABLE `PREFIX_cart_rule` (
	`id_cart_rule` int(10) unsigned NOT NULL auto_increment,
	`id_customer` int unsigned NOT NULL default 0,
	`date_from` datetime NOT NULL,
	`date_to` datetime NOT NULL,
	`description` text,
	`quantity` int(10) unsigned NOT NULL default 0,
	`quantity_per_user` int(10) unsigned NOT NULL default 0,
	`priority` int(10) unsigned NOT NULL default 1,
	`code` varchar(254) NOT NULL,
	`minimum_amount` decimal(17,2) NOT NULL default 0,
	`minimum_amount_tax` tinyint(1) NOT NULL default 0,
	`minimum_amount_currency` int unsigned NOT NULL default 0,
	`minimum_amount_shipping` tinyint(1) NOT NULL default 0,
	`country_restriction` tinyint(1) unsigned NOT NULL default 0,
	`carrier_restriction` tinyint(1) unsigned NOT NULL default 0,
	`group_restriction` tinyint(1) unsigned NOT NULL default 0,
	`cart_rule_restriction` tinyint(1) unsigned NOT NULL default 0,
	`product_restriction` tinyint(1) unsigned NOT NULL default 0,
	`free_shipping` tinyint(1) NOT NULL default 0,
	`reduction_percent` decimal(5,2) NOT NULL default 0,
	`reduction_amount` decimal(17,2) NOT NULL default 0,
	`reduction_tax` tinyint(1) unsigned NOT NULL default 0,
	`reduction_currency` int(10) unsigned NOT NULL default 0,
	`reduction_product` int(10) NOT NULL default 0,
	`gift_product` int(10) unsigned NOT NULL default 0,
	`active` tinyint(1) unsigned NOT NULL default 0,
	`date_add` datetime NOT NULL,
	`date_upd` datetime NOT NULL,
	PRIMARY KEY (`id_cart_rule`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE `PREFIX_cart_rule_country` (
	`id_cart_rule` int(10) unsigned NOT NULL,
	`id_country` int(10) unsigned NOT NULL,
	PRIMARY KEY  (`id_cart_rule`, `id_country`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE `PREFIX_cart_rule_group` (
	`id_cart_rule` int(10) unsigned NOT NULL,
	`id_group` int(10) unsigned NOT NULL,
	PRIMARY KEY  (`id_cart_rule`, `id_group`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE `PREFIX_cart_rule_carrier` (
	`id_cart_rule` int(10) unsigned NOT NULL,
	`id_carrier` int(10) unsigned NOT NULL,
	PRIMARY KEY  (`id_cart_rule`, `id_carrier`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE `PREFIX_cart_rule_combination` (
	`id_cart_rule_1` int(10) unsigned NOT NULL,
	`id_cart_rule_2` int(10) unsigned NOT NULL,
	PRIMARY KEY  (`id_cart_rule_1`, `id_cart_rule_2`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE `PREFIX_cart_rule_product_rule` (
	`id_product_rule` int(10) unsigned NOT NULL auto_increment,
	`id_cart_rule` int(10) unsigned NOT NULL,
	`quantity` int(10) unsigned NOT NULL default 1,
	`type` ENUM('products', 'categories', 'attributes') NOT NULL,
	PRIMARY KEY  (`id_product_rule`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

SET @id_currency_default = (SELECT value FROM `PREFIX_configuration` WHERE name = 'PS_CURRENCY_DEFAULT' LIMIT 1);
INSERT INTO `PREFIX_cart_rule` (
	`id_cart_rule`,
	`id_customer`,
	`date_from`,
	`date_to`,
	`description`,
	`quantity`,
	`quantity_per_user`,
	`priority`,
	`code`,
	`minimum_amount`,
	`minimum_amount_tax`,
	`minimum_amount_currency`,
	`minimum_amount_shipping`,
	`country_restriction`,
	`carrier_restriction`,
	`group_restriction`,
	`cart_rule_restriction`,
	`product_restriction`,
	`free_shipping`,
	`reduction_percent`,
	`reduction_amount`,
	`reduction_tax`,
	`reduction_currency`,
	`reduction_product`,
	`gift_product`,
	`active`,
	`date_add`,
	`date_upd` 
) (
	SELECT
		`id_discount`,
		`id_customer`,
		`date_from`,
		`date_to`,
		`name`,
		`quantity`,
		`quantity_per_user`,
		1,
		`name`,
		`minimal`,
		1,
		@id_currency_default,
		1,
		0,
		0,
		IF(id_group = 0, 0, 1),
		IF(cumulable = 1, 0, 1),
		0,
		IF(id_discount_type = 3, 1, 0),
		IF(id_discount_type = 1, value, 0),
		IF(id_discount_type = 2, value, 0),
		1,
		`id_currency`,
		0,
		0,
		`active`,
		`date_add`,
		`date_upd` 
	FROM `PREFIX_discount`
);

RENAME TABLE `PREFIX_discount_lang` TO `PREFIX_cart_rule_lang`;
ALTER TABLE `PREFIX_cart_rule_lang` CHANGE `id_discount` `id_cart_rule` int(10) unsigned NOT NULL;
ALTER TABLE `PREFIX_cart_rule_lang` CHANGE `description` `name` varchar(254) NOT NULL;
RENAME TABLE `PREFIX_discount_category` TO `PREFIX_cart_rule_product_rule_value`;
ALTER TABLE `PREFIX_cart_rule_product_rule_value` CHANGE `id_category` `id_item` int(10) unsigned NOT NULL;
ALTER TABLE `PREFIX_cart_rule_product_rule_value` CHANGE `id_discount` `id_product_rule` int(10) unsigned NOT NULL;
INSERT INTO `PREFIX_cart_rule_product_rule` (`id_product_rule`, `id_cart_rule`, `quantity`, `type`) (
	SELECT DISTINCT `id_product_rule`, `id_product_rule`, 1, 'categories' FROM `PREFIX_cart_rule_product_rule_value`
);
UPDATE `PREFIX_cart_rule` SET product_restriction = 1 WHERE `id_cart_rule` IN (SELECT `id_cart_rule` FROM `PREFIX_cart_rule_product_rule`);

ALTER TABLE `PREFIX_cart_discount` CHANGE `id_discount` `id_cart_rule` int(10) unsigned NOT NULL;
ALTER TABLE `PREFIX_order_discount` CHANGE `id_discount` `id_cart_rule` int(10) unsigned NOT NULL;
ALTER TABLE `PREFIX_order_discount` CHANGE `id_order_discount` `id_order_cart_rule` int(10) unsigned NOT NULL AUTO_INCREMENT;

RENAME TABLE `PREFIX_order_discount` TO `PREFIX_order_cart_rule`;
RENAME TABLE `PREFIX_cart_discount` TO `PREFIX_cart_cart_rule`;

INSERT INTO `PREFIX_configuration` (`name`, `value`, `date_add`, `date_upd`) (
	SELECT 'PS_CART_RULE_FEATURE_ACTIVE', `value`, NOW(), NOW() FROM `PREFIX_configuration` WHERE `name` = 'PS_DISCOUNT_FEATURE_ACTIVE' LIMIT 1
);

UPDATE `PREFIX_tab` SET class_name = 'AdminCartRules' WHERE class_name = 'AdminDiscounts';

UPDATE `PREFIX_hook` SET `name` = 'displayPayment' WHERE `name` = 'payment';
UPDATE `PREFIX_hook` SET `name` = 'actionValidateOrder' WHERE `name` = 'newOrder';
UPDATE `PREFIX_hook` SET `name` = 'actionPaymentConfirmation' WHERE `name` = 'paymentConfirm';
UPDATE `PREFIX_hook` SET `name` = 'displayPaymentReturn' WHERE `name` = 'paymentReturn';
UPDATE `PREFIX_hook` SET `name` = 'actionUpdateQuantity' WHERE `name` = 'updateQuantity';
UPDATE `PREFIX_hook` SET `name` = 'displayRightColumn' WHERE `name` = 'rightColumn';
UPDATE `PREFIX_hook` SET `name` = 'displayLeftColumn' WHERE `name` = 'leftColumn';
UPDATE `PREFIX_hook` SET `name` = 'displayHome' WHERE `name` = 'home';
UPDATE `PREFIX_hook` SET `name` = 'displayHeader' WHERE `name` = 'header';
UPDATE `PREFIX_hook` SET `name` = 'actionCartSave' WHERE `name` = 'cart';
UPDATE `PREFIX_hook` SET `name` = 'actionAuthentication' WHERE `name` = 'authentication';
UPDATE `PREFIX_hook` SET `name` = 'actionProductAdd' WHERE `name` = 'addproduct';
UPDATE `PREFIX_hook` SET `name` = 'actionProductUpdate' WHERE `name` = 'updateproduct';
UPDATE `PREFIX_hook` SET `name` = 'displayTop' WHERE `name` = 'top';
UPDATE `PREFIX_hook` SET `name` = 'displayRightColumnProduct' WHERE `name` = 'extraRight';
UPDATE `PREFIX_hook` SET `name` = 'actionProductDelete' WHERE `name` = 'deleteproduct';
UPDATE `PREFIX_hook` SET `name` = 'displayFooterProduct' WHERE `name` = 'productfooter';
UPDATE `PREFIX_hook` SET `name` = 'displayInvoice' WHERE `name` = 'invoice';
UPDATE `PREFIX_hook` SET `name` = 'actionOrderStatusUpdate' WHERE `name` = 'updateOrderStatus';
UPDATE `PREFIX_hook` SET `name` = 'displayAdminOrder' WHERE `name` = 'adminOrder';
UPDATE `PREFIX_hook` SET `name` = 'displayFooter' WHERE `name` = 'footer';
UPDATE `PREFIX_hook` SET `name` = 'displayPDFInvoice' WHERE `name` = 'PDFInvoice';
UPDATE `PREFIX_hook` SET `name` = 'displayAdminCustomers' WHERE `name` = 'adminCustomers';
UPDATE `PREFIX_hook` SET `name` = 'displayOrderConfirmation' WHERE `name` = 'orderConfirmation';
UPDATE `PREFIX_hook` SET `name` = 'actionCustomerAccountAdd' WHERE `name` = 'createAccount';
UPDATE `PREFIX_hook` SET `name` = 'displayCustomerAccount' WHERE `name` = 'customerAccount';
UPDATE `PREFIX_hook` SET `name` = 'actionOrderSlipAdd' WHERE `name` = 'orderSlip';
UPDATE `PREFIX_hook` SET `name` = 'displayProductTab' WHERE `name` = 'productTab';
UPDATE `PREFIX_hook` SET `name` = 'displayProductTabContent' WHERE `name` = 'productTabContent';
UPDATE `PREFIX_hook` SET `name` = 'displayShoppingCartFooter' WHERE `name` = 'shoppingCart';
UPDATE `PREFIX_hook` SET `name` = 'displayCustomerAccountForm' WHERE `name` = 'createAccountForm';
UPDATE `PREFIX_hook` SET `name` = 'displayAdminStatsModules' WHERE `name` = 'AdminStatsModules';
UPDATE `PREFIX_hook` SET `name` = 'displayAdminStatsGraphEngine' WHERE `name` = 'GraphEngine';
UPDATE `PREFIX_hook` SET `name` = 'actionOrderReturn' WHERE `name` = 'orderReturn';
UPDATE `PREFIX_hook` SET `name` = 'displayProductButtons' WHERE `name` = 'productActions';
UPDATE `PREFIX_hook` SET `name` = 'displayBackOfficeHome' WHERE `name` = 'backOfficeHome';
UPDATE `PREFIX_hook` SET `name` = 'displayAdminStatsGridEngine' WHERE `name` = 'GridEngine';
UPDATE `PREFIX_hook` SET `name` = 'actionWatermark' WHERE `name` = 'watermark';
UPDATE `PREFIX_hook` SET `name` = 'actionProductCancel' WHERE `name` = 'cancelProduct';
UPDATE `PREFIX_hook` SET `name` = 'displayLeftColumnProduct' WHERE `name` = 'extraLeft';
UPDATE `PREFIX_hook` SET `name` = 'actionProductOutOfStock' WHERE `name` = 'productOutOfStock';
UPDATE `PREFIX_hook` SET `name` = 'actionProductAttributeUpdate' WHERE `name` = 'updateProductAttribute';
UPDATE `PREFIX_hook` SET `name` = 'displayCarrierList' WHERE `name` = 'extraCarrier';
UPDATE `PREFIX_hook` SET `name` = 'displayShoppingCart' WHERE `name` = 'shoppingCartExtra';
UPDATE `PREFIX_hook` SET `name` = 'actionSearch' WHERE `name` = 'search';
UPDATE `PREFIX_hook` SET `name` = 'displayBeforePayment' WHERE `name` = 'backBeforePayment';
UPDATE `PREFIX_hook` SET `name` = 'actionCarrierUpdate' WHERE `name` = 'updateCarrier';
UPDATE `PREFIX_hook` SET `name` = 'actionOrderStatusPostUpdate' WHERE `name` = 'postUpdateOrderStatus';
UPDATE `PREFIX_hook` SET `name` = 'displayCustomerAccountFormTop' WHERE `name` = 'createAccountTop';
UPDATE `PREFIX_hook` SET `name` = 'displayBackOfficeHeader' WHERE `name` = 'backOfficeHeader';
UPDATE `PREFIX_hook` SET `name` = 'displayBackOfficeTop' WHERE `name` = 'backOfficeTop';
UPDATE `PREFIX_hook` SET `name` = 'displayBackOfficeFooter' WHERE `name` = 'backOfficeFooter';
UPDATE `PREFIX_hook` SET `name` = 'actionProductAttributeDelete' WHERE `name` = 'deleteProductAttribute';
UPDATE `PREFIX_hook` SET `name` = 'actionCarrierProcess' WHERE `name` = 'processCarrier';
UPDATE `PREFIX_hook` SET `name` = 'actionOrderDetail' WHERE `name` = 'orderDetail';
UPDATE `PREFIX_hook` SET `name` = 'displayBeforeCarrier' WHERE `name` = 'beforeCarrier';
UPDATE `PREFIX_hook` SET `name` = 'displayOrderDetail' WHERE `name` = 'orderDetailDisplayed';
UPDATE `PREFIX_hook` SET `name` = 'actionPaymentCCAdd' WHERE `name` = 'paymentCCAdded';
UPDATE `PREFIX_hook` SET `name` = 'displayProductComparison' WHERE `name` = 'extraProductComparison';
UPDATE `PREFIX_hook` SET `name` = 'actionCategoryAdd' WHERE `name` = 'categoryAddition';
UPDATE `PREFIX_hook` SET `name` = 'actionCategoryUpdate' WHERE `name` = 'categoryUpdate';
UPDATE `PREFIX_hook` SET `name` = 'actionCategoryDelete' WHERE `name` = 'categoryDeletion';
UPDATE `PREFIX_hook` SET `name` = 'actionBeforeAuthentication' WHERE `name` = 'beforeAuthentication';
UPDATE `PREFIX_hook` SET `name` = 'displayPaymentTop' WHERE `name` = 'paymentTop';
UPDATE `PREFIX_hook` SET `name` = 'actionHtaccessCreate' WHERE `name` = 'afterCreateHtaccess';
UPDATE `PREFIX_hook` SET `name` = 'actionAdminMetaSave' WHERE `name` = 'afterSaveAdminMeta';
UPDATE `PREFIX_hook` SET `name` = 'displayAttributeGroupForm' WHERE `name` = 'attributeGroupForm';
UPDATE `PREFIX_hook` SET `name` = 'actionAttributeGroupSave' WHERE `name` = 'afterSaveAttributeGroup';
UPDATE `PREFIX_hook` SET `name` = 'actionAttributeGroupDelete' WHERE `name` = 'afterDeleteAttributeGroup';
UPDATE `PREFIX_hook` SET `name` = 'displayFeatureForm' WHERE `name` = 'featureForm';
UPDATE `PREFIX_hook` SET `name` = 'actionFeatureSave' WHERE `name` = 'afterSaveFeature';
UPDATE `PREFIX_hook` SET `name` = 'actionFeatureDelete' WHERE `name` = 'afterDeleteFeature';
UPDATE `PREFIX_hook` SET `name` = 'actionProductSave' WHERE `name` = 'afterSaveProduct';
UPDATE `PREFIX_hook` SET `name` = 'actionProductListOverride' WHERE `name` = 'productListAssign';
UPDATE `PREFIX_hook` SET `name` = 'displayAttributeGroupPostProcess' WHERE `name` = 'postProcessAttributeGroup';
UPDATE `PREFIX_hook` SET `name` = 'displayFeaturePostProcess' WHERE `name` = 'postProcessFeature';
UPDATE `PREFIX_hook` SET `name` = 'displayFeatureValueForm' WHERE `name` = 'featureValueForm';
UPDATE `PREFIX_hook` SET `name` = 'displayFeatureValuePostProcess' WHERE `name` = 'postProcessFeatureValue';
UPDATE `PREFIX_hook` SET `name` = 'actionFeatureValueDelete' WHERE `name` = 'afterDeleteFeatureValue';
UPDATE `PREFIX_hook` SET `name` = 'actionFeatureValueSave' WHERE `name` = 'afterSaveFeatureValue';
UPDATE `PREFIX_hook` SET `name` = 'displayAttributeForm' WHERE `name` = 'attributeForm';
UPDATE `PREFIX_hook` SET `name` = 'actionAttributePostProcess' WHERE `name` = 'postProcessAttribute';
UPDATE `PREFIX_hook` SET `name` = 'actionAttributeDelete' WHERE `name` = 'afterDeleteAttribute';
UPDATE `PREFIX_hook` SET `name` = 'actionAttributeSave' WHERE `name` = 'afterSaveAttribute';
UPDATE `PREFIX_hook` SET `name` = 'actionTaxManager' WHERE `name` = 'taxManager';

ALTER TABLE `PREFIX_order_detail_tax` 
ADD `unit_amount` DECIMAL(16, 6) NOT NULL AFTER `id_tax`,
ADD `total_amount` DECIMAL(16, 6) NOT NULL AFTER `unit_amount`;


ALTER TABLE `PREFIX_specific_price` ADD `id_product_attribute` INT UNSIGNED NOT NULL AFTER `id_product`;
ALTER TABLE `PREFIX_specific_price` DROP INDEX `id_product`;
ALTER TABLE `PREFIX_specific_price` ADD INDEX `id_product` (`id_product`, `id_product_attribute`, `id_shop`, `id_currency`, `id_country`, `id_group`, `from_quantity`, `from`, `to`);


/* PHP:add_column_orders_reference_if_not_exists(); */;
ALTER TABLE `PREFIX_orders` ADD COLUMN `id_warehouse` int(10) unsigned DEFAULT 0 AFTER `id_carrier`;

ALTER TABLE `PREFIX_cart` ADD COLUMN `order_reference` varchar(9) AFTER `id_cart`;
ALTER TABLE `PREFIX_cart` ADD COLUMN `delivery_option` varchar(100) AFTER `id_carrier`;

ALTER TABLE `PREFIX_tax` ADD COLUMN `account_number` VARCHAR(64) NOT NULL;

/* PHP:add_new_tab(AdminAttributeGenerator, fr:Générateur de déclinaisons|es:Combinations generator|en:Combinations generator|de:Combinations generator|it:Combinations generator, -1); */;
/* PHP:add_new_tab(AdminCMSCategories, fr:Catégories CMS|es:CMS categories|en:CMS categories|de:CMS categories|it:CMS categories, -1); */;
/* PHP:add_new_tab(AdminCMS, fr:Pages CMS|es:CMS pages|en:CMS pages|de:CMS pages|it:CMS pages, -1); */;

UPDATE `PREFIX_quick_access` SET `link` = 'index.php?controller=AdminCategories&addcategory' WHERE `id_quick_access` = 3;

UPDATE `PREFIX_quick_access` SET `link` = 'index.php?controller=AdminProducts&addproduct' WHERE `id_quick_access` = 4;

UPDATE `PREFIX_access` SET `view` = '0', `add` = '0', `edit` = '0', `delete` = '0' WHERE `id_tab` = (SELECT `id_tab` FROM `PREFIX_tab` t WHERE t.`class_name` = 'AdminCmsCategories' LIMIT 1) AND `id_profile` = '3';
UPDATE `PREFIX_access` SET `view` = '0', `add` = '0', `edit` = '0', `delete` = '0' WHERE `id_tab` = (SELECT `id_tab` FROM `PREFIX_tab` t WHERE t.`class_name` = 'AdminCmsCategories' LIMIT 1) AND `id_profile` = '5';

UPDATE `PREFIX_access` SET `view` = '0', `add` = '0', `edit` = '0', `delete` = '0' WHERE `id_tab` = (SELECT `id_tab` FROM `PREFIX_tab` t WHERE t.`class_name` = 'AdminCms' LIMIT 1) AND `id_profile` = '3';
UPDATE `PREFIX_access` SET `view` = '0', `add` = '0', `edit` = '0', `delete` = '0' WHERE `id_tab` = (SELECT `id_tab` FROM `PREFIX_tab` t WHERE t.`class_name` = 'AdminCms' LIMIT 1) AND `id_profile` = '5';

INSERT INTO `PREFIX_configuration` (`name`, `value`, `date_add`, `date_upd`) VALUES ('PS_CUSTOMER_GROUP', '1', NOW(), NOW());

/* PHP:add_new_groups('Visiteur', 'Visitor'); */;
/* PHP:add_new_groups('Invité', 'Guest'); */;

UPDATE `PREFIX_employee` SET `bo_theme` = 'default';

UPDATE `PREFIX_tab` SET `class_name` = 'AdminCmsContent' WHERE `class_name` = 'AdminCMSContent';
UPDATE `PREFIX_tab` SET `class_name` = 'AdminCms' WHERE `class_name` = 'AdminCMS';
UPDATE `PREFIX_tab` SET `class_name` = 'AdminCmsCategories' WHERE `class_name` = 'AdminCMSCategories';
UPDATE `PREFIX_tab` SET `class_name` = 'AdminPdf' WHERE `class_name` = 'AdminPDF';

CREATE TABLE `PREFIX_order_carrier` (
  `id_order_carrier` int(11) NOT NULL AUTO_INCREMENT,
  `id_order` int(11) unsigned NOT NULL,
  `id_carrier` int(11) unsigned NOT NULL,
  `id_order_invoice` int(11) unsigned DEFAULT NULL,
  `weight` float DEFAULT NULL,
  `shipping_cost_tax_excl` decimal(20,6) DEFAULT NULL,
  `shipping_cost_tax_incl` decimal(20,6) DEFAULT NULL,
  `tracking_number` varchar(64) DEFAULT NULL,
  `date_add` datetime NOT NULL,
  PRIMARY KEY (`id_order_carrier`),
  KEY `id_order` (`id_order`),
  KEY `id_carrier` (`id_carrier`),
  KEY `id_order_invoice` (`id_order_invoice`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

ALTER TABLE `PREFIX_order_slip` ADD COLUMN `amount` DECIMAL(10,2) NOT NULL AFTER `shipping_cost`;
ALTER TABLE `PREFIX_order_slip` ADD COLUMN `shipping_cost_amount` DECIMAL(10,2) NOT NULL AFTER `amount`;
ALTER TABLE `PREFIX_order_slip` ADD COLUMN `partial` TINYINT(1) NOT NULL AFTER `shipping_cost_amount`;
ALTER TABLE `PREFIX_order_slip_detail` ADD COLUMN `amount` DECIMAL(10,2) NOT NULL AFTER `product_quantity`;

INSERT INTO `PREFIX_tab` (`id_parent`, `class_name`, `position`) VALUES (-1, 'AdminLogin', 0);

CREATE TABLE `PREFIX_hook_alias` (
  `id_hook_alias` int(10) unsigned NOT NULL auto_increment,
  `alias` varchar(64) NOT NULL,
  `name` varchar(64) NOT NULL,
  PRIMARY KEY  (`id_hook_alias`),
  UNIQUE KEY `alias` (`alias`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

INSERT INTO `PREFIX_hook_alias` (`id_hook_alias`, `name`, `alias`) VALUES
(1, 'displayPayment', 'payment'),
(2, 'actionValidateOrder', 'newOrder'),
(3, 'actionPaymentConfirmation', 'paymentConfirm'),
(4, 'displayPaymentReturn', 'paymentReturn'),
(5, 'actionUpdateQuantity', 'updateQuantity'),
(6, 'displayRightColumn', 'rightColumn'),
(7, 'displayLeftColumn', 'leftColumn'),
(8, 'displayHome', 'home'),
(9, 'displayHeader', 'header'),
(10, 'actionCartSave', 'cart'),
(11, 'actionAuthentication', 'authentication'),
(12, 'actionProductAdd', 'addproduct'),
(13, 'actionProductUpdate', 'updateproduct'),
(14, 'displayTop', 'top'),
(15, 'displayRightColumnProduct', 'extraRight'),
(16, 'actionProductDelete', 'deleteproduct'),
(17, 'displayFooterProduct', 'productfooter'),
(18, 'displayInvoice', 'invoice'),
(19, 'actionOrderStatusUpdate', 'updateOrderStatus'),
(20, 'displayAdminOrder', 'adminOrder'),
(21, 'displayFooter', 'footer'),
(22, 'displayPDFInvoice', 'PDFInvoice'),
(23, 'displayAdminCustomers', 'adminCustomers'),
(24, 'displayOrderConfirmation', 'orderConfirmation'),
(25, 'actionCustomerAccountAdd', 'createAccount'),
(26, 'displayCustomerAccount', 'customerAccount'),
(27, 'actionOrderSlipAdd', 'orderSlip'),
(28, 'displayProductTab', 'productTab'),
(29, 'displayProductTabContent', 'productTabContent'),
(30, 'displayShoppingCartFooter', 'shoppingCart'),
(31, 'displayCustomerAccountForm', 'createAccountForm'),
(32, 'displayAdminStatsModules', 'AdminStatsModules'),
(33, 'displayAdminStatsGraphEngine', 'GraphEngine'),
(34, 'actionOrderReturn', 'orderReturn'),
(35, 'displayProductButtons', 'productActions'),
(36, 'displayBackOfficeHome', 'backOfficeHome'),
(37, 'displayAdminStatsGridEngine', 'GridEngine'),
(38, 'actionWatermark', 'watermark'),
(39, 'actionProductCancel', 'cancelProduct'),
(40, 'displayLeftColumnProduct', 'extraLeft'),
(41, 'actionProductOutOfStock', 'productOutOfStock'),
(42, 'actionProductAttributeUpdate', 'updateProductAttribute'),
(43, 'displayCarrierList', 'extraCarrier'),
(44, 'displayShoppingCart', 'shoppingCartExtra'),
(45, 'actionSearch', 'search'),
(46, 'displayBeforePayment', 'backBeforePayment'),
(47, 'actionCarrierUpdate', 'updateCarrier'),
(48, 'actionOrderStatusPostUpdate', 'postUpdateOrderStatus'),
(49, 'displayCustomerAccountFormTop', 'createAccountTop'),
(50, 'displayBackOfficeHeader', 'backOfficeHeader'),
(51, 'displayBackOfficeTop', 'backOfficeTop'),
(52, 'displayBackOfficeFooter', 'backOfficeFooter'),
(53, 'actionProductAttributeDelete', 'deleteProductAttribute'),
(54, 'actionCarrierProcess', 'processCarrier'),
(55, 'actionOrderDetail', 'orderDetail'),
(56, 'displayBeforeCarrier', 'beforeCarrier'),
(57, 'displayOrderDetail', 'orderDetailDisplayed'),
(58, 'actionPaymentCCAdd', 'paymentCCAdded'),
(59, 'displayProductComparison', 'extraProductComparison'),
(60, 'actionCategoryAdd', 'categoryAddition'),
(61, 'actionCategoryUpdate', 'categoryUpdate'),
(62, 'actionCategoryDelete', 'categoryDeletion'),
(63, 'actionBeforeAuthentication', 'beforeAuthentication'),
(64, 'displayPaymentTop', 'paymentTop'),
(65, 'actionHtaccessCreate', 'afterCreateHtaccess'),
(66, 'actionAdminMetaSave', 'afterSaveAdminMeta'),
(67, 'displayAttributeGroupForm', 'attributeGroupForm'),
(68, 'actionAttributeGroupSave', 'afterSaveAttributeGroup'),
(69, 'actionAttributeGroupDelete', 'afterDeleteAttributeGroup'),
(70, 'displayFeatureForm', 'featureForm'),
(71, 'actionFeatureSave', 'afterSaveFeature'),
(72, 'actionFeatureDelete', 'afterDeleteFeature'),
(73, 'actionProductSave', 'afterSaveProduct'),
(74, 'actionProductListOverride', 'productListAssign'),
(75, 'displayAttributeGroupPostProcess', 'postProcessAttributeGroup'),
(76, 'displayFeaturePostProcess', 'postProcessFeature'),
(77, 'displayFeatureValueForm', 'featureValueForm'),
(78, 'displayFeatureValuePostProcess', 'postProcessFeatureValue'),
(79, 'actionFeatureValueDelete', 'afterDeleteFeatureValue'),
(80, 'actionFeatureValueSave', 'afterSaveFeatureValue'),
(81, 'displayAttributeForm', 'attributeForm'),
(82, 'actionAttributePostProcess', 'postProcessAttribute'),
(83, 'actionAttributeDelete', 'afterDeleteAttribute'),
(84, 'actionAttributeSave', 'afterSaveAttribute'),
(85, 'actionTaxManager', 'taxManager');

