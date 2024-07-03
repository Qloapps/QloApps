SET NAMES 'utf8';

ALTER TABLE `PREFIX_cart` ADD `is_advance_payment` tinyint(1) NOT NULL DEFAULT '0' AFTER `allow_seperated_package`;
UPDATE `PREFIX_cart`
SET `is_advance_payment` = 1
WHERE `PREFIX_cart`.`id_cart` IN (SELECT `id_cart` FROM `PREFIX_htl_customer_adv_payment`);

ALTER TABLE `PREFIX_orders`
ADD `is_advance_payment` tinyint(1) NOT NULL DEFAULT '0' AFTER `valid`,
ADD `advance_paid_amount` decimal(20,6) NOT NULL DEFAULT '0.00' AFTER `is_advance_payment`;
UPDATE `PREFIX_orders` as ord
INNER JOIN `PREFIX_htl_customer_adv_payment` adv ON ord.`id_order` = adv.`id_order`
SET
    ord.`is_advance_payment` = 1,
    ord.`advance_paid_amount` = adv.`total_paid_amount`;

ALTER TABLE `PREFIX_order_return`
CHANGE `state` `state` int(10) unsigned NOT NULL DEFAULT '1',
ADD `id_transaction` varchar(100) NOT NULL DEFAULT '' AFTER `state`,
ADD `payment_mode` varchar(255) NOT NULL DEFAULT '' AFTER `id_transaction`,
ADD `refunded_amount` decimal(20,6) NOT NULL DEFAULT '0.000000' AFTER `payment_mode`,
ADD `by_admin` tinyint(1) unsigned NOT NULL DEFAULT '0' AFTER `question`;

ALTER TABLE `PREFIX_order_return_detail`
ADD `id_order_return_detail` int(10) unsigned NOT NULL auto_increment FIRST,
ADD `id_htl_booking` int(11) NOT NULL AFTER `id_order_return`,
ADD `refunded_amount` decimal(20,6) NOT NULL DEFAULT '0.000000' AFTER id_htl_booking,
CHANGE `id_order_detail` `id_order_detail` int(10) unsigned NOT NULL DEFAULT '0',
ADD KEY `id_htl_booking` (`id_htl_booking`),
DROP PRIMARY KEY,
ADD PRIMARY KEY (`id_order_return_detail`);

ALTER TABLE `PREFIX_order_return_state`
ADD `send_email_to_customer` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' AFTER `color`,
ADD `send_email_to_superadmin` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' AFTER `send_email_to_customer`,
ADD `send_email_to_employee` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' AFTER `send_email_to_superadmin`,
ADD `send_email_to_hotelier` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' AFTER `send_email_to_employee`,
ADD `denied` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' AFTER `send_email_to_hotelier`,
ADD `refunded` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' AFTER `denied`,
ADD `module_name` VARCHAR(255) NULL DEFAULT NULL AFTER `refunded`;
TRUNCATE TABLE `PREFIX_order_return_state`;
INSERT INTO `PREFIX_order_return_state`
    (`id_order_return_state`, `color`, `send_email_to_customer`, `send_email_to_superadmin`, `send_email_to_employee`, `send_email_to_hotelier`, `denied`, `refunded`, `module_name`)
VALUES
    ('1', '#4169E1', '1', '1', '1', '1', '0', '0', NULL),
    ('2', '#32CD32', '1', '1', '1', '1', '0', '0', NULL),
    ('3', '#4169E1', '1', '1', '1', '1', '1', '0', NULL),
    ('4', '#32CD32', '1', '1', '1', '1', '0', '1', NULL);

ALTER TABLE `PREFIX_order_return_state_lang`
ADD `customer_template` varchar(64) NOT NULL AFTER `name`,
ADD `admin_template` varchar(64) NOT NULL AFTER `customer_template`;
TRUNCATE TABLE `PREFIX_order_return_state_lang`;
INSERT INTO `PREFIX_order_return_state_lang` (`id_order_return_state`, `id_lang`, `name`, `customer_template`, `admin_template`)
SELECT '1', `id_lang`, 'Waiting for confirmation', 'order_refund_waiting_customer', 'order_refund_waiting_admin' FROM `PREFIX_lang`;
INSERT INTO `PREFIX_order_return_state_lang` (`id_order_return_state`, `id_lang`, `name`, `customer_template`, `admin_template`)
SELECT '2', `id_lang`, 'Request received', 'order_refund_received_customer', 'order_refund_received_admin' FROM `PREFIX_lang`;
INSERT INTO `PREFIX_order_return_state_lang` (`id_order_return_state`, `id_lang`, `name`, `customer_template`, `admin_template`)
SELECT '3', `id_lang`, 'Denied', 'order_refund_denied_customer', 'order_refund_denied_admin' FROM `PREFIX_lang`;
INSERT INTO `PREFIX_order_return_state_lang` (`id_order_return_state`, `id_lang`, `name`, `customer_template`, `admin_template`)
SELECT '4', `id_lang`, 'Completed', 'order_refund_completed_customer', 'order_refund_completed_admin' FROM `PREFIX_lang`;


ALTER TABLE `PREFIX_order_slip_detail`
ADD `id_htl_booking` int(10) unsigned NOT NULL AFTER `id_order_detail`,
DROP PRIMARY KEY,
ADD PRIMARY KEY (`id_order_slip`,`id_htl_booking`);

DELETE FROM `PREFIX_tab`
WHERE `class_name` = 'AdminReturn';

update `PREFIX_feature_value_lang`
set value=replace(value,'.png','.jpg');


/* PHP:change_feature_image_to_jpg(); */;




