<?php
namespace WRDSB\Lamson\Controllers;

class Posts extends WP_REST_Controller
{
    /**
     * Register the routes for the objects of the controller.
     */
    public function registerRoutes()
    {
        $version = '1';
        $namespace = 'lamson/v' . $version;
        $base = 'posts';
        register_rest_route( $namespace, '/' . $base, array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array( $this, 'getItems' ),
                'permission_callback' => array( $this, 'getItemsPermissionsCheck' ),
                'args'                => array(
                ),
            ),
            array(
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => array( $this, 'createItem' ),
                'permission_callback' => array( $this, 'createItemPermissionsCheck' ),
                'args'                => $this->get_endpoint_args_for_item_schema(true),
            ),
        ) );
        register_rest_route( $namespace, '/' . $base . '/(?P<id>[\d]+)', array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array( $this, 'getItem' ),
                'permission_callback' => array( $this, 'getItemPermissionsCheck' ),
                'args'                => array(
                    'context'          => array(
                        'default'      => 'view',
                    ),
                ),
            ),
            array(
                'methods'         => WP_REST_Server::EDITABLE,
                'callback'        => array( $this, 'updateItem' ),
                'permission_callback' => array( $this, 'updateItemPermissionsCheck' ),
                'args'            => $this->get_endpoint_args_for_item_schema(false),
            ),
            array(
                'methods'  => WP_REST_Server::DELETABLE,
                'callback' => array( $this, 'deleteItem' ),
                'permission_callback' => array( $this, 'deleteItemPermissionsCheck' ),
                'args'     => array(
                    'force'    => array(
                        'default'      => false,
                    ),
                ),
            ),
        ) );
        register_rest_route( $namespace, '/' . $base . '/schema', array(
            'methods'         => WP_REST_Server::READABLE,
            'callback'        => array( $this, 'getPublicItemSchema' ),
        ) );
    }

    /**
     * Get a collection of items
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_Error|WP_REST_Response
     */
    public function getItems($request)
    {
        $items = get_posts();
        $data = array();

        foreach ($items as $item) {
            //$itemdata = $this->prepareItemForResponse($item, $request);
            //$data[] = $this->prepareResponseForCollection($itemdata);
            $data[] = $item;
        }

        return new WP_REST_Response($data, 200);
    }

    /**
     * Get one item from the collection
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_Error|WP_REST_Response
     */
    public function getItem($request)
    {
        //get parameters from request
        $params = $request->get_params();
        $item = array();//do a query, call another class, etc
        $data = $this->prepareItemForResponse($item, $request);

        //return a response or error based on some conditional
        if (1 == 1) {
            return new WP_REST_Response($data, 200);
        } else {
            return new WP_Error('code', __('message', 'text-domain'));
        }
    }

    /**
     * Create one item from the collection
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_Error|WP_REST_Request
     */
    public function createItem($request)
    {
        $item = $this->prepareItemForDatabase($request);

        if (function_exists('slugSomeFunctionToCreateItem')) {
            $data = slugSomeFunctionToCreateItem($item);
            if (is_array($data)) {
                return new WP_REST_Response($data, 200);
            }
        }
        return new WP_Error('cant-create', __('message', 'text-domain'), array('status' => 500));
    }

    /**
     * Update one item from the collection
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_Error|WP_REST_Request
     */
    public function updateItem($request)
    {
        $item = $this->prepareItemForDatabase($request);

        if (function_exists('slugSomeFunctionToUpdateItem')) {
            $data = slugSomeFunctionToUpdateItem($item);
            if (is_array($data)) {
                return new WP_REST_Response($data, 200);
            }
        }
        return new WP_Error('cant-update', __('message', 'text-domain'), array('status' => 500));
    }

    /**
     * Delete one item from the collection
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_Error|WP_REST_Request
     */
    public function deleteItem($request)
    {
        $item = $this->prepareItemForDatabase($request);

        if (function_exists('slugSomeFunctionToDeleteItem')) {
            $deleted = slugSomeFunctionToDeleteItem($item);
            if ($deleted) {
                return new WP_REST_Response(true, 200);
            }
        }
        return new WP_Error('cant-delete', __('message', 'text-domain'), array('status' => 500));
    }

    /**
     * Check if a given request has access to get items
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_Error|bool
     */
    public function getItemsPermissionsCheck($request)
    {
        //return true; <--use to make readable by all
        return current_user_can('edit_posts');
    }

    /**
     * Check if a given request has access to get a specific item
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_Error|bool
     */
    public function getItemPermissionsCheck($request)
    {
        return $this->getItemsPermissionsCheck($request);
    }

    /**
     * Check if a given request has access to create items
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_Error|bool
     */
    public function createItemPermissionsCheck($request)
    {
        return current_user_can('edit_posts');
    }

    /**
     * Check if a given request has access to update a specific item
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_Error|bool
     */
    public function updateItemPermissionsCheck($request)
    {
        return $this->createItemPermissionsCheck($request);
    }

    /**
     * Check if a given request has access to delete a specific item
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_Error|bool
     */
    public function deleteItemPermissionsCheck($request)
    {
        return $this->createItemPermissionsCheck($request);
    }

    /**
     * Prepare the item for create or update operation
     *
     * @param WP_REST_Request $request Request object
     * @return WP_Error|object $prepared_item
     */
    protected function prepareItemForDatabase($request)
    {
        return array();
    }

    /**
     * Prepare the item for the REST response
     *
     * @param mixed $item WordPress representation of the item.
     * @param WP_REST_Request $request Request object.
     * @return mixed
     */
    public function prepareItemForResponse($item, $request)
    {
        return array();
    }

    /**
     * Get the query params for collections
     *
     * @return array
     */
    public function getCollectionParams()
    {
        return array(
            'page'                   => array(
                'description'        => 'Current page of the collection.',
                'type'               => 'integer',
                'default'            => 1,
                'sanitize_callback'  => 'absint',
            ),
            'per_page'               => array(
                'description'        => 'Maximum number of items to be returned in result set.',
                'type'               => 'integer',
                'default'            => 10,
                'sanitize_callback'  => 'absint',
            ),
            'search'                 => array(
                'description'        => 'Limit results to those matching a string.',
                'type'               => 'string',
                'sanitize_callback'  => 'sanitize_text_field',
            ),
        );
    }
}
