<?php

/*
 * This class will load all shortcodes
 *
 * @package WP_JOURNAL
 */

namespace WP_JOURNAL\Inc;

use WP_JOURNAL\Inc\Traits\Singleton;

class Shortcodes {

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
        add_shortcode('wp-journals', [$this, 'load_journals'], 10, 1);
        add_shortcode('wp-add-journal', [$this, 'add_journal'], 10, 1);
    }

    public function add_journal() {
        $html = '';

        if (!is_user_logged_in()) {
            return $html;
        }

        $views = Views::get_instance();
        $btn = $views->load_view('front/btn_new_entry');
        $html = $views->load_view('front/shortcodes/add_journal', ['btn_new_entry' => $btn]);

        return $html;
    }

    public function load_journals($atts) {

        global $current_user;

        $html = '';

        if (!is_user_logged_in()) {
            return $html;
        }

        $parmas = [
            'post_type' => WP_JOURNAL_POST_TYPE,
            'post_status' => 'publish',
            'numberposts' => -1
        ];

        //if (!current_user_can('manage_options')) {
        $parmas['author'] = $current_user->ID;
        //}

        $journals = get_posts($parmas);

        $views = Views::get_instance();
        $btn = $views->load_view('front/btn_new_entry');

        $html = $views->load_view('front/shortcodes/journals', ['journals' => $journals, 'atts' => $atts, 'btn_new_entry' => $btn]);

        return $html;
    }
}
