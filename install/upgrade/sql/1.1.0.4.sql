SET NAMES 'utf8';

/* ##################################### */
/* 					STRUCTURE				  */
/* ##################################### */

ALTER TABLE PREFIX_order_detail
	DROP `deleted`,
	ADD product_quantity_cancelled INT(10) UNSIGNED NOT NULL AFTER product_quantity_return;

ALTER TABLE PREFIX_customization ADD quantity INT(10) NOT NULL;

ALTER TABLE PREFIX_order_return_detail ADD id_customization INT(10) NOT NULL DEFAULT 0 AFTER id_order_detail;
ALTER TABLE PREFIX_order_return_detail DROP PRIMARY KEY;
ALTER TABLE PREFIX_order_return_detail ADD PRIMARY KEY (id_order_return, id_order_detail, id_customization);


ALTER TABLE PREFIX_orders
  CHANGE payment payment VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  CHANGE module module VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;

/* ################################# */
/* 					CONTENTS				*/
/* ################################# */

INSERT INTO PREFIX_hook (`name`, `title`, `description`, `position`) VALUES
  ('productOutOfStock', 'Product out of stock', 'Make action while product is out of stock', 1),
  ('updateProductAttribute', 'Product attribute update', NULL, 1);
