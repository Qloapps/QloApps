SET NAMES 'utf8';

UPDATE `PREFIX_orders` o
SET o.`current_state` = IFNULL((
	SELECT oh.`id_order_state`
	FROM `PREFIX_order_history` oh
	WHERE oh.`id_order` = o.`id_order`
	ORDER BY oh.`date_add` DESC
	LIMIT 1
), 0);

