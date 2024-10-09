<?php

function create_entry_for_payment_detail_table_160()
{
/* PHP:create_entry_for_payment_detail_table_160(); */;

    $sql = 'SELECT op.*, o.`id_order`,  FROM `'._DB_PREFIX_.'order_payment` op
        INNER JOIN `'._DB_PREFIX_.'orders` o ON (o.`reference` - op.`order_reference`)';
    if (!empty($payments = Db::getInstance()->executeS($sql))) {

    }
}