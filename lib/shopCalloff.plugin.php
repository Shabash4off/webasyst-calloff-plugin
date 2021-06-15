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

            $this->addCss('css/calloff.frontend.css');
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

            $script = '<script>$(function () { new CalloffFrontend('. json_encode($frontend_options) .') })</script>';

            return $style . "\n" . $template . "\n" . $script;
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
}