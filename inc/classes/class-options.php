<?php

/*
 * Class to create theme options page
 *
 * @package WP_JOURNAL
 */

namespace WP_JOURNAL\Inc;

use WP_JOURNAL\Inc\Traits\Singleton;

class Options {

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
        add_action('admin_menu', [$this, 'add_plugin_admin_menu']);
    }

    /**
     * Settings page output
     *
     * @since 1.0.0
     */
    public function settings_admin_page() {

        // WordPress media uploader scripts
        wp_enqueue_media();

        wp_enqueue_style(WP_JOURNAL_TEXT_DOMAIN . '_bootstrap');
        wp_enqueue_style(WP_JOURNAL_TEXT_DOMAIN . '_select2');

        wp_enqueue_script(WP_JOURNAL_TEXT_DOMAIN . '_bootstrap');
        wp_enqueue_script(WP_JOURNAL_TEXT_DOMAIN . '_select2');

        $views = Views::get_instance();

        $html = $views->load_view('admin/settings', ['post_types' => []]);

        echo $html;
    }

    /**
     * Returns all plugin options
     *
     * @since 1.0.0
     */
    public function get_plugin_options() {

        $options = get_option(WP_JOURNAL_SETTINGS_KEY);

        if (empty($options) || !is_array($options)) {
            $options = [];
        }

        return $options;
    }

    /**
     * Returns single plugin option
     *
     * @since 1.0.0
     */
    public function get_plugin_option($id) {

        $options = $this->get_plugin_options();

        if (isset($options[$id])) {

            return $options[$id];
        }
    }

    /*
     * Function to update all options values
     *
     * @param $options mixed value of the options
     */

    public function update_plugin_options($options) {

        update_option(WP_JOURNAL_SETTINGS_KEY, $options);
    }

    /*
     * Function to update any single option value
     *
     * @param $id string of the option that need to update
     * @param $val mixed value of the option
     */

    public function update_plugin_option($id, $val) {

        $options = $this->get_plugin_options();

        $options[$id] = $val;

        update_option(WP_JOURNAL_SETTINGS_KEY, $options);
    }

    /**
     * Add sub menu page
     *
     * @since 1.0.0
     */
    public function add_plugin_admin_menu() {

        add_menu_page(
                esc_html__(WP_JOURNAL_PLUGIN_NAME, WP_JOURNAL_TEXT_DOMAIN),
                esc_html__(WP_JOURNAL_PLUGIN_NAME, WP_JOURNAL_TEXT_DOMAIN),
                'manage_options',
                'wp_journal_settings',
                [$this, 'settings_admin_page']
        );
    }

    /*
     * Ajax function to save the settings
     */

    public function save_options() {

        $message = '';
        $error = false;

        $options = $this->get_plugin_options();

        $views = Views::get_instance();

        if (is_array($_POST)) {

            $exclude = array('btnsave');

            foreach ($_POST as $k => $v) {

                if (!in_array($k, $exclude)) {

                    if (!is_array($v)) {
                        $val = sanitize_text_field($v);
                    } else {
                        $val = $v;
                    }

                    $options[$k] = $val;
                }
            }

            $this->update_plugin_options($options);

            $message = $views->load_admin_alerts('message', 'Settings Saved Successfully!');
        } else {
            $error = true;
            $message = $views->load_admin_alerts('error', 'There is no data to save!');
        }

        $return = array('error' => $error, 'message' => $message);
        wp_send_json($return);
    }
}
