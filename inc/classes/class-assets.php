<?php

/*
 * Enqueue plugin assets
 *
 * @package WP_JOURNAL
 */

namespace WP_JOURNAL\Inc;

use WP_JOURNAL\Inc\Traits\Singleton;

class Assets {

    use Singleton;

    //Construct function
    protected function __construct() {

        //load class
        $this->setup_hooks();
    }

    /*
     * Function to load action and filter hooks
     */

    protected function setup_hooks() {

        //actions and filters
        //Register styles
        add_action('wp_enqueue_scripts', [$this, 'register_styles']);

        //Register styles for the admin
        if (is_admin()) {
            add_action('admin_enqueue_scripts', [$this, 'register_styles']);
        }

        //Register scripts
        add_action('wp_enqueue_scripts', [$this, 'register_scripts']);

        //Register scripts for the admin
        if (is_admin()) {
            add_action('admin_enqueue_scripts', [$this, 'register_scripts']);
        }
    }

    /*
     * Function to register styles
     */

    public function register_styles() {

        //Register style
        wp_register_style(WP_JOURNAL_TEXT_DOMAIN . '_style', WP_JOURNAL_ASSETS_DIR_URL . 'css/style.css', [], filemtime(WP_JOURNAL_ASSETS_DIR_PATH . 'css/style.css'));
        wp_register_style(WP_JOURNAL_TEXT_DOMAIN . '_bootstrap', WP_JOURNAL_ASSETS_DIR_URL . 'css/bootstrap.min.css', [], filemtime(WP_JOURNAL_ASSETS_DIR_PATH . 'css/bootstrap.min.css'));
        wp_register_style(WP_JOURNAL_TEXT_DOMAIN . '_select2', WP_JOURNAL_ASSETS_DIR_URL . 'css/select2.min.css', [], filemtime(WP_JOURNAL_ASSETS_DIR_PATH . 'css/select2.min.css'));
        wp_register_style(WP_JOURNAL_TEXT_DOMAIN . '_font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css');

        //enqueue style
        wp_enqueue_style(WP_JOURNAL_TEXT_DOMAIN . '_style');
        wp_enqueue_style(WP_JOURNAL_TEXT_DOMAIN . '_font-awesome');
    }

    /*
     * Function to register scripts
     */

    public function register_scripts() {

        //Register script
        wp_register_script(WP_JOURNAL_TEXT_DOMAIN . '_js', WP_JOURNAL_ASSETS_DIR_URL . 'javascript/main.js', ['jquery'], filemtime(WP_JOURNAL_ASSETS_DIR_PATH . 'javascript/main.js'), true);
        wp_register_script(WP_JOURNAL_TEXT_DOMAIN . '_bootstrap', WP_JOURNAL_ASSETS_DIR_URL . 'javascript/bootstrap.bundle.min.js', ['jquery'], filemtime(WP_JOURNAL_ASSETS_DIR_PATH . 'javascript/bootstrap.bundle.min.js'), true);
        wp_register_script(WP_JOURNAL_TEXT_DOMAIN . '_select2', WP_JOURNAL_ASSETS_DIR_URL . 'javascript/select2.min.js', ['jquery'], filemtime(WP_JOURNAL_ASSETS_DIR_PATH . 'javascript/select2.min.js'), true);

        wp_enqueue_script('jquery');

        //enqueue script
        wp_enqueue_script(WP_JOURNAL_TEXT_DOMAIN . '_js');

        $options = Options::get_instance()->get_plugin_options();

        //localize a registered script with data for a JavaScript variable
        wp_localize_script(WP_JOURNAL_TEXT_DOMAIN . '_js', WP_JOURNAL_TEXT_DOMAIN . '_data', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'auto_save_limit' => isset($options['auto_save_limit']) ? $options['auto_save_limit'] : 5,
            'auto_save_start' => 0
        ));
    }
}
