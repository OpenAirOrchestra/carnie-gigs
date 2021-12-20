<?php

class carnieGigsUsersRestController extends WP_REST_Controller
{

    /**
     * Register the routes for the objects of the controller.
     */
    public function register_routes()
    {
        $version = '1';
        $namespace = 'carnie-gigs/v' . $version;
        $base = 'users';
        register_rest_route($namespace, '/' . $base, array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array($this, 'get_items'),
                'permission_callback' => array($this, 'get_items_permissions_check'),
                'args'                => array(),
            ),
            array(
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => array($this, 'create_item'),
                'permission_callback' => array($this, 'create_item_permissions_check'),
                'args'                => $this->get_endpoint_args_for_item_schema(true),
            ),
        ));
        register_rest_route($namespace, '/' . $base . '/(?P<id>[\d]+)', array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array($this, 'get_item'),
                'permission_callback' => array($this, 'get_item_permissions_check'),
                'args'                => array(
                    'context' => array(
                        'default' => 'view',
                    ),
                ),
            ),
            array(
                'methods'             => WP_REST_Server::EDITABLE,
                'callback'            => array($this, 'update_item'),
                'permission_callback' => array($this, 'update_item_permissions_check'),
                'args'                => $this->get_endpoint_args_for_item_schema(false),
            ),
            array(
                'methods'             => WP_REST_Server::DELETABLE,
                'callback'            => array($this, 'delete_item'),
                'permission_callback' => array($this, 'delete_item_permissions_check'),
                'args'                => array(
                    'force' => array(
                        'default' => false,
                    ),
                ),
            ),
        ));
        register_rest_route($namespace, '/' . $base . '/schema', array(
            'methods'  => WP_REST_Server::READABLE,
            'callback' => array($this, 'get_public_item_schema'),
        ));
    }

    /**
     * Get a collection of items
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_Error|WP_REST_Response
     */
    public function get_items($request)
    {
        //get parameters from request
        $params = $request->get_params();
        $page = $params['page'];
        $per_page = $params['per_page'];

        if ($page == 0) {
            $page = 1;
        }

        if ($per_page == 0) {
            $per_page = 100;
        }

        $offset = ($page - 1) * $per_page;

        global $wpdb;

        $users_name = $wpdb->prefix . "users";
        $usermeta_name = $wpdb->prefix . "usermeta";

        // Unfiltered users sql query.
        $sql = $wpdb->prepare("SELECT display_name, u.ID, user_email, m.meta_value as first_name, m3.meta_value as last_name, m4.meta_value as 'description'
                            FROM  `$users_name` u
                            JOIN  `$usermeta_name` m ON u.id = m.user_id AND m.meta_key = 'first_name'
                            JOIN  `$usermeta_name` m2 ON u.id = m2.user_id AND m2.meta_key = 'wp_capabilities'
                            JOIN  `$usermeta_name` m3 ON u.id = m3.user_id AND m3.meta_key = 'last_name'
                            JOIN  `$usermeta_name` m4 ON u.id = m4.user_id AND m4.meta_key = 'description'
                            WHERE u.id <> 1 
                            AND 
                                (
                                    m2.meta_value LIKE '%%author%%'
                                    OR m2.meta_value LIKE '%%editor%%'
                                    OR m2.meta_value LIKE '%%administrator%%'
                                )
                            LIMIT %d OFFSET %d", $per_page, $offset);;

        $items = $wpdb->get_results($sql, ARRAY_A);

        if (!$wpdb->last_error) {
            $data = array();
            foreach ($items as $item) {
                $itemdata = $this->prepare_item_for_response($item, $request);
                $data[] = $this->prepare_response_for_collection($itemdata);
            }

            return new WP_REST_Response($data, 200);
        }
        return new WP_Error('cant-retrieve', __($wpdb->last_error, 'text-domain'), array('status' => 500));
    }

    /**
     * Get one item from the collection
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_Error|WP_REST_Response
     */
    public function get_item($request)
    {
        return new WP_Error('cant-get', __('not implemented', 'text-domain'), array('status' => 500));
    }

    /**
     * Create one item from the collection
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_Error|WP_REST_Response
     */
    public function create_item($request)
    {
        return new WP_Error('cant-create', __('not implemented', 'text-domain'), array('status' => 500));
    }

    /**
     * Update one item from the collection
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_Error|WP_REST_Response
     */
    public function update_item($request)
    {
        return new WP_Error('cant-update', __('not implemented', 'text-domain'), array('status' => 500));
    }

    /**
     * Delete one item from the collection
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_Error|WP_REST_Response
     */
    public function delete_item($request)
    {
        return new WP_Error('cant-delete', __('not implemented', 'text-domain'), array('status' => 500));
    }

    /**
     * Check if a given request has access to get items
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_Error|bool
     */
    public function get_items_permissions_check($request)
    {
        return current_user_can('read_private_posts');
    }

    /**
     * Check if a given request has access to get a specific item
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_Error|bool
     */
    public function get_item_permissions_check($request)
    {
        return $this->get_items_permissions_check($request);
    }

    /**
     * Check if a given request has access to create items
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_Error|bool
     */
    public function create_item_permissions_check($request)
    {
        return current_user_can('edit_others_posts');
    }

    /**
     * Check if a given request has access to update a specific item
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_Error|bool
     */
    public function update_item_permissions_check($request)
    {
        return $this->create_item_permissions_check($request);
    }

    /**
     * Check if a given request has access to delete a specific item
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_Error|bool
     */
    public function delete_item_permissions_check($request)
    {
        return $this->create_item_permissions_check($request);
    }

    /**
     * Prepare the item for create or update operation
     *
     * @param WP_REST_Request $request Request object
     * @return WP_Error|object $prepared_item
     */
    protected function prepare_item_for_database($request)
    {
        $item = $request;
        $item['ID'] = $request['id'];
        unset($item['id']);

        $item['email'] = $request['user_email'];
        unset($item['user_email']);

        return $item;
    }

    /**
     * Prepare the item for the REST response
     *
     * @param mixed $item WordPress representation of the item.
     * @param WP_REST_Request $request Request object.
     * @return mixed
     */
    public function prepare_item_for_response($item, $request)
    {
        $newItem = $item;
        $newItem['id'] = $item['ID'];
        unset($newItem['ID']);

        $newItem['email'] = $item['user_email'];
        unset($newItem['user_email']);

        return $newItem;
    }

    /**
     * Prepares a response for insertion into a collection.
     *
     * @param mixed $item WordPress representation of the item.
     * @param WP_REST_Response $response response object.
     * @return Array|Mixed. Response data, ready for insertion into collection data.
     */
    public function prepare_response_for_collection($response)
    {
        return $response;
    }

    /**
     * Get the query params for collections
     *
     * @return array
     */
    public function get_collection_params()
    {
        return array(
            'page'     => array(
                'description'       => 'Current page of the collection.',
                'type'              => 'integer',
                'default'           => 1,
                'sanitize_callback' => 'absint',
            ),
            'per_page' => array(
                'description'       => 'Maximum number of items to be returned in result set.',
                'type'              => 'integer',
                'default'           => 10,
                'sanitize_callback' => 'absint',
            ),
            'search'   => array(
                'description'       => 'Limit results to those matching a string.',
                'type'              => 'string',
                'sanitize_callback' => 'sanitize_text_field',
            ),
        );
    }
}
