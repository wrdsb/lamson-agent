<?php
namespace Lamson;

use Lamson\Model as Model;
use Lamson\Views as Views;
use Lamson\Controllers as Controllers;

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.wrdsb.ca
 * @since             1.0.0
 * @package           Lamson
 *
 * @wordpress-plugin
 * Plugin Name:       Lamson Client
 * Plugin URI:        https://github.com/wrdsb/wordpress-plugin-lamson-client
 * Description:       Provides a set of webhooks and API endpoints for interacting with the Lamson service.
 * Version:           1.0.0
 * Author:            WRDSB
 * Author URI:        https://github.com/wrdsb
 * License:           GPL-3.0+
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain:       lamson
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

require_once 'vendor/autoload.php';

/**
 * Instantiate the container.
 */
$container = Plugin::getContainer();

/**
 * Current plugin name.
 * Change this to your plugin's slug.
 */
$container['plugin_slug'] = 'lamson-agent';

/**
 * Current plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 */
$container['version'] = '1.0.0';

/**
 * Current plugin file.
 * Theres should be no need to change this.
 */
$container['plugin_file'] = __FILE__;

/**
 * Plugin priority.
 * Passed as an argument when registering hooks.
 */
$container['plugin_priority'] = 12838790321;

/**
 * Instantiate main plugin class.
 * Pass properties stored in the container to the plugin's constructor.
 */
$container['Plugin'] = function ($c) {
    return new Plugin($c['plugin_slug'], $c['plugin_file'], $c['version']);
};

/**
 * Bootstrap the plugin.
 */
$plugin = $container['Plugin'];

$plugin->add_action('publish_post', 'Lamson\Model\Post', 'sendToService', $container['plugin_priority'], 2);
$plugin->add_action('publish_page', 'Lamson\Model\Post', 'sendToService', $container['plugin_priority'], 2);

$plugin->add_action('rest_api_init', 'Lamson\Controllers\Posts', 'registerRoutes');

$plugin->addFilter('rwmb_meta_boxes', 'Lamson\Views', 'PostEdit', 'addMetaBoxes');

$plugin->registerHooks();
