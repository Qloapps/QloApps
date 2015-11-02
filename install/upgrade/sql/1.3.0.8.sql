SET NAMES 'utf8';

ALTER TABLE `PREFIX_product_attribute` ADD INDEX `id_product_id_product_attribute` (`id_product_attribute` , `id_product`);
ALTER TABLE `PREFIX_image_lang` ADD INDEX `id_image` (`id_image`);

