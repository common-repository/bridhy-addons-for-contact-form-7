<?php

namespace CF7VB;

class Builder
{

    public function __construct()
    {

        $this->init_hooks();
    }



    /**
     * Initialize our hooks for the admin page
     *
     * @return void
     */
    public function init_hooks()
    {
        add_action('enqueue_scripts', [$this, 'enqueue_scripts_frontend']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_scripts']);

        add_filter('wpcf7_editor_panels', array($this, 'load_panel'), 10, 1);
    }

    /**
     * Load scripts and styles for the app
     *
     * @return void
     */

    public function enqueue_scripts($hook_suffix)
    {
        if (false === strpos($hook_suffix, 'wpcf7')) {
            return;
        }


        wp_enqueue_style('cf7vb-style');
        wp_enqueue_script('cf7vb-core-');
        wp_localize_script('cf7vb-core-', 'cf7vb', [
            'url' => CF7VB_URL,
            'home_url' => home_url()
        ]);
    }
    /**
     * Load scripts and styles for the app
     *
     * @return void
     */
    public function enqueue_scripts_frontend()
    {


        // wp_enqueue_style('cf7vb-frontend');

    }
    public function load_panel($panels)
    {
        // make filter magic happen here...
        $tab['cf7vb'] = array(
            'title' => 'Visual Builder',
            'callback' => array($this, 'plugin_page')
        );
        $panels = $tab + $panels;
        return $panels;
    }
    /**
     * Render our admin page
     *
     * @return void
     */
    public function plugin_page()
    {
        $unique_id = wp_unique_id('cf7vb');
        echo '<div class="wrap"><div id="cf7vb-builder-app" data-app-id="' . esc_attr($unique_id) . '"></div></div>';
    }
}
