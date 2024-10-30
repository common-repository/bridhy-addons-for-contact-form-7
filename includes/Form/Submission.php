<?php

/**
 * form submission
 * @package cf7vb
 */

namespace CF7VB\Form;

use WPCF7_Submission;

class Submission
{
    public function __construct()
    {
        add_action('wpcf7_before_send_mail', [$this, 'cf7vb_before_send_mail']);
    }

    /**
     * cf7vb_before_send_mail
     */
    public function cf7vb_before_send_mail($form_tag)
    {
        global $wpdb;
        $table_name    = $wpdb->prefix . 'cf7vb_forms';
        $upload_dir    = wp_upload_dir();
        $cf7vb_dirname = $upload_dir['basedir'] . '/cf7vb_uploads';
        $time_now      = time();
        $submission    = WPCF7_Submission::get_instance();
        $tags_names    = array();
        $strict_keys   = apply_filters('cf7vb_strict_keys', false);

        if ($submission) {

            $contact_form = $submission->get_contact_form();
            $allowed_tags = array();
            $bl   = array('\"', "\'", '/', '\\', '"', "'");
            $wl   = array('&quot;', '&#039;', '&#047;', '&#092;', '&quot;', '&#039;');
            if ($strict_keys) {
                $tags  = $contact_form->scan_form_tags();
                foreach ($tags as $tag) {
                    if (!empty($tag->name)) {
                        $tags_names[] = $tag->name;
                    }
                }
                $allowed_tags = $tags_names;
            }
            $not_allowed_tags = apply_filters('cf7vb_not_allowed_tags', array('g-recaptcha-response'));
            $allowed_tags     = apply_filters('cf7vb_allowed_tags', $allowed_tags);
            $data         = $submission->get_posted_data();
            $files            = $submission->uploaded_files();
            $uploaded_files   = array();
            foreach ($_FILES as $file_key => $file) {
                array_push($uploaded_files, $file_key);
            }

            foreach ($files as $file_key => $file) {
                $file = is_array($file) ? reset($file) : $file;
                if (empty($file)) continue;
                copy($file, $cf7vb_dirname . '/' . $time_now . '-' . $file_key . '-' . basename($file));
            }

            $form_data   = array();
            foreach ($data as $key => $value) {
                if ($strict_keys && !in_array($key, $allowed_tags)) continue;

                if (!in_array($key, $not_allowed_tags) && !in_array($key, $uploaded_files)) { 

                    $tmpD = $value;

                    if (!is_array($value)) {
                        $tmpD = str_replace($bl, $wl, $tmpD);
                    } else {
                        $tmpD = array_map(function ($item) use ($bl, $wl) {
                            return str_replace($bl, $wl, $item);
                        }, $tmpD);
                    }
                    $key = sanitize_text_field($key);
                    $form_data[$key] = $tmpD;
                }
                if (in_array($key, $uploaded_files)) {
                    $file = is_array($files[$key]) ? reset($files[$key]) : $files[$key];
                    $file_name = empty($file) ? '' : $time_now . '-' . $key . '-' . basename($file);
                    $key = sanitize_text_field($key);
                    $form_data[$key . 'cf7vb_file'] = $file_name;
                }
            }

            /* cfdb7 before save data. */
            $form_data = apply_filters('cf7vb_before_save_data', $form_data);
            do_action('cf7vb_before_save', $form_data);

            $form_value = serialize($form_data);
            $form_post_id = $form_tag->id();
            $form_date    = current_time('Y-m-d H:i:s');

            $wpdb->insert($table_name, array(
                'form_post_id' => $form_post_id,
                'form_data'   =>  $form_value,
                'form_date'    => $form_date
            ));

            /* cfdb7 after save data */
            $insert_id = $wpdb->insert_id;
            do_action('cf7vb_after_save', $insert_id);
        }
    }
}
