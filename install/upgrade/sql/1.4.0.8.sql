SET NAMES 'utf8';

CREATE TEMPORARY TABLE `PREFIX_tab_tmp1` (
	`id_parent` int(11)
);
INSERT INTO `PREFIX_tab_tmp1` (SELECT * FROM (SELECT `id_parent` FROM `PREFIX_tab` WHERE `class_name` = 'AdminTaxes') AS  tmp);

CREATE TEMPORARY TABLE `PREFIX_tab_tmp2` (
	`position` int(10)
);

INSERT INTO `PREFIX_tab_tmp2` (SELECT * FROM (SELECT `position` FROM `PREFIX_tab` WHERE `class_name` = 'AdminTaxes') AS  tmp2);

UPDATE `PREFIX_tab` SET `position` = `position` + 1 WHERE `id_parent` = (SELECT id_parent FROM PREFIX_tab_tmp1 AS tmp) AND `position` > (SELECT position FROM PREFIX_tab_tmp2 AS tmp2);
DROP TABLE PREFIX_tab_tmp1;
DROP TABLE PREFIX_tab_tmp2;

CREATE TEMPORARY TABLE `PREFIX_tab_tmp` (
	`position` int(10)
);

INSERT INTO `PREFIX_tab_tmp` (SELECT * FROM (SELECT `position` FROM `PREFIX_tab` WHERE `class_name` = 'AdminTaxes') AS tmp);
UPDATE `PREFIX_tab` SET `position` = (SELECT position FROM PREFIX_tab_tmp tmp) + 1 WHERE `class_name` = 'AdminTaxRulesGroup';
DROP TABLE PREFIX_tab_tmp;

UPDATE `PREFIX_hook` SET `title` = 'Category creation', description = '' WHERE `name` = 'categoryAddition' LIMIT 1;
UPDATE `PREFIX_hook` SET `title` = 'Category modification', description = '' WHERE `name` = 'categoryUpdate' LIMIT 1;
UPDATE `PREFIX_hook` SET `title` = 'Category removal', description = '' WHERE `name` = 'categoryDeletion' LIMIT 1;

DELETE FROM `PREFIX_module` WHERE `name` = 'canonicalurl' LIMIT 1;
DELETE FROM `PREFIX_hook_module` WHERE `id_module` NOT IN (SELECT id_module FROM `PREFIX_module`);

/* PHP:gridextjs_deprecated(); */;
/* PHP:shop_url(); */;
/* PHP:updateproductcomments(); */;