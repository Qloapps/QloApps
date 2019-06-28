<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_2_9_1($object)
{
    $id_tab = (int)Tab::getIdFromClassName('AdminBlockCategories');

    if (!$id_tab) {
        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = 'AdminBlockCategories';
        $tab->name = array();
        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = 'BlockCategories';
        }
        $tab->id_parent = -1;
        $tab->module = $object->name;

        return $tab->add();
    }
    return true;
}
