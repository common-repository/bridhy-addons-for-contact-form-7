<?php

namespace CF7VB;

/**
 * when plugin is activate
 * then installer plugin is 
 * called
 */
class Installer
{
    /**
     * create a table into database
     * for store information
     * @return void
     */
    public function cf7vb_create_Table()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'cf7vb_forms';
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
            $charset_collate = $wpdb->get_charset_collate();

            $sql = "CREATE TABLE $table_name (
                form_id bigint(20) NOT NULL AUTO_INCREMENT,
                form_post_id bigint(20) NOT NULL,
                form_data longtext NOT NULL,
                form_date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
                PRIMARY KEY  (form_id)
            ) $charset_collate;";
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);
        }


    
    }

    /**
     * Drop table when plugin is
     * deactivate.
     *
     * @return void
     */
    public function delete_cf7vb_forms_Table()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'cf7vb_forms';

        $wpdb->query($wpdb->prepare("DROP TABLE IF EXISTS %s",$table_name));
    }

    /**
     * Check if Contact Form 7 is installed and active
     *
     * @return void
     */
    public function cf7vb_contact_form_activate()
    {
        if (!function_exists('is_plugin_active')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        if (!is_plugin_active('contact-form-7/wp-contact-form-7.php')) {

            deactivate_plugins(plugin_basename(__FILE__));

            add_action('admin_notices', [$this, 'cf7vb_contact_form_dependency_notice']);
        }
    }


    /**
     * Display an admin notice about the dependency
     */
    public function cf7vb_contact_form_dependency_notice()
    {
?>
        <div class="notice notice-error is-dismissible">
            <p><?php printf('CF7-Visual Builder Plugin requires Contact Form 7 to be installed and activated. Please download and install it from <a href="%s" target="_blank">here</a>.', 'https://wordpress.org/plugins/contact-form-7/'); ?></p>
        </div>
<?php
    }
}
