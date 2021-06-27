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

    public static function getStorefrontCode($domain, $url)
    {
        return $domain === '*' && $url === '*' ? '*' : base64_encode($domain . '/' . $url);
    }

    /**
     * Returns plugin settings
     *
     * @return array<string, mixed>
     */
    public static function getSettings()
    {
        $settings = self::getPlugin()->getSettings();

        $settings = shopCalloffPluginSettingsHelper::normalizeSettings($settings, 'common');

        return $settings;
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

        if(empty($domain)) $domain = shopCalloffPluginRoutingHelper::getDomain();
        if(empty($url)) $url = shopCalloffPluginRoutingHelper::getUrl();

        $storefront_code = self::getStorefrontCode($domain, $url);

        $current_storefront_settings = shopCalloffPluginSettingsHelper::model()->get($storefront_code);
        $current_storefront_settings = shopCalloffPluginSettingsHelper::normalizeSettings($current_storefront_settings, 'storefront');
        $current_storefront_settings_active = self::toBoolean($current_storefront_settings['active']);

        $common_settings = shopCalloffPluginSettingsHelper::model()->get('*');
        $common_settings = shopCalloffPluginSettingsHelper::normalizeSettings($common_settings, 'storefront');
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
    public static function getFrontendSettings($domain = null, $url = null) 
    {
        $storefront_domain = $domain ?: shopCalloffPluginRoutingHelper::getDomain();
        $storefront_url = $url ?: shopCalloffPluginRoutingHelper::getUrl();

        $storefront_settings = self::getStorefrontSettings($storefront_domain, $storefront_url);

        $session_value = wa()->getStorage()->get('shop/calloff/' . $storefront_domain . '/' . $storefront_url . '/option');
        $value = isset($session_value) ? $session_value : $storefront_settings['default_value'];

        $form = self::renderForm($storefront_domain, $storefront_url);

        return [
            'value' => $value,
            'storefront' => [
                'domain' => $storefront_domain,
                'url' => $storefront_url
            ],
            'form_type' => $storefront_settings['frontend']['form_type'],
            'form' => $form,
            'selector' => $storefront_settings['display_step'] === 'selector' ? $storefront_settings['selector'] : null 
        ];
    }

    public static function renderForm($domain = null, $url = null) 
    {
        $settings = shopCalloffPluginHelper::getStorefrontSettings($domain, $url);

        $form_type = $settings['frontend']['form_type'];

        $form_template = shopCalloffPluginHelper::render($settings['frontend']['form_template'][$form_type], [
            'yes_option' => $settings['frontend']['option']['yes'],
            'no_option' => $settings['frontend']['option']['no']
        ]);

        $template = shopCalloffPluginHelper::render($settings['frontend']['template'], [
            'description' => $settings['frontend']['description'],
            'form' => $form_template
        ]);

        $style = "<style>" . $settings['frontend']['da_css'] . "</style>";

        return $style . "\n" . $template;
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

    public static function toBoolean($value) 
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }
}