<?php

class shopCalloffPluginStorefrontsSettingGroup extends shopCalloffPluginSettingGroup
{
    public function __construct($name, $setting_fields)
    {
        $storefront_setting_fields = [];
        $storefronts = shopCalloffPluginHelper::getStorefronts();

        foreach($storefronts as $domain=>$routes) {
            $route_groups = [];

            foreach($routes as $route) {
                $url = $route['url'];

                $route_groups[] = new shopCalloffPluginSettingGroup($url, $setting_fields);
            }

            $storefront_setting_fields[] = new shopCalloffPluginSettingGroup($domain, $route_groups);
        }

        // Common storefront
        $storefront_setting_fields[] = new shopCalloffPluginSettingGroup('*', [
            new shopCalloffPluginSettingGroup('*', $setting_fields)
        ]);

        parent::__construct($name, $storefront_setting_fields);
    }
}