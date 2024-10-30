<?php
namespace CF7VB\Api;

use WP_REST_Controller;

/**
 * REST_API Handler
 */
class Builder extends WP_REST_Controller {

    public $widdgets = [];
    public $styles = [];
    public $generated_css = [];
    /**
     * [__construct description]
     */
    public function __construct() {
        $this->namespace = 'cf7vb/v1';
        $this->rest_base = 'builder';
    }

    /**
     * Register the routes
     *
     * @return void
     */
    public function register_routes() {
        register_rest_route(
            $this->namespace,
            '/'.$this->rest_base,
            array(
                array(
                    'methods' => 'GET',
                    'callback' => array($this, 'get_items'),
                    'permission_callback' => array($this, 'get_items_permissions_check'),
                    'args' => $this->get_collection_params(),
                ),
            )
        );
        register_rest_route(
            $this->namespace,
            '/'.$this->rest_base.'/update',
            array(
                array(
                    'methods' => 'POST',
                    'callback' => array($this, 'update_items'),
                    'permission_callback' => array($this, 'update_items_permissions_check'),
                    'args' => $this->get_collection_params(),
                ),
            )
        );
    }

    /**
     * Retrieves a collection of items.
     *
     * @param WP_REST_Request $request Full details about the request.
     *
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function get_items($request) {


        $data = [
            'widgets' => get_option($request['id'].'_widgets'),
            'styles' => get_option($request['id'].'_styles')
        ];


        $response = rest_ensure_response($data);

        return $response;
    }

    /**
     * Retrieves a collection of items.
     *
     * @param WP_REST_Request $request Full details about the request.
     *
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function update_items($request) {


        if(isset($request['cf7ID'])) {
            update_post_meta((int)$request['cf7ID'], 'cf7vb_app_id', $request['id']);
        }
        $data = [
            'widgets' => update_option($request['id'].'_widgets', $request['widgets']),
            'styles' => update_option($request['id'].'_styles', $request['styles']),
            'css' => update_option($request['id'].'_generated_css', $request['css'])
        ];

        $response = rest_ensure_response($request['styles']);
        return $response;
    }

    /**
     * Checks if a given request has access to read the items.
     *
     * @param  WP_REST_Request $request Full details about the request.
     *
     * @return true|WP_Error True if the request has read access, WP_Error object otherwise.
     */
    public function update_items_permissions_check($request) {
        if(isset($request['widgets']) && isset($request['styles']) && isset($request['id'])) {

            return true;
        }
        return false;
    }

    /**
     * Checks if a given request has access to read the items.
     *
     * @param  WP_REST_Request $request Full details about the request.
     *
     * @return true|WP_Error True if the request has read access, WP_Error object otherwise.
     */
    public function get_items_permissions_check($request) {
        if(isset($request['id'])) {

            return true;
        }
        return false;
    }

    /**
     * Retrieves the query params for the items collection.
     *
     * @return array Collection parameters.
     */
    public function get_collection_params() {
        return [];
    }
}
