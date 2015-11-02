/* STRUCTURE */
SET NAMES 'utf8';

ALTER TABLE PREFIX_orders
	ADD total_wrapping DECIMAL(10,2) NOT NULL DEFAULT 0 AFTER total_shipping;

ALTER TABLE PREFIX_carrier
	ADD range_behavior TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 AFTER shipping_handling;
	
ALTER TABLE PREFIX_order_detail
	ADD product_supplier_reference VARCHAR(32) NULL AFTER product_reference;
	
ALTER TABLE PREFIX_product
	ADD supplier_reference VARCHAR(32) NULL AFTER reference;

ALTER TABLE PREFIX_product_attribute
	ADD supplier_reference VARCHAR(32) NULL AFTER reference;

ALTER TABLE PREFIX_customer
	ADD UNIQUE customer_email(email(128));

ALTER TABLE PREFIX_product_download
	ADD active TINYINT(1) UNSIGNED NOT NULL DEFAULT 1 AFTER nb_downloadable;


/*  CONTENTS */
INSERT INTO PREFIX_hook (`name`, `title`, `description`, `position`) VALUES ('createAccountForm', 'Customer account creation form', 'Display some information on the form to create a customer account', 1);

INSERT INTO PREFIX_lang (`name`, `active`, `iso_code`) VALUES
('Română (Romanian)', 0, 'ro'),
('Νεοελληνική (Greek)', 0, 'gr'),
('Slovenčina  (Slovak)', 0, 'sk');

/* CONFIGURATION VARIABLE */

