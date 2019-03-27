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
	$prefix = 'lamson_';

	$meta_boxes[] = array(
		'id' => 'notificationsandsyndication',
		'title' => esc_html__( 'Notifications and Syndication', 'default' ),
		'post_types' => array('post', 'page' ),
		'context' => 'after_editor',
		'priority' => 'default',
		'autosave' => 'true',
		'fields' => array(
			array(
				'id' => $prefix . 'send_notification',
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
			array(
				'id'      => $prefix . 'syndication_targets',
				'name'    => 'Schools',
				'type'    => 'checkbox_list',
				// Options of checkboxes, in format 'value' => 'Label'
				'options' => array(
					'schools-all'        => 'All Schools',
					'schools-elementary' => 'Elementary Schools',
					'schools-secondary'  => 'Secondary Schools',
				),
			),
			array(
				'id'      => $prefix . 'syndication_targets',
				'name'    => 'Secondary Schools',
				'type'    => 'checkbox_list',
				// Options of checkboxes, in format 'value' => 'Label'
				'options' => array(
					'schools-bci' => 'BCI',
					'schools-chc' => 'CHCI',
					'schools-eci' => 'ECI',
					'schools-eds' => 'EDSS',
					'schools-fhc' => 'FHCI',
					'schools-gci' => 'GCI',
					'schools-gps' => 'GPSS',
					'schools-grc' => 'GRCI',
					'schools-hrh' => 'HHSS',
					'schools-jhs' => 'JHSS',
					'schools-kci' => 'KCI',
					'schools-phs' => 'PHS',
					'schools-jam' => 'SJAM',
					'schools-sss' => 'SSS',
					'schools-wci' => 'WCI',
					'schools-wod' => 'WODSS',
				),
				// Display options in a single row?
				// 'inline' => true,
				// Display "Select All / None" button?
				'select_all_none' => true,
			),
			array(
				'id'      => $prefix . 'syndication_targets',
				'name'    => 'Elementary Schools',
				'type'    => 'checkbox_list',
				// Options of checkboxes, in format 'value' => 'Label'
				'options' => array(
					'schools-ark' => 'A R Kaufman',
					'schools-abe' => 'Abraham Erb',
					'schools-alp' => 'Alpine',
					'schools-ave' => 'Avenue Road',
					'schools-ayr' => 'Ayr',

					'schools-bdn' => 'Baden',
					'schools-blr' => 'Blair Road',
					'schools-bre' => 'Breslau',
					'schools-brp' => 'Bridgeport',
					'schools-bgd' => 'Brigadoon',

					'schools-cdc' => 'Cedar Creek',
					'schools-ced' => 'Cedarbrae',
					'schools-cnc' => 'Centennial (Cambridge)',
					'schools-cnw' => 'Centennial (Waterloo)',
					'schools-ctr' => 'Central',
					'schools-cha' => 'Chalmers Street',
					'schools-chi' => 'Chicopee Hills',
					'schools-cle' => 'Clemens Mill',
					'schools-con' => 'Conestogo',
					'schools-cor' => 'Coronation',
					'schools-coh' => 'Country Hills',
					'schools-crl' => 'Courtland',
					'schools-cre' => 'Crestview',

					'schools-doo' => 'Doon',
					'schools-dpk' => 'Driftwood Park',

					'schools-est' => 'Edna Staebler',
					'schools-elg' => 'Elgin Street',
					'schools-elz' => 'Elizabeth Ziegler',
					'schools-emp' => 'Empire',

					'schools-flo' => 'Floradale',
					'schools-fgl' => 'Forest Glen',
					'schools-fhl' => 'Forest Hill',
					'schools-fra' => 'Franklin',

					'schools-gcp' => 'Glencairn',
					'schools-gvc' => 'Grand View (Cambridge)',
					'schools-gvn' => 'Grandview (New Hamburg)',
					'schools-gro' => 'Groh',

					'schools-hes' => 'Hespeler',
					'schools-hig' => 'Highland',
					'schools-hil' => 'Hillcrest',
					'schools-how' => 'Howard Robertson',

					'schools-jfc' => 'J F Carmichael',
					'schools-jwg' => 'J W Gerth',
					'schools-jme' => 'Janet Metcalfe',
					'schools-jst' => 'Jean Steckle',
					'schools-jdp' => 'John Darling',
					'schools-jma' => 'John Mahood',

					'schools-kea' => 'Keatsway',
					'schools-ked' => 'King Edward',

					'schools-lkw' => 'Lackner Woods',
					'schools-lrw' => 'Laurelwood',
					'schools-lau' => 'Laurentian',
					'schools-lbp' => 'Lester B Pearson',
					'schools-lex' => 'Lexington',
					'schools-lnh' => 'Lincoln Heights',
					'schools-lin' => 'Linwood',

					'schools-mcg' => 'MacGregor',
					'schools-mck' => 'Mackenzie King',
					'schools-man' => 'Manchester',
					'schools-mrg' => 'Margaret Avenue',
					'schools-mjp' => 'Mary Johnston',
					'schools-mea' => 'Meadowlane',
					'schools-mil' => 'Millen Woods',
					'schools-mof' => 'Moffat Creek',

					'schools-nam' => 'N A MacEachern',
					'schools-ndd' => 'New Dundee',
					'schools-nlw' => 'Northlake Woods',

					'schools-pkm' => 'Park Manor',
					'schools-pkw' => 'Parkway',
					'schools-pio' => 'Pioneer Park',
					'schools-pre' => 'Preston',
					'schools-pru' => 'Prueter',

					'schools-qel' => 'Queen Elizabeth',
					'schools-qsm' => 'Queensmount',

					'schools-riv' => 'Riverside',
					'schools-roc' => 'Rockway',
					'schools-rmt' => 'Rosemount',
					'schools-rye' => 'Ryerson',

					'schools-sag' => 'Saginaw',
					'schools-shl' => 'Sandhills',
					'schools-snd' => 'Sandowne',
					'schools-she' => 'Sheppard',
					'schools-sil' => 'Silverheights',
					'schools-sab' => 'Sir Adam Beck',
					'schools-smi' => 'Smithson',
					'schools-srg' => 'Southridge',
					'schools-sta' => 'St Andrew\'s',
					'schools-stj' => 'St Jacobs',
					'schools-stn' => 'Stanley Park',
					'schools-stw' => 'Stewart Avenue',
					'schools-sud' => 'Suddaby',
					'schools-sun' => 'Sunnyside',

					'schools-tai' => 'Tait Street',
					'schools-tri' => 'Trillium',

					'schools-vis' => 'Vista Hills',

					'schools-wtt' => 'W T Townshend',
					'schools-wel' => 'Wellesley',
					'schools-wsh' => 'Westheights',
					'schools-wsm' => 'Westmount',
					'schools-wsv' => 'Westvale',
					'schools-wgd' => 'William G Davis',
					'schools-wlm' => 'Williamsburg',
					'schools-wls' => 'Wilson Avenue',
					'schools-wcp' => 'Winston Churchill',
					'schools-wpk' => 'Woodland Park',
				),
				// Display options in a single row?
				// 'inline' => true,
				// Display "Select All / None" button?
				'select_all_none' => true,
			),
			array(
				'id'      => $prefix . 'syndication_targets',
				'name'    => 'Testing',
				'type'    => 'checkbox_list',
				// Options of checkboxes, in format 'value' => 'Label'
				'options' => array(
					'wplabs-didi'    => "Diana's lab",
					'wplabs-becks'   => "Becky's lab",
					'wplabs-cubicle' => "Jane's lab",
				),
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

	$obj_to_post['lamson_send_notification'] = $_POST['lamson_send_notification'];

	$syndication_targets = [];
	$targets = $_POST['lamson_syndication_targets'];
	if ($targets) {
		foreach ($targets as $target) {
			$syndication_targets[] = $target;
		}
	}
	$obj_to_post['lamson_syndication_targets'] = $syndication_targets;

	$obj_to_post = json_encode($obj_to_post);

	$request = array(
		'headers' => array(
			'Content-Type' => 'application/json',
			//'x-api-key' => LAMSON_API_KEY
		),
		'body' => $obj_to_post
	);

	switch ($post->post_type) {
		case 'post':
			return wp_remote_post(LAMSON_CLIENT_WP_POSTS_POST, $request);
			break;
		case 'page':
			return wp_remote_post(LAMSON_CLIENT_WP_PAGES_POST, $request);
			break;
	}
}
