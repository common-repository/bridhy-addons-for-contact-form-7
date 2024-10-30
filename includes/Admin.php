<?php

namespace CF7VB;

/**
 * Admin Pages Handler
 */

class Admin
{

    public function __construct()
    {
        add_action('admin_menu', [$this, 'admin_menu']);
    }

    /**
     * Register our menu page
     *
     * @return void
     */
    public function admin_menu()
    {
        global $submenu;
        // wp_enqueue_style('cf7vb-style');
        $capability = 'manage_options';
        $slug       = 'cf7vb';


        add_submenu_page('wpcf7', __('Visual Builder', 'cf7vb'), __('Visual Builder', 'cf7vb'), $capability, 'cf7vb_builder', array($this, 'cf7vb_builde_menu'));
        add_submenu_page('wpcf7', __('Bridhy DB', 'cf7vb'), __('Bridhy DB', 'cf7vb'), $capability, $slug, array($this, 'plugin_page'));


        $this->init_hooks();
    }

    /**
     * Undocumented function
     * add submenu template here 
     */
    public function cf7vb_builde_menu()
    {

        require_once CF7VB_INCLUDES . "/template/admin-builder-page.php";
    }

    /**
     * Initialize our hooks for the admin page
     *
     * @return void
     */
    public function init_hooks()
    {


        add_action('admin_enqueue_scripts', [$this, 'enqueue_scripts']);
    }

    /**
     * Load scripts and styles for the app
     *
     * @return void
     */
    public function enqueue_scripts($screen)
    {



        // wp_enqueue_style( $handle:string, $src:string, $deps:array, $ver:string|boolean|null, $media:string )
        // wp_enqueue_script('cf7vb-core');
        wp_localize_script('cf7vb-core', 'cf7vb', [
            'url' => CF7VB_URL,
        ]);
    }

    /**
     * Render our admin page
     *
     * @return void
     */
    public function plugin_page()
    {
        if (!is_plugin_active('contact-form-7/wp-contact-form-7.php')) {
            return;
        } else {
            if (!isset($_GET['fid'])) {
                require_once CF7VB_INCLUDES . "/template/admin-template.php";
                return;
            } else {
                require_once CF7VB_INCLUDES . "/template/admin-app-subpage.php";
            }
        }
    }
}
