<?php

class shopCalloffPluginSettingGroup
{
    protected $name;
    protected $setting_fields;

    /**
     * @param string $name
     * @param array<string, mixed> $setting_fields
     */
    public function __construct($name, $setting_fields)
    {
        $this->name = $name;
        $this->setting_fields = $setting_fields;
    }

    /**
     * Returns setting group name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns setting group fields
     *
     * @param array<string, mixed> $setting_values
     * @return array<string, mixed>
     */
    public function getValue($setting_values)
    {
        $setting = [];

        foreach ($this->setting_fields as $setting_field) {
            $name = $setting_field->getName();
            $value = $setting_field->getValue(isset($setting_values[$name]) ? $setting_values[$name] : null);

            $setting[$name] = $value;
        }

        return $setting;
    }
}