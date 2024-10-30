<?php
namespace CF7VB;

/**
 * Frontend Pages Handler
 */
class Frontend {

    public function __construct() {
        add_shortcode('vue-app', [$this, 'render_frontend']);
        add_action('wp_footer', [$this, 'enqueue_scripts']);

    }

    public function enqueue_scripts() {
        wp_enqueue_style('cf7vb-frontend', CF7VB_ASSETS.'/css/frontend.css');
        $forms = get_posts(
            array(
                'post_type' => 'wpcf7_contact_form',
                'posts_per_page' => -1,
                'post_status' => 'publish',
            )
        );

        $form_css = '';

        // Extract IDs and titles
        foreach($forms as $form) {
            $form_data[] = array(
                'id' => $form->ID,
                'title' => get_the_title($form->ID),
            );

            $all_styles = get_post_meta($form->ID, 'cf7vb_generated_css', true);


            if(isset($all_styles['font'])) {
                $form_css .= '@import url("'.$all_styles['font'].'");';
            }


        }
        foreach($forms as $form) {
            $form_data[] = array(
                'id' => $form->ID,
                'title' => get_the_title($form->ID),
            );

            $cf7vb_app_id = get_post_meta($form->ID, 'cf7vb_app_id', true);
            $all_styles = get_option($cf7vb_app_id.'_generated_css');




            if(isset($all_styles['body'])) {
                $form_css .= '
                #cf7vb-form-'.$form->ID.'.cf7vb-buildr-frontend{'.$all_styles['body'].'}
                ';
            }
            if(isset($all_styles['label'])) {
                $form_css .= '
                #cf7vb-form-'.$form->ID.'.cf7vb-buildr-frontend label{'.$all_styles['label'].'}
                ';
            }
            if(isset($all_styles['input'])) {
                $form_css .= '
                #cf7vb-form-'.$form->ID.'.cf7vb-buildr-frontend input,.cf7vb-buildr-frontend textarea, .cf7vb-buildr-frontend select {'.$all_styles['input'].'}
                ';
            }
            if(isset($all_styles['submit'])) {
                $form_css .= '
                #cf7vb-form-'.$form->ID.'.cf7vb-buildr-frontend  input[type=submit] {'.$all_styles['submit'].'}
                ';
            }


        }
        wp_add_inline_style('cf7vb-frontend', $form_css);



    }
    /**
     * Render frontend app
     *
     * @param  array $atts
     * @param  string $content
     *
     * @return string
     */
    public function render_frontend($atts, $content = '') {
        // wp_enqueue_style( 'cf7vb-frontend' );
        // wp_enqueue_script( 'cf7vb-frontend' );

        $content .= '<div id="vue-frontend-app"></div>';

        return $content;
    }
}

