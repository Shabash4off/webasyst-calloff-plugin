<?php

class shopCalloffPluginRoutingHelper
{
    /**
     * Returns webasyst routing instance
     *
     * @return waRouting
     */
    public static function getRouting()
    {
        return wa()->getRouting();
    }

    /**
     * Returns current location domain
     *
     * @return string
     */
    public static function getDomain()
    {
        return self::getRouting()->getDomain();
    }

    /**
     * Returns current location route
     *
     * @return string
     */
    public static function getUrl()
    {
        return self::getRouting()->getRoute('url');
    }
}