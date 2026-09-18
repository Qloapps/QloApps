<?php

function update_stock_mvt_reason()
{
    //Get all stock mvts reasons already presents in the solution (from 1.4.x)
    //Remove standard movements to keep only custom movement
    $mvts = Db::getInstance()->executeS('
		SELECT smr.*
		FROM `'._DB_PREFIX_.'stock_mvt_reason`
		WHERE `id` > 5
	');

    //Get all stock mvts reasons language traduction already presents in the solution (from 1.4.x)
    //Remove standard movements to keep only custom movement
    $mvts_lang = Db::getInstance()->executeS('
		SELECT smrl.*
		FROM `'._DB_PREFIX_.'stock_movement_reason_lang`
		WHERE `id_stock_mvt_reason` > 5
	');

    //Clean table
    Db::getInstance()->query('TRUNCATE TABLE `'._DB_PREFIX_.'stock_movement_reason`');
    Db::getInstance()->query('TRUNCATE TABLE `'._DB_PREFIX_.'stock_movement_reason_lang`');

    //Recreate new standards movements
    Db::getInstance()->execute('
		INSERT INTO `'._DB_PREFIX_.'stock_mvt_reason` (`id_stock_mvt_reason`, `sign`, `date_add`, `date_upd`)
		VALUES
			(1, 1, NOW(), NOW()),
			(2, -1, NOW(), NOW()),
			(3, -1, NOW(), NOW()),
			(4, -1, NOW(), NOW()),
			(5, 1, NOW(), NOW()),
			(6, -1, NOW(), NOW()),
			(7, 1, NOW(), NOW()),
			(8, 1, NOW(), NOW())
	');

    Db::getInstance()->execute("
		INSERT INTO `"._DB_PREFIX_."stock_mvt_reason_lang` (`id_stock_mvt_reason`, `id_lang`, `name`)
		VALUES
			(1, 1, 'Increase'),
			(1, 2, 'Augmenter'),
			(1, 3, 'Aumentar'),
			(1, 4, 'Erhöhen'),
			(1, 5, 'Increase'),
			(2, 1, 'Decrease'),
			(2, 2, 'Diminuer'),
			(2, 3, 'Disminuir'),
			(2, 4, 'Reduzieren'),
			(2, 5, 'Decrease'),
			(3, 1, 'Customer Order'),
			(3, 2, 'Commande client'),
			(3, 3, 'Pedido'),
			(3, 4, 'Bestellung'),
			(3, 5, 'Ordine'),
			(4, 1, 'Regulation following an inventory of stock'),
			(4, 2, 'Régularisation du stock suite à un inventaire'),
			(4, 3, 'Regulation following an inventory of stock'),
			(4, 4, 'Bestandsberichtigung nach Inventur'),
			(4, 5, 'Regulation following an inventory of stock'),
			(5, 1, 'Regulation following an inventory of stock'),
			(5, 2, 'Régularisation du stock suite à un inventaire'),
			(5, 3, 'Regulation following an inventory of stock'),
			(5, 4, 'Regulation following an inventory of stock'),
			(5, 5, 'Regulation following an inventory of stock'),
			(6, 1, 'Transfer to another warehouse'),
			(6, 2, 'Transfert vers un autre entrepôt'),
			(6, 3, 'Transfer to another warehouse'),
			(6, 4, 'Übertragung in anderes Lager'),
			(6, 5, 'Transfer to another warehouse'),
			(7, 1, 'Transfer from another warehouse'),
			(7, 2, 'Transfert depuis un autre entrepôt'),
			(7, 3, 'Transfer from another warehouse'),
			(7, 4, 'Übertragung von anderem Lager'),
			(7, 5, 'Transfer from another warehouse'),
			(8, 1, 'Supply Order'),
			(8, 2, 'Commande fournisseur'),
			(8, 3, 'Supply Order'),
			(8, 4, 'Lieferbestellung'),
			(8, 5, 'Supply Order')
	");

    //Add custom movements
    if (is_array($mvts)) {
        foreach ($mvts as $mvt) {
            Db::getInstance()->execute('
				INSERT INTO `'._DB_PREFIX_.'stock_mvt_reason` (`sign`, `date_add`, `date_upd`)
				VALUES ("'.(int)$mvt['sign'].'", "'.pSQL($mvt['date_add']).'", "'.pSQL($mvt['date_upd']).'")
			');

            $row_id = Db::getInstance()->Insert_ID();

            foreach ($mvts_lang as $mvt_lang) {
                if ($mvt_lang['id_stock_mvt_reason'] != $mvt['id']) {
                    continue;
                }

                Db::getInstance()->execute('
					INSERT INTO `'._DB_PREFIX_.'stock_mvt_reason_lang` (`id_stock_mvt_reason`, `id_lang`, `name`)
					VALUES ("'.(int)$row_id.'", "'.(int)$mvt_lang['id_lang'].'", "'.pSQL($mvt_lang['name']).'")
				');
            }
        }
    }
}
