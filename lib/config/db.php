<?php

return [
    'shop_calloff_settings' => array(
        'id' => array('int', 11, 'null' => 0, 'autoincrement' => 1),
        'storefront_code' => array('varchar', 100),
        'name' => array('varchar', 50, 'null' => 0),
        'value' => array('text'),
        'groups' => array('text'),
        ':keys' => array(
            'PRIMARY' => 'id',
        ),
    ),
]; 
