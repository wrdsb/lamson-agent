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
define('LAMSON_CLIENT_PRIORITY', 12838790321);

function lamson_controllers_init()
{
    require_once dirname(__FILE__) . "/controllers/LamsonWPPostsAPIController.php";
    $plugins_api = new LamsonWPPostsAPIController;
    $plugins_api->register_routes();
}
add_action('rest_api_init', 'lamson_controllers_init');

function lamson_client_init()
{
    lamson_client_register_hooks();
}
add_action('init', 'lamson_client_init');

function lamson_client_register_hooks()
{
    add_action('transition_post_status', 'lamson_client_transition_post_status_hook', 10, 3);

    add_filter('rwmb_meta_boxes', 'lamson_client_post_edit_meta');
}

function lamson_client_transition_post_status_hook($new_status, $old_status, $post)
{
    $ID = $post->ID;

    $obj_to_post = buildLamsonWPPost($ID, $post);

    if (! empty($_POST['lamson_send_notification'])) {
        $lamson_send_notification = $_POST['lamson_send_notification'];
    } else {
        $lamson_send_notification = get_post_meta($post->ID, 'lamson_send_notification', true);
    }

    if ($lamson_send_notification !== 'yes' && $lamson_send_notification !== 'no') {
        $lamson_send_notification = 'no';
    }

    $obj_to_post['lamson_send_notification'] = $lamson_send_notification;

    if (! empty($_POST['lamson_do_syndication'])) {
        $lamson_do_syndication = $_POST['lamson_do_syndication'];
        $lamson_syndication_targets = $_POST['lamson_syndication_targets'];
    } else {
        $lamson_do_syndication = get_post_meta($post->ID, 'lamson_do_syndication', true);
        $lamson_syndication_targets = get_post_meta($post->ID, 'lamson_syndication_targets', false);
    }

    if ($lamson_do_syndication !== 'yes' && $lamson_do_syndication !== 'no') {
        $lamson_do_syndication = 'no';
    }

    $syndication_targets = [];
    foreach ($lamson_syndication_targets as $target) {
        $syndication_targets[] = $target;
    }

    $obj_to_post['lamson_do_syndication'] = $lamson_do_syndication;
    $obj_to_post['lamson_syndication_targets'] = $syndication_targets;

    $obj_to_post['lamson_old_status'] = $old_status;
    $obj_to_post['lamson_new_status'] = $new_status;

    $result = lamson_hook_transition_post_status_request($obj_to_post);
}

function lamson_client_post_edit_meta($meta_boxes)
{
    $prefix = 'lamson_';
    $lamson_features = (LAMSON_FEATURE_FLAGS) ? LAMSON_FEATURE_FLAGS : array();

    $fields = array();
    if ($lamson_features["email_notification_toggle"]) {
        $fields[] = sendNotificationToggle($prefix);
    }
    if ($lamson_features["post_syndication_options"] || $lamson_features["test_syndication_options"]) {
        $fields[] = syndicationToggle($prefix);
    }
    if ($lamson_features["post_syndication_options"]) {
        $fields[] = miscSyndicationOptions($prefix);
        $fields[] = secondarySchoolOptions($prefix);
        $fields[] = elementarySchoolOptions($prefix);
    }
    if ($lamson_features["test_syndication_options"]) {
        $fields[] = testSyndicationOptions($prefix);
    }

    $meta_boxes[] = array(
        'id' => 'notificationsandsyndication',
        'title' => esc_html__('Notifications and Syndication', 'default'),
        'post_types' => array('post'),
        'context' => 'after_editor',
        'priority' => 'default',
        'autosave' => 'true',
        'fields' => $fields,
    );

    return $meta_boxes;
}

function buildLamsonWPPost($ID, $post)
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
    switch ($obj_to_post->site_privacy) {
        case '-1':
            $visible_to.push("${site_domain}:members");
            $visible_to.push("${site_url}:members");
            $visible_to.push("${site_url}:admins");
            break;
        case '-2':
            $visible_to.push("${site_url}:members");
            $visible_to.push("${site_url}:admins");
            break;
        case '-3':
            $visible_to.push("${site_url}:admins");
            break;
        case '0':
            $visible_to.push("${site_domain}:members");
            $visible_to.push("${site_url}:members");
            $visible_to.push("${site_url}:admins");
            $visible_to.push("public");
            break;
        case '1':
            $visible_to.push("${site_domain}:members");
            $visible_to.push("${site_url}:members");
            $visible_to.push("${site_url}:admins");
            $visible_to.push("public");
            break;
        default:
            break;
    }
    $obj_to_post->visible_to = $visible_to;

    return $obj_to_post;
}

function lamson_hook_transition_post_status_request($obj_to_post)
{
    $encoded_obj = json_encode($obj_to_post);

    $request = array(
        'headers' => array(
            'Content-Type' => 'application/json',
            //'x-api-key' => LAMSON_API_KEY
        ),
        'body' => $encoded_obj
    );

    return wp_remote_post(LAMSON_HOOK_TRANSITION_POST_STATUS, $request);
}

function sendNotificationToggle($prefix)
{
    return array(
        'id' => $prefix . 'send_notification',
        'name' => esc_html__('Notify email subscribers?', 'default'),
        'type' => 'radio',
        'desc' => esc_html__('When the "Publish" (or "Update") button is pressed, send a copy of this post to this site\'s email notification subscribers.', 'default'),
        'placeholder' => '',
        'options' => array(
            'yes' => 'Yes',
            'no' => 'No',
        ),
        'inline' => 'true',
        'std' => 'yes',
    );
}

function syndicationToggle($prefix)
{
    return array(
        'id' => $prefix . 'do_syndication',
        'name' => esc_html__('Syndicate post?', 'default'),
        'type' => 'radio',
        'desc' => esc_html__('When the "Publish" (or "Update") button is pressed, syndicate a copy of this post to the sites below.', 'default'),
        'placeholder' => '',
        'options' => array(
            'yes' => 'Yes',
            'no' => 'No',
        ),
        'inline' => 'true',
        'std' => 'yes',
    );
}

function miscSyndicationOptions($prefix)
{
    return array(
        'id'      => $prefix . 'syndication_targets',
        'name'    => 'Schools',
        'type'    => 'checkbox_list',
        // Options of checkboxes, in format 'value' => 'Label'
        'options' => array(
            'www-labour'                    => 'Labour',
            'www-pic'                       => 'PIC',
            'schools-athome'                => 'WRDSB@Home',
            'schools-alternative-education' => 'Alt. Ed.',
            'schools-continuing-education'  => 'Con. Ed.',
            'schools-all'        => 'All Schools',
            'schools-elementary' => 'Elementary Schools',
            'schools-secondary'  => 'Secondary Schools',
        ),
    );
}

function testSyndicationOptions($prefix)
{
    return array(
        'id'      => $prefix . 'syndication_targets',
        'name'    => 'Testing',
        'type'    => 'checkbox_list',
        // Options of checkboxes, in format 'value' => 'Label'
        'options' => array(
            'wplabs-didi'    => "Diana's lab",
            'wplabs-becks'   => "Becky's lab",
            'wplabs-cubicle' => "Jane's lab",
        ),
    );
}

function secondarySchoolOptions($prefix)
{
    return array(
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
    );
}

function elementarySchoolOptions($prefix)
{
    return array(
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
            'schools-erl' => 'Elementary Remote Learning',
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
    );
}
