<?php

class shopCalloffPluginSettingsModel extends waModel
{
    protected $table = 'shop_calloff_settings';

    public function get($storefront_code = '*')
    {
        $sql = "SELECT * FROM {$this->table} WHERE `storefront_code` = s:storefront_code";
        $rows = $this->query($sql, ['storefront_code' => $storefront_code]);

        $settings = $this->parse($rows);

        return $settings;
    }

    public function set($storefront_code, $name, $value, $groups = null)
    {
        $fields = [ 
            'storefront_code' => $storefront_code, 
            'name' => $name, 
            'groups' => json_encode($groups),
        ];

        if($this->getByField($fields)){
            $this->updateByField($fields, ['value' => $value]);
        } else {
            $this->insert(array_merge($fields, ['value' => $value]));
        }
    }

    private function parse($rows)
    {
        $settings = [];

        foreach ($rows as $row) {
            $name = $row['name'];
            $value = $row['value'];

            $groups = !empty($row['groups']) ? json_decode($row['groups']) : null;

            if(is_array($groups)) {
                $settings_group = &$settings;
                foreach ($groups as $group) {
                    if(!isset($settings_group[$group])) {
                        $settings_group[$group] = [];
                    }
                    $settings_group = &$settings_group[$group];
                }

                $settings_group[$name] = $value;
            } else {
                $settings[$name] = $value;
            }
        }

        return $settings;
    }
} 