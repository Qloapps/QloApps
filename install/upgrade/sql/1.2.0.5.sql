SET NAMES 'utf8';

/* ##################################### */
/* 				STRUCTURE			 		 */
/* ##################################### */

/* ##################################### */
/* 					CONTENTS					 */
/* ##################################### */

UPDATE `PREFIX_order_state_lang`
SET `name` = 'Shipped'
WHERE `id_order_state` = 4 AND `id_lang` = 1;

UPDATE `PREFIX_order_state_lang` SET `template` = 'shipped' WHERE `id_order_state` = 4 AND `template` = 'shipping';

/* PHP:reorderpositions(); */;