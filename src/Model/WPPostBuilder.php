<?php
namespace Lamson\Model;

use Lamson\Model\WPPost as LamsonPost;
use Lamson\Model\WPSite as LamsonSite;

use WP\Model\Post as WPPost;
use WP\Model\Site as WPSite;

/**
 * Define the "WPPostBuilder" Model
 *
 * @package    Lamson
 * @subpackage Lamson/Model
 */

class WPPostBuilder
{
    /** @var WPSite $site */
    private $wpSite;

    /** @var WPPost $post */
    private $wpPost;

    /** @var LamsonPost $postID */
    private $lamsonPost;

    /**
     * Someting
     *
     * Something else
     *
     * @since    1.0.0
     */
    public function __construct()
    {
    }

    public function build(WPSite $site, WPPost $post): LamsonPost
    {
        $this->wpSite = $site;
        $this->wpPost = $post;

        $properties = $this->fromPost($this->wpPost);

        $this->initLamsonPost();
        $this->populateLamsonPost($properties);

        return $this->lamsonPost;
    }

    private function fromPost(): array
    {
        $post = array();

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

        return $post;
    }

    private function fromJSON(string $jsonString)
    {
    }

    private function fromArray(array $array)
    {
    }

    private function initLamsonPost(string $siteDomain, int $siteID, int $postID)
    {
        $this->lamsonPost = new LamsonPost($siteDomain, $siteID, $postID);
    }

    private function populateLamsonPost(array $properties)
    {
        $this->lamsonPost->setSiteSlug($properties['slug']);
        $this->lamsonPost->setSiteName($properties['siteName']);
        $this->lamsonPost->setPostSlug($properties['postSlug']);
        $this->lamsonPost->setPostPermalink($properties['postPermalink']);
        $this->lamsonPost->setPostGUID($properties['postGUID']);
        $this->lamsonPost->setPostType($properties['postType']);
        $this->lamsonPost->setPostStatus($properties['postStatus']);
        $this->lamsonPost->setPostDate($properties['postDate']);
        $this->lamsonPost->setPostDateGMT($properties['postDateGMT']);
        $this->lamsonPost->setPostModified($properties['postModified']);
        $this->lamsonPost->setPostModifiedGMT($properties['postModifiedGMT']);
        $this->lamsonPost->setPostAuthorID($properties['postAuthorID']);
        $this->lamsonPost->setPostAuthorName($properties['postAuthorName']);
        $this->lamsonPost->setPostAuthorEmail($properties['postAuthorEmail']);
        $this->lamsonPost->setPostTitle($properties['postTitle']);
        $this->lamsonPost->setPostContent($properties['postContent']);
        $this->lamsonPost->setPostExcerpt($properties['postExcerpt']);
        $this->lamsonPost->setPostParent($properties['postParent']);
        $this->lamsonPost->setPostMenuOrder($properties['postMenuOrder']);
        $this->lamsonPost->setCommentStatus($properties['commentStatus']);
        $this->lamsonPost->setCommentCount($properties['commentCount']);
        $this->lamsonPost->setPingStatus($properties['pingStatus']);
        $this->lamsonPost->setPinged($properties['pinged']);
        $this->lamsonPost->setToPing($properties['toPing']);
        $this->lamsonPost->setPostFilter($properties['postFilter']);
        $this->lamsonPost->setPostContentFiltered($properties['postContentFiltered']);
        $this->lamsonPost->setPostMimeType($properties['postMimeType']);
        $this->lamsonPost->setPostCategories($properties['postCategories']);
        $this->lamsonPost->setPostTags($properties['postTags']);
        $this->lamsonPost->setLamsonSendNotification($properties['lamsonSendNotification']);
        $this->lamsonPost->setLamsonSyndicationTargets($properties['lamsonSyndicationTargets']);
    }
}
