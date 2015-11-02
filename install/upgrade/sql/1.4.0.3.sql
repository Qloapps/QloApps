SET NAMES 'utf8';

CREATE TABLE IF NOT EXISTS `PREFIX_country_tax` (
  `id_country_tax` int(11) NOT NULL AUTO_INCREMENT,
  `id_country` int(11) NOT NULL,
  `id_tax` int(11) NOT NULL,
  PRIMARY KEY (`id_country_tax`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_product_country_tax` (
  `id_product` int(11) NOT NULL,
  `id_country` int(11) NOT NULL,
  `id_tax` int(11) NOT NULL,
  UNIQUE KEY `id_product` (`id_product`,`id_country`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

DELETE FROM `PREFIX_tab` WHERE `class_name` = 'AdminStatsModules' LIMIT 1;
DELETE FROM `PREFIX_tab_lang` WHERE `id_tab` NOT IN (SELECT id_tab FROM `PREFIX_tab`);
DELETE FROM `PREFIX_access` WHERE `id_tab` NOT IN (SELECT id_tab FROM `PREFIX_tab`);

INSERT INTO `PREFIX_module` (`name`, `active`) VALUES ('statsforecast', 1);
INSERT INTO `PREFIX_hook_module` (`id_module`, `id_hook` , `position`) (SELECT id_module, 32, (SELECT max_position from (SELECT MAX(position)+1 as max_position FROM `PREFIX_hook_module` WHERE `id_hook` = 32) tmp) FROM `PREFIX_module` WHERE `name` = 'statsforecast'); 

INSERT INTO `PREFIX_configuration` (`name`, `value`, `date_add`, `date_upd`) VALUES
('PS_GEOLOCATION_ENABLED', '0', NOW(), NOW()),
('PS_ALLOWED_COUNTRIES',
'AF;ZA;AX;AL;DZ;DE;AD;AO;AI;AQ;AG;AN;SA;AR;AM;AW;AU;AT;AZ;BS;BH;BD;BB;BY;BE;BZ;BJ;BM;BT;BO;BA;BW;BV;BR;BN;BG;BF;MM;BI;KY;KH;CM;CA;CV;CF;CL;CN;CX;CY;CC;CO;KM;CG;CD;CK;KR;KP;CR;CI;HR;CU;DK;DJ;DM;EG;IE;SV;AE;EC;ER;ES;EE;ET;FK;FO;FJ;FI;FR;GA;GM;GE;GS;GH;GI;GR;GD;GL;GP;GU;GT;GG;GN;GQ;GW;GY;GF;HT;HM;HN;HK;HU;IM;MU;VG;VI;IN;ID;IR;IQ;IS;IL;IT;JM;JP;JE;JO;KZ;KE;KG;KI;KW;LA;LS;LV;LB;LR;LY;LI;LT;LU;MO;MK;MG;MY;MW;MV;ML;MT;MP;MA;MH;MQ;MR;YT;MX;FM;MD;MC;MN;ME;MS;MZ;NA;NR;NP;NI;NE;NG;NU;NF;NO;NC;NZ;IO;OM;UG;UZ;PK;PW;PS;PA;PG;PY;NL;PE;PH;PN;PL;PF;PR;PT;QA;DO;CZ;RE;RO;UK;RU;RW;EH;BL;KN;SM;MF;PM;VA;VC;LC;SB;WS;AS;ST;SN;RS;SC;SL;SG;SK;SI;SO;SD;LK;SE;CH;SR;SJ;SZ;SY;TJ;TW;TZ;TD;TF;TH;TL;TG;TK;TO;TT;TN;TM;TC;TR;TV;UA;UY;US;VU;VE;VN;WF;YE;ZM;ZW', NOW(), NOW()),
('PS_GEOLOCATION_BEHAVIOR', '0', NOW(), NOW());

ALTER TABLE `PREFIX_orders` ADD `conversion_rate` decimal(13,6) NOT NULL default 1 AFTER `payment`;
UPDATE `PREFIX_orders` o SET o.`conversion_rate` = IFNULL((
	SELECT c.`conversion_rate`
	FROM `PREFIX_currency` c
	WHERE c.`id_currency` = o.`id_currency`
	LIMIT 1), 0
);

ALTER TABLE `PREFIX_order_slip` ADD `conversion_rate` decimal(13,6) NOT NULL default 1 AFTER `id_order`;
UPDATE `PREFIX_order_slip` os SET os.`conversion_rate` = IFNULL((
	SELECT o.`conversion_rate`
	FROM `PREFIX_orders` o
	WHERE os.`id_order` = o.`id_order`
	LIMIT 1), 0
);

UPDATE `PREFIX_configuration` SET `value` = 'gridhtml' WHERE `name` = 'PS_STATS_GRID_RENDER' LIMIT 1;
UPDATE `PREFIX_module` SET `name` = 'gridhtml' WHERE `name` = 'gridextjs' LIMIT 1;

ALTER TABLE `PREFIX_attachment` CHANGE `mime` `mime` varchar(64) NOT NULL;
ALTER TABLE `PREFIX_attachment` ADD `file_name` varchar(128) NOT NULL default '' AFTER `file`;
UPDATE `PREFIX_attachment` a SET `file_name` = (
		SELECT `name` FROM `PREFIX_attachment_lang` al WHERE al.`id_attachment` = a.`id_attachment` AND al.`id_lang` = (
				SELECT `value` FROM `PREFIX_configuration` WHERE `name` = 'PS_LANG_DEFAULT')
		);

UPDATE `PREFIX_tab` SET `class_name` = 'AdminCMSContent' WHERE `class_name` = 'AdminCMS' LIMIT 1;

SET @id_timezone = (SELECT `name` FROM `PREFIX_timezone` WHERE `id_timezone` = (SELECT `value` FROM `PREFIX_configuration` WHERE `name` = 'PS_TIMEZONE' LIMIT 1) LIMIT 1);
UPDATE `PREFIX_configuration` SET `value` = @id_timezone WHERE `name` = "PS_TIMEZONE" LIMIT 1;

ALTER TABLE `PREFIX_country` ADD `id_currency` INT NOT NULL DEFAULT '0' AFTER `id_zone`;

/* PHP */
/* PHP:group_reduction_column_fix(); */;
/* PHP:ecotax_tax_application_fix(); */;
/* PHP:cms_block(); */;
/* PHP:add_new_tab(AdminGeolocation, es:Geolocalización|it:Geolocalizzazione|en:Geolocation|de:Geotargeting|fr:Géolocalisation,  8); */;
