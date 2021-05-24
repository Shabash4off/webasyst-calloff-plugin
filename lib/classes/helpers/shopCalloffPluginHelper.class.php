<?php

class shopCalloffPluginHelper 
{

    const APP_ID = "shop";

    const PLUGIN_ID = "calloff";

    /**
     * Returns plugin instance
     *
     * @return waPlugin
     */
    public static function getPlugin()
    {
        return wa(shopCalloffPluginHelper::APP_ID)->getPlugin(shopCalloffPluginHelper::PLUGIN_ID);
    }

    /**
     * Returns plugin settings
     *
     * @return array<string, mixed>
     */
    public static function getSettings()
    {
        $settings = shopCalloffPluginHelper::getPlugin()->getSettings();

        return shopCalloffPluginSettingsHelper::normalizeSettings($settings);
    }

    /**
     * Returns storefront list
     *
     * @return array<string, array<number, mixed>>
     */
    public static function getStorefronts()
    {
        return shopCalloffPluginRoutingHelper::getRouting()->getByApp(self::APP_ID);
    }

    /**
     * Returns current storefront plugin settings
     *
     * @return array<string, mixed>
     */
    public static function getStorefrontSettings($domain = null, $url = null)
    {
        $settings = self::getSettings();
        $routing = wa()->getRouting();

        if(empty($domain)) $domain = $routing->getDomain();
        if(empty($url)) $url = $routing->getRoute('url');

        $current_storefront_settings = $settings['storefronts'][$domain][$url];
        $current_storefront_settings_active = self::toBoolean($current_storefront_settings['active']);

        $common_settings = $settings['storefronts']['*']['*'];
        $common_settings_active = self::toBoolean($common_settings['active']);
        
        if($common_settings_active && !$current_storefront_settings_active) {
            return $common_settings;
        }

        return $current_storefront_settings;
    }

    /**
     * Returns settings for frontend script
     *
     * @return array<string, mixed>
     */
    public static function getFrontendSettings() 
    {
        $storefront_settings = self::getStorefrontSettings();
        $storefront_domain = shopCalloffPluginRoutingHelper::getDomain();
        $storefront_url = shopCalloffPluginRoutingHelper::getUrl();

        $value = wa()->getStorage()->get('shop/calloff/' . $storefront_domain . '/' . $storefront_url . '/option') ?: $storefront_settings['default_value'];

        return [
            'value' => $value,
            'storefront' => [
                'domain' => $storefront_domain,
                'url' => $storefront_url
            ],
            'form_type' => $storefront_settings['frontend']['form_type']
        ];
    }

    /**
     * Returns plugin status
     *
     * @return boolean
     */
    public static function isActive()
    {
        return self::toBoolean(self::getSettings()['active']) && self::toBoolean(self::getStorefrontSettings()['active']);
    }

    /**
     * Returns rendered template
     *
     * @param string $template
     * @param array<string, mixed> $vars
     * @return string
     */
    public static function render($template, $vars)
    {
        $view = wa()->getView();

        foreach ($vars as $key => $value) {
            $view->assign($key, $value);
        }

        return $view->fetch('string:' . $template);
    }

    private static function toBoolean($value) 
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }
}