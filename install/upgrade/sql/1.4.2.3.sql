SET NAMES 'utf8';

UPDATE `PREFIX_address_format` SET `format`=REPLACE(`format`, 'state', 'State:name');

SET @defaultOOS = (SELECT value FROM `PREFIX_configuration` WHERE name = 'PS_ORDER_OUT_OF_STOCK');
/* Set 0 for every non-attribute product */
UPDATE `PREFIX_product` p SET `cache_default_attribute` =  0 WHERE `id_product` NOT IN (SELECT `id_product` FROM `PREFIX_product_attribute`);
/* First default attribute in stock */
UPDATE `PREFIX_product` p SET `cache_default_attribute` = (SELECT `id_product_attribute` FROM `PREFIX_product_attribute` WHERE `id_product` = p.`id_product` AND default_on = 1 AND quantity > 0 LIMIT 1) WHERE `cache_default_attribute` IS NULL;
/* Then default attribute without stock if we don't care */
UPDATE `PREFIX_product` p SET `cache_default_attribute` = (SELECT `id_product_attribute` FROM `PREFIX_product_attribute` WHERE `id_product` = p.`id_product` AND default_on = 1 LIMIT 1) WHERE `cache_default_attribute` IS NULL AND `out_of_stock` = 1 OR `out_of_stock` = IF(@defaultOOS = 1, 2, 1);
/* Next, the default attribute can be any attribute with stock */
UPDATE `PREFIX_product` p SET `cache_default_attribute` = (SELECT `id_product_attribute` FROM `PREFIX_product_attribute` WHERE `id_product` = p.`id_product` AND quantity > 0 LIMIT 1) WHERE `cache_default_attribute` IS NULL;
/* If there is still no default attribute, then we go back to the default one */
UPDATE `PREFIX_product` p SET `cache_default_attribute` = (SELECT `id_product_attribute` FROM `PREFIX_product_attribute` WHERE `id_product` = p.`id_product` AND default_on = 1 LIMIT 1) WHERE `cache_default_attribute` IS NULL;

UPDATE `PREFIX_order_state_lang` SET `name` = 'Zahlung eingegangen' WHERE `PREFIX_order_state_lang`.`id_order_state` =2 AND `PREFIX_order_state_lang`.`id_lang` = (SELECT id_lang FROM `PREFIX_lang` WHERE `iso_code` = 'de');
UPDATE `PREFIX_order_state_lang` SET `name` = 'Bestellung eingegangen' WHERE `PREFIX_order_state_lang`.`id_order_state` =3 AND `PREFIX_order_state_lang`.`id_lang` = (SELECT id_lang FROM `PREFIX_lang` WHERE `iso_code` = 'de');
UPDATE `PREFIX_order_state_lang` SET `name` = 'Versendet' WHERE `PREFIX_order_state_lang`.`id_order_state` =4 AND `PREFIX_order_state_lang`.`id_lang` = (SELECT id_lang FROM `PREFIX_lang` WHERE `iso_code` = 'de');
UPDATE `PREFIX_order_state_lang` SET `name` = 'Erfolgreich abgeschlossen' WHERE `PREFIX_order_state_lang`.`id_order_state` =5 AND `PREFIX_order_state_lang`.`id_lang` = (SELECT id_lang FROM `PREFIX_lang` WHERE `iso_code` = 'de');
UPDATE `PREFIX_order_state_lang` SET `name` = 'Storniert' WHERE `PREFIX_order_state_lang`.`id_order_state` =6 AND `PREFIX_order_state_lang`.`id_lang` = (SELECT id_lang FROM `PREFIX_lang` WHERE `iso_code` = 'de');
UPDATE `PREFIX_order_state_lang` SET `name` = 'Fehler bei der Bezahlung' WHERE `PREFIX_order_state_lang`.`id_order_state` =8 AND `PREFIX_order_state_lang`.`id_lang` = (SELECT id_lang FROM `PREFIX_lang` WHERE `iso_code` = 'de');
UPDATE `PREFIX_order_state_lang` SET `name` = 'Artikel erwartet' WHERE `PREFIX_order_state_lang`.`id_order_state` =9 AND `PREFIX_order_state_lang`.`id_lang` = (SELECT id_lang FROM `PREFIX_lang` WHERE `iso_code` = 'de');
UPDATE `PREFIX_order_state_lang` SET `name` = 'Warten auf Zahlungseingang' WHERE `PREFIX_order_state_lang`.`id_order_state` =10 AND `PREFIX_order_state_lang`.`id_lang` = (SELECT id_lang FROM `PREFIX_lang` WHERE `iso_code` = 'de');
UPDATE `PREFIX_order_state_lang` SET `name` = 'Warten auf Zahlungseingang von PayPal' WHERE `PREFIX_order_state_lang`.`id_order_state` =11 AND `PREFIX_order_state_lang`.`id_lang` = (SELECT id_lang FROM `PREFIX_lang` WHERE `iso_code` = 'de');
UPDATE `PREFIX_order_state_lang` SET `name` = 'PayPal Anmeldung erfolgreich' WHERE `PREFIX_order_state_lang`.`id_order_state` =12 AND `PREFIX_order_state_lang`.`id_lang` = (SELECT id_lang FROM `PREFIX_lang` WHERE `iso_code` = 'de');

UPDATE `PREFIX_meta_lang` SET `url_rewrite` = 'identita' WHERE `url_rewrite` = 'Identità';

/* PHP:add_missing_rewrite_value(); */;
