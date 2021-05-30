<?php 

class shopCalloffPluginSettingsHelper
{

    /**
     * Returns settings structure
     *
     * @return array<shopCalloffPluginSettingField|shopCalloffPluginSettingGroup>
     */
    public static function getStructure() 
    {
        return [
            new shopCalloffPluginSettingField('active', false),
            new shopCalloffPluginStorefrontsSettingGroup('storefronts', [
                new shopCalloffPluginSettingField('active', false),
                new shopCalloffPluginSettingField('default_value', 'yes'),
                new shopCalloffPluginSettingField('display_step', 'contactinfo'),
                new shopCalloffPluginSettingField('backend_display_mode', 'message'),
                new shopCalloffPluginSettingGroup('frontend', [
                    new shopCalloffPluginSettingField('da_jss', ''),
                    new shopCalloffPluginSettingField('da_css', ''),
                    new shopCalloffPluginSettingField('description', "<p>Вы можете отказаться от обязательного звонка менеджера для подтверждения заказа, Ваш заказ сразу будет собран и отправлен.</p><p><span style=\"color: #888;\">При необходимости, нам все же придется перезвонить Вам для уточнения контактной информации или наличия товара.</span></p>"),
                    new shopCalloffPluginSettingGroup('option', [
                        new shopCalloffPluginSettingField('yes', "<strong>Перезвоните мне</strong> для подтверждения заказа"),
                        new shopCalloffPluginSettingField('no', "<strong>Не перезванивать мне</strong>, сразу отправлять заказ"),
                    ]),
                    new shopCalloffPluginSettingField('form_type', 'checkbox'),

                    new shopCalloffPluginSettingGroup('form_template', [
                        new shopCalloffPluginSettingField('radio', "<ul class=\"calloff_radio\">\n\t<li>\n\t\t<input type=\"radio\" name=\"calloff_option\" id=\"calloff_yes_option\" value=\"yes\">\n\t\t<label for=\"calloff_yes_option\">{\$yes_option}</label>\n\t</li>\n\t<li>\n\t\t<input type=\"radio\" name=\"calloff_option\" id=\"calloff_no_option\" value=\"no\">\n\t\t<label for=\"calloff_no_option\">{\$no_option}</label>\n\t</li>\n</ul>"),
                        new shopCalloffPluginSettingField('checkbox', "<div class=\"calloff_checkbox\">\n\t<input type=\"checkbox\" name=\"calloff_option\" id=\"calloff_option\">\n\t<label for=\"calloff_option\">{\$yes_option}</label>\n</div>"),
                    ]),
                    new shopCalloffPluginSettingField('template', "<div class=\"calloff_plugin\">\n\t<div class=\"calloff_description\">\n\t\t{\$description}\n\t</div>\n\n\t{\$form}\n\n</div>"),
                ]),
                new shopCalloffPluginSettingGroup('backend', [
                    new shopCalloffPluginSettingField('template', "<div class=\"profile image50px\" style=\"margin-top: -18px;\">\n\t<div class=\"image\"></div>\n\n\t<div class=\"details\" style=\"min-height: 1px\">\n\t\t<ul class=\"menu-v with-icons compact\">\n\t\t\t<li>\n\t\t\t\t<div class=\"calloff_plugin\">\n\t\t\t\t\t<img style=\"width: 16px;margin-right: 5px;margin-bottom: -3px;\" src=\"{\$icon_url}\">\n\t\t\t\t\t{\$answer}\n\t\t\t\t</div>\n\t\t\t\t<p class=\"hint\"></p>\n\t\t\t</li>\n\t\t</ul>\n\t</div>\n</div>"),
                    new shopCalloffPluginSettingGroup('answer', [
                        new shopCalloffPluginSettingField('yes', "<span style=\"color: rgb(89, 166, 13);\"><strong>Перезвонить клиенту для подтверждения заказа</strong>.</span>"),
                        new shopCalloffPluginSettingField('no', "<span style=\"color: rgb(204, 0, 0);\"><strong>Не перезванивать клиенту, сразу собирать заказ</strong>.</span>"),
                    ]),
                ]),
            ]),
        ];
    }

    /**
     * Returns normalized settings  
     * If setting value is empty,then assign default value to setting
     *
     * @param array<string, mixed> $settings
     * @return <string, mixed>
     */
    public static function normalizeSettings($settings)
    {
        $setting_structure = self::getStructure();

        $setting_group = new shopCalloffPluginSettingGroup('setting', $setting_structure);

        return $setting_group->getValue($settings);
    }
}