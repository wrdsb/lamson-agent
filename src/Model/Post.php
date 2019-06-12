<?php
namespace WRDSB\Lamson\Model;

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://github.com/wrdsb
 * @since      1.0.0
 *
 * @package    WRDSB_Lamson
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    WRDSB_Lamson
 * @author     WRDSB <website@wrdsb.ca>
 */
class Post
{
    /**
     * Someting
     *
     * Something else
     *
     * @since    1.0.0
     */
    public function __construct($data)
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
    
        $author_details = get_userdata($data->post_author);
        $post_author_name = $author_details->first_name.' '.$author_details->last_name;
        $post_author_email = $author_details->user_email;
    
        $this->id = $site_domain.'_'.$site_slug.'_'.$data->ID;

        $this->post_id   = $data->ID;
        $this->post_slug = $data->post_name;
        $this->post_guid = $data->guid;

        $this->post_type = $data->post_type;

        $this->post_status = $data->post_status;

        // Probably not a good idea to include this field:
        //'post_password' => $data->post_password,

        $this->post_date     = $data->post_date;
        $this->post_date_gmt = $data->post_date_gmt;
    
        $this->post_modified     = $data->post_modified;
        $this->post_modified_gmt = $data->post_modified_gmt;

        $this->post_author_id    = $data->post_author;
        $this->post_author_name  = $post_author_name;
        $this->post_author_email = $post_author_email;
    
        $this->post_title   = $data->post_title;
        $this->post_content = $data->post_content;
        $this->post_excerpt = $data->post_excerpt;

        $this->post_parent     = $data->post_parent;
        $this->post_menu_order = $data->menu_order;

        $this->comment_status = $data->comment_status;
        $this->comment_count  = $data->comment_count;
    
        $this->ping_status = $data->ping_status;
        $this->pinged      = $data->pinged;
        $this->to_ping     = $data->to_ping;
    
        $this->post_filter           = $data->filter;
        $this->post_content_filtered = $data->post_content_filtered;
        $this->post_mime_type        = $data->post_mime_type;

        $this->site_url     = $site_url;
        $this->site_domain  = $site_domain;
        $this->site_slug    = $site_slug;
        $this->site_name    = $site_name;
        $this->site_link    = $site_link;
        $this->site_privacy = $site_privacy;
    
        $post_categories = [];
        $categories = get_the_terms($data->ID, 'category');
        if ($categories) {
            foreach ($categories as $category) {
                $post_categories[] = $category->name;
            }
        }
        $this->post_categories = $post_categories;
    
        $post_tags = [];
        $tags = get_the_terms($data->ID, 'post_tag');
        if ($tags) {
            foreach ($tags as $tag) {
                $post_tags[] = $tag->name;
            }
        }
        $this->post_tags = $post_tags;
    
        $visible_to = [];
        switch ($this->site_privacy) {
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
        $this->visible_to = $visible_to;
    }

    public function sendToService($ID, $post)
    {
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
    
        $post_type = $this->post_type;
        $encoded_obj = json_encode($this);
    
        $request = array(
            'headers' => array(
                'Content-Type' => 'application/json',
            ),
            'body' => $encoded_obj
        );
    
        switch ($post_type) {
            case 'post':
                return wp_remote_post(LAMSON_CLIENT_WP_POSTS_POST, $request);
                break;
            case 'page':
                return wp_remote_post(LAMSON_CLIENT_WP_PAGES_POST, $request);
                break;
        }
    }
}
