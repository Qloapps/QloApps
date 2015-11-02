<?php

if (!defined('_PS_VERSION_'))
        exit;

function upgrade_module_1_2_3($object)
{
        return Configuration::updateValue('BLOCKTAGS_RANDOMIZE', false);
}
