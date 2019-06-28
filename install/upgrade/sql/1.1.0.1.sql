/* PHP */
/* PHP:attribute_group_clean_combinations(); */;
/* PHP:configuration_double_cleaner(); */;

SET NAMES 'utf8';

/* ##################################### */
/* 					STRUCTURE				*/
/* ##################################### */
DROP TABLE IF EXISTS PREFIX_gender;
DROP TABLE IF EXISTS PREFIX_search;
ALTER TABLE PREFIX_category_lang
	ADD INDEX category_name (name);
ALTER TABLE PREFIX_order_detail
	MODIFY COLUMN product_name VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE PREFIX_order_detail
	ADD deleted TINYINT(3) UNSIGNED NOT NULL DEFAULT 0;
ALTER TABLE PREFIX_configuration
	MODIFY COLUMN name VARCHAR(32) NOT NULL UNIQUE;
ALTER TABLE PREFIX_orders
	ADD invoice_number INTEGER(10) UNSIGNED NOT NULL DEFAULT 0 AFTER total_wrapping;
ALTER TABLE PREFIX_orders
	ADD delivery_number INTEGER(10) UNSIGNED NOT NULL DEFAULT 0 AFTER invoice_number;
ALTER TABLE PREFIX_orders
	ADD invoice_date DATETIME NOT NULL AFTER delivery_number;
ALTER TABLE PREFIX_orders
	ADD delivery_date DATETIME NOT NULL AFTER invoice_date;
ALTER TABLE PREFIX_order_detail
	CHANGE product_price product_price DECIMAL(13, 6) NOT NULL DEFAULT 0.000000;
ALTER TABLE PREFIX_order_slip
	ADD shipping_cost TINYINT UNSIGNED NOT NULL DEFAULT 0 AFTER id_order;
ALTER TABLE PREFIX_order_state
	ADD delivery TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 AFTER logable;
ALTER TABLE PREFIX_country
	DROP deleted;
ALTER TABLE PREFIX_product
	ADD customizable BOOL NOT NULL DEFAULT 0 AFTER quantity_discount;
ALTER TABLE PREFIX_product
	ADD uploadable_files TINYINT NOT NULL DEFAULT 0 AFTER customizable;
ALTER TABLE PREFIX_product
	ADD text_fields TINYINT NOT NULL DEFAULT 0 AFTER uploadable_files;
ALTER TABLE PREFIX_product_lang
	CHANGE availability available_now VARCHAR(255) NULL;
ALTER TABLE PREFIX_product_lang
	ADD available_later VARCHAR(255) NULL AFTER available_now;
ALTER TABLE PREFIX_access
	DROP id_access;
ALTER TABLE PREFIX_access
	DROP INDEX access_profile;
ALTER TABLE PREFIX_access
	DROP INDEX access_tab;
ALTER TABLE PREFIX_access
	ADD PRIMARY KEY(id_profile, id_tab);
ALTER TABLE PREFIX_currency
	ADD blank TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 AFTER sign;
ALTER TABLE PREFIX_currency
	ADD decimals TINYINT(1) UNSIGNED NOT NULL DEFAULT 1 AFTER format;
ALTER TABLE PREFIX_product_attribute
	ADD wholesale_price decimal(13,6) NOT NULL DEFAULT 0.000000 AFTER ean13;
ALTER  TABLE PREFIX_employee
	ADD last_passwd_gen TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER passwd;
ALTER  TABLE PREFIX_customer
	ADD last_passwd_gen TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER passwd;
ALTER TABLE PREFIX_customer
	ADD ip_registration_newsletter VARCHAR(15) NULL DEFAULT NULL AFTER newsletter;
ALTER TABLE PREFIX_image_type
	ADD scenes TINYINT(1) NOT NULL DEFAULT 1;
ALTER TABLE PREFIX_image_lang
	CHANGE legend legend VARCHAR(128) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;

/* CMS */
CREATE TABLE  PREFIX_cms (
  id_cms INTEGER UNSIGNED NOT NULL auto_increment,
  PRIMARY KEY  (id_cms)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE  PREFIX_cms_lang (
  id_cms INTEGER UNSIGNED NOT NULL auto_increment,
  id_lang INTEGER UNSIGNED NOT NULL,
  meta_title VARCHAR(128) NOT NULL,
  meta_description VARCHAR(255) DEFAULT NULL,
  meta_keywords VARCHAR(255) DEFAULT NULL,
  content longtext NULL,
  link_rewrite VARCHAR(128) NOT NULL,
  PRIMARY KEY  (id_cms, id_lang)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE PREFIX_block_cms (
	id_block INTEGER(10) NOT NULL,
	id_cms INTEGER(10) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/* PAYMENT MODULE RESTRICTIONS */
CREATE TABLE `PREFIX_module_country` (
  `id_module` INTEGER UNSIGNED NOT NULL,
  `id_country` INTEGER UNSIGNED NOT NULL,
  PRIMARY KEY (`id_module`, `id_country`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `PREFIX_module_currency` (
  `id_module` INTEGER UNSIGNED NOT NULL,
  `id_currency` INTEGER NOT NULL,
  PRIMARY KEY (`id_module`, `id_currency`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/* ORDER-MESSAGE */
CREATE TABLE PREFIX_order_message
(
  id_order_message int(10) unsigned NOT NULL auto_increment,
  date_add datetime NOT NULL,
  PRIMARY KEY  (id_order_message)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE PREFIX_order_message_lang
(
  id_order_message int(10) unsigned NOT NULL,
  id_lang int(10) unsigned NOT NULL,
  name varchar(128) NOT NULL,
  message text NOT NULL,
  PRIMARY KEY  (id_order_message,id_lang)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/* SUB-DOMAINS */
CREATE TABLE PREFIX_subdomain (
  id_subdomain INTEGER(10) NOT NULL AUTO_INCREMENT,
  name VARCHAR(16) NOT NULL,
  PRIMARY KEY(id_subdomain)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/* META-CLASS */
CREATE TABLE PREFIX_meta (
  id_meta INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  page VARCHAR(64) NOT NULL,
  PRIMARY KEY(id_meta),
  KEY `meta_name` (`page`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE PREFIX_meta_lang (
  id_meta INTEGER UNSIGNED NOT NULL,
  id_lang INTEGER UNSIGNED NOT NULL,
  title VARCHAR(255) NULL,
  description VARCHAR(255) NULL,
  keywords VARCHAR(255) NULL,
  PRIMARY KEY (id_meta, id_lang)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE PREFIX_discount_category (
  id_discount INTEGER(11) NOT NULL,
  id_category INTEGER(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/* Customized products */
CREATE TABLE PREFIX_customization (
  id_customization int(10) NOT NULL AUTO_INCREMENT,
  id_product_attribute int(10) NOT NULL DEFAULT 0,
  id_cart int(10) NOT NULL,
  id_product int(10) NOT NULL,
  PRIMARY KEY(id_customization, id_cart, id_product)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE PREFIX_customized_data (
  id_customization int(10) NOT NULL,
  `type` tinyint(1) NOT NULL,
  `index` int(3) NOT NULL,
  `value` varchar(255) NOT NULL,
  PRIMARY KEY(id_customization, `type`, `index`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE PREFIX_customization_field (
  id_customization_field int(10) NOT NULL AUTO_INCREMENT,
  id_product int(10) NOT NULL,
  type tinyint(1) NOT NULL,
  required tinyint(1) NOT NULL,
  PRIMARY KEY(id_customization_field)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE PREFIX_customization_field_lang (
  id_customization_field int(10) NOT NULL,
  id_lang int(10) NOT NULL,
  name varchar(255) NOT NULL,
  PRIMARY KEY(id_customization_field, id_lang)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/* Product location */
ALTER TABLE `PREFIX_product_attribute` ADD `location` VARCHAR(64) NULL AFTER `supplier_reference`;
ALTER TABLE `PREFIX_product` ADD `location` VARCHAR(64) NULL AFTER `supplier_reference`;

/* Paypal default e-mail fix */
UPDATE `PREFIX_configuration` SET value = 'paypal@prestashop.com' WHERE name = 'PAYPAL_BUSINESS' AND value = 'your-address@paypal.com';

/* ##################################### */
/* 					CONTENTS				*/
/* ##################################### */
INSERT INTO PREFIX_subdomain (id_subdomain, name) VALUES (NULL, 'www');
UPDATE PREFIX_currency SET blank = 1 WHERE iso_code = 'EUR';
UPDATE PREFIX_order_state SET delivery = 1 WHERE id_order_state = 3;
UPDATE PREFIX_order_state SET delivery = 1 WHERE id_order_state = 4;
UPDATE PREFIX_order_state SET delivery = 1 WHERE id_order_state = 5;

/* IMAGE MAPPING */
UPDATE PREFIX_image_type SET scenes = 0;
INSERT INTO `PREFIX_image_type` (`name` ,`width` ,`height` ,`products` ,`categories` ,`manufacturers` ,`suppliers` ,`scenes`) VALUES ('large_scene', '556', '200', '0', '0', '0', '0', '1');
INSERT INTO `PREFIX_image_type` (`name` ,`width` ,`height` ,`products` ,`categories` ,`manufacturers` ,`suppliers` ,`scenes`)	VALUES ('thumb_scene', '161', '58', '0', '0', '0', '0', '1');

/* CONFIGURATION VARIABLE */
INSERT INTO PREFIX_configuration (name, value, date_add, date_upd) VALUES ('PS_INVOICE', '1', NOW(), NOW());
INSERT INTO PREFIX_configuration (name, value, date_add, date_upd) VALUES ('PS_INVOICE_PREFIX', 'IN', NOW(), NOW());
INSERT INTO PREFIX_configuration (name, value, date_add, date_upd) VALUES ('PS_DELIVERY_PREFIX', 'DE', NOW(), NOW());
INSERT INTO PREFIX_configuration (name, value, date_add, date_upd) VALUES ('PS_PRODUCT_PICTURE_MAX_SIZE', '131072', NOW(), NOW());
INSERT INTO PREFIX_configuration (name, value, date_add, date_upd) VALUES ('PS_PRODUCT_PICTURE_WIDTH', '64', NOW(), NOW());
INSERT INTO PREFIX_configuration (name, value, date_add, date_upd) VALUES ('PS_PRODUCT_PICTURE_HEIGHT', '64', NOW(), NOW());
INSERT INTO PREFIX_configuration (name, value, date_add, date_upd) VALUES ('PS_PASSWD_TIME_BACK', '360', NOW(), NOW());
INSERT INTO PREFIX_configuration (name, value, date_add, date_upd) VALUES ('PS_PASSWD_TIME_FRONT', '360', NOW(), NOW());

INSERT INTO `PREFIX_configuration_lang` (`id_configuration`, `id_lang`, `value`, `date_upd`) VALUES ((SELECT id_configuration FROM PREFIX_configuration c WHERE c.name = 'PS_INVOICE_PREFIX' LIMIT 1), 1, 'IN', NOW());
INSERT INTO `PREFIX_configuration_lang` (`id_configuration`, `id_lang`, `value`, `date_upd`) VALUES ((SELECT id_configuration FROM PREFIX_configuration c WHERE c.name = 'PS_INVOICE_PREFIX' LIMIT 1), 2, 'FA', NOW());
INSERT INTO `PREFIX_configuration_lang` (`id_configuration`, `id_lang`, `value`, `date_upd`) VALUES ((SELECT id_configuration FROM PREFIX_configuration c WHERE c.name = 'PS_DELIVERY_PREFIX' LIMIT 1), 1, 'DE', NOW());
INSERT INTO `PREFIX_configuration_lang` (`id_configuration`, `id_lang`, `value`, `date_upd`) VALUES ((SELECT id_configuration FROM PREFIX_configuration c WHERE c.name = 'PS_DELIVERY_PREFIX' LIMIT 1), 2, 'LI', NOW());

/* HOOKS/MODULES */
UPDATE PREFIX_hook SET description = 'This hook is called when a product is deleted' WHERE name = 'deleteProduct' LIMIT 1;
UPDATE PREFIX_hook SET name = 'extraLeft', title = 'Extra actions on the product page (left column).' WHERE name = 'extra' LIMIT 1;
INSERT INTO PREFIX_hook (name, title, position, description) VALUES ('orderReturn', 'Product returned', 0, 'When an order return is made');
INSERT INTO PREFIX_hook (name, title, position, description) VALUES ('postUpdateOrderStatus', 'Post Order\'s status update event', 0, 'Launch modules when the order\'s status was changed (enables automated workflow).');
INSERT INTO PREFIX_hook (name, title, position, description) VALUES ('productActions', 'Product actions', 1, 'Put new action buttons on product page');
INSERT INTO PREFIX_hook (name, title, position, description) VALUES ('cancelProduct', 'Product cancelled', 0, 'This hook is called when you cancel a product in an order');
INSERT INTO PREFIX_hook (name, title, position) VALUES ('backOfficeHome', 'Administration panel homepage', 1);
INSERT INTO PREFIX_hook (name, title, position, description) VALUES ('extraRight', 'Extra actions on the product page (right column).', 0, NULL);
UPDATE PREFIX_hook SET position = 1 WHERE name = 'top';
UPDATE PREFIX_hook SET position = 0 WHERE name = 'header';

/* ORDER MESSAGES */
INSERT INTO `PREFIX_order_message` (`id_order_message`, `date_add`) VALUES (1, NOW());
INSERT INTO `PREFIX_order_message_lang` (`id_order_message`, `id_lang`, `name`, `message`) VALUES
(1, 1, 'Delay', 'Hi,

Unfortunately, an item on your order is currently out of stock. This may cause a slight delay in delivery.
Please accept our apologies and rest assured that we are working hard to rectify this.

Best regards,
');
INSERT INTO `PREFIX_order_message_lang` (`id_order_message`, `id_lang`, `name`, `message`) VALUES
(1, 2, 'Délai', 'Bonjour,

Un des éléments de votre commande est actuellement en réapprovisionnement, ce qui peut légèrement retarder son envoi.

Merci de votre compréhension.

Cordialement,
');

/* META */
INSERT INTO `PREFIX_meta` (`id_meta`, `page`) VALUES
(1, '404'),
(2, 'best-sales'),
(3, 'contact-form'),
(4, 'index'),
(5, 'manufacturer'),
(6, 'new-products'),
(7, 'password'),
(8, 'prices-drop'),
(9, 'sitemap'),
(10, 'supplier');

INSERT INTO `PREFIX_meta_lang` (`id_meta`, `id_lang`, `title`, `description`, `keywords`) VALUES
(1, 1, '404 error', 'This page cannot be found', 'error, 404, not found'),
(1, 2, 'Erreur 404', 'Cette page est introuvable', 'erreur, 404, introuvable'),
(2, 1, 'Best sales', 'Our best sales', 'best sales'),
(2, 2, 'Meilleurs ventes', 'Liste de nos produits les mieux vendus', 'meilleurs ventes'),
(3, 1, 'Contact us', 'Use our form to contact us', 'contact, form, e-mail'),
(3, 2, 'Contactez-nous', 'Utilisez notre formulaire pour nous contacter', 'contact, formulaire, e-mail'),
(4, 1, '', 'Website powered by Webkul', 'shop, prestashop'),
(4, 2, '', 'Boutique propulsé par PrestaShop', 'boutique, prestashop'),
(5, 1, 'Manufacturers', 'Manufacturers list', 'manufacturer'),
(5, 2, 'Fabricants', 'Liste de nos fabricants', 'fabricants'),
(6, 1, 'New products', 'Our new products', 'new, products'),
(6, 2, 'Nouveaux produits', 'Liste de nos nouveaux produits', 'nouveau, produit'),
(7, 1, 'Forgot your password', 'Enter your e-mail address used to register in goal to get e-mail with your new password', 'forgot, password, e-mail, new, reset'),
(7, 2, 'Mot de passe oublié', 'Renseignez votre adresse e-mail afin de recevoir votre nouveau mot de passe.', 'mot de passe, oublié, e-mail, nouveau, regénération'),
(8, 1, 'Specials', 'Our special products', 'special, prices drop'),
(8, 2, 'Promotions', 'Nos produits en promotion', 'promotion, réduction'),
(9, 1, 'Sitemap', 'Lost ? Find what your are looking for', 'sitemap'),
(9, 2, 'Plan du site', 'Perdu ? Trouvez ce que vous cherchez', 'plan, site'),
(10, 1, 'Suppliers', 'Suppliers list', 'supplier'),
(10, 2, 'Fournisseurs', 'Liste de nos fournisseurs', 'fournisseurs');

/* CMS */
INSERT INTO `PREFIX_cms` VALUES (1),(2),(3),(4),(5);
INSERT INTO `PREFIX_cms_lang` (`id_cms`, `id_lang`, `meta_title`, `meta_description`, `meta_keywords`, `content`, `link_rewrite`) VALUES
(1, 1, 'Delivery', 'Our terms and conditions of delivery', 'conditions, delivery, delay, shipment, pack', '<h2>Shipments and returns</h2><h3>Your pack shipment</h3><p>Packages are generally dispatched within 2 days after receipt of payment and are shipped via Colissimo with tracking and drop-off without signature. If you prefer delivery by Colissimo Extra with required signature, an additional cost will be applied, so please contact us before choosing this method. Whichever shipment choice you make, we will provide you with a link to track your package online.</p><p>Shipping fees include handling and packing fees as well as postage costs. Handling fees are fixed, whereas transport fees vary according to total weight of the shipment. We advise you to group your items in one order. We cannot group two distinct orders placed separately, and shipping fees will apply to each of them. Your package will be dispatched at your own risk, but special care is taken to protect fragile objects.<br /><br />Boxes are amply sized and your items are well-protected.</p>', 'delivery');
INSERT INTO `PREFIX_cms_lang` (`id_cms`, `id_lang`, `meta_title`, `meta_description`, `meta_keywords`, `content`, `link_rewrite`) VALUES
(1, 2, 'Livraison', 'Nos conditions générales de livraison', 'conditions, livraison, délais, transport, colis', '<h2>Livraisons et retours</h2><h3>Le transport de votre colis</h3><p>Les colis sont g&eacute;n&eacute;ralement exp&eacute;di&eacute;s en 48h apr&egrave;s r&eacute;ception de votre paiement. Le mode d''expédidition standard est le Colissimo suivi, remis sans signature. Si vous souhaitez une remise avec signature, un co&ucirc;t suppl&eacute;mentaire s''applique, merci de nous contacter. Quel que soit le mode d''expédition choisi, nous vous fournirons d&egrave;s que possible un lien qui vous permettra de suivre en ligne la livraison de votre colis.</p><p>Les frais d''exp&eacute;dition comprennent l''emballage, la manutention et les frais postaux. Ils peuvent contenir une partie fixe et une partie variable en fonction du prix ou du poids de votre commande. Nous vous conseillons de regrouper vos achats en une unique commande. Nous ne pouvons pas grouper deux commandes distinctes et vous devrez vous acquitter des frais de port pour chacune d''entre elles. Votre colis est exp&eacute;di&eacute; &agrave; vos propres risques, un soin particulier est apport&eacute; au colis contenant des produits fragiles..<br /><br />Les colis sont surdimensionn&eacute;s et prot&eacute;g&eacute;s.</p>', 'livraison');
INSERT INTO `PREFIX_cms_lang` (`id_cms`, `id_lang`, `meta_title`, `meta_description`, `meta_keywords`, `content`, `link_rewrite`) VALUES
(2, 1, 'Legal Notice', 'Legal notice', 'notice, legal, credits', '<h2>Legal</h2><h3>Credits</h3><p>Concept and production:</p><p>This Web site was created using <a href="http://www.prestashop.com">PrestaShop</a>&trade; open-source software.</p>', 'legal-notice');
INSERT INTO `PREFIX_cms_lang` (`id_cms`, `id_lang`, `meta_title`, `meta_description`, `meta_keywords`, `content`, `link_rewrite`) VALUES
(2, 2, 'Mentions légales', 'Mentions légales', 'mentions, légales, crédits', '<h2>Mentions l&eacute;gales</h2><h3>Cr&eacute;dits</h3><p>Concept et production :</p><p>Ce site internet a &eacute;t&eacute; r&eacute;alis&eacute; en utilisant la solution open-source <a href="http://www.prestashop.com">PrestaShop</a>&trade; .</p>', 'mentions-legales');
INSERT INTO `PREFIX_cms_lang` (`id_cms`, `id_lang`, `meta_title`, `meta_description`, `meta_keywords`, `content`, `link_rewrite`) VALUES
(3, 1, 'Terms and conditions of use', 'Our terms and conditions of use', 'conditions, terms, use, sell', '<h2>Your terms and conditions of use</h2><h3>Rule 1</h3><p>Here is the rule 1 content</p>\r\n<h3>Rule 2</h3><p>Here is the rule 2 content</p>\r\n<h3>Rule 3</h3><p>Here is the rule 3 content</p>', 'terms-and-conditions-of-use');
INSERT INTO `PREFIX_cms_lang` (`id_cms`, `id_lang`, `meta_title`, `meta_description`, `meta_keywords`, `content`, `link_rewrite`) VALUES
(3, 2, 'Conditions d''utilisation', 'Nos conditions générales de ventes', 'conditions, utilisation, générales, ventes', '<h2>Vos conditions de ventes</h2><h3>Règle n°1</h3><p>Contenu de la règle numéro 1</p>\r\n<h3>Règle n°2</h3><p>Contenu de la règle numéro 2</p>\r\n<h3>Règle n°3</h3><p>Contenu de la règle numéro 3</p>', 'conditions-generales-de-ventes');
INSERT INTO `PREFIX_cms_lang` (`id_cms`, `id_lang`, `meta_title`, `meta_description`, `meta_keywords`, `content`, `link_rewrite`) VALUES
(4, 1, 'About us', 'Learn more about us', 'about us, informations', '<h2>About us</h2>\r\n<h3>Our company</h3><p>Our company</p>\r\n<h3>Our team</h3><p>Our team</p>\r\n<h3>Informations</h3><p>Informations</p>', 'about-us');
INSERT INTO `PREFIX_cms_lang` (`id_cms`, `id_lang`, `meta_title`, `meta_description`, `meta_keywords`, `content`, `link_rewrite`) VALUES
(4, 2, 'A propos', 'Apprenez-en d''avantage sur nous', 'à propos, informations', '<h2>A propos</h2>\r\n<h3>Notre entreprise</h3><p>Notre entreprise</p>\r\n<h3>Notre équipe</h3><p>Notre équipe</p>\r\n<h3>Informations</h3><p>Informations</p>', 'a-propos');
INSERT INTO `PREFIX_cms_lang` (`id_cms`, `id_lang`, `meta_title`, `meta_description`, `meta_keywords`, `content`, `link_rewrite`) VALUES
(5, 1, 'Secure payment', 'Our secure payment mean', 'secure payment, ssl, visa, mastercard, paypal', '<h2>Secure payment</h2>\r\n<h3>Our secure payment</h3><p>With SSL</p>\r\n<h3>Using Visa/Mastercard/Paypal</h3><p>About this services</p>', 'secure-payment');
INSERT INTO `PREFIX_cms_lang` (`id_cms`, `id_lang`, `meta_title`, `meta_description`, `meta_keywords`, `content`, `link_rewrite`) VALUES
(5, 2, 'Paiement sécurisé', 'Notre offre de paiement sécurisé', 'paiement sécurisé, ssl, visa, mastercard, paypal', '<h2>Paiement sécurisé</h2>\r\n<h3>Notre offre de paiement sécurisé</h3><p>Avec SSL</p>\r\n<h3>Utilisation de Visa/Mastercard/Paypal</h3><p>A propos de ces services</p>', 'paiement-securise');

INSERT INTO PREFIX_block_cms (`id_block`, `id_cms`) VALUES (IFNULL((SELECT id_module FROM PREFIX_module m WHERE m.name = 'blockvariouslinks' LIMIT 1), 0), 3);
INSERT INTO PREFIX_block_cms (`id_block`, `id_cms`) VALUES (IFNULL((SELECT id_module FROM PREFIX_module m WHERE m.name = 'blockvariouslinks' LIMIT 1), 0), 4);
INSERT INTO PREFIX_block_cms (`id_block`, `id_cms`) VALUES (IFNULL((SELECT id_module FROM PREFIX_module m WHERE m.name = 'blockinfos' LIMIT 1), 0), 1);
INSERT INTO PREFIX_block_cms (`id_block`, `id_cms`) VALUES (IFNULL((SELECT id_module FROM PREFIX_module m WHERE m.name = 'blockinfos' LIMIT 1), 0), 2);
INSERT INTO PREFIX_block_cms (`id_block`, `id_cms`) VALUES (IFNULL((SELECT id_module FROM PREFIX_module m WHERE m.name = 'blockinfos' LIMIT 1), 0), 3);
INSERT INTO PREFIX_block_cms (`id_block`, `id_cms`) VALUES (IFNULL((SELECT id_module FROM PREFIX_module m WHERE m.name = 'blockinfos' LIMIT 1), 0), 4);
DELETE FROM PREFIX_block_cms WHERE id_block = 0;

/* NEW TABS */
UPDATE PREFIX_tab_lang
	SET name = 'Vouchers'
	WHERE id_lang = (SELECT `id_lang` FROM `PREFIX_lang` l WHERE l.iso_code = 'en' LIMIT 1)
	AND id_tab = (SELECT `id_tab` FROM PREFIX_tab t WHERE t.class_name = 'AdminDiscounts' LIMIT 1);

INSERT INTO PREFIX_tab (id_parent, class_name, position) VALUES ((SELECT tmp.`id_tab` FROM (SELECT `id_tab` FROM PREFIX_tab t WHERE t.class_name = 'AdminTools' LIMIT 1) AS tmp), 'AdminCMS', (SELECT tmp.max FROM (SELECT MAX(position) max FROM `PREFIX_tab` WHERE id_parent = (SELECT tmp.`id_tab` FROM (SELECT `id_tab` FROM PREFIX_tab t WHERE t.class_name = 'AdminTools' LIMIT 1) AS tmp )) AS tmp));
INSERT INTO PREFIX_tab_lang (id_lang, id_tab, name) (
	SELECT id_lang,
	(SELECT id_tab FROM PREFIX_tab t WHERE t.class_name = 'AdminCMS' LIMIT 1),
	'CMS' FROM PREFIX_lang);
INSERT INTO PREFIX_access (id_profile, id_tab, `view`, `add`, edit, `delete`) VALUES ('1', (SELECT id_tab FROM PREFIX_tab t WHERE t.class_name = 'AdminCMS' LIMIT 1), 1, 1, 1, 1);

INSERT INTO PREFIX_tab (id_parent, class_name, position) VALUES ((SELECT tmp.`id_tab` FROM (SELECT `id_tab` FROM PREFIX_tab t WHERE t.class_name = 'AdminTools' LIMIT 1) AS tmp), 'AdminSubDomains', (SELECT tmp.max FROM (SELECT MAX(position) max FROM `PREFIX_tab` WHERE id_parent = (SELECT tmp.`id_tab` FROM (SELECT `id_tab` FROM PREFIX_tab t WHERE t.class_name = 'AdminTools' LIMIT 1) AS tmp )) AS tmp));
INSERT INTO PREFIX_tab_lang (id_lang, id_tab, name) (
	SELECT id_lang,
	(SELECT id_tab FROM PREFIX_tab t WHERE t.class_name = 'AdminSubDomains' LIMIT 1),
	'Subdomains' FROM PREFIX_lang);
UPDATE `PREFIX_tab_lang` SET `name` = 'Sous domaines'
	WHERE `id_tab` = (SELECT `id_tab` FROM `PREFIX_tab` t WHERE t.class_name = 'AdminSubDomains')
	AND `id_lang` = (SELECT `id_lang` FROM `PREFIX_lang` l WHERE l.iso_code = 'fr');
INSERT INTO PREFIX_access (id_profile, id_tab, `view`, `add`, edit, `delete`) VALUES ('1', (SELECT id_tab FROM PREFIX_tab t WHERE t.class_name = 'AdminSubDomains' LIMIT 1), 1, 1, 1, 1);

INSERT INTO PREFIX_tab (id_parent, class_name, position) VALUES ((SELECT tmp.`id_tab` FROM (SELECT `id_tab` FROM PREFIX_tab t WHERE t.class_name = 'AdminOrders' LIMIT 1) AS tmp), 'AdminOrderMessage', (SELECT tmp.max FROM (SELECT MAX(position) max FROM `PREFIX_tab` WHERE id_parent = (SELECT tmp.`id_tab` FROM (SELECT `id_tab` FROM PREFIX_tab t WHERE t.class_name = 'AdminOrders' LIMIT 1) AS tmp )) AS tmp));
INSERT INTO PREFIX_tab_lang (id_lang, id_tab, name) (
	SELECT id_lang,
	(SELECT id_tab FROM PREFIX_tab t WHERE t.class_name = 'AdminOrderMessage' LIMIT 1),
	'Order messages' FROM PREFIX_lang);
UPDATE `PREFIX_tab_lang` SET `name` = 'Messages prédéfinis'
	WHERE `id_tab` = (SELECT `id_tab` FROM `PREFIX_tab` t WHERE t.class_name = 'AdminOrderMessage')
	AND `id_lang` = (SELECT `id_lang` FROM `PREFIX_lang` l WHERE l.iso_code = 'fr');
INSERT INTO PREFIX_access (id_profile, id_tab, `view`, `add`, edit, `delete`) VALUES ('1', (SELECT id_tab FROM PREFIX_tab t WHERE t.class_name = 'AdminOrderMessage' LIMIT 1), 1, 1, 1, 1);

INSERT INTO PREFIX_tab (id_parent, class_name, position) VALUES ((SELECT tmp.`id_tab` FROM (SELECT `id_tab` FROM PREFIX_tab t WHERE t.class_name = 'AdminOrders' LIMIT 1) AS tmp), 'AdminDeliverySlip', (SELECT tmp.max FROM (SELECT MAX(position) max FROM `PREFIX_tab` WHERE id_parent = (SELECT tmp.`id_tab` FROM (SELECT `id_tab` FROM PREFIX_tab t WHERE t.class_name = 'AdminOrders' LIMIT 1) AS tmp )) AS tmp));
INSERT INTO PREFIX_tab_lang (id_lang, id_tab, name) (
	SELECT id_lang,
	(SELECT id_tab FROM PREFIX_tab t WHERE t.class_name = 'AdminDeliverySlip' LIMIT 1),
	'Delivery slips' FROM PREFIX_lang);
UPDATE `PREFIX_tab_lang` SET `name` = 'Bons de livraison'
	WHERE `id_tab` = (SELECT `id_tab` FROM `PREFIX_tab` t WHERE t.class_name = 'AdminDeliverySlip')
	AND `id_lang` = (SELECT `id_lang` FROM `PREFIX_lang` l WHERE l.iso_code = 'fr');
INSERT INTO PREFIX_access (id_profile, id_tab, `view`, `add`, edit, `delete`) VALUES ('1', (SELECT id_tab FROM PREFIX_tab t WHERE t.class_name = 'AdminDeliverySlip' LIMIT 1), 1, 1, 1, 1);

INSERT INTO PREFIX_tab (id_parent, class_name, position) VALUES ((SELECT tmp.`id_tab` FROM (SELECT `id_tab` FROM PREFIX_tab t WHERE t.class_name = 'AdminTools' LIMIT 1) AS tmp), 'AdminBackup', (SELECT tmp.max FROM (SELECT MAX(position) max FROM `PREFIX_tab` WHERE id_parent = (SELECT tmp.`id_tab` FROM (SELECT `id_tab` FROM PREFIX_tab t WHERE t.class_name = 'AdminTools' LIMIT 1) AS tmp )) AS tmp));
INSERT INTO PREFIX_tab_lang (id_lang, id_tab, name) (
	SELECT id_lang,
	(SELECT id_tab FROM PREFIX_tab t WHERE t.class_name = 'AdminBackup' LIMIT 1),
	'Database backup' FROM PREFIX_lang);
UPDATE `PREFIX_tab_lang` SET `name` = 'Sauvegarde BDD'
	WHERE `id_tab` = (SELECT `id_tab` FROM `PREFIX_tab` t WHERE t.class_name = 'AdminBackup')
	AND `id_lang` = (SELECT `id_lang` FROM `PREFIX_lang` l WHERE l.iso_code = 'fr');
INSERT INTO PREFIX_access (id_profile, id_tab, `view`, `add`, edit, `delete`) VALUES ('1', (SELECT id_tab FROM PREFIX_tab t WHERE t.class_name = 'AdminBackup' LIMIT 1), 1, 1, 1, 1);

INSERT INTO PREFIX_tab (id_parent, class_name, position) VALUES ((SELECT tmp.`id_tab` FROM (SELECT `id_tab` FROM PREFIX_tab t WHERE t.class_name = 'AdminPreferences' LIMIT 1) AS tmp), 'AdminMeta', (SELECT tmp.max FROM (SELECT MAX(position) max FROM `PREFIX_tab` WHERE id_parent = (SELECT tmp.`id_tab` FROM (SELECT `id_tab` FROM PREFIX_tab t WHERE t.class_name = 'AdminPreferences' LIMIT 1) AS tmp )) AS tmp));
INSERT INTO PREFIX_tab_lang (id_lang, id_tab, name) (
	SELECT id_lang,
	(SELECT id_tab FROM PREFIX_tab t WHERE t.class_name = 'AdminMeta' LIMIT 1),
	'Meta-tags' FROM PREFIX_lang);
UPDATE `PREFIX_tab_lang` SET `name` = 'Méta-Tags'
	WHERE `id_tab` = (SELECT `id_tab` FROM `PREFIX_tab` t WHERE t.class_name = 'AdminMeta')
	AND `id_lang` = (SELECT `id_lang` FROM `PREFIX_lang` l WHERE l.iso_code = 'fr');
INSERT INTO PREFIX_access (id_profile, id_tab, `view`, `add`, edit, `delete`) VALUES ('1', (SELECT id_tab FROM PREFIX_tab t WHERE t.class_name = 'AdminMeta' LIMIT 1), 1, 1, 1, 1);

INSERT INTO PREFIX_tab (id_parent, class_name, position) VALUES ((SELECT tmp.`id_tab` FROM (SELECT `id_tab` FROM PREFIX_tab t WHERE t.class_name = 'AdminCatalog' LIMIT 1) AS tmp), 'AdminScenes', (SELECT tmp.max FROM (SELECT MAX(position) max FROM `PREFIX_tab` WHERE id_parent = (SELECT tmp.`id_tab` FROM (SELECT `id_tab` FROM PREFIX_tab t WHERE t.class_name = 'AdminCatalog' LIMIT 1) AS tmp )) AS tmp));
INSERT INTO PREFIX_tab_lang (id_lang, id_tab, name) (
    SELECT id_lang,
    (SELECT id_tab FROM PREFIX_tab t WHERE t.class_name = 'AdminScenes' LIMIT 1),
    'Image mapping' FROM PREFIX_lang);
UPDATE `PREFIX_tab_lang` SET `name` = 'Scènes'
    WHERE `id_tab` = (SELECT `id_tab` FROM `PREFIX_tab` t WHERE t.class_name = 'AdminScenes')
    AND `id_lang` = (SELECT `id_lang` FROM `PREFIX_lang` l WHERE l.iso_code = 'fr');
INSERT INTO PREFIX_access (id_profile, id_tab, `view`, `add`, edit, `delete`) VALUES ('1', (SELECT id_tab FROM PREFIX_tab t WHERE t.class_name = 'AdminScenes' LIMIT 1), 1, 1, 1, 1);

/* NEW TEAM TAB */
UPDATE PREFIX_tab SET position = 10 WHERE class_name = 'AdminTools';
UPDATE PREFIX_tab SET position = 9 WHERE class_name = 'AdminPreferences';
UPDATE PREFIX_tab SET position = 8, id_parent = 0 WHERE class_name = 'AdminEmployees';
UPDATE PREFIX_tab SET position = 1, id_parent = 29 WHERE class_name = 'AdminProfiles';
UPDATE PREFIX_tab SET position = 2, id_parent = 29 WHERE class_name = 'AdminAccess';
UPDATE PREFIX_tab SET position = 3, id_parent = 29 WHERE class_name = 'AdminContacts';
UPDATE PREFIX_tab SET position = 1 WHERE class_name = 'AdminLanguages';
UPDATE PREFIX_tab SET position = 2 WHERE class_name = 'AdminTranslations';
UPDATE PREFIX_tab SET position = 3 WHERE class_name = 'AdminTabs';
UPDATE PREFIX_tab SET position = 4 WHERE class_name = 'AdminQuickAccesses';
UPDATE PREFIX_tab SET position = 5 WHERE class_name = 'AdminAliases';
UPDATE PREFIX_tab SET position = 6 WHERE class_name = 'AdminImport';
UPDATE PREFIX_tab SET position = 7 WHERE class_name = 'AdminSubDomains';

/* UPDATE ORDER TABS */
UPDATE PREFIX_tab SET class_name = 'AdminInvoices' WHERE class_name = 'AdminPrintPDF';
UPDATE PREFIX_tab SET position = 1 WHERE class_name = 'AdminInvoices';
UPDATE PREFIX_tab SET position = 2 WHERE class_name = 'AdminReturn';
UPDATE PREFIX_tab SET position = 3 WHERE class_name = 'AdminSlip';
UPDATE PREFIX_tab SET position = 4 WHERE class_name = 'AdminOrdersStates';
UPDATE PREFIX_tab_lang SET name = 'Invoices' WHERE name = 'PDF Invoice';
UPDATE PREFIX_tab_lang SET name = 'Factures' WHERE name = 'Facture PDF';

/* ##################################### */
/* 					STATS 					  */
/* ##################################### */
CREATE TABLE PREFIX_web_browser (
  id_web_browser INTEGER(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(64) NULL,
  PRIMARY KEY(id_web_browser)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE PREFIX_operating_system (
  id_operating_system INTEGER(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(64) NULL,
  PRIMARY KEY(id_operating_system)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE PREFIX_page_type (
  id_page_type INTEGER(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(256) NOT NULL,
  PRIMARY KEY(id_page_type)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE PREFIX_date_range (
  id_date_range INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  time_start DATETIME NOT NULL,
  time_end DATETIME NOT NULL,
  PRIMARY KEY(id_date_range)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE PREFIX_page (
  id_page INTEGER(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  id_page_type INTEGER(10) UNSIGNED NOT NULL,
  id_object VARCHAR(256) NULL,
  PRIMARY KEY(id_page)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE PREFIX_page_viewed (
  id_page INTEGER(10) UNSIGNED NOT NULL,
  id_date_range INTEGER UNSIGNED NOT NULL,
  counter INTEGER UNSIGNED NOT NULL,
  PRIMARY KEY(id_page, id_date_range)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE PREFIX_guest (
  id_guest INTEGER(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  id_operating_system INTEGER(10) UNSIGNED NULL DEFAULT NULL,
  id_web_browser INTEGER(10) UNSIGNED NULL DEFAULT NULL,
  id_customer INTEGER(10) UNSIGNED NULL DEFAULT NULL,
  javascript BOOL NULL DEFAULT 0,
  screen_resolution_x SMALLINT UNSIGNED NULL DEFAULT NULL,
  screen_resolution_y SMALLINT UNSIGNED NULL DEFAULT NULL,
  screen_color TINYINT UNSIGNED NULL DEFAULT NULL,
  sun_java BOOL NULL DEFAULT NULL,
  adobe_flash BOOL NULL DEFAULT NULL,
  adobe_director BOOL NULL DEFAULT NULL,
  apple_quicktime BOOL NULL DEFAULT NULL,
  real_player BOOL NULL DEFAULT NULL,
  windows_media BOOL NULL DEFAULT NULL,
  accept_language VARCHAR(8) NULL DEFAULT NULL,
  PRIMARY KEY(id_guest)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_scene` (
  `id_scene` int(10) NOT NULL auto_increment,
  `active` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`id_scene`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_scene_category` (
  `id_scene` int(10) NOT NULL,
  `id_category` int(10) NOT NULL,
  PRIMARY KEY  (`id_scene`,`id_category`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_scene_lang` (
  `id_scene` int(10) NOT NULL,
  `id_lang` int(10) NOT NULL,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY  (`id_scene`,`id_lang`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_scene_products` (
  `id_scene` int(10) NOT NULL,
  `id_product` int(10) NOT NULL,
  `x_axis` int(4) NOT NULL,
  `y_axis` int(4) NOT NULL,
  `zone_width` int(3) NOT NULL,
  `zone_height` int(3) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO PREFIX_guest (id_customer) SELECT id_customer FROM PREFIX_customer;

ALTER TABLE PREFIX_connections ADD id_guest INTEGER(10) UNSIGNED NULL AFTER id_connections;
ALTER TABLE PREFIX_connections ADD id_page INTEGER(10) UNSIGNED NOT NULL AFTER id_guest;
ALTER TABLE PREFIX_connections ADD http_referer VARCHAR(256) NULL;
ALTER TABLE PREFIX_connections CHANGE date date_add DATETIME NOT NULL;

UPDATE PREFIX_connections, PREFIX_guest SET PREFIX_connections.id_guest=PREFIX_guest.id_guest WHERE PREFIX_connections.id_customer=PREFIX_guest.id_customer;
ALTER TABLE PREFIX_connections CHANGE id_guest id_guest INTEGER(10) UNSIGNED NOT NULL;
ALTER TABLE PREFIX_connections DROP id_customer;

CREATE TABLE PREFIX_connections_page (
  id_connections INTEGER(10) UNSIGNED NOT NULL,
  id_page INTEGER(10) UNSIGNED NOT NULL,
  time_start DATETIME NOT NULL,
  time_end DATETIME NULL,
  PRIMARY KEY(id_connections, id_page, time_start)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `PREFIX_operating_system` (`name`) VALUES ('Windows XP'),('Windows Vista'),('MacOsX'),('Linux');
INSERT INTO `PREFIX_web_browser` (`name`) VALUES ('Safari'),('Firefox 2.x'),('Firefox 3.x'),('Opera'),('IE 6.x'),('IE 7.x'),('IE 8.x'),('Google Chrome');
INSERT INTO `PREFIX_page_type` (`name`) VALUES ('product.php'),('category.php'),('order.php'),('manufacturer.php');

INSERT INTO `PREFIX_hook` (`name`, `title`, `position`) VALUES
('AdminStatsModules', 'Stats - Modules', 1),
('GraphEngine', 'Graph Engines', 0),
('GridEngine', 'Grid Engines', 0);

/* Temporary configuration variable used in the following query */
INSERT INTO `PREFIX_configuration` (`name`, `value`, `date_add`, `date_upd`) VALUES ('TMP_ID_TAB_STATS', (SELECT `id_tab` FROM `PREFIX_tab` WHERE `class_name` = 'AdminStats'), NOW(), NOW());
INSERT INTO `PREFIX_tab` (`id_parent`, `class_name`, `position`) VALUES
	((SELECT `value` FROM `PREFIX_configuration` WHERE `name` = 'TMP_ID_TAB_STATS'), 'AdminStatsModules', 1);
INSERT INTO `PREFIX_tab` (`id_parent`, `class_name`, `position`) VALUES
	((SELECT `value` FROM `PREFIX_configuration` WHERE `name` = 'TMP_ID_TAB_STATS'), 'AdminStatsConf', 2);
INSERT INTO `PREFIX_tab_lang` (`id_lang`, `id_tab`, `name`) VALUES
	(1, (SELECT `id_tab` FROM `PREFIX_tab` WHERE `class_name` = 'AdminStatsModules'), 'Modules');
INSERT INTO `PREFIX_tab_lang` (`id_lang`, `id_tab`, `name`) VALUES
	(2, (SELECT `id_tab` FROM `PREFIX_tab` WHERE `class_name` = 'AdminStatsModules'), 'Modules');
INSERT INTO `PREFIX_tab_lang` (`id_lang`, `id_tab`, `name`) VALUES
	(1, (SELECT `id_tab` FROM `PREFIX_tab` WHERE `class_name` = 'AdminStatsConf'), 'Settings');
INSERT INTO `PREFIX_tab_lang` (`id_lang`, `id_tab`, `name`) VALUES
	(2, (SELECT `id_tab` FROM `PREFIX_tab` WHERE `class_name` = 'AdminStatsConf'), 'Configuration');
INSERT INTO `PREFIX_access` (`id_profile`, `id_tab`, `view`, `add`, `edit`, `delete`) VALUES
	(1, (SELECT `id_tab` FROM `PREFIX_tab` WHERE `class_name` = 'AdminStatsModules'), 1, 1, 1, 1);
INSERT INTO `PREFIX_access` (`id_profile`, `id_tab`, `view`, `add`, `edit`, `delete`) VALUES
	(1, (SELECT `id_tab` FROM `PREFIX_tab` WHERE `class_name` = 'AdminStatsConf'), 1, 1, 1, 1);
DELETE FROM `PREFIX_configuration` WHERE `name` = 'TMP_ID_TAB_STATS';

/* ##################################### */
/* 				DOUBLE LANGUAGE				*/
/* ##################################### */
INSERT IGNORE INTO `PREFIX_discount_type_lang` (`id_discount_type`, `id_lang`, `name`)
    (SELECT `id_discount_type`, id_lang, (SELECT tl.`name`
        FROM `PREFIX_discount_type_lang` tl
        WHERE tl.`id_lang` = (SELECT c.`value`
            FROM `PREFIX_configuration` c
            WHERE c.`name` = 'PS_LANG_DEFAULT' LIMIT 1) AND tl.`id_discount_type`=`PREFIX_discount_type`.`id_discount_type`)
    FROM `PREFIX_lang` CROSS JOIN `PREFIX_discount_type`);

INSERT IGNORE INTO `PREFIX_cms_lang` (`id_cms`, `id_lang`, `link_rewrite`, `meta_description`, `meta_keywords`, `meta_title`, `content`)
    (SELECT `id_cms`, id_lang,
	(SELECT tl.`link_rewrite`
        FROM `PREFIX_cms_lang` tl
        WHERE tl.`id_lang` = (SELECT c.`value`
            FROM `PREFIX_configuration` c
            WHERE c.`name` = 'PS_LANG_DEFAULT' LIMIT 1) AND tl.`id_cms`=`PREFIX_cms`.`id_cms`),
	(SELECT tl.`meta_description`
        FROM `PREFIX_cms_lang` tl
        WHERE tl.`id_lang` = (SELECT c.`value`
            FROM `PREFIX_configuration` c
            WHERE c.`name` = 'PS_LANG_DEFAULT' LIMIT 1) AND tl.`id_cms`=`PREFIX_cms`.`id_cms`),
	(SELECT tl.`meta_keywords`
        FROM `PREFIX_cms_lang` tl
        WHERE tl.`id_lang` = (SELECT c.`value`
            FROM `PREFIX_configuration` c
            WHERE c.`name` = 'PS_LANG_DEFAULT' LIMIT 1) AND tl.`id_cms`=`PREFIX_cms`.`id_cms`),
	(SELECT tl.`meta_title`
        FROM `PREFIX_cms_lang` tl
        WHERE tl.`id_lang` = (SELECT c.`value`
            FROM `PREFIX_configuration` c
            WHERE c.`name` = 'PS_LANG_DEFAULT' LIMIT 1) AND tl.`id_cms`=`PREFIX_cms`.`id_cms`),
	(SELECT tl.`content`
        FROM `PREFIX_cms_lang` tl
        WHERE tl.`id_lang` = (SELECT c.`value`
            FROM `PREFIX_configuration` c
            WHERE c.`name` = 'PS_LANG_DEFAULT' LIMIT 1) AND tl.`id_cms`=`PREFIX_cms`.`id_cms`)
	FROM `PREFIX_lang` CROSS JOIN `PREFIX_cms`);

INSERT IGNORE INTO `PREFIX_meta_lang` (`id_meta`, `id_lang`, `description`, `keywords`, `title`)
    (SELECT `id_meta`, id_lang,
	(SELECT tl.`description`
        FROM `PREFIX_meta_lang` tl
        WHERE tl.`id_lang` = (SELECT c.`value`
            FROM `PREFIX_configuration` c
            WHERE c.`name` = 'PS_LANG_DEFAULT' LIMIT 1) AND tl.`id_meta`=`PREFIX_meta`.`id_meta`),
	(SELECT tl.`keywords`
        FROM `PREFIX_meta_lang` tl
        WHERE tl.`id_lang` = (SELECT c.`value`
            FROM `PREFIX_configuration` c
            WHERE c.`name` = 'PS_LANG_DEFAULT' LIMIT 1) AND tl.`id_meta`=`PREFIX_meta`.`id_meta`),
	(SELECT tl.`title`
        FROM `PREFIX_meta_lang` tl
        WHERE tl.`id_lang` = (SELECT c.`value`
            FROM `PREFIX_configuration` c
            WHERE c.`name` = 'PS_LANG_DEFAULT' LIMIT 1) AND tl.`id_meta`=`PREFIX_meta`.`id_meta`)
	FROM `PREFIX_lang` CROSS JOIN `PREFIX_meta`);

INSERT IGNORE INTO `PREFIX_order_message_lang` (`id_order_message`, `id_lang`, `name`, `message`)
    (SELECT `id_order_message`, id_lang,
	(SELECT tl.`name`
        FROM `PREFIX_order_message_lang` tl
        WHERE tl.`id_lang` = (SELECT c.`value`
            FROM `PREFIX_configuration` c
            WHERE c.`name` = 'PS_LANG_DEFAULT' LIMIT 1) AND tl.`id_order_message`=`PREFIX_order_message`.`id_order_message`),
	(SELECT tl.`message`
        FROM `PREFIX_order_message_lang` tl
        WHERE tl.`id_lang` = (SELECT c.`value`
            FROM `PREFIX_configuration` c
            WHERE c.`name` = 'PS_LANG_DEFAULT' LIMIT 1) AND tl.`id_order_message`=`PREFIX_order_message`.`id_order_message`)
	FROM `PREFIX_lang` CROSS JOIN `PREFIX_order_message`);

/* PHP */
/* PHP:invoice_number_set(); */;
/* PHP:delivery_number_set(); */;
/* PHP:set_payment_module(); */;
/* PHP:set_discount_category(); */;
