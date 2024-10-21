<?php

/*
 * This class will load all Ajax functions
 *
 * @package WP_JOURNAL
 */

namespace WP_JOURNAL\Inc;

use WP_JOURNAL\Inc\Traits\Singleton;

class Ajax {

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
        add_action('wp_ajax_save_journal', [$this, 'save_journal']);
        add_action('wp_ajax_nopriv_save_journal', [$this, 'save_journal']);
        add_action('wp_ajax_delete_journal', [$this, 'delete_journal']);
        add_action('wp_ajax_nopriv_delete_journal', [$this, 'delete_journal']);
        add_action('wp_ajax_get_transcribe', [$this, 'get_transcribe']);
        add_action('wp_ajax_nopriv_get_transcribe', [$this, 'get_transcribe']);

        add_action('wp_ajax_wp_journal_save_options', [$this, 'save_options']);
    }

    public function get_transcribe() {

        $api_key = Options::get_instance()->get_plugin_option('openai_api_key');
        $text = $message = '';
        $error = false;

        if (empty($api_key)) {
            $message = __('No API Key provided', WP_JOURNAL_TEXT_DOMAIN);
            $error = true;
        }

        if (!$error && isset($_FILES['audio'])) {
            $audio_file = $_FILES['audio']['tmp_name'];

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, WP_JOURNAL_OPENAI_TRANSCRIPTIONS_URL);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $api_key,
            ]);

            $postfields = [
                'file' => new \CURLFile($audio_file, 'audio/wav', 'audio.wav'),
                'model' => WP_JOURNAL_OPENAI_TRANSCRIPTIONS_MODEL,
            ];

            curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);

            $response = curl_exec($ch);

            if (curl_errno($ch)) {
                $error = true;
                $message = curl_error($ch);
            } else {
                $result = json_decode($response, true);
                if (isset($result['text'])) {
                    $message = $result['text'];
                } else {
                    $error = true;
                    $message = __('Error: ' . $result['error']['message'], WP_JOURNAL_TEXT_DOMAIN);
                }
            }

            curl_close($ch);
        } elseif (!$error) {
            $message = __('No Audio file provided', WP_JOURNAL_TEXT_DOMAIN);
            $error = true;
        }

        $return = array('error' => $error, 'message' => $message);
        wp_send_json($return);
    }

    public function delete_journal() {

        $error = false;
        $message = '';

        $post_id = filter_input(INPUT_POST, 'id');

        if (!wp_delete_post($post_id)) {
            $error = true;
            $message = __('Unable to dlete the journal', WP_JOURNAL_TEXT_DOMAIN);
        }

        $return = array('error' => $error, 'message' => $message, 'id' => $post_id);
        wp_send_json($return);
    }

    public function save_journal() {

        global $current_user;

        $error = false;
        $message = '';

        $content = filter_input(INPUT_POST, 'content');
        $title = filter_input(INPUT_POST, 'title');
        $id = filter_input(INPUT_POST, 'id');
        if (empty($title)) {
            $message = __('No title provided', WP_JOURNAL_TEXT_DOMAIN);
            $error = true;
        }

        if (!$error) {
            $journal = [
                'post_title' => wp_strip_all_tags($title),
                'post_content' => sanitize_textarea_field($content),
                'post_author' => $current_user->ID,
                'post_type' => WP_JOURNAL_POST_TYPE,
                'post_status' => 'publish'
            ];

            if (empty($id)) {
                $post_id = wp_insert_post($journal);
            } else {
                $journal['ID'] = $id;
                $post_id = wp_update_post($journal);
            }

            if (is_wp_error($post_id)) {
                $message = $post_id->get_error_message();
                $error = true;
                $post_id = 0;
            }
        }

        $return = array('error' => $error, 'message' => $message, 'id' => $post_id);
        wp_send_json($return);
    }

    public function save_options() {

        $options = Options::get_instance();
        $options->save_options();
        exit();
    }
}
