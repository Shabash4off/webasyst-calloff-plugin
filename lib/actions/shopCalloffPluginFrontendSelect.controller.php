<?php

class shopCalloffPluginFrontendSelectController extends waJsonController
{

    public function execute()
    {
        $option = waRequest::post('option');
        $storefront = waRequest::post('storefront');

        if(isset($option) && isset($storefront)) {
            $this->getStorage()->set('shop/calloff/' . $storefront['domain'] . '/' . $storefront['url'] . '/option', $option);
        }
    }
}