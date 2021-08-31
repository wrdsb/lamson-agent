<?php

class LamsonWPPostsAPIController extends WP_REST_Controller
{
    /**
     * Register the routes for the objects of the controller.
     */
    public function register_routes()
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
        $posts = get_posts(array(
            'fields'          => 'ids', // Only get post IDs
            'posts_per_page'  => -1,
            'post_type'       => 'any'
        ));

        //$data = array();
        //foreach ($items as $item) {
            //$itemdata = $this->prepareItemForResponse($item, $request);
            //$data[] = $this->prepareResponseForCollection($itemdata);
            //$data[] = $item;
        //}

        return new WP_REST_Response($posts, 200);
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
        $id = $request->get_param('id');
        $item = get_post($id);//do a query, call another class, etc
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
    public function prepareItemForResponse($post, $request)
    {
        $site_details = get_blog_details(get_current_blog_id());
    
        $site_url = str_replace('http://', '', site_url());
        $site_url = str_replace('https://', '', $site_url);
    
        $site_domain = $site_details->domain;
        $site_slug = str_replace('/', '', $site_details->path);
        $site_link = get_bloginfo('url');
        $site_name = get_bloginfo('name');
        $site_privacy = get_option('blog_public');
    
        if ($site_slug == '') {
            $site_slug = 'top';
        }
    
        $author_details = get_userdata($post->post_author);
        $post_author_name = $author_details->first_name.' '.$author_details->last_name;
        $post_author_email = $author_details->user_email;
    
        $obj_to_post = [
            'id' => $site_domain.'_'.$site_slug.'_'.$post->ID,
    
            'post_id' => $post->ID,
            'post_slug' => $post->post_name,
            'post_guid' => $post->guid,
    
            'post_type' => $post->post_type,
    
            'post_status' => $post->post_status,
    
            // Probably not a good idea to include this field:
            //'post_password' => $post->post_password,
    
            'post_date' => $post->post_date,
            'post_date_gmt' => $post->post_date_gmt,
        
            'post_modified' => $post->post_modified,
            'post_modified_gmt' => $post->post_modified_gmt,
    
            'post_author_id' => $post->post_author,
            'post_author_name' => $post_author_name,
            'post_author_email' => $post_author_email,
        
            'post_title' => $post->post_title,
            'post_content' => $post->post_content,
            'post_excerpt' => $post->post_excerpt,
    
            'post_parent' => $post->post_parent,
            'post_menu_order' => $post->menu_order,
    
            'comment_status' => $post->comment_status,
            'comment_count' => $post->comment_count,
        
            'ping_status' => $post->ping_status,
            'pinged' => $post->pinged,
            'to_ping' => $post->to_ping,
        
            'post_filter' => $post->filter,
            'post_content_filtered' => $post->post_content_filtered,
            'post_mime_type' => $post->post_mime_type,
    
            'site_url' => $site_url,
            'site_domain' => $site_domain,
            'site_slug' => $site_slug,
            'site_name' => $site_name,
            'site_link' => $site_link,
            'site_privacy' => $site_privacy,
        ];
    
        $post_categories = [];
        $categories = get_the_terms($post->ID, 'category');
        if ($categories) {
            foreach ($categories as $category) {
                $post_categories[] = $category->name;
            }
        }
        $obj_to_post['post_categories'] = $post_categories;
    
        $post_tags = [];
        $tags = get_the_terms($post->ID, 'post_tag');
        if ($tags) {
            foreach ($tags as $tag) {
                $post_tags[] = $tag->name;
            }
        }
        $obj_to_post['post_tags'] = $post_tags;
    
        $visible_to = [];
        switch ($obj_to_post['site_privacy']) {
            case '-1':
                array_push($visible_to, "${site_domain}:members");
                array_push($visible_to, "${site_url}:members");
                array_push($visible_to, "${site_url}:admins");
                break;
            case '-2':
                array_push($visible_to, "${site_url}:members");
                array_push($visible_to, "${site_url}:admins");
                break;
            case '-3':
                array_push($visible_to, "${site_url}:admins");
                break;
            case '0':
                array_push($visible_to, "${site_domain}:members");
                array_push($visible_to, "${site_url}:members");
                array_push($visible_to, "${site_url}:admins");
                array_push($visible_to, "public");
                break;
            case '1':
                array_push($visible_to, "${site_domain}:members");
                array_push($visible_to, "${site_url}:members");
                array_push($visible_to, "${site_url}:admins");
                array_push($visible_to, "public");
                break;
            default:
                break;
        }
        $obj_to_post['visible_to'] = $visible_to;
    
        return $obj_to_post;
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
