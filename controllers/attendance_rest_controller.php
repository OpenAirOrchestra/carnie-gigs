<?php

class carnieGigsAttendanceRestController extends WP_REST_Controller
{

  /**
   * Register the routes for the objects of the controller.
   */
  public function register_routes()
  {
    $version = '1';
    $namespace = 'carnie-gigs/v' . $version;
    $base = 'attendees';
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

    $hasSearch = array_key_exists('search', $params);
    $search = $hasSearch ? $params['search'] : 0;  // Should be an event id (gig id).

    if ($page == 0) {
      $page = 1;
    }

    if ($per_page == 0) {
      $per_page = 100;
    }

    $offset = ($page - 1) * $per_page;

    // query the database
    global $wpdb;
    $table_name = $wpdb->prefix . "gig_attendance";

    $sql = "SELECT * FROM `$table_name`";
    if ($hasSearch && $search) {
      $sql = $wpdb->prepare("SELECT * FROM `$table_name` WHERE gigid = %s  ORDER BY id DESC LIMIT %d OFFSET %d", $search, $per_page, $offset);
    } else {
      $sql = $wpdb->prepare("SELECT * FROM `$table_name`  ORDER BY id DESC LIMIT %d OFFSET %d", $per_page, $offset);
    }

    $items = $wpdb->get_results($sql, ARRAY_A);

    if (!$wpdb->last_error) {
      $data = array();
      foreach ($items as $item) {
        $itemdata = $this->prepare_item_for_response($item, $request);
        $data[] = $this->prepare_response_for_collection($itemdata);
      }

      return new WP_REST_Response($data, 200);
    }
    return new WP_Error('cant-delete', __($wpdb->last_error, 'text-domain'), array('status' => 500));
  }

  /**
   * Get one item from the collection
   *
   * @param WP_REST_Request $request Full data about the request.
   * @return WP_Error|WP_REST_Response
   */
  public function get_item($request)
  {
    //get parameters from request
    $params = $request->get_params();
    $record_id = $params['id'];

    // query the database
    global $wpdb;
    $table_name = $wpdb->prefix . "gig_attendance";

    $sql = $wpdb->prepare("SELECT * FROM `$table_name` WHERE id = %d", $record_id);
    $gig = $wpdb->get_row($sql, ARRAY_A);

    $item = $gig;

    $data = $this->prepare_item_for_response($item, $request);

    //return a response or error
    if (!$wpdb->last_error) {
      return new WP_REST_Response($data, 200);
    }
    return new WP_Error('cant-get', __($wpdb->last_error, 'text-domain'), array('status' => 500));
  }

  /**
   * Create one item from the collection
   *
   * @param WP_REST_Request $request Full data about the request.
   * @return WP_Error|WP_REST_Response
   */
  public function create_item($request)
  {
    $item = $this->prepare_item_for_database($request);

    // query the database
    global $wpdb;
    $table_name = $wpdb->prefix . "gig_attendance";

    $gig_data = array();
    $gig_format = array();

    $gig_data['gigid'] = $item['gigid'];
    array_push($gig_format, "%d");

    $gig_data['user_id'] = $item['user_id'] ? $item['user_id'] : 0;
    array_push($gig_format, "%d");

    $gig_data['firstname'] = $item['firstname'];
    array_push($gig_format, "%s");

    if (isset($item['lastname'])) {
      $gig_data['lastname'] = $item['lastname'];
      array_push($gig_format, "%s");
    }

    if (isset($item['notes'])) {
      $gig_data['notes'] = $item['notes'];
      array_push($gig_format, "%s");
    }

    $rowCount = $wpdb->insert(
      $table_name,
      $gig_data,
      $gig_format
    );

    if (!$wpdb->last_error) {
      $gig_id = $wpdb->insert_id;

      $item['id'] = $gig_id;

      $data = $this->prepare_item_for_response($item, $request);
      return new WP_REST_Response($data, 200);
    } else {
      return new WP_Error('cant-create', __($wpdb->last_error, 'text-domain'), array('status' => 500));
    }
  }

  /**
   * Update one item from the collection
   *
   * @param WP_REST_Request $request Full data about the request.
   * @return WP_Error|WP_REST_Response
   */
  public function update_item($request)
  {
    $params = $request->get_params();
    $record_id = $params['id'];

    // Some configurations of apache don't support DELETE, so for those, we do an "update" request with a "method" param set to "DELETE".  It's a hack.
    $method = $params['method'];
    if ($method == 'DELETE') {
      return $this->delete_item($request);
    }

    // We haven't implemented regular udate, don't need it yet.
    
    // $item = $this->prepare_item_for_database($request);
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
    $item = $this->prepare_item_for_database($request);
    $record_id = $item['id'];

    // query the database
    global $wpdb;
    $table_name = $wpdb->prefix . "gig_attendance";

    $wpdb->delete($table_name, array('id' => $record_id), array('%d'));

    if (!$wpdb->last_error) {
      return new WP_REST_Response(true, 200);
    }
    return new WP_Error('cant-delete', __($wpdb->last_error, 'text-domain'), array('status' => 500));
  }

  /**
   * Check if a given request has access to get items
   *
   * @param WP_REST_Request $request Full data about the request.
   * @return WP_Error|bool
   */
  public function get_items_permissions_check($request)
  {
    // DFDF
    return true;
    // return current_user_can( 'read_private_posts' );
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
    // DFDF
    return true;
    // return current_user_can( 'edit_others_posts' );
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
    $item['gigid'] = $request['event_id'];
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
    $newItem['event_id'] = $item['gigid'];
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
