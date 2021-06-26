<?php

$app_id = 'shop.calloff';
$wa_settings_model = new waAppSettingsModel();
$storefronts = json_decode($wa_settings_model->get($app_id, 'storefronts', '{}'), true);


// Create settings table
$model = new waModel();
$model->exec("CREATE TABLE IF NOT EXISTS `shop_calloff_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `storefront_code` varchar(126) NOT NULL,
  `name` varchar(64) NOT NULL,
  `value` text,
  `groups` text,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8");


if(!empty($storefronts)) {
    foreach ($storefronts as $domain => $domain_urls) {
        
        foreach($domain_urls as $url => $storefront_settings) {
            $storefront_code = $domain === '*' && $url === '*' ? '*' : base64_encode($domain . '/' . $url);

            foreach ($storefront_settings as $setting_name => $setting_value) {
                if(is_array($setting_value)) {
                    
                    foreach ($setting_value as $setting_group_name => $setting_group_fields) {
                        if(is_array($setting_group_fields)) {

                            foreach ($setting_group_fields as $setting_group_field_name => $setting_group_field_value) {

                                $sql = "INSERT INTO `shop_calloff_settings` (storefront_code, name, value, groups) VALUES (s:storefront_code, s:name, s:value, s:groups)";

                                $model->exec($sql, [ 
                                    'storefront_code' => $storefront_code, 
                                    'name' => $setting_group_field_name, 
                                    'value' => $setting_group_field_value,
                                    'groups' => json_encode([$setting_name, $setting_group_name])
                                ]);

                            }

                        } else {
                            $sql = "INSERT INTO `shop_calloff_settings` (storefront_code, name, value, groups) VALUES (s:storefront_code, s:name, s:value, s:groups)";

                            $model->exec($sql, [ 
                                'storefront_code' => $storefront_code, 
                                'name' => $setting_group_name, 
                                'value' => $setting_group_fields,
                                'groups' => json_encode([$setting_name])
                            ]);
                        }
                    }
            
                } else {

                    $sql = "INSERT INTO `shop_calloff_settings` (storefront_code, name, value) VALUES (s:storefront_code, s:name, s:value)";
                    $model->exec($sql, [ 
                        'storefront_code' => $storefront_code, 
                        'name' => $setting_name, 
                        'value' => $setting_value
                    ]);
                    
                }
            }
        }
    }

    $wa_settings_model->del($app_id, 'storefronts');

}
