/* STRUCTURE */

CREATE TABLE `PREFIX_discount_quantity` (
	id_discount_quantity INT UNSIGNED NOT NULL auto_increment,
	id_discount_type INT UNSIGNED NOT NULL,
	id_product INT UNSIGNED NOT NULL,
	id_product_attribute INT UNSIGNED NULL,
	quantity INT UNSIGNED NOT NULL,
	value DECIMAL(10,2) UNSIGNED NOT NULL,
	PRIMARY KEY (id_discount_quantity)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

ALTER TABLE `PREFIX_product` ADD quantity_discount BOOL NULL DEFAULT 0 AFTER out_of_stock;

/*  CONTENTS */


/* CONFIGURATION VARIABLE */

UPDATE `PREFIX_configuration` SET name = 'PS_TAX', value = 1 WHERE name = 'PS_TAX_NO' AND value = 0;
UPDATE `PREFIX_configuration` SET name = 'PS_TAX', value = 0 WHERE name = 'PS_TAX_NO' AND value = 1;