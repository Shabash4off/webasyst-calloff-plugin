<?php

class shopCalloffPluginSettingsStorefrontAction extends waViewAction
{
    public function execute()
    {
        $storefront_domain = waRequest::post('domain');
        $storefront_url = waRequest::post('url');
        
        $storefront_code = shopCalloffPluginHelper::getStorefrontCode($storefront_domain, $storefront_url);

        $settings = shopCalloffPluginSettingsHelper::model()->get($storefront_code);
        $settings = shopCalloffPluginSettingsHelper::normalizeSettings($settings, 'storefront');

        $app_id = shopCalloffPluginHelper::APP_ID;
        $plugin_id = shopCalloffPluginHelper::PLUGIN_ID;

        // Locale config
        waLocale::loadByDomain(array('shop', 'calloff'));
        waSystem::pushActivePlugin('calloff', 'shop');

        $this->view->assign('app_id', $app_id);
        $this->view->assign('plugin_id', $plugin_id);

        $this->view->assign('storefront_code', $storefront_code);
        $this->view->assign('settings', $settings);
    }
}