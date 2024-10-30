<?php
/*
Plugin Name: Bridhy CF7 - Ultimate Contact Form 7 Addons
Plugin URI: https://wpgrids.com
Description:  The Bridhy plugin is a powerful tool designed to enhance your Contact Form 7 experience with the help of Elementor. With Bridhy, you can effortlessly create stunning forms by utilizing our advanced features and functionalities.
Version: 1.0.1
Author: wpgrids
Author URI: https://profiles.wordpress.org/wpgrids/
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: cf7vb
Domain Path: /languages
*/


// don't call the file directly
if (!defined('ABSPATH'))
    exit;

require_once(__DIR__ . '/vendor/autoload.php');

use CF7VB\Admin;
use CF7VB\Api;
use CF7VB\Frontend;
use CF7VB\Form\Submission;
use CF7VB\Fields\CountryTag;
use CF7VB\Fields\TimeZoneTag;
use CF7VB\Settings;
use CF7VB\Installer;

/**
 * CF7VB class
 *
 * @class CF7VB The class that holds the entire CF7VB plugin
 */
final class CF7VB
{

    /**
     * Plugin version
     *
     * @var string
     */
    public $version = '0.1.0';

    /**
     * Holds various class instances
     *
     * @var array
     */
    private $container = array();

    /**
     * create a instance for installer calss
     */
    public $table;

    /**
     * Constructor for the CF7VB class
     *
     * Sets up all the appropriate hooks and actions
     * within our plugin.
     */
    public function __construct()
    {
        $this->table = new Installer();

        $this->define_constants();

        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));

        add_action('plugins_loaded', array($this, 'init_plugin'));
    }

    /**
     * Initializes the CF7VB() class
     *
     * Checks for an existing CF7VB() instance
     * and if it doesn't find one, creates it.
     */
    public static function init()
    {
        static $instance = false;

        if (!$instance) {
            $instance = new CF7VB();
        }

        return $instance;
    }

    /**
     * Magic getter to bypass referencing plugin.
     *
     * @param $prop
     *
     * @return mixed
     */
    public function __get($prop)
    {
        if (array_key_exists($prop, $this->container)) {
            return $this->container[$prop];
        }

        return $this->{$prop};
    }

    /**
     * Magic isset to bypass referencing plugin.
     *
     * @param $prop
     *
     * @return mixed
     */
    public function __isset($prop)
    {
        return isset($this->{$prop}) || isset($this->container[$prop]);
    }

    /**
     * Define the constants
     *
     * @return void
     */
    public function define_constants()
    {
        define('CF7VB_VERSION', $this->version);
        define('CF7VB_FILE', __FILE__);
        define('CF7VB_PATH', dirname(CF7VB_FILE));
        define('CF7VB_INCLUDES', CF7VB_PATH . '/includes');
        define('CF7VB_URL', plugins_url('', CF7VB_FILE));
        define('CF7VB_ASSETS', CF7VB_URL . '/assets');
    }

    /**
     * Load the plugin after all plugis are loaded
     *
     * @return void
     */
    public function init_plugin()
    {
        $this->table->cf7vb_contact_form_activate();
        $this->includes();
        $this->init_hooks();
    }

    /**
     * Placeholder for activation function
     *
     * Nothing being called here yet.
     */
    public function activate()
    {

        $installed = get_option('cf7vb_installed');

        if (!$installed) {
            update_option('cf7vb_installed', time());
        }
        update_option('cf7vb_version', CF7VB_VERSION);

        // create instance for installer class
        $this->table->cf7vb_create_Table();
    }

    /**
     * Placeholder for deactivation function
     *
     * Nothing being called here yet.
     */
    public function deactivate()
    {
        $this->table->delete_cf7vb_forms_Table();
    }


    /**
     * Include the required files
     *
     * @return void
     */
    public function includes()
    {

        require_once CF7VB_INCLUDES . '/Assets.php';

        // if ($this->is_request('admin')) {
        //     require_once CF7VB_INCLUDES . '/Admin.php';
        // }
        // Check if Contact Form 7 is installed and active
        if (!function_exists('is_plugin_active')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        if (!is_plugin_active('contact-form-7/wp-contact-form-7.php')) {
            return;
        } else {
            $admin = new Admin();
        }

        $formsubmission = new Submission();
        $countrytag = new CountryTag();
        $timezone = new TimeZoneTag();
        $API = new Api();
        $settings = new Settings();

        if ($this->is_request('frontend')) {
            $frontend = new Frontend();
        }

        // if ($this->is_request('ajax')) {
        //     // require_once CF7VB_INCLUDES . '/class-ajax.php';
        // }

        // require_once CF7VB_INCLUDES . '/Api.php';
    }

    /**
     * Initialize the hooks
     *
     * @return void
     */
    public function init_hooks()
    {

        add_action('init', array($this, 'init_classes'));

        // Localize our plugin
        add_action('init', array($this, 'localization_setup'));
    }

    /**
     * Instantiate the required classes
     *
     * @return void
     */
    public function init_classes()
    {

        if ($this->is_request('admin')) {
            // $this->container['admin'] = new CF7VB\Admin();
            $this->container['builder'] = new CF7VB\Builder();
        }

        if ($this->is_request('frontend')) {
            $this->container['frontend'] = new CF7VB\Frontend();
        }

        if ($this->is_request('ajax')) {
            // $this->container['ajax'] =  new App\Ajax();
        }

        // $this->container['api'] = new App\Api();
        $this->container['assets'] = new CF7VB\Assets();
    }

    /**
     * Initialize plugin for localization
     *
     * @uses load_plugin_cf7vb()
     */
    public function localization_setup()
    {
        load_plugin_textdomain('cf7vb', false, dirname(plugin_basename(__FILE__)) . '/languages/');
    }

    /**
     * What type of request is this?
     *
     * @param  string $type admin, ajax, cron or frontend.
     *
     * @return bool
     */
    private function is_request($type)
    {
        switch ($type) {
            case 'admin':
                return is_admin();

            case 'ajax':
                return defined('DOING_AJAX');

            case 'rest':
                return defined('REST_REQUEST');

            case 'cron':
                return defined('DOING_CRON');

            case 'frontend':
                return (!is_admin() || defined('DOING_AJAX')) && !defined('DOING_CRON');
        }
    }
} // CF7VB

$cf7vb = CF7VB::init();
