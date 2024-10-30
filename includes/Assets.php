<?php

namespace CF7VB;

/**
 * Scripts and Styles Class
 */
class Assets
{

    function __construct()
    {


        if (is_admin()) {
            add_action('admin_enqueue_scripts', [$this, 'admin_register'], 5);
            add_filter('script_loader_tag', [$this, 'load_script_Module'], 10, 3);
        }

        add_action('wp_enqueue_scripts', [$this, 'frontend_register']);
    }

    /**
     * Register our app scripts and styles
     *
     * @return void
     */
    public function admin_register()
    {
        $this->register_scripts($this->get_admin_scripts());
        $this->register_styles($this->get_admin_styles());
    }

    /**
     * Register our app scripts and styles
     *
     * @return void
     */
    public function frontend_register()
    {
        $this->register_styles($this->get_frontend_styles());
        // $this->register_scripts($this->get_frontend_scripts());
    }

    /**
     * Register scripts
     *
     * @param  array $scripts
     *
     * @return void
     */
    private function register_scripts($scripts)
    {
        foreach ($scripts as $handle => $script) {
            $deps = isset($script['deps']) ? $script['deps'] : false;
            $in_footer = isset($script['in_footer']) ? $script['in_footer'] : false;
            $version = isset($script['version']) ? $script['version'] : CF7VB_VERSION;

            wp_register_script($handle, $script['src'], $deps, $version, $in_footer);
        }
    }

    /**
     * Register styles
     *
     * @param  array $styles
     *
     * @return void
     */
    public function register_styles($styles)
    {

        foreach ($styles as $handle => $style) {
            $deps = isset($style['deps']) ? $style['deps'] : false;

            wp_register_style($handle, $style['src'], $deps, CF7VB_VERSION);
        }
    }

    /**
     * Get all registered scripts
     *
     * @return array
     */
    public function get_admin_scripts()
    {
        $prefix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '.min' : '';

        // wp_enqueue_script('cf7vb-core', '//localhost:5173/src/main.js', [], time(), true);

        $scripts = [
            'cf7vb-core-' => [
                'src' => CF7VB_URL . '/admin/builder.js',
                'version' => time(),
                'in_footer' => true,
                // 'type' => 'module'
            ],
            'cf7vb-core' => [
                'src' => '//localhost:5173/src/main.js',
                'version' => time(),
                'in_footer' => true,
                'type' => 'module'
            ],
            'cf7vb-admin-redirect-script' => [
                'src' => CF7VB_ASSETS . '/js/admin-script.js',
                'version' => null,
                'in_footer' => true,
                'type' => 'module'
            ],
            'cf-admin-srcript' => [
                'src' => CF7VB_ASSETS . '/js/cf-admin-script.js',
                'version' => '0.1.0',
                'in_footer' => true,
                'type' => 'module'
            ],
            'cf7vb-form-settings' => [
                'src' => CF7VB_ASSETS . '/js/form-setting.js',
                'version' => '0.1.0',
                'in_footer' => true,
                'type' => 'module'
            ],
            'cf7vb-nice-select' => [
                'src' => CF7VB_ASSETS . '/js/jquery.nice-select.min.js',
                'version' => '1.0',
                'in_footer' => true,
                'type' => 'module'
            ],


        ];

        return $scripts;
    }
    // public function get_frontend_scripts()
    // {
    //     $prefix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '.min' : '';

    //     // wp_enqueue_script('cf7vb-core', '//localhost:5173/src/main.js', [], time(), true);

    //     $scripts = [
    //         'cf7vb-redirect-script' => [
    //             'src' => CF7VB_ASSETS . '/js/redirect.js',
    //             'deps' => ['jquery'],
    //             'version' => null,
    //             'in_footer' => true,
    //             'type' => 'module'
    //         ],


    //     ];

    //     return $scripts;
    // }

    /**
     * Get registered styles
     *
     * @return array
     */
    public function get_admin_styles()
    {

        $styles = [
            'cf7vb-style' => [
                'src' => CF7VB_URL . '/admin/builder.css',
            ],
            'cf-admin-style' => [
                'src' => CF7VB_ASSETS . '/css/cf-admin-style.css',
            ],
            'cf7vb-redirect-syle' => [
                'src' => CF7VB_ASSETS . '/css/cf7vb-redirect-syle.css',
            ],
            'cf7vb-form-settings' => [
                'src' => CF7VB_ASSETS . '/css/form-settings.css',
            ],
            'cf7vb-nice-select-syle' => [
                'src' => CF7VB_ASSETS . '/css/nice-select.css',
            ],
            // 'cf7vb-frontend' => [
            //     'src' => CF7VB_ASSETS . '/css/frontend.css'
            // ],
            // 'cf7vb-admin' => [
            //     'src' =>  CF7VB_ASSETS . '/css/admin.css'
            // ],
        ];


        // return $styles;

        return $styles;
    }
    /**
     * Get registered styles
     *
     * @return array
     */
    public function get_frontend_styles()
    {

        $styles = [
            // 'cf7vb-style' => [
            //     'src' =>  CF7VB_ASSETS . '/css/style.css'
            // ],
            'cf7vb-frontend' => [
                'src' => CF7VB_ASSETS . '/css/frontend.css'
            ],
            // 'cf7vb-admin' => [
            //     'src' =>  CF7VB_ASSETS . '/css/admin.css'
            // ],
        ];

        // return $styles;
        return [];
    }

    public function load_script_Module($tag, $handle, $src)
    {
        if ('cf7vb-core' !== $handle) {
            return $tag;
        }

        foreach ($this->get_admin_scripts() as $handler => $script) {

            $type = isset($script['type']) ? $script['type'] : '';

            if ($type = 'module' && $handle == $handler) {
                $tag = '<script type="module" id="' . esc_attr($handle) . '-js" src="' . esc_url($src) . '"></script>';
            }
        }
        return $tag;
    }
}
