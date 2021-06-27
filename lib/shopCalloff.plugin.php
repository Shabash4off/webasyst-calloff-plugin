<?php
class shopCalloffPlugin extends shopPlugin 
{

    public function orderActionCreate($data) 
    {
        $storefront_domain = shopCalloffPluginRoutingHelper::getDomain();
        $storefront_url = shopCalloffPluginRoutingHelper::getUrl();

        $storage = wa()->getStorage(); 
        $settings = shopCalloffPluginHelper::getStorefrontSettings($storefront_domain, $storefront_url);

        $active = shopCalloffPluginHelper::toBoolean(shopCalloffPluginHelper::getSettings()['active']);
        $storefront_active = shopCalloffPluginHelper::toBoolean($settings['active']);

        if ($active && $storefront_active) {
            $session_name = 'shop/calloff/' . $storefront_domain . '/' . $storefront_url . '/option';
    
            $session_option = $storage->get($session_name);
            $option = isset($session_option) ? $session_option : $settings['default_value'];
            
            $model = new shopOrderParamsModel();
            $model->setOne($data['order_id'], 'calloff_option', $option);
            $model->setOne($data['order_id'], 'calloff_storefront_domain', $storefront_domain);
            $model->setOne($data['order_id'], 'calloff_storefront_url', $storefront_url);
            
            $storage->remove($session_name);
        }
    }

    public function backendOrder($order)
    {
        return [
            'title_suffix' => $this->icon($order),
            'info_section' => $this->message($order)
        ];
    }

    public function frontendCheckout($params)
    {
        return $this->form($params['step']) ?: $this->form('selector', 'checkout');
    }

    public function checkoutRenderAuth($params)
    {
        return $this->form('contactinfo');
    }

    public function checkoutRenderConfirm($params)
    {
        return $this->form('confirmation');
    }

    public function frontendOrder()
    {
        return $this->form('selector');
    }

    public static function display() 
    {
        return shopCalloffPluginHelper::getPlugin()->form('helper');
    }

    public function form($step)
    {
        $active = shopCalloffPluginHelper::isActive();
        $settings = shopCalloffPluginHelper::getStorefrontSettings();

        if($active && $settings['display_step'] === $step) {

            $this->addCss('css/calloff.frontend.css');
            $this->addJs('js/calloff.jss.js');
            $this->addJs('js/calloff.frontend.js');
            
            $frontend_options = shopCalloffPluginHelper::getFrontendSettings();

            $script = '<script id="calloff-script">$(function () { new CalloffFrontend('. json_encode($frontend_options) .') })</script>';

            return $script;            
        }

        return '';
    }

    private function message($order)
    {
        if(isset($order['params']['calloff_option'])) {
            $option = $order['params']['calloff_option'];

            $storefront_domain = $order['params']['calloff_storefront_domain'];
            $storefront_url = $order['params']['calloff_storefront_url'];

            $settings = shopCalloffPluginHelper::getStorefrontSettings($storefront_domain, $storefront_url);

            if($settings['backend_display_mode'] === 'message') {
                $answer = $settings['backend']['answer'][$option];

                $plugin_static_url = $this->getPluginStaticUrl(true);

                $icon_url = $plugin_static_url . 'img/option/' . $option . '_option.png';

                $template = shopCalloffPluginHelper::render($settings['backend']['template'], [
                    'answer' => $answer,
                    'icon_url' => $icon_url,
                ]);
    
                return $template;
            }
        }

        return '';
    }

    private function icon($order) {
        if(isset($order['params']['calloff_option'])) {
            $option = $order['params']['calloff_option'];

            $storefront_domain = $order['params']['calloff_storefront_domain'];
            $storefront_url = $order['params']['calloff_storefront_url'];

            $settings = shopCalloffPluginHelper::getStorefrontSettings($storefront_domain, $storefront_url);

            if($settings['backend_display_mode'] === 'icon') {
                $answer = $settings['backend']['answer'][$option];

                $plugin_static_url = $this->getPluginStaticUrl(true);

                $icon_src = $plugin_static_url . 'img/option/' . $option . '_option.png';
                $icon_title = strip_tags($answer);

                return '<img style="width:20px" title="' . $icon_title . '" src="' . $icon_src . '">';
            }
        }

        return '';
    }

    public function saveSettings($settings = array())
    {
        foreach ($settings['storefronts'] as $storefront_code => $storefront_settings) {
            foreach ($storefront_settings as $setting_name => $setting_value) {
                if(is_array($setting_value)) {

                    foreach ($setting_value as $setting_group_name => $setting_group_fields) {
                        if(is_array($setting_group_fields)) {
                            foreach ($setting_group_fields as $setting_group_field_name => $setting_group_field_value) {
                                shopCalloffPluginSettingsHelper::model()->set($storefront_code, $setting_group_field_name, $setting_group_field_value, [$setting_name, $setting_group_name]);
                            }
                        }
                        else {
                            shopCalloffPluginSettingsHelper::model()->set($storefront_code, $setting_group_name, $setting_group_fields, [$setting_name]);
                        }  
                    }
                } else {
                    shopCalloffPluginSettingsHelper::model()->set($storefront_code, $setting_name, $setting_value);
                }
            }
        }

        if (isset($settings['active'])) parent::saveSettings(['active' => $settings['active']]);
    }
}