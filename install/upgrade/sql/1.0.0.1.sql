/* PHP */
/* PHP:latin1_database_to_utf8(); */;

/* STRUCTURE */
SET NAMES 'utf8';

CREATE TABLE PREFIX_attribute_impact (
  id_attribute_impact int(11) NOT NULL AUTO_INCREMENT,
  id_product int(11) NOT NULL,
  id_attribute int(11) NOT NULL,
  weight float NOT NULL,
  price decimal(10,2) NOT NULL,
  PRIMARY KEY  (id_attribute_impact),
  UNIQUE KEY id_product (id_product,id_attribute)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE PREFIX_supplier_lang (
  id_supplier INTEGER UNSIGNED NOT NULL,
  id_lang INTEGER UNSIGNED NOT NULL,
  description TEXT NULL,
  INDEX supplier_lang_index(id_supplier, id_lang)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE PREFIX_manufacturer_lang (
  id_manufacturer INTEGER UNSIGNED NOT NULL,
  id_lang INTEGER UNSIGNED NOT NULL,
  description TEXT NULL,
  INDEX manufacturer_lang_index(id_manufacturer, id_lang)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE PREFIX_state (
  id_state int(10) unsigned NOT NULL AUTO_INCREMENT,
  id_country int(11) NOT NULL,
  name varchar(64) NOT NULL,
  iso_code varchar(3) NOT NULL,
  active tinyint(1) NOT NULL default 0,
  PRIMARY KEY (id_state)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

ALTER TABLE PREFIX_customer ADD secure_key VARCHAR(32) NOT NULL DEFAULT '-1' AFTER id_gender;
ALTER TABLE PREFIX_orders ADD secure_key VARCHAR(32) NOT NULL DEFAULT '-1' AFTER id_address_invoice;
ALTER TABLE PREFIX_product ADD id_category_default INT NULL AFTER id_tax;
ALTER TABLE PREFIX_category_product ADD position INTEGER UNSIGNED NOT NULL DEFAULT 0 AFTER id_product;
ALTER TABLE PREFIX_product ADD INDEX (id_category_default);
ALTER TABLE PREFIX_order_detail ADD ecotax DECIMAL(10, 2) NOT NULL DEFAULT 0 AFTER tax_rate;
ALTER TABLE PREFIX_employee
	CHANGE name lastname VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
	CHANGE surname firstname VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE PREFIX_address
	CHANGE name lastname VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
	CHANGE surname firstname VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE PREFIX_customer
	CHANGE name lastname VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
	CHANGE surname firstname VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE PREFIX_quick_access ADD new_window TINYINT( 1 ) NOT NULL DEFAULT 0 AFTER id_quick_access;

/*  CONTENTS */
UPDATE PREFIX_hook_module SET id_hook = 14 WHERE id_hook = 9;
UPDATE PREFIX_quick_access SET new_window = 1 WHERE id_quick_access = 2 LIMIT 1;
INSERT INTO PREFIX_hook (name, title, description, position) VALUES ('orderConfirmation', 'Order confirmation page', 'Called on order confirmation page', 0);
UPDATE PREFIX_order_detail odt
	SET product_price = (
		odt.product_price * (
			SELECT conversion_rate FROM PREFIX_currency c, PREFIX_orders o WHERE o.id_order = odt.id_order AND c.id_currency = o.id_currency
		)
);
UPDATE PREFIX_product p SET p.id_category_default = (SELECT id_category FROM PREFIX_category_product cp WHERE cp.id_product = p.id_product GROUP BY id_product ORDER BY cp.id_category ASC);
UPDATE PREFIX_category_product cp SET cp.position= cp.id_product;

/* NEW TABS */

INSERT INTO PREFIX_tab (id_parent, class_name, position) VALUES ((SELECT tmp.`id_tab` FROM (SELECT `id_tab` FROM PREFIX_tab t WHERE t.class_name = 'AdminOrders' LIMIT 1) AS tmp), 'AdminPrintPDF', (SELECT tmp.max FROM (SELECT MAX(position) max FROM `PREFIX_tab` WHERE id_parent = (SELECT tmp.`id_tab` FROM (SELECT `id_tab` FROM PREFIX_tab t WHERE t.class_name = 'AdminOrders' LIMIT 1) AS tmp )) AS tmp));
INSERT INTO PREFIX_tab_lang (id_lang, id_tab, name) (
	SELECT id_lang,
	(SELECT id_tab FROM PREFIX_tab t WHERE t.class_name = 'AdminPrintPDF' LIMIT 1),
	'Print invoices' FROM PREFIX_lang);
UPDATE `PREFIX_tab_lang` SET `name` = 'Impression factures'
	WHERE `id_tab` = (SELECT `id_tab` FROM `PREFIX_tab` t WHERE t.class_name = 'AdminPrintPDF')
	AND `id_lang` = (SELECT `id_lang` FROM `PREFIX_lang` l WHERE l.iso_code = 'fr');
INSERT INTO PREFIX_access (id_profile, id_tab, `view`, `add`, edit, `delete`) VALUES ('1', (SELECT id_tab FROM PREFIX_tab t WHERE t.class_name = 'AdminPrintPDF' LIMIT 1), 1, 1, 1, 1);

INSERT INTO PREFIX_tab (id_parent, class_name, position) VALUES (-1, 'AdminSearch', 2);
INSERT INTO PREFIX_tab_lang (id_lang, id_tab, name) (
	SELECT id_lang,
	(SELECT id_tab FROM PREFIX_tab t WHERE t.class_name = 'AdminSearch' LIMIT 1),
	'Search' FROM PREFIX_lang);
UPDATE `PREFIX_tab_lang` SET `name` = 'Recherche'
	WHERE `id_tab` = (SELECT `id_tab` FROM `PREFIX_tab` t WHERE t.class_name = 'AdminSearch')
	AND `id_lang` = (SELECT `id_lang` FROM `PREFIX_lang` l WHERE l.iso_code = 'fr');
INSERT INTO PREFIX_access (id_profile, id_tab, `view`, `add`, edit, `delete`) VALUES ('1', (SELECT id_tab FROM PREFIX_tab t WHERE t.class_name = 'AdminSearch' LIMIT 1), 1, 1, 1, 1);

INSERT INTO PREFIX_tab (id_parent, class_name, position) VALUES ((SELECT tmp.`id_tab` FROM (SELECT `id_tab` FROM PREFIX_tab t WHERE t.class_name = 'AdminPreferences' LIMIT 1) AS tmp), 'AdminLocalization', (SELECT tmp.max FROM (SELECT MAX(position) max FROM `PREFIX_tab` WHERE id_parent = (SELECT tmp.`id_tab` FROM (SELECT `id_tab` FROM PREFIX_tab t WHERE t.class_name = 'AdminPreferences' LIMIT 1) AS tmp )) AS tmp));
INSERT INTO PREFIX_tab_lang (id_lang, id_tab, name) (
	SELECT id_lang,
	(SELECT id_tab FROM PREFIX_tab t WHERE t.class_name = 'AdminLocalization' LIMIT 1),
	'Localization' FROM PREFIX_lang);
UPDATE `PREFIX_tab_lang` SET `name` = 'Localisation'
	WHERE `id_tab` = (SELECT `id_tab` FROM `PREFIX_tab` t WHERE t.class_name = 'AdminLocalization')
	AND `id_lang` = (SELECT `id_lang` FROM `PREFIX_lang` l WHERE l.iso_code = 'fr');
INSERT INTO PREFIX_access (id_profile, id_tab, `view`, `add`, edit, `delete`) VALUES ('1', (SELECT id_tab FROM PREFIX_tab t WHERE t.class_name = 'AdminLocalization' LIMIT 1), 1, 1, 1, 1);

INSERT INTO PREFIX_tab (id_parent, class_name, position) VALUES ((SELECT tmp.`id_tab` FROM (SELECT `id_tab` FROM PREFIX_tab t WHERE t.class_name = 'AdminShipping' LIMIT 1) AS tmp), 'AdminStates', (SELECT tmp.max FROM (SELECT MAX(position) max FROM `PREFIX_tab` WHERE id_parent = (SELECT tmp.`id_tab` FROM (SELECT `id_tab` FROM PREFIX_tab t WHERE t.class_name = 'AdminShipping' LIMIT 1) AS tmp )) AS tmp));
INSERT INTO PREFIX_tab_lang (id_lang, id_tab, name) (
	SELECT id_lang,
	(SELECT id_tab FROM PREFIX_tab t WHERE t.class_name = 'AdminStates' LIMIT 1),
	'States' FROM PREFIX_lang);
UPDATE `PREFIX_tab_lang` SET `name` = 'Etats'
	WHERE `id_tab` = (SELECT `id_tab` FROM `PREFIX_tab` t WHERE t.class_name = 'AdminStates')
	AND `id_lang` = (SELECT `id_lang` FROM `PREFIX_lang` l WHERE l.iso_code = 'fr');
INSERT INTO PREFIX_access (`id_profile`, `id_tab`, `view`, `add`, edit, `delete`) VALUES ('1', (SELECT id_tab FROM PREFIX_tab t WHERE t.class_name = 'AdminStates' LIMIT 1), 1, 1, 1, 1);

INSERT INTO PREFIX_image_type (`name`, `width`, `height`, `products`, `categories`, `manufacturers`, `suppliers`) VALUES ('home', 129, 129, 1, 0, 0, 0);

/* CONFIGURATION VARIABLE */
INSERT INTO PREFIX_configuration (name, value, date_add, date_upd) VALUES ('PS_NB_DAYS_NEW_PRODUCT', 20, NOW(), NOW());
INSERT INTO PREFIX_configuration (name, value, date_add, date_upd) VALUES ('PS_WEIGHT_UNIT', 'kg', NOW(), NOW());
INSERT INTO PREFIX_configuration (name, value, date_add, date_upd) VALUES ('PS_BLOCK_CART_AJAX', '1', NOW(), NOW());
INSERT INTO PREFIX_configuration (name, value, date_add, date_upd) VALUES ('PS_FO_PROTOCOL', 'http://', NOW(), NOW());
UPDATE PREFIX_configuration SET name = 'PS_MAIL_SMTP_PORT', value = 25 WHERE name = 'PS_MAIL_SMTP_PORT' AND value = 'default';
UPDATE PREFIX_configuration SET name = 'PS_MAIL_SMTP_PORT', value = 465 WHERE name = 'PS_MAIL_SMTP_PORT' AND value = 'secure';

/* PHP:add_new_tab(AdminPDF, fr:PDF|es:PDF|en:PDF|de:PDF|it:PDF, 3); */;