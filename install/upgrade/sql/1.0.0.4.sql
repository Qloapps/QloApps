/* STRUCTURE */
SET NAMES 'utf8';

ALTER TABLE PREFIX_order_detail
	ADD product_ean13 VARCHAR(13) CHARACTER SET utf8 COLLATE utf8_general_ci NULL AFTER product_price;
ALTER TABLE PREFIX_order_detail
	ADD product_quantity_return INT(10) UNSIGNED NOT NULL DEFAULT 0 AFTER product_quantity;

ALTER TABLE PREFIX_state
	ADD tax_behavior SMALLINT(1) NOT NULL DEFAULT 0 AFTER iso_code;

ALTER TABLE PREFIX_product
	ADD reduction_from DATE NOT NULL AFTER reduction_percent;
ALTER TABLE PREFIX_product
	ADD reduction_to DATE NOT NULL AFTER reduction_from;

ALTER TABLE PREFIX_range_weight
	ADD id_carrier INTEGER UNSIGNED DEFAULT NULL AFTER id_range_weight;
ALTER TABLE PREFIX_range_weight
	DROP INDEX range_weight_index,
	ADD UNIQUE range_weight_unique (delimiter1, delimiter2, id_carrier);
ALTER TABLE PREFIX_range_price
	ADD id_carrier INTEGER UNSIGNED DEFAULT NULL AFTER id_range_price;
ALTER TABLE PREFIX_range_price
	DROP INDEX range_price_index,
	ADD UNIQUE range_price_unique (delimiter1, delimiter2, id_carrier);

/*  CONTENTS */
/* One request per insert, if one die, other can be inserted */
INSERT INTO PREFIX_hook (`name`, `title`, `description`, `position`) VALUES ('adminCustomers', 'Display in Back-Office, tab AdminCustomers', 'Launch modules when the tab AdminCustomers is displayed on back-office.', 0);
INSERT INTO PREFIX_hook (`name`, `title`, `description`, `position`) VALUES ('createAccount', 'Successful customer create account', 'Called when new customer create account successfuled', 0);
INSERT INTO PREFIX_hook (`name`, `title`, `description`, `position`) VALUES ('customerAccount', 'Customer account page display in front office', 'Called when a customer access to his account.', 1);
INSERT INTO PREFIX_hook (`name`, `title`, `description`, `position`) VALUES ('orderSlip', 'Called when a order slip is created', 'Called when a quantity of one product change in an order.', 0);
INSERT INTO PREFIX_hook (`name`, `title`, `description`, `position`) VALUES ('productTab', 'Tabs on product page', 'Called on order product page tabs', 0);
INSERT INTO PREFIX_hook (`name`, `title`, `description`, `position`) VALUES ('productTabContent', 'Content of tabs on product page', 'Called on order product page tabs', 0);
INSERT INTO PREFIX_hook (`name`, `title`, `description`, `position`) VALUES ('shoppingCart', 'Shopping cart footer', 'Display some specific informations on the shopping cart page', 0);

INSERT INTO `PREFIX_configuration` (`name`, `value`, `date_add`, `date_upd`) VALUES ('PS_MAIL_TYPE', '3', NOW(), NOW());

INSERT INTO `PREFIX_configuration` (`name`, `value`, `date_add`, `date_upd`) VALUES ('PS_TOKEN_ENABLE', '0', NOW(), NOW());

INSERT INTO `PREFIX_configuration` (`name`, `value`, `date_add`, `date_upd`) VALUES ('PS_GIFT_WRAPPING_PRICE', '0', NOW(), NOW());

/* NEW RANGES */

INSERT  IGNORE  INTO  PREFIX_range_price ( id_carrier, delimiter1, delimiter2 )
	(SELECT c.id_carrier, rp.delimiter1, rp.delimiter2
	FROM  PREFIX_range_price rp
	CROSS  JOIN  PREFIX_carrier c
	WHERE c.deleted = 0
	AND c.active = 1
	);

UPDATE `PREFIX_delivery` d SET d.`id_range_price` = (
	SELECT rw.`id_range_price` FROM `PREFIX_range_price` rw WHERE
		rw.`id_carrier` = d.`id_carrier` AND
		rw.`delimiter1` = (
			SELECT `delimiter1` FROM `PREFIX_range_price` rw2 WHERE rw2.`id_range_price` = d.`id_range_price` LIMIT 1
		) AND
		rw.`delimiter2` = (
			SELECT `delimiter2` FROM `PREFIX_range_price` rw3 WHERE rw3.`id_range_price` = d.`id_range_price` LIMIT 1
		)
);

INSERT  IGNORE  INTO  PREFIX_range_weight ( id_carrier, delimiter1, delimiter2 )
	(SELECT c.id_carrier, rp.delimiter1, rp.delimiter2
	FROM  PREFIX_range_weight rp
	CROSS  JOIN  PREFIX_carrier c
	WHERE c.deleted = 0
	AND c.active = 1
	);

UPDATE `PREFIX_delivery` d SET d.`id_range_weight` = (
	SELECT rw.`id_range_weight` FROM `PREFIX_range_weight` rw WHERE
		rw.`id_carrier` = d.`id_carrier` AND
		rw.`delimiter1` = (
			SELECT `delimiter1` FROM `PREFIX_range_weight` rw2 WHERE rw2.`id_range_weight` = d.`id_range_weight` LIMIT 1
		) AND
		rw.`delimiter2` = (
			SELECT `delimiter2` FROM `PREFIX_range_weight` rw3 WHERE rw3.`id_range_weight` = d.`id_range_weight` LIMIT 1
		)
);

DELETE FROM PREFIX_range_price WHERE id_carrier IS NULL;
DELETE FROM PREFIX_range_weight WHERE id_carrier IS NULL;

/* CONFIGURATION VARIABLE */

