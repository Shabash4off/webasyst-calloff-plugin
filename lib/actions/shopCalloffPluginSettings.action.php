<?php

class shopCalloffPluginSettingsAction extends waViewAction
{

    public function execute()
    {

        $app_id = shopCalloffPluginHelper::APP_ID;
        $plugin_id = shopCalloffPluginHelper::PLUGIN_ID;

        $settings = shopCalloffPluginHelper::getSettings();
        $storefronts = shopCalloffPluginHelper::getStorefronts();

        $this->view->assign('app_id', $app_id);
        $this->view->assign('plugin_id', $plugin_id);
        $this->view->assign('settings', $settings);
        $this->view->assign('storefronts', $storefronts);
    }
}