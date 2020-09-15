<?php

/**
 * The class containing informatin about the plugin.
 */
class DanteFramework_Info
{
    /**
     * The plugin slug.
     *
     * @var string
     */
    const SLUG = 'dante-framework';

    /**
     * The plugin version.
     *
     * @var string
     */
    const VERSION = '1.0.2';

    /**
     * The name for the entry in the options table.
     *
     * @var string
     */
    const OPTION_NAME = 'dante_framework';

    /**
     * The URL where your update server is located (uses wp-update-server).
     *
     * @var string
     */
    const UPDATE_URL = 'http://swiftideas.com/dante-framework/';

    /**
     * Retrieves the plugin title from the main plugin file.
     *
     * @return string The plugin title
     */
    public static function get_plugin_title() {
        $path = plugin_dir_path(dirname(__FILE__)).self::SLUG.'.php';
        return get_plugin_data($path)['Name'];
    }
}
