/* PHP */
/* PHP:attribute_group_clean_combinations() */;

/* STRUCTURE */
SET NAMES 'utf8';

ALTER TABLE PREFIX_order_detail
	ADD product_quantity_discount DECIMAL(13,6) NOT NULL DEFAULT 0 AFTER product_price;
ALTER TABLE PREFIX_country
	ADD deleted TINYINT(1) NOT NULL DEFAULT 0;


/*  CONTENTS */

INSERT INTO PREFIX_lang (`name`, `active`, `iso_code`) VALUES
('Norsk  (Norwegian)', 0, 'no'),
('ภาษาไทย (Thai)', 0, 'th'),
('Dansk (Danish)', 0, 'dk');

/* CONFIGURATION VARIABLE */

