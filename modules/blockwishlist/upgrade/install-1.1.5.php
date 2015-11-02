<?php

if (!defined('_PS_VERSION_'))
    exit;

function upgrade_module_1_1_5()
{
    $list_fields = Db::getInstance()->executeS('SHOW FIELDS FROM `'._DB_PREFIX_.'wishlist`');

    if (is_array($list_fields))
        foreach ($list_fields as $k => $field)
            if ($field['Field'] == 'default' && $field['Type'] == 'int(11)')
                return (bool)Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'wishlist` CHANGE `default` `default` INT( 11 ) NOT NULL DEFAULT "0"');

    return true;
}
