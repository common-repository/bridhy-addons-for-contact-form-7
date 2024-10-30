<?php

/**
 * RedirectionSettings page 
 */

namespace CF7VB;

use WPCF7_Submission;
use WP_Query;

class RedirectionSettings
{
    public function __construct()
    {

        add_action('wp_enqueue_scripts', array($this, 'enqueue_redirect_script'));
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_redirect_script'));
        add_action('wpcf7_after_save', array($this, 'cf7vb_save_meta'));
        add_action('wpcf7_submit', array($this, 'cf7vb_non_ajax_redirection'));
    }

    /**
     * Undocumented function
     * enqueue_redirect_script
     * @return void
     */

    public function enqueue_redirect_script()
    {

        wp_enqueue_script('cf7vb-redirect-script', CF7VB_ASSETS . '/js/redirect.js', array('jquery'), null, true);
        wp_localize_script('cf7vb-redirect-script', 'cf7vb_redirect_object', $this->get_forms());
        wp_localize_script('cf7vb-redirect-script', 'cf7vb_redirect_enable', $this->cf7vb_redirect_enable());

        if (isset($this->enqueue_new_tab_script) && $this->enqueue_new_tab_script) {
            wp_add_inline_script('wpcf7-redirect-script', 'window.open("' . $this->redirect_url . '");');
        }
    }

    /**
     * Undocumented function
     * admin_enqueue_redirect_script
     * @return void
     */
    public function admin_enqueue_redirect_script()
    {
        wp_enqueue_style('cf7vb-nice-select-syle');
        wp_enqueue_style('cf7vb-redirect-syle');
        wp_enqueue_script('cf7vb-admin-redirect-script');
        wp_enqueue_script('cf7vb-nice-select');
    }

    /**
     * Undocumented function
     *  get_forms 
     *  form all value get
     * @return void
     */
    public function get_forms()
    {
        $args  = array(
            'post_type'        => 'wpcf7_contact_form',
            'posts_per_page'   => -1,
        );
        $query = new WP_Query($args);

        $forms = array();

        if ($query->have_posts()) :

            $fields = $this->fields();

            while ($query->have_posts()) :
                $query->the_post();

                $post_id = get_the_ID();

                foreach ($fields as $field) {
                    $forms[$post_id][$field['name']] = get_post_meta($post_id, 'cf7vb_redirect_' . $field['name'], true);
                }

                $forms[$post_id]['thankyou_page_url'] = $forms[$post_id]['page_id'] ? get_permalink($forms[$post_id]['page_id']) : '';
            endwhile;
            wp_reset_postdata();
        endif;

        return $forms;
    }

    public function cf7vb_get_options($post_id)
    {
        $fields = $this->fields();
        foreach ($fields as $field) {
            $values[$field['name']] = get_post_meta($post_id, 'cf7vb_redirect_' . $field['name'], true);
        }
        return $values;
    }

    /***
     * cf7vb_non_ajax_redirection
     */

    public function cf7vb_non_ajax_redirection($contact_form)
    {
        $this->fields = $this->cf7vb_get_options($contact_form->id());

        if (isset($this->fields) && !WPCF7_Submission::is_restful()) {
            $submission = WPCF7_Submission::get_instance();

            if ($submission->get_status() === 'mail_sent') {

                if ('to_url' === $this->fields['cf7vb_redirect_to_type'] && $this->fields['external_url']) {
                    $this->redirect_url = $this->fields['external_url'];
                }
                if ('to_page' === $this->fields['cf7vb_redirect_to_type'] && $this->fields['page_id']) {
                    $this->redirect_url = get_permalink($this->fields['page_id']);
                }
                var_dump($this->redirect_url);

                // Open link in a new tab
                if (isset($this->redirect_url) && $this->redirect_url) {
                    if ('on' === $this->fields['open_in_new_tab']) {
                        $this->enqueue_new_tab_script = true;
                    } else {
                        wp_redirect($this->redirect_url);
                        exit;
                    }
                }
            }
        }
    }


    /**
     * inlude all redirectional settigns 
     * field
     * @return void
     */
    public function cf7vb_redirectional_template($post)
    { ?>
        <div class="redirect-wrap">

            <fieldset>
                <?php

                $options = $this->cf7vb_get_options($post->id());
                $cf7vb_redirect_to_type = !empty($options['cf7vb_redirect_to_type']) ? $options['cf7vb_redirect_to_type'] : 'to_page';
                $cf7vb_redirect_enable = get_post_meta($post->id(), 'cf7vb_redirect_enable', true);
                ?>

                <p class="cf7vb-redirect-enable ">
                    <label for="cf7vb_redirect_enable">
                        <input class="cf7vb_redirect_enable" id="cf7vb_redirect_enable" name="cf7vb_redirect_enable" type="checkbox" value="yes" <?php checked('yes', $cf7vb_redirect_enable, true); ?>> <?php echo esc_html__('Enable Redirection'); ?>
                    </label><br>
                </p>

                <div class="cf7vb_default_redirect_wraper" style="margin: 20px;">
                    <p>
                        <label for="cf7vb_redirect_to_page">
                            <input class="cf7vb_redirect_to_type" id="cf7vb_redirect_to_page" name="cf7vb_redirect[cf7vb_redirect_to_type]" type="radio" value="to_page" <?php checked('to_page', $cf7vb_redirect_to_type, true); ?>> <?php echo esc_html__('Redirect to page'); ?>
                        </label>
                    </p>
                    <p>
                        <label for="cf7vb_redirect_to_url">
                            <input class="cf7vb_redirect_to_type" id="cf7vb_redirect_to_url" name="cf7vb_redirect[cf7vb_redirect_to_type]" type="radio" value="to_url" <?php checked('to_url', $cf7vb_redirect_to_type, true); ?>> <?php echo esc_html__('Redirect to external URL'); ?>
                        </label>
                    </p>
                    <p class="cf7vb_redirect_to_page">
                        <label for="cf7vb-redirect-page">
                            <?php esc_html_e('Select a Page to Redirect', 'cf7vb'); ?>
                        </label>
                        <?php
                        $pages = get_posts(array(
                            'post_type'        => 'page',
                            'posts_per_page'   => -1,
                            'post_status'      => 'published',
                        ));
                        ?>
                        <select name="cf7vb_redirect[page_id]" id="cf7vb-redirect-page" class="cf7vb-page-list">
                            <option value="0" <?php selected(0, $options['page_id']); ?>>
                                <?php echo esc_html__('Choose Page', 'cf7vb'); ?>
                            </option>

                            <?php foreach ($pages as $page) : ?>

                                <option value="<?php echo esc_attr($page->ID); ?>" <?php selected($page->ID, $options['page_id']); ?>>
                                    <?php echo esc_html($page->post_title); ?>
                                </option>

                            <?php endforeach; ?>
                        </select>
                    </p>
                    <p class="cf7vb_redirect_to_url">
                        <label><?php esc_html_e('Add External Link', 'cf7vb'); ?></label>
                        <input type="url" id="cf7vb-external-url" name="cf7vb_redirect[external_url]" class="large-text" value="<?php echo esc_html($options['external_url']); ?>" placeholder="<?php echo esc_html__('https://', 'cf7vb'); ?>">
                    </p>
                    <p class="cf7vb-new-tab">
                        <input id="cf7vb_tab_target" type="checkbox" name="cf7vb_redirect[target]" <?php checked($options['target'], 'on', true); ?>>
                        <label for="cf7vb_tab_target"><?php echo esc_html__('Open page in a new tab', 'cf7vb'); ?></label>
                    </p>
                    <div class="cf7vb-submit">
                        <?php submit_button(__('Update', 'cf7vb' ), 'secondary'); ?>
                    </div>

                </div>



            </fieldset>
        </div>

<?php
        wp_nonce_field('cf7vb_redirection_nonce_action', 'cf7vb_redirect_nonce');
    }

    /*
    * Fields array
    */
    public function fields()
    {
        $fields = array(
            array(
                'name'  => 'cf7vb_redirect_to_type',
                'type'  => 'radio',
            ),
            array(
                'name'  => 'page_id',
                'type'  => 'number',
            ),
            array(
                'name'  => 'external_url',
                'type'  => 'url',
            ),
            array(
                'name'  => 'target',
                'type'  => 'checkbox',
            ),
        );
        return $fields;
    }

    /*
    * Save meta value
    */
    public function cf7vb_save_meta($post)
    {
        if (!isset($_POST) || empty($_POST)) {
            return;
        }
        if (!wp_verify_nonce($_POST['cf7vb_redirect_nonce'], 'cf7vb_redirection_nonce_action')) {
            return;
        }

        if (isset($_POST['cf7vb_redirect_enable'])) {
            update_post_meta($post->id(), 'cf7vb_redirect_enable', sanitize_text_field($_POST['cf7vb_redirect_enable']));
        } else {
            update_post_meta($post->id(), 'cf7vb_redirect_enable', 'off');
        }


        $fields = $this->fields();
        $data = $_POST['cf7vb_redirect'];

        foreach ($fields as $field) {
            $value = isset($data[$field['name']]) ? $data[$field['name']] : '';

            switch ($field['type']) {

                case 'radio':
                    $value = sanitize_text_field($value);
                    break;

                case 'number':
                    $value = intval($value);
                    break;

                case 'checkbox':
                    $value = sanitize_text_field($value);
                    break;

                case 'url':
                    $value = sanitize_text_field($value);
                    break;
            }

            update_post_meta($post->id(), 'cf7vb_redirect_' . $field['name'], $value);
        }
    }

    /*
    Enable conditional redirect
    */
    public function cf7vb_redirect_enable()
    {
        $args  = array(
            'post_type'        => 'wpcf7_contact_form',
            'posts_per_page'   => -1,
        );
        $query = new WP_Query($args);

        $forms = array();

        if ($query->have_posts()) :

            while ($query->have_posts()) :
                $query->the_post();

                $post_id = get_the_ID();

                $cf7vb_redirect = get_post_meta(get_the_ID(), 'cf7vb_redirect_enable', true);

                if (!empty($cf7vb_redirect) && $cf7vb_redirect == 'yes') {

                    $forms[$post_id] = $cf7vb_redirect;
                }

            endwhile;
            wp_reset_postdata();
        endif;

        return $forms;
    }
}
