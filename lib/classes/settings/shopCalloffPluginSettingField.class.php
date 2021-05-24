<?php

class shopCalloffPluginSettingField
{
    protected $name;
    protected $default_value;

    /**
     * @param string $name
     * @param mixed $default_value
     */
    public function __construct($name, $default_value)
    {
        $this->name = $name;
        $this->default_value = $default_value;
    }

    /**
     * Returns setting name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns setting value
     *
     * @param mixed $setting_value
     * @return mixed
     */
    public function getValue($setting_value) {
        return isset($setting_value) || $setting_value === '' ? $setting_value : $this->default_value;
    }
}