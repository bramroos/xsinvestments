<?php
/**
 * Plugin Name:       Dante Framework
 * Plugin URI:        http://swiftideas.com/dante-framework/
 * Description:       Framework plugin for the Neightborhood WordPress theme
 * Version:           1.0.9
 * Author:            Swift Ideas
 * Author URI:        http://swiftideas.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       dante-framework
 * Domain Path:       /languages
 */

define( 'DANTE_FRAMEWORK_VERSION', '1.0.9' );


// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// The class that contains the plugin info.
require_once plugin_dir_path(__FILE__) . 'includes/class-info.php';

/**
 * The code that runs during plugin activation.
 */
function dante_framework_activation() {
    require_once plugin_dir_path(__FILE__) . 'includes/class-activator.php';
    DanteFramework_Activator::activate();
}
register_activation_hook(__FILE__, 'dante_framework_activation');

/**
 * Run the plugin.
 */
function dante_framework_run() {
    require_once plugin_dir_path(__FILE__) . 'includes/class-plugin.php';
    $plugin = new DanteFramework_Plugin();
    $plugin->run();


	/* UPDATE
	================================================== */
	require_once plugin_dir_path(__FILE__) . 'includes/plugin_update_check.php';
	$KernlUpdater = new PluginUpdateChecker_2_0 (
	    'https://kernl.us/api/v1/updates/5ced5fb58d51452e00d3c026/',
	    __FILE__,
	    'dante-framework',
	    1
	);
}
dante_framework_run();

