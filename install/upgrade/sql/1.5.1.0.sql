SET NAMES 'utf8';

ALTER TABLE `PREFIX_address` ADD `auto_generated` tinyint(1) unsigned NOT NULL DEFAULT '0' AFTER `deleted`;
