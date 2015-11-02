SET NAMES 'utf8';

ALTER TABLE `PREFIX_order_detail` ADD INDEX `id_order_id_order_detail` (`id_order`, `id_order_detail`);
ALTER TABLE `PREFIX_category_group` ADD INDEX `id_group` (`id_group`);
ALTER TABLE `PREFIX_product` ADD INDEX `date_add` (`date_add`);