SET NAMES 'utf8';

ALTER TABLE `PREFIX_order_detail` ADD `reduction_percent` DECIMAL(10, 2) NOT NULL AFTER `product_price`;
ALTER TABLE `PREFIX_order_detail` ADD `reduction_amount` DECIMAL(20, 6) NOT NULL AFTER `reduction_percent`;

ALTER TABLE `PREFIX_country` CHANGE `need_identification_number` `need_identification_number` TINYINT(1) NOT NULL DEFAULT '0';

INSERT INTO `PREFIX_configuration` (`name`, `value`, `date_add`, `date_upd`) VALUES
('PS_1_3_2_UPDATE_DATE', NOW(), NOW(), NOW());

ALTER TABLE `PREFIX_search_index` CHANGE `weight` `weight` SMALLINT(4) unsigned NOT NULL DEFAULT '1';

ALTER TABLE `PREFIX_image` DROP INDEX `product_position`, ADD UNIQUE `product_position` (`id_product`, `position`);

ALTER TABLE `PREFIX_zone` DROP `enabled`; 

SET @id_hook = (SELECT id_hook FROM PREFIX_hook WHERE name = 'backOfficeHeader');
SET @position = (SELECT IFNULL(MAX(position),0)+1 FROM PREFIX_hook_module WHERE id_hook = @id_hook);
INSERT IGNORE INTO PREFIX_hook_module (id_hook, id_module, position) VALUES (@id_hook, (SELECT id_module FROM PREFIX_module WHERE name = 'statsbestcustomers'), @position);
SET @position = @position + 1;
INSERT IGNORE INTO PREFIX_hook_module (id_hook, id_module, position) VALUES (@id_hook, (SELECT id_module FROM PREFIX_module WHERE name = 'statsbestproducts'), @position);
SET @position = @position + 1;
INSERT IGNORE INTO PREFIX_hook_module (id_hook, id_module, position) VALUES (@id_hook, (SELECT id_module FROM PREFIX_module WHERE name = 'statsbestvouchers'), @position);
SET @position = @position + 1;
INSERT IGNORE INTO PREFIX_hook_module (id_hook, id_module, position) VALUES (@id_hook, (SELECT id_module FROM PREFIX_module WHERE name = 'statsbestcategories'), @position);
SET @position = @position + 1;
INSERT IGNORE INTO PREFIX_hook_module (id_hook, id_module, position) VALUES (@id_hook, (SELECT id_module FROM PREFIX_module WHERE name = 'statsbestcarriers'), @position);
