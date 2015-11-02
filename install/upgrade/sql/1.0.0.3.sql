/* STRUCTURE */
SET NAMES 'utf8';

ALTER TABLE PREFIX_attribute_group_lang DROP INDEX attribute_group_lang_index, ADD PRIMARY KEY (id_attribute_group, id_lang);
ALTER TABLE PREFIX_discount_lang DROP INDEX  discount_lang_index, ADD PRIMARY KEY (id_discount, id_lang);
ALTER TABLE PREFIX_discount_type_lang DROP INDEX  discount_type_lang_index, ADD PRIMARY KEY  (id_discount_type, id_lang);
ALTER TABLE PREFIX_manufacturer_lang DROP INDEX  manufacturer_lang_index, ADD PRIMARY KEY  (id_manufacturer, id_lang);
ALTER TABLE PREFIX_supplier_lang DROP INDEX  supplier_lang_index, ADD PRIMARY KEY  (id_supplier, id_lang);
ALTER TABLE PREFIX_profile_lang DROP INDEX  profile_lang_index, ADD PRIMARY KEY  (id_profile, id_lang);
ALTER TABLE PREFIX_configuration_lang DROP INDEX  configuration_lang_index, ADD PRIMARY KEY  (id_configuration, id_lang);
ALTER TABLE PREFIX_tab_lang DROP INDEX  tab_lang, ADD PRIMARY KEY  (id_tab, id_lang);

ALTER TABLE PREFIX_product ADD id_color_default INT UNSIGNED NULL AFTER id_category_default;
ALTER TABLE PREFIX_attribute_group ADD is_color_group TINYINT(1) NOT NULL DEFAULT 0;
ALTER TABLE PREFIX_attribute ADD color VARCHAR(32) NULL DEFAULT NULL;
ALTER TABLE PREFIX_currency CHANGE conversion_rate conversion_rate DECIMAL(13, 6) NOT NULL  ;
ALTER TABLE PREFIX_address ADD id_state INT NULL AFTER id_country;
ALTER TABLE PREFIX_state ADD id_zone INT NULL AFTER id_country;
ALTER TABLE PREFIX_country ADD contains_states tinyint(1) NOT NULL DEFAULT 0;

UPDATE PREFIX_customer SET secure_key = MD5(RAND()) WHERE secure_key = '-1';
UPDATE PREFIX_orders o SET secure_key = (SELECT secure_key FROM PREFIX_customer c WHERE c.id_customer = o.id_customer);

CREATE TABLE PREFIX_order_return (
  id_order_return INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  id_customer INTEGER UNSIGNED NOT NULL,
  id_order INTEGER UNSIGNED NOT NULL,
  state tinyint(1) unsigned NOT NULL DEFAULT 0,
  question TEXT NOT NULL,
  date_add DATETIME NOT NULL,
  date_upd DATETIME NOT NULL,
  PRIMARY KEY(id_order_return),
  INDEX order_return_customer(id_customer)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE PREFIX_order_return_detail (
  id_order_return INTEGER UNSIGNED NOT NULL,
  id_order_detail  INTEGER UNSIGNED NOT NULL,
  product_quantity int(10) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY  (id_order_return,id_order_detail)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE PREFIX_order_return_state (
  id_order_return_state int(10) unsigned NOT NULL auto_increment,
  color varchar(32) default NULL,
  PRIMARY KEY  (`id_order_return_state`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE PREFIX_order_return_state_lang (
  id_order_return_state int(10) unsigned NOT NULL,
  id_lang int(10) unsigned NOT NULL,
  name varchar(64) NOT NULL,
  UNIQUE KEY `order_state_lang_index` (`id_order_return_state`,`id_lang`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE PREFIX_order_slip (
  id_order_slip INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  id_customer INTEGER UNSIGNED NOT NULL,
  id_order INTEGER UNSIGNED NOT NULL,
  date_add DATETIME NOT NULL,
  date_upd DATETIME NOT NULL,
  PRIMARY KEY(id_order_slip),
  INDEX order_slip_customer(id_customer)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE PREFIX_order_slip_detail (
  id_order_slip INTEGER UNSIGNED NOT NULL,
  id_order_detail  INTEGER UNSIGNED NOT NULL,
  product_quantity int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY  (`id_order_slip`,`id_order_detail`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE PREFIX_tax_state (
  id_tax int(10) unsigned NOT NULL,
  id_state int(10) unsigned NOT NULL,
  INDEX tax_state_index(id_tax, id_state)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*  CONTENTS */

INSERT INTO PREFIX_order_return_state (`id_order_return_state`, `color`) VALUES
(1, '#ADD8E6'),
(2, '#EEDDFF'),
(3, '#DDFFAA'),
(4, '#FFD3D3'),
(5, '#FFFFBB');

INSERT INTO PREFIX_order_return_state_lang (`id_order_return_state`, `id_lang`, `name`) VALUES
(1, 1, 'Waiting for confirmation'),
(2, 1, 'Waiting for package'),
(3, 1, 'Package received'),
(4, 1, 'Return denied'),
(5, 1, 'Return completed'),
(1, 2, 'En attente de confirmation'),
(2, 2, 'En attente du colis'),
(3, 2, 'Colis reçu'),
(4, 2, 'Retour refusé'),
(5, 2, 'Retour terminé');

UPDATE PREFIX_country SET contains_states = 1 WHERE id_country = 21;

INSERT INTO `PREFIX_state` (`id_state`, `id_country`, `id_zone`, `name`, `iso_code`, `active`) VALUES
(1, 21, 2, 'Alabama', 'AL', 1),
(2, 21, 2, 'Alaska', 'AK', 1),
(3, 21, 2, 'Arizona', 'AZ', 1),
(4, 21, 2, 'Arkansas', 'AR', 1),
(5, 21, 2, 'California', 'CA', 1),
(6, 21, 2, 'Colorado', 'CO', 1),
(7, 21, 2, 'Connecticut', 'CT', 1),
(8, 21, 2, 'Delaware', 'DE', 1),
(9, 21, 2, 'Florida', 'FL', 1),
(10, 21, 2, 'Georgia', 'GA', 1),
(11, 21, 2, 'Hawaii', 'HI', 1),
(12, 21, 2, 'Idaho', 'ID', 1),
(13, 21, 2, 'Illinois', 'IL', 1),
(14, 21, 2, 'Indiana', 'IN', 1),
(15, 21, 2, 'Iowa', 'IA', 1),
(16, 21, 2, 'Kansas', 'KS', 1),
(17, 21, 2, 'Kentucky', 'KY', 1),
(18, 21, 2, 'Louisiana', 'LA', 1),
(19, 21, 2, 'Maine', 'ME', 1),
(20, 21, 2, 'Maryland', 'MD', 1),
(21, 21, 2, 'Massachusetts', 'MA', 1),
(22, 21, 2, 'Michigan', 'MI', 1),
(23, 21, 2, 'Minnesota', 'MN', 1),
(24, 21, 2, 'Mississippi', 'MS', 1),
(25, 21, 2, 'Missouri', 'MO', 1),
(26, 21, 2, 'Montana', 'MT', 1),
(27, 21, 2, 'Nebraska', 'NE', 1),
(28, 21, 2, 'Nevada', 'NV', 1),
(29, 21, 2, 'New Hampshire', 'NH', 1),
(30, 21, 2, 'New Jersey', 'NJ', 1),
(31, 21, 2, 'New Mexico', 'NM', 1),
(32, 21, 2, 'New York', 'NY', 1),
(33, 21, 2, 'North Carolina', 'NC', 1),
(34, 21, 2, 'North Dakota', 'ND', 1),
(35, 21, 2, 'Ohio', 'OH', 1),
(36, 21, 2, 'Oklahoma', 'OK', 1),
(37, 21, 2, 'Oregon', 'OR', 1),
(38, 21, 2, 'Pennsylvania', 'PA', 1),
(39, 21, 2, 'Rhode Island', 'RI', 1),
(40, 21, 2, 'South Carolina', 'SC', 1),
(41, 21, 2, 'South Dakota', 'SD', 1),
(42, 21, 2, 'Tennessee', 'TN', 1),
(43, 21, 2, 'Texas', 'TX', 1),
(44, 21, 2, 'Utah', 'UT', 1),
(45, 21, 2, 'Vermont', 'VT', 1),
(46, 21, 2, 'Virginia', 'VA', 1),
(47, 21, 2, 'Washington', 'WA', 1),
(48, 21, 2, 'West Virginia', 'WV', 1),
(49, 21, 2, 'Wisconsin', 'WI', 1),
(50, 21, 2, 'Wyoming', 'WY', 1),
(51, 21, 2, 'Puerto Rico', 'PR', 1),
(52, 21, 2, 'US Virgin Islands', 'VI', 1);

INSERT INTO `PREFIX_lang` (`name`, `active`, `iso_code`) VALUES
('Deutsch (German)', 1, 'de'),
('Español (Spanish)', 1, 'es'),
('Nederlands (Dutch)', 1, 'nl'),
('Bahasa Indonesia (Indonesian)', 1, 'id'),
('Italiano (Italian)', 1, 'it'),
('Język polski (Polish)', 1, 'pl'),
('Português (Portuguese)', 1, 'pt'),
('Čeština (Czech)', 1, 'cs'),
('Pусский язык (Russian)', 0, 'ru'),
('Türkçe (Turkish)', 0, 'tr'),
('Tiếng Việt (Vietnamese)', 0, 'vn');

/* NEW LANGS */

INSERT IGNORE INTO `PREFIX_tab_lang` (`id_tab`, `id_lang`, `name`)
    (SELECT `id_tab`, id_lang, (SELECT tl.`name`
        FROM `PREFIX_tab_lang` tl
        WHERE tl.`id_lang` = (SELECT c.`value`
            FROM `PREFIX_configuration` c
            WHERE c.`name` = 'PS_LANG_DEFAULT' LIMIT 1) AND tl.`id_tab`=`PREFIX_tab`.`id_tab`)
    FROM `PREFIX_lang` CROSS JOIN `PREFIX_tab`);

INSERT IGNORE INTO `PREFIX_country_lang` (`id_country`, `id_lang`, `name`)
    (SELECT `id_country`, id_lang, (SELECT tl.`name`
        FROM `PREFIX_country_lang` tl
        WHERE tl.`id_lang` = (SELECT c.`value`
            FROM `PREFIX_configuration` c
            WHERE c.`name` = 'PS_LANG_DEFAULT' LIMIT 1) AND tl.`id_country`=`PREFIX_country`.`id_country`)
    FROM `PREFIX_lang` CROSS JOIN `PREFIX_country`);

INSERT IGNORE INTO `PREFIX_quick_access_lang` (`id_quick_access`, `id_lang`, `name`)
    (SELECT `id_quick_access`, id_lang, (SELECT tl.`name`
        FROM `PREFIX_quick_access_lang` tl
        WHERE tl.`id_lang` = (SELECT c.`value`
            FROM `PREFIX_configuration` c
            WHERE c.`name` = 'PS_LANG_DEFAULT' LIMIT 1) AND tl.`id_quick_access`=`PREFIX_quick_access`.`id_quick_access`)
    FROM `PREFIX_lang` CROSS JOIN `PREFIX_quick_access`);

INSERT IGNORE INTO `PREFIX_attribute_group_lang` (`id_attribute_group`, `id_lang`, `name`, `public_name`)
    (SELECT `id_attribute_group`, id_lang, (SELECT tl.`name`
        FROM `PREFIX_attribute_group_lang` tl
        WHERE tl.`id_lang` = (SELECT c.`value`
            FROM `PREFIX_configuration` c
            WHERE c.`name` = 'PS_LANG_DEFAULT' LIMIT 1) AND tl.`id_attribute_group`=`PREFIX_attribute_group`.`id_attribute_group`),
		(SELECT tl.`public_name`
        FROM `PREFIX_attribute_group_lang` tl
        WHERE tl.`id_lang` = (SELECT c.`value`
            FROM `PREFIX_configuration` c
            WHERE c.`name` = 'PS_LANG_DEFAULT' LIMIT 1) AND tl.`id_attribute_group`=`PREFIX_attribute_group`.`id_attribute_group`)
    FROM `PREFIX_lang` CROSS JOIN `PREFIX_attribute_group`);

INSERT IGNORE INTO `PREFIX_attribute_lang` (`id_attribute`, `id_lang`, `name`)
    (SELECT `id_attribute`, id_lang, (SELECT tl.`name`
        FROM `PREFIX_attribute_lang` tl
        WHERE tl.`id_lang` = (SELECT c.`value`
            FROM `PREFIX_configuration` c
            WHERE c.`name` = 'PS_LANG_DEFAULT' LIMIT 1) AND tl.`id_attribute`=`PREFIX_attribute`.`id_attribute`)
    FROM `PREFIX_lang` CROSS JOIN `PREFIX_attribute`);

INSERT IGNORE INTO `PREFIX_carrier_lang` (`id_carrier`, `id_lang`, `delay`)
    (SELECT `id_carrier`, id_lang, (SELECT tl.`delay`
        FROM `PREFIX_carrier_lang` tl
        WHERE tl.`id_lang` = (SELECT c.`value`
            FROM `PREFIX_configuration` c
            WHERE c.`name` = 'PS_LANG_DEFAULT' LIMIT 1) AND tl.`id_carrier`=`PREFIX_carrier`.`id_carrier`)
    FROM `PREFIX_lang` CROSS JOIN `PREFIX_carrier`);

INSERT IGNORE INTO `PREFIX_contact_lang` (`id_contact`, `id_lang`, `name`, `description`)
    (SELECT `id_contact`, id_lang, (SELECT tl.`name`
        FROM `PREFIX_contact_lang` tl
        WHERE tl.`id_lang` = (SELECT c.`value`
            FROM `PREFIX_configuration` c
            WHERE c.`name` = 'PS_LANG_DEFAULT' LIMIT 1) AND tl.`id_contact`=`PREFIX_contact`.`id_contact`),
		(SELECT tl.`description`
        FROM `PREFIX_contact_lang` tl
        WHERE tl.`id_lang` = (SELECT c.`value`
            FROM `PREFIX_configuration` c
            WHERE c.`name` = 'PS_LANG_DEFAULT' LIMIT 1) AND tl.`id_contact`=`PREFIX_contact`.`id_contact`)
    FROM `PREFIX_lang` CROSS JOIN `PREFIX_contact`);

INSERT IGNORE INTO `PREFIX_discount_lang` (`id_discount`, `id_lang`, `description`)
    (SELECT `id_discount`, id_lang, (SELECT tl.`description`
        FROM `PREFIX_discount_lang` tl
        WHERE tl.`id_lang` = (SELECT c.`value`
            FROM `PREFIX_configuration` c
            WHERE c.`name` = 'PS_LANG_DEFAULT' LIMIT 1) AND tl.`id_discount`=`PREFIX_discount`.`id_discount`)
    FROM `PREFIX_lang` CROSS JOIN `PREFIX_discount`);
	
INSERT IGNORE INTO `PREFIX_discount_type_lang` (`id_discount_type`, `id_lang`, `name`)
    (SELECT `id_discount_type`, id_lang, (SELECT tl.`name`
        FROM `PREFIX_discount_type_lang` tl
        WHERE tl.`id_lang` = (SELECT c.`value`
            FROM `PREFIX_configuration` c
            WHERE c.`name` = 'PS_LANG_DEFAULT' LIMIT 1) AND tl.`id_discount_type`=`PREFIX_discount_type`.`id_discount_type`)
    FROM `PREFIX_lang` CROSS JOIN `PREFIX_discount_type`);

INSERT IGNORE INTO `PREFIX_feature_lang` (`id_feature`, `id_lang`, `name`)
    (SELECT `id_feature`, id_lang, (SELECT tl.`name`
        FROM `PREFIX_feature_lang` tl
        WHERE tl.`id_lang` = (SELECT c.`value`
            FROM `PREFIX_configuration` c
            WHERE c.`name` = 'PS_LANG_DEFAULT' LIMIT 1) AND tl.`id_feature`=`PREFIX_feature`.`id_feature`)
    FROM `PREFIX_lang` CROSS JOIN `PREFIX_feature`);

INSERT IGNORE INTO `PREFIX_feature_value_lang` (`id_feature_value`, `id_lang`, `value`)
    (SELECT `id_feature_value`, id_lang, (SELECT tl.`value`
        FROM `PREFIX_feature_value_lang` tl
        WHERE tl.`id_lang` = (SELECT c.`value`
            FROM `PREFIX_configuration` c
            WHERE c.`name` = 'PS_LANG_DEFAULT' LIMIT 1) AND tl.`id_feature_value`=`PREFIX_feature_value`.`id_feature_value`)
    FROM `PREFIX_lang` CROSS JOIN `PREFIX_feature_value`);

INSERT IGNORE INTO `PREFIX_image_lang` (`id_image`, `id_lang`, `legend`)
    (SELECT `id_image`, id_lang, (SELECT tl.`legend`
        FROM `PREFIX_image_lang` tl
        WHERE tl.`id_lang` = (SELECT c.`value`
            FROM `PREFIX_configuration` c
            WHERE c.`name` = 'PS_LANG_DEFAULT' LIMIT 1) AND tl.`id_image`=`PREFIX_image`.`id_image`)
    FROM `PREFIX_lang` CROSS JOIN `PREFIX_image`);

INSERT IGNORE INTO `PREFIX_manufacturer_lang` (`id_manufacturer`, `id_lang`, `description`)
    (SELECT `id_manufacturer`, id_lang, (SELECT tl.`description`
        FROM `PREFIX_manufacturer_lang` tl
        WHERE tl.`id_lang` = (SELECT c.`value`
            FROM `PREFIX_configuration` c
            WHERE c.`name` = 'PS_LANG_DEFAULT' LIMIT 1) AND tl.`id_manufacturer`=`PREFIX_manufacturer`.`id_manufacturer`)
    FROM `PREFIX_lang` CROSS JOIN `PREFIX_manufacturer`);

INSERT IGNORE INTO `PREFIX_order_return_state_lang` (`id_order_return_state`, `id_lang`, `name`)
    (SELECT `id_order_return_state`, id_lang, (SELECT tl.`name`
        FROM `PREFIX_order_return_state_lang` tl
        WHERE tl.`id_lang` = (SELECT c.`value`
            FROM `PREFIX_configuration` c
            WHERE c.`name` = 'PS_LANG_DEFAULT' LIMIT 1) AND tl.`id_order_return_state`=`PREFIX_order_return_state`.`id_order_return_state`)
    FROM `PREFIX_lang` CROSS JOIN `PREFIX_order_return_state`);

INSERT IGNORE INTO `PREFIX_order_state_lang` (`id_order_state`, `id_lang`, `name`, `template`)
    (SELECT `id_order_state`, id_lang, (SELECT tl.`name`
        FROM `PREFIX_order_state_lang` tl
        WHERE tl.`id_lang` = (SELECT c.`value`
            FROM `PREFIX_configuration` c
            WHERE c.`name` = 'PS_LANG_DEFAULT' LIMIT 1) AND tl.`id_order_state`=`PREFIX_order_state`.`id_order_state`),
	(SELECT tl.`template`
        FROM `PREFIX_order_state_lang` tl
        WHERE tl.`id_lang` = (SELECT c.`value`
            FROM `PREFIX_configuration` c
            WHERE c.`name` = 'PS_LANG_DEFAULT' LIMIT 1) AND tl.`id_order_state`=`PREFIX_order_state`.`id_order_state`)
    FROM `PREFIX_lang` CROSS JOIN `PREFIX_order_state`);

INSERT IGNORE INTO `PREFIX_profile_lang` (`id_profile`, `id_lang`, `name`)
    (SELECT `id_profile`, id_lang, (SELECT tl.`name`
        FROM `PREFIX_profile_lang` tl
        WHERE tl.`id_lang` = (SELECT c.`value`
            FROM `PREFIX_configuration` c
            WHERE c.`name` = 'PS_LANG_DEFAULT' LIMIT 1) AND tl.`id_profile`=`PREFIX_profile`.`id_profile`)
    FROM `PREFIX_lang` CROSS JOIN `PREFIX_profile`);

INSERT IGNORE INTO `PREFIX_supplier_lang` (`id_supplier`, `id_lang`, `description`)
    (SELECT `id_supplier`, id_lang, (SELECT tl.`description`
        FROM `PREFIX_supplier_lang` tl
        WHERE tl.`id_lang` = (SELECT c.`value`
            FROM `PREFIX_configuration` c
            WHERE c.`name` = 'PS_LANG_DEFAULT' LIMIT 1) AND tl.`id_supplier`=`PREFIX_supplier`.`id_supplier`)
    FROM `PREFIX_lang` CROSS JOIN `PREFIX_supplier`);

INSERT IGNORE INTO `PREFIX_tax_lang` (`id_tax`, `id_lang`, `name`)
    (SELECT `id_tax`, id_lang, (SELECT tl.`name`
        FROM `PREFIX_tax_lang` tl
        WHERE tl.`id_lang` = (SELECT c.`value`
            FROM `PREFIX_configuration` c
            WHERE c.`name` = 'PS_LANG_DEFAULT' LIMIT 1) AND tl.`id_tax`=`PREFIX_tax`.`id_tax`)
    FROM `PREFIX_lang` CROSS JOIN `PREFIX_tax`);

/* products */
INSERT IGNORE INTO `PREFIX_product_lang` (`id_product`, `id_lang`, `description`, `description_short`, `link_rewrite`, `meta_description`, `meta_keywords`, `meta_title`, `name`, `availability`)
    (SELECT `id_product`, id_lang,
	(SELECT tl.`description`
        FROM `PREFIX_product_lang` tl
        WHERE tl.`id_lang` = (SELECT c.`value`
            FROM `PREFIX_configuration` c
            WHERE c.`name` = 'PS_LANG_DEFAULT' LIMIT 1) AND tl.`id_product`=`PREFIX_product`.`id_product`),
	(SELECT tl.`description_short`
        FROM `PREFIX_product_lang` tl
        WHERE tl.`id_lang` = (SELECT c.`value`
            FROM `PREFIX_configuration` c
            WHERE c.`name` = 'PS_LANG_DEFAULT' LIMIT 1) AND tl.`id_product`=`PREFIX_product`.`id_product`),
	(SELECT tl.`link_rewrite`
        FROM `PREFIX_product_lang` tl
        WHERE tl.`id_lang` = (SELECT c.`value`
            FROM `PREFIX_configuration` c
            WHERE c.`name` = 'PS_LANG_DEFAULT' LIMIT 1) AND tl.`id_product`=`PREFIX_product`.`id_product`),
	(SELECT tl.`meta_description`
        FROM `PREFIX_product_lang` tl
        WHERE tl.`id_lang` = (SELECT c.`value`
            FROM `PREFIX_configuration` c
            WHERE c.`name` = 'PS_LANG_DEFAULT' LIMIT 1) AND tl.`id_product`=`PREFIX_product`.`id_product`),
	(SELECT tl.`meta_keywords`
        FROM `PREFIX_product_lang` tl
        WHERE tl.`id_lang` = (SELECT c.`value`
            FROM `PREFIX_configuration` c
            WHERE c.`name` = 'PS_LANG_DEFAULT' LIMIT 1) AND tl.`id_product`=`PREFIX_product`.`id_product`),
	(SELECT tl.`meta_title`
        FROM `PREFIX_product_lang` tl
        WHERE tl.`id_lang` = (SELECT c.`value`
            FROM `PREFIX_configuration` c
            WHERE c.`name` = 'PS_LANG_DEFAULT' LIMIT 1) AND tl.`id_product`=`PREFIX_product`.`id_product`),
	(SELECT tl.`name`
        FROM `PREFIX_product_lang` tl
        WHERE tl.`id_lang` = (SELECT c.`value`
            FROM `PREFIX_configuration` c
            WHERE c.`name` = 'PS_LANG_DEFAULT' LIMIT 1) AND tl.`id_product`=`PREFIX_product`.`id_product`),
	(SELECT tl.`availability`
        FROM `PREFIX_product_lang` tl
        WHERE tl.`id_lang` = (SELECT c.`value`
            FROM `PREFIX_configuration` c
            WHERE c.`name` = 'PS_LANG_DEFAULT' LIMIT 1) AND tl.`id_product`=`PREFIX_product`.`id_product`)
	FROM `PREFIX_lang` CROSS JOIN `PREFIX_product`);
	
/* categories */
INSERT IGNORE INTO `PREFIX_category_lang` (`id_category`, `id_lang`, `description`, `link_rewrite`, `meta_description`, `meta_keywords`, `meta_title`, `name`)
    (SELECT `id_category`, id_lang,
	(SELECT tl.`description`
        FROM `PREFIX_category_lang` tl
        WHERE tl.`id_lang` = (SELECT c.`value`
            FROM `PREFIX_configuration` c
            WHERE c.`name` = 'PS_LANG_DEFAULT' LIMIT 1) AND tl.`id_category`=`PREFIX_category`.`id_category`),
	(SELECT tl.`link_rewrite`
        FROM `PREFIX_category_lang` tl
        WHERE tl.`id_lang` = (SELECT c.`value`
            FROM `PREFIX_configuration` c
            WHERE c.`name` = 'PS_LANG_DEFAULT' LIMIT 1) AND tl.`id_category`=`PREFIX_category`.`id_category`),
	(SELECT tl.`meta_description`
        FROM `PREFIX_category_lang` tl
        WHERE tl.`id_lang` = (SELECT c.`value`
            FROM `PREFIX_configuration` c
            WHERE c.`name` = 'PS_LANG_DEFAULT' LIMIT 1) AND tl.`id_category`=`PREFIX_category`.`id_category`),
	(SELECT tl.`meta_keywords`
        FROM `PREFIX_category_lang` tl
        WHERE tl.`id_lang` = (SELECT c.`value`
            FROM `PREFIX_configuration` c
            WHERE c.`name` = 'PS_LANG_DEFAULT' LIMIT 1) AND tl.`id_category`=`PREFIX_category`.`id_category`),
	(SELECT tl.`meta_title`
        FROM `PREFIX_category_lang` tl
        WHERE tl.`id_lang` = (SELECT c.`value`
            FROM `PREFIX_configuration` c
            WHERE c.`name` = 'PS_LANG_DEFAULT' LIMIT 1) AND tl.`id_category`=`PREFIX_category`.`id_category`),
	(SELECT tl.`name`
        FROM `PREFIX_category_lang` tl
        WHERE tl.`id_lang` = (SELECT c.`value`
            FROM `PREFIX_configuration` c
            WHERE c.`name` = 'PS_LANG_DEFAULT' LIMIT 1) AND tl.`id_category`=`PREFIX_category`.`id_category`)
	FROM `PREFIX_lang` CROSS JOIN `PREFIX_category`);



/* NEW TABS */

INSERT INTO PREFIX_tab (id_parent, class_name, position) VALUES ((SELECT tmp.`id_tab` FROM (SELECT `id_tab` FROM PREFIX_tab t WHERE t.class_name = 'AdminOrders' LIMIT 1) AS tmp), 'AdminReturn', (SELECT tmp.max FROM (SELECT MAX(position) max FROM `PREFIX_tab` WHERE id_parent = (SELECT tmp.`id_tab` FROM (SELECT `id_tab` FROM PREFIX_tab t WHERE t.class_name = 'AdminOrders' LIMIT 1) AS tmp )) AS tmp));
INSERT INTO PREFIX_tab_lang (id_lang, id_tab, name) (
	SELECT id_lang,
	(SELECT id_tab FROM PREFIX_tab t WHERE t.class_name = 'AdminReturn' LIMIT 1),
	'Merchandise returns (RMAs)' FROM PREFIX_lang);
UPDATE `PREFIX_tab_lang` SET `name` = 'Retours produit'
	WHERE `id_tab` = (SELECT `id_tab` FROM `PREFIX_tab` t WHERE t.class_name = 'AdminReturn')
	AND `id_lang` = (SELECT `id_lang` FROM `PREFIX_lang` l WHERE l.iso_code = 'fr');
INSERT INTO PREFIX_access (id_profile, id_tab, `view`, `add`, edit, `delete`) VALUES ('1', (SELECT id_tab FROM PREFIX_tab t WHERE t.class_name = 'AdminReturn' LIMIT 1), 1, 1, 1, 1);

INSERT INTO PREFIX_tab (id_parent, class_name, position) VALUES ((SELECT tmp.`id_tab` FROM (SELECT `id_tab` FROM PREFIX_tab t WHERE t.class_name = 'AdminOrders' LIMIT 1) AS tmp), 'AdminSlip', (SELECT tmp.max FROM (SELECT MAX(position) max FROM `PREFIX_tab` WHERE id_parent = (SELECT tmp.`id_tab` FROM (SELECT `id_tab` FROM PREFIX_tab t WHERE t.class_name = 'AdminOrders' LIMIT 1) AS tmp )) AS tmp));
INSERT INTO PREFIX_tab_lang (id_lang, id_tab, name) (
	SELECT id_lang,
	(SELECT id_tab FROM PREFIX_tab t WHERE t.class_name = 'AdminSlip' LIMIT 1),
	'Credit slips' FROM PREFIX_lang);
UPDATE `PREFIX_tab_lang` SET `name` = 'Avoirs'
	WHERE `id_tab` = (SELECT `id_tab` FROM `PREFIX_tab` t WHERE t.class_name = 'AdminSlip')
	AND `id_lang` = (SELECT `id_lang` FROM `PREFIX_lang` l WHERE l.iso_code = 'fr');
INSERT INTO PREFIX_access (id_profile, id_tab, `view`, `add`, edit, `delete`) VALUES ('1', (SELECT id_tab FROM PREFIX_tab t WHERE t.class_name = 'AdminSlip' LIMIT 1), 1, 1, 1, 1);


/* CONFIGURATION VARIABLE */
INSERT INTO `PREFIX_configuration` (`name`, `value`, `date_add`, `date_upd`) VALUES ('PS_ORDER_RETURN', '0', NOW(), NOW());
INSERT INTO `PREFIX_configuration` (`name`, `value`, `date_add`, `date_upd`) VALUES ('PS_ORDER_RETURN_NB_DAYS', '7', NOW(), NOW());
UPDATE PREFIX_configuration SET name = 'PS_SSL_ENABLED' WHERE name = 'PS_FO_PROTOCOL';
UPDATE PREFIX_configuration SET name = 'PS_SSL_ENABLED', value = 0 WHERE name = 'PS_SSL_ENABLED' AND value = 'http://';
UPDATE PREFIX_configuration SET name = 'PS_SSL_ENABLED', value = 1 WHERE name = 'PS_SSL_ENABLED' AND value = 'https://';

