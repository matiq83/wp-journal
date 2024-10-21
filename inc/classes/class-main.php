<?php

/*
 * Bootstraps the plugin, this class will load all other classes
 *
 * @package WP_JOURNAL
 */

namespace WP_JOURNAL\Inc;

use WP_JOURNAL\Inc\Traits\Singleton;

class Main {

    use Singleton;

    //Construct function
    protected function __construct() {

        //load class
        $this->setup_hooks();

        //Load assets
        Assets::get_instance();

        //Load cron
        //Cron::get_instance();

        Theme::get_instance();

        Ajax::get_instance();

        Shortcodes::get_instance();

        //Load views
        Views::get_instance();

        //Load admin classes
        $this->load_admin_classes();
    }

    /*
     * Function to load action and filter hooks
     */

    protected function setup_hooks() {

        //actions and filters
        add_action('init', [$this, 'load_textdomain']);
    }

    /*
     * Function to load classes only for admin side
     */

    protected function load_admin_classes() {

        if (is_admin()) {
            //Load options
            Options::get_instance();
        }
    }

    /**
     * Load plugin textdomain, i.e language directory
     */
    public function load_textdomain() {

        load_plugin_textdomain(WP_JOURNAL_TEXT_DOMAIN, false, WP_JOURNAL_LANG_DIR);
    }

    /*
     * Function that executes once the plugin is activated
     */

    public function wp_journal_install() {

        //Run code once when plugin activated
        /*
          if (! wp_next_scheduled ( WP_JOURNAL_TEXT_DOMAIN.'_cron_event' )) {

          wp_schedule_event( time(), WP_JOURNAL_TEXT_DOMAIN.'_cron_interval', WP_JOURNAL_TEXT_DOMAIN.'_cron_event' );
          }

          $db = Db::get_instance();

          $db->create_photos_table();
         */
    }

    /*
     * Function that executes once the plugin is deactivated
     */

    public function wp_journal_uninstall() {

        //Run code once when plugin deactivated
        //wp_clear_scheduled_hook( WP_JOURNAL_TEXT_DOMAIN.'_cron_event' );
    }
}
