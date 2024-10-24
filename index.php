<?php

/*
  Plugin Name: WP Journal
  Description: Journal/Note taking plugin
  Version: 1.0.0
  Author: Muhammad Atiq
 */

// Make sure we don't expose any info if called directly
if (!function_exists('add_action')) {
    echo "Hi there!  I'm just a plugin, not much I can do when called directly.";
    exit;
}

//ini_set('display_errors', 1);ini_set('display_startup_errors', 1);error_reporting(E_ALL);
//Global define variables
define('WP_JOURNAL_PLUGIN_NAME', 'WP Journal');
define('WP_JOURNAL_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('WP_JOURNAL_PLUGIN_URL', plugin_dir_url(__FILE__));
define('WP_JOURNAL_SLUG', plugin_basename(__DIR__));
define('WP_JOURNAL_SITE_BASE_URL', rtrim(get_bloginfo('url'), "/") . "/");
define('WP_JOURNAL_LANG_DIR', WP_JOURNAL_PLUGIN_PATH . 'language/');
define('WP_JOURNAL_INC_DIR', WP_JOURNAL_PLUGIN_PATH . 'inc/');
define('WP_JOURNAL_VIEWS_DIR', WP_JOURNAL_PLUGIN_PATH . 'views/');
define('WP_JOURNAL_ASSETS_DIR_URL', WP_JOURNAL_PLUGIN_URL . 'assets/');
define('WP_JOURNAL_ASSETS_DIR_PATH', WP_JOURNAL_PLUGIN_PATH . 'assets/');
define('WP_JOURNAL_SETTINGS_KEY', '_wp_journal_options');
define('WP_JOURNAL_TEXT_DOMAIN', 'wp_journal');
define('WP_JOURNAL_POST_TYPE', 'wp_journal');

//Plugin update checker
require WP_JOURNAL_PLUGIN_PATH . 'update/plugin-update-checker.php';

use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

$updateChecker = PucFactory::buildUpdateChecker(
                WP_JOURNAL_UPDATE_URL . WP_JOURNAL_SLUG . '.json',
                __FILE__,
                WP_JOURNAL_SLUG
);

//Load the classes
require_once WP_JOURNAL_PLUGIN_PATH . '/inc/helpers/autoloader.php';

//Get main class instance
$main = WP_JOURNAL\Inc\Main::get_instance();

//Plugin activation hook
register_activation_hook(__FILE__, [$main, 'wp_journal_install']);

//Plugin deactivation hook
register_deactivation_hook(__FILE__, [$main, 'wp_journal_uninstall']);

if (!function_exists('nectar_meta_viewport')) {

    function nectar_meta_viewport() {

        global $nectar_options;
        $interactive_widget = ', interactive-widget=resizes-content, viewport-fit=cover';
        if (isset($nectar_options['meta_viewport']) && 'scalable' === $nectar_options['meta_viewport']) {
            echo '<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5' . $interactive_widget . '" />';
        } else if (!empty($nectar_options['responsive']) && '1' === $nectar_options['responsive']) {
            echo '<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0' . $interactive_widget . '" />';
        } else {
            echo '<meta name="viewport" content="width=1200' . $interactive_widget . '" />';
        }
    }

}