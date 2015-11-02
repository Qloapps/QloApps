SET NAMES 'utf8';

ALTER TABLE `PREFIX_tax_rule` DROP PRIMARY KEY ;
ALTER TABLE `PREFIX_tax_rule` ADD `id_tax_rule` INT NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST ;
ALTER TABLE `PREFIX_tax_rule` ADD INDEX ( `id_tax` ) ;
ALTER TABLE `PREFIX_tax_rule` ADD INDEX ( `id_tax_rules_group` ) ;

ALTER TABLE `PREFIX_address` MODIFY `dni` VARCHAR(16) NULL AFTER `vat_number`;

INSERT INTO `PREFIX_configuration` (`name`, `value`, `date_add`, `date_upd`) VALUES 
('BLOCKSTORE_IMG', 'store.jpg', NOW(), NOW()),
('PS_STORES_CENTER_LAT', '25.948969', NOW(), NOW()),
('PS_STORES_CENTER_LONG', '-80.226439', NOW(), NOW());

/* PHP:add_new_tab(AdminInformation, en:Configuration Information|fr:Informations|es:Informations|it:Informazioni di configurazione|de:Konfigurationsinformationen,  9); */;
/* PHP:add_new_tab(AdminPerformance, de:Leistung|en:Performance|it:Performance|fr:Performances|es:Rendimiento,  8); */;
/* PHP:add_new_tab(AdminCustomerThreads, en:Customer Service|de:Kundenservice|fr:SAV|es:Servicio al cliente|it:Servizio clienti,  29); */;
/* PHP:add_new_tab(AdminWebservice, fr:Service web|es:Web service|en:Webservice|de:Webservice|it:Webservice, 9); */;
/* PHP:add_new_tab(AdminAddonsCatalog, fr:Catalogue de modules et thèmes|de:Module und Themenkatalog|en:Modules & Themes Catalog|it:Moduli & Temi catalogo,  7); */;
/* PHP:add_new_tab(AdminAddonsMyAccount, it:Il mio Account|de:Mein Konto|fr:Mon compte|en:My Account,  7); */;
/* PHP:add_new_tab(AdminThemes, es:Temas|it:Temi|de:Themen|en:Themes|fr:Thèmes,  7); */;
/* PHP:add_new_tab(AdminGeolocation, es:Geolocalización|it:Geolocalizzazione|en:Geolocation|de:Geotargeting|fr:Géolocalisation,  8); */;
/* PHP:add_new_tab(AdminTaxRulesGroup, it:Regimi fiscali|es:Reglas de Impuestos|fr:Règles de taxes|de:Steuerregeln|en:Tax Rules,  4); */;
/* PHP:add_new_tab(AdminLogs, en:Log|fr:Log|es:Log|de:Log|it:Log,  9); */;
