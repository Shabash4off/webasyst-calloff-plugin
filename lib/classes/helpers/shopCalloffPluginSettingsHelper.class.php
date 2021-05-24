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
                new shopCalloffPluginSettingGroup('frontend', [
                    new shopCalloffPluginSettingField('da_jss', ''),
                    new shopCalloffPluginSettingField('da_css', ''),
                    new shopCalloffPluginSettingField('description', "Вы можете отказаться от обязательного звонка консультанта интернет-магазина, а Ваши заказы будут сразу же собираться и отправляться. Это значит, что Вы сможете быстрее получить свой заказ.<br>\nМы будем Вам перезванивать если:<br>\n- Для оформления заказа нужно уточнение контактной информации или наличия товара<br>\n- Вы отметите «Да, перезвонить для подтверждения заказа»"),
                    new shopCalloffPluginSettingGroup('option', [
                        new shopCalloffPluginSettingField('yes', "<strong>Да.</strong> Перезвоните мне для подтверждения заказа."),
                        new shopCalloffPluginSettingField('no', "<strong>Нет.</strong> Можете мне не перезванивать, а сразу собирать и отправлять заказ."),
                    ]),
                    new shopCalloffPluginSettingField('form_type', 'checkbox'),

                    new shopCalloffPluginSettingGroup('form_template', [
                        new shopCalloffPluginSettingField('radio', "<ul>\n\t<li>\n\t\t<input type=\"radio\" name=\"calloff_option\" id=\"calloff_yes_option\" value=\"yes\">\n\t\t<label for=\"calloff_yes_option\">{\$yes_option}</label>\n\t</li>\n\t<li>\n\t\t<input type=\"radio\" name=\"calloff_option\" id=\"calloff_no_option\" value=\"no\">\n\t\t<label for=\"calloff_no_option\">{\$no_option}</label>\n\t</li>\n</ul>"),
                        new shopCalloffPluginSettingField('checkbox', "<input type=\"checkbox\" name=\"calloff_option\" id=\"calloff_option\">\n<label for=\"calloff_option\">{\$yes_option}</label>"),
                    ]),
                    new shopCalloffPluginSettingField('template', "<div class=\"calloff_plugin\">\n\t<div>\n\t\t{\$description}\n\t</div>\n\n\t{\$form}\n\n</div>"),
                ]),
                new shopCalloffPluginSettingGroup('backend', [
                    new shopCalloffPluginSettingField('da_jss', ''),
                    new shopCalloffPluginSettingField('da_css', ''),
                    new shopCalloffPluginSettingField('template', "<div class=\"calloff_plugin\">\n\t{\$details}\n</div>"),
                    new shopCalloffPluginSettingField('details', 'Перезвоните мне: {$option}'),
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