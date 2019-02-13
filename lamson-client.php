<?php
/*
* Plugin Name: Lamson Client
* Plugin URI: https://github.com/wrdsb/wordpress-plugin-lamson-client
* Description: Provides a set of webhooks and API endpoints for interacting with the Lamson service.
* Author: WRDSB
* Author URI: https://github.com/wrdsb
* Version: 1.0.0
* License: GPLv3 or later
*/

define('LAMSON_CLIENT_PRIORITY',12838790321);

function Lamson_Client_init() {
	Lamson_Client_register_hooks();
}
add_action('init', 'Lamson_Client_init');

function Lamson_Client_register_hooks() {
	add_action('publish_post', 'Lamson_Client_publish_post_hook', LAMSON_CLIENT_PRIORITY, 2);
	add_action('publish_page', 'Lamson_Client_publish_post_hook', LAMSON_CLIENT_PRIORITY, 2);

	add_filter( 'rwmb_meta_boxes', 'wrdsb_lamson_post_edit_meta' );
}

function wrdsb_lamson_post_edit_meta( $meta_boxes ) {
	$prefix = 'lamson-';

	$meta_boxes[] = array(
		'id' => 'notificationsandsyndication',
		'title' => esc_html__( 'Notifications and Syndication', 'default' ),
		'post_types' => array('post', 'page' ),
		'context' => 'after_editor',
		'priority' => 'default',
		'autosave' => 'true',
		'fields' => array(
			array(
				'id' => $prefix . 'send-notification',
				'name' => esc_html__( 'Notify email subscribers?', 'default' ),
				'type' => 'radio',
				'desc' => esc_html__( 'When the "Publish" (or "Update") button is pressed, send a copy of this post to this site\'s email notification subscribers.', 'default' ),
				'placeholder' => '',
				'options' => array(
					'true' => 'Yes',
					'false' => 'No',
				),
				'inline' => 'true',
				'std' => 'true',
			),
		),
	);

	return $meta_boxes;
}

function Lamson_Client_publish_post_hook($ID, $post) {
	$site_details = get_blog_details(get_current_blog_id());

	$site_url = str_replace('http://', '', site_url());
	$site_url = str_replace('https://', '', $site_url);

	$site_domain = $site_details->domain;
	$site_slug = str_replace('/', '', $site_details->path);
	$site_link = get_bloginfo('url');
	$site_name = get_bloginfo('name');

	if ($site_slug == '') {
		$site_slug = 'top';
	}

	$author_details = get_userdata($post->post_author);
	$post_author_name = $author_details->first_name.' '.$author_details->last_name;

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

		'lamson_send_notification' => ($post->lamson_send_notification == 'true') ? true : false;
	];

	$post_categories = [];
	$categories = get_the_terms( $post->ID, 'category' );
	if ($categories) {
		foreach ($categories as $category) {
			$post_categories[] = $category->name;
		}
	}
	$obj_to_post['post_categories'] = $post_categories;

	$post_tags = [];
	$tags = get_the_terms( $post->ID, 'post_tag' );
	if ($tags) {
		foreach ($tags as $tag) {
			$post_tags[] = $tag->name;
		}
	}
	$obj_to_post['post_tags'] = $post_tags;

	$syndication_categories = [];
	if (taxonomy_exists('syndication_categories')) {
		$syndication_terms = get_the_terms($post->ID, 'syndication_categories');
		if ($syndication_terms) {
			foreach ($syndication_terms as $syndication_term) {
				$syndication_categories[] = $syndication_term->slug;
			}
		}
	}
	$obj_to_post['syndication_categories'] = $syndication_categories;

	$obj_to_post = json_encode($obj_to_post);

	$request = array(
		'headers' => array(
			'Content-Type' => 'application/json',
			'x-api-key' => LAMSON_API_KEY
		),
		'body' => $obj_to_post
	);

	switch ($post->post_type) {
		case 'post':
			return wp_remote_post(LAMSON_ENDPOINT_WP_POSTS_POST, $request);
			break;
		case 'page':
			return wp_remote_post(LAMSON_ENDPOINT_WP_PAGES_POST, $request);
			break;
	}
}
