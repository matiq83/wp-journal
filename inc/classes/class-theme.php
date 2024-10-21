<?php

/*
 * This class will load all functions for themes
 *
 * @package WP_JOURNAL
 */

namespace WP_JOURNAL\Inc;

use WP_JOURNAL\Inc\Traits\Singleton;

class Theme {

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
        add_action('wp_footer', [$this, 'website_footer']);
        add_action('template_redirect', [$this, 'template_redirect'], 5);
        add_filter('query_vars', [$this, 'add_query_vars_filter']);
    }

    public function add_query_vars_filter($vars) {

        $vars[] = "irpdf";

        return $vars;
    }

    public function template_redirect() {

        if (get_query_var('irpdf') == 'download' && is_user_logged_in()) {
            global $current_user;

            $parmas = [
                'post_type' => WP_JOURNAL_POST_TYPE,
                'post_status' => 'publish',
                'numberposts' => -1
            ];

            if (!current_user_can('manage_options')) {
                $parmas['author'] = $current_user->ID;
            }

            $journals = get_posts($parmas);

            $views = Views::get_instance();

            $html = $views->load_view('front/insight_report', ['journals' => $journals]);

            require_once WP_JOURNAL_INC_DIR . 'mpdf/vendor/autoload.php';
            $mpdf = new \Mpdf\Mpdf();
            $mpdf->WriteHTML($html);
            $mpdf->OutputHttpDownload('insight-report.pdf');
        }
    }

    public function website_footer() {

        $views = Views::get_instance();

        $html = $views->load_view('front/website_footer');

        echo $html;
    }
}
