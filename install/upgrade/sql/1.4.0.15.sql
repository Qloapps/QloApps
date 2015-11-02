/* PHP:add_new_tab(AdminCounty, fr:Comtés|es:Condados|en:Counties|de:Counties|it:Counties,  5); */;

ALTER TABLE `PREFIX_tax_rule` ADD `county_behavior` INT NOT NULL AFTER `state_behavior`;
ALTER TABLE `PREFIX_tax_rule` ADD `id_county` INT NOT NULL AFTER `id_country`;

ALTER TABLE `PREFIX_tax_rule` ADD UNIQUE (
`id_tax_rules_group` ,
`id_country` ,
`id_state` ,
`id_county`
);

CREATE TABLE `PREFIX_county` (
  `id_county` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `id_state` int(11) NOT NULL,
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY (`id_county`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 ;


CREATE TABLE `PREFIX_county_zip_code` (
	`id_county` INT NOT NULL ,
	`from_zip_code` INT NOT NULL ,
	`to_zip_code` INT NOT NULL ,
	PRIMARY KEY ( `id_county` , `from_zip_code` , `to_zip_code` )
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;


INSERT INTO `PREFIX_configuration` (`name`, `value`, `date_add`, `date_upd`) VALUES ('PS_HOMEPAGE_PHP_SELF', 'index.php', NOW(), NOW());


INSERT INTO `PREFIX_configuration` (`name`, `value`, `date_add`, `date_upd`)
VALUES ('PS_USE_ECOTAX',
		  (SELECT IF((SELECT `ecotax` FROM `PREFIX_product` WHERE  `ecotax` != 0 LIMIT 1),'1','0')),
        NOW(),
        NOW());

ALTER TABLE `PREFIX_hook` ADD `live_edit` TINYINT NOT NULL DEFAULT '0';

UPDATE  `PREFIX_hook` SET  `live_edit` =  '1' WHERE  `PREFIX_hook`.`name` IN ('rightColumn', 'leftColumn', 'home');
