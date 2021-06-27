<?php

class shopCalloffPluginFrontendSelectController extends waJsonController
{

    public function execute()
    {
        $option = waRequest::post('option');
        $storefront = waRequest::post('storefront');

        if(isset($option) && isset($storefront)) {
            $this->getStorage()->set('shop/calloff/' . $storefront['domain'] . '/' . $storefront['url'] . '/option', $option);
        } else if(isset($storefront)) {
            $settings = shopCalloffPluginHelper::getStorefrontSettings($storefront['domain'], $storefront['url']);

            $session_value = wa()->getStorage()->get('shop/calloff/' . $storefront['domain'] . '/' . $storefront['url'] . '/option');
            $value = isset($session_value) ? $session_value : $settings['default_value'];

            $this->response = $value;
        }
    }
}