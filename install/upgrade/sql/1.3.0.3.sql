SET NAMES 'utf8';

ALTER TABLE `PREFIX_tab` CHANGE  `id_parent`  `id_parent` INT(11) NOT NULL;
INSERT INTO `PREFIX_tab` (`id_tab`, `class_name`, `id_parent`, `position`) 
VALUES (43, 'AdminSearch', -1, 0) 
ON DUPLICATE KEY 
UPDATE `id_parent` = -1;

ALTER TABLE `PREFIX_search_engine` ADD UNIQUE (`server`,`getvar`);
REPLACE INTO `PREFIX_search_engine` (`server`,`getvar`)
VALUES  ('google','q'),('aol','q'),('yandex','text'),('ask.com','q'),('nhl.com','q'),('yahoo','p'),('baidu','wd'),
('lycos','query'),('exalead','q'),('search.live','q'),('voila','rdata'),('altavista','q'),('bing','q'),('daum','q'),
('eniro','search_word'),('naver','query'),('msn','q'),('netscape','query'),('cnn','query'),('about','terms'),('mamma','query'),
('alltheweb','q'),('virgilio','qs'),('alice','qs'),('najdi','q'),('mama','query'),('seznam','q'),('onet','qt'),('szukacz','q'),
('yam','k'),('pchome','q'),('kvasir','q'),('sesam','q'),('ozu','q'),('terra','query'),('mynet','q'),('ekolay','q'),('rambler','words');