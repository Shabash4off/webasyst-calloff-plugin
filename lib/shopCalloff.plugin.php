<?php

class shopCalloffPlugin extends shopPlugin 
{

    public function orderActionCreate($data) 
    {
        $settings = shopCalloffPluginHelper::getStorefrontSettings();
        
        $storefront_domain = shopCalloffPluginRoutingHelper::getDomain();
        $storefront_url = shopCalloffPluginRoutingHelper::getUrl();

        $storage = wa()->getStorage(); 

        $session_name = 'shop/calloff/' . $storefront_domain . '/' . $storefront_url . '/option';
        $option = $storage->get($session_name) ?: $settings['default_value'];
        
        $model = new shopOrderParamsModel();
        $model->setOne($data['order_id'], 'calloff_option', $option);
        $model->setOne($data['order_id'], 'calloff_storefront_domain', $storefront_domain);
        $model->setOne($data['order_id'], 'calloff_storefront_url', $storefront_url);
        
        $storage->remove($session_name);
    }

    public function backendOrder($order)
    {
        $option = $order['params']['calloff_option'];

        if(isset($option)) {
            $storefront_domain = $order['params']['calloff_storefront_domain'];
            $storefront_url = $order['params']['calloff_storefront_url'];

            $settings = shopCalloffPluginHelper::getStorefrontSettings($storefront_domain, $storefront_url);

            $details = shopCalloffPluginHelper::render($settings['backend']['details'], [
                'option' => $settings['frontend']['option'][$option] 
            ]); 

            $template = shopCalloffPluginHelper::render($settings['backend']['template'], [
                'details' => $details
            ]);

            $style = "<style>" . $settings['backend']['da_css'] . "</style>";

            return array( 'info_section' => $style . "\n" . $template );
        }

        return array();
    }

    public function frontendCheckout($params)
    {
        return $this->form($params['step']);
    }

    public function checkoutRenderAuth($params)
    {
        return $this->form('contactinfo');
    }

    public function checkoutRenderConfirm($params)
    {
        return $this->form('confirmation');
    }

    private function form($step)
    {
        $active = shopCalloffPluginHelper::isActive();
        $settings = shopCalloffPluginHelper::getStorefrontSettings();

        if($active && $settings['display_step'] === $step) {

            $this->addJs('js/calloff.jss.js');
            $this->addJs('js/calloff.frontend.js');
            
            $frontend_options = shopCalloffPluginHelper::getFrontendSettings();

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

            $script = '<script defer>$(function () { new CalloffFrontend('. json_encode($frontend_options) .') })</script>';

            return $style . "\n" . $template . "\n" . $script;
        }

        return '';
    }
}