SET NAMES 'utf8';

ALTER TABLE `PREFIX_address` CHANGE `company` `company` VARCHAR(32) NULL;

/* PHP:fix_cms_shop_1520(); */;