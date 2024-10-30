<?php

/**
 * cf7vb form settings page
 */

namespace CF7VB;


class Settings
{

    public $redirect;
    public function __construct()
    {

        $this->redirect = new \CF7VB\RedirectionSettings();
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_formsettings_script'));
        add_filter('wpcf7_editor_panels', array($this, 'load_panel'), 10, 1);
    }

    /**
     * Undocumented function
     * admin_enqueue_redirect_script
     * @return void
     */
    public function admin_enqueue_formsettings_script()
    {

        wp_enqueue_style('cf7vb-form-settings');
        wp_enqueue_script('cf7vb-form-settings');
    }


    /*
    * Function create tab panel
    */

    public function load_panel($panels)
    {
        // make filter magic happen here...
        $tab['cf7vb-redirect-panel'] = array(
            'title' => 'Form Settings',
            'callback' => array($this, 'cf7vb_create_redirect_panel_fields')
        );
        $panels = $tab + $panels;
        return $panels;
    }


    /*
    * Function redirect fields
    */
    public function cf7vb_create_redirect_panel_fields($post)
    {
?>
        <div class="cf7vb-tab-wrap">
            <div class="tab-header">
                <button class="tab-button active" data-tab="tab1"><?php esc_html_e('General', 'cf7vb') ?></button>
                <button class="tab-button" data-tab="tab2"><?php esc_html_e('MailChimp Settings', 'cf7vb') ?></button>
                <button class="tab-button" data-tab="tab3"><?php esc_html_e('Conditional Field', 'cf7vb') ?></button>


            </div>

            <div id="tab1" class="tab-content">
                <?php

                $this->redirect->cf7vb_redirectional_template($post);

                ?>
            </div>

            <div id="tab2" class="tab-content">

                <h3><?php esc_html_e('Coming Soon...', 'cf7vb'); ?></h3>

            </div>

            <div id="tab3" class="tab-content">
                <!-- Content for Tab 3 -->
                <h3><?php esc_html_e('Coming Soon...', 'cf7vb'); ?></h3>
            </div>
        </div>


<?php

    }
}
?>