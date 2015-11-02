SET NAMES 'utf8';

ALTER TABLE `PREFIX_customer_message` CHANGE `ip_address` `ip_address` VARCHAR( 16 ) NULL DEFAULT NULL;

UPDATE `PREFIX_theme` SET product_per_page = '12' WHERE `product_per_page` = 0;


UPDATE `PREFIX_hook` SET  live_edit = '1' WHERE `name` IN('displayTop','displayAttributeForm','displayAttributeGroupForm','displayBeforeCarrier',
'displayBeforePayment','displayCarrierList','displayCustomerAccount','displayCustomerAccountForm','displayCustomerAccountFormTop','displayFeatureForm',
'displayFeatureValueForm','displayFooter','displayLeftColumnProduct','displayMyAccountBlock','displayMyAccountBlockfooter','displayOrderConfirmation',
'displayOrderDetail','displayPaymentReturn','displayPaymentTop','displayProductButtons','displayProductComparison','displayProductListFunctionalButtons',
'displayProductTab','displayProductTabContent','displayRightColumnProduct','displayShoppingCart','displayShoppingCartFooter');

ALTER TABLE `PREFIX_order_detail_tax` ADD INDEX id_order_detail (`id_order_detail`);

INSERT INTO `PREFIX_configuration` (`name`, `value`, `date_add`, `date_upd`) VALUES('PS_DISPLAY_BEST_SELLERS', '1', NOW(), NOW());

ALTER TABLE `PREFIX_orders` ADD INDEX (`current_state`);

INSERT INTO `PREFIX_configuration` (`name`, `value`, `date_add`, `date_upd`) VALUES('PS_USE_HTMLPURIFIER', '1', NOW(), NOW());

/* PHP:ps1607_language_code_update(); */;
/* PHP:drop_module_non_unique_index(); */;
/* PHP:p1607_drop_primary_key_if_exists(); */;
