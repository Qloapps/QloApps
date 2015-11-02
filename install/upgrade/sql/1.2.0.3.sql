SET NAMES 'utf8';

/* ##################################### */
/* 				STRUCTURE			 		 */
/* ##################################### */

ALTER TABLE `PREFIX_customization`
	ADD `quantity_refunded` INT NOT NULL DEFAULT '0';
ALTER TABLE `PREFIX_customization`
	ADD `quantity_returned` INT NOT NULL DEFAULT '0';

ALTER TABLE `PREFIX_alias`
	CHANGE `id_alias` `id_alias` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `PREFIX_attribute_impact`
	CHANGE `id_attribute_impact` `id_attribute_impact` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `PREFIX_customization`
	CHANGE `id_customization` `id_customization` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `PREFIX_customization_field`
	CHANGE `id_customization_field` `id_customization_field` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `PREFIX_subdomain`
	CHANGE `id_subdomain` `id_subdomain` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT;


/* ##################################### */
/* 					CONTENTS					 */
/* ##################################### */

INSERT INTO `PREFIX_search_engine` (`server`,`getvar`) VALUES
	('bing.com','q');

INSERT INTO `PREFIX_hook_module` (`id_module`, `id_hook`, `position`) VALUES
	(19, 9, 1);

