<?php

return [
    'name' => "Без звонка",
    'description' => "Даёт возможность покупателю отказаться от звонка",
    'version' => "1.0.0",
    'vendor' => 1059969,
    'frontend' => true,
    'custom_settings' => true,
    'handlers' => [
        'order_action.create' => 'orderActionCreate',

        'frontend_checkout' => 'frontendCheckout',
        'checkout_render_auth' => 'checkoutRenderAuth',
        'checkout_render_confirm' => 'checkoutRenderConfirm',

        'backend_order' => 'backendOrder'

    ]
];