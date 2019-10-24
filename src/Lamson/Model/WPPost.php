<?php
namespace Lamson\Model;

/**
 * Define the "WPPost" Model
 *
 * @package    Lamson
 * @subpackage Lamson/Model
 */

class WPPost implements \JsonSerializable
{
    /** @var string $id */
    private $id;

    /** @var string $siteDomain */
    private $siteDomain;

    /** @var int $siteID */
    private $siteID;

    /** @var int $postID */
    private $postID;

    /** @var string $siteSlug */
    private $siteSlug;

    /** @var string $siteName */
    private $siteName;

    /** @var string $postslug */
    private $postslug;

    /** @var string $postPermalink */
    private $postPermalink;

    /** @var string $postGUID */
    private $postGUID;

    /** @var string $postType */
    private $postType;

    /** @var string $postStatus */
    private $postStatus;

    /** @var string $postDate */
    private $postDate;

    /** @var string $postDateGMT */
    private $postDateGMT;

    /** @var string $postModified */
    private $postModified;

    /** @var string $postModifiedGMT */
    private $postModifiedGMT;

    /** @var int $postAuthorID */
    private $postAuthorID;

    /** @var string $postAuthorName */
    private $postAuthorName;

    /** @var string $postAuthorEmail */
    private $postAuthorEmail;

    /** @var string $postTitle */
    private $postTitle;

    /** @var string $postContent */
    private $postContent;

    /** @var string $postExcerpt */
    private $postExcerpt;

    /** @var int $postParent */
    private $postParent;

    /** @var int $postMenuOrder */
    private $postMenuOrder;

    /** @var string $commentStatus */
    private $commentStatus;

    /** @var int $commentCount */
    private $commentCount;

    /** @var string $pingStatus */
    private $pingStatus;

    /** @var array $pinged */
    private $pinged;

    /** @var array $toPing */
    private $toPing;

    /** @var string $postFilter */
    private $postFilter;

    /** @var string $postContentFiltered */
    private $postContentFiltered;

    /** @var string $postMimeType */
    private $postMimeType;

    // TODO: PostCategory class and PostCategoriesCollection class

    /** @var array $postCategories */
    private $postCategories;

    // TODO: PostTag class and PostTagsCollection class

    /** @var array $postTags */
    private $postTags;

    /** @var int $sitePrivacy */
    private $sitePrivacy;

    // TODO: VisibilityGroup class and VisibilityGroupCollection class

    /** @var array $visibleTo */
    private $visibleTo;

    /**
     * Someting
     *
     * Something else
     *
     * @since    1.0.0
     */
    public function __construct(string $siteDomain, int $siteID, int $postID)
    {
        $this->id = "{$siteDomain}_{$siteID}_{$postID}";

        $this->siteDomain = $siteDomain;
        $this->siteID = $siteID;
        $this->postID = $postID;
    }

    public function jsonSerialize()
    {
        $vars = get_object_vars($this);

        $stripped = array_filter($vars, function ($val) {
            return !is_null($val);
        });

        return $stripped;
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

    public function getID(): string
    {
        return $this->id;
    }

    public function getSiteDomain(): string
    {
        return $this->siteDomain;
    }

    public function getSiteID(): int
    {
        return $this->siteID;
    }

    public function getPostID(): int
    {
        return $this->postID;
    }

    public function getSiteSlug(): string
    {
        return $this->siteSlug;
    }
    public function setSiteSlug(string $slug)
    {
        $this->siteSlug = $slug;
    }

    public function getSiteName(): string
    {
        return $this->siteName;
    }
    public function setSiteName(string $siteName)
    {
        $this->siteName = $siteName;
    }

    public function getSiteURL(): string
    {
        return "{$this->siteDomain}_{$this->siteSlug}";
    }

    public function getSiteLink(): string
    {
        return "https://{$this->siteDomain}_{$this->siteSlug}/";
    }

    public function getPostSlug(): string
    {
        return $this->postSlug;
    }
    public function setPostSlug(string $postSlug)
    {
        $this->postSlug = $postSlug;
    }

    public function getPostPermalink(): string
    {
        return $this->postPermalink;
    }
    public function setPostPermalink(string $postPermalink)
    {
        $this->postPermalink = $postPermalink;
    }

    public function getPostGUID(): string
    {
        return $this->postGUID;
    }
    public function setPostGUID(string $postGUID)
    {
        $this->postGUID = $postGUID;
    }

    public function getPostType(): string
    {
        return $this->postType;
    }
    public function setPostType(string $postType)
    {
        $this->postType = $postType;
    }

    public function getPostStatus(): string
    {
        return $this->postStatus;
    }
    public function setPostStatus(string $postStatus)
    {
        $this->postStatus = $postStatus;
    }

    public function getPostDate(): string
    {
        return $this->postDate;
    }
    public function setPostDate(string $postDate)
    {
        $this->postDate = $postDate;
    }

    public function getPostDateGMT(): string
    {
        return $this->postDateGMT;
    }
    public function setPostDateGMT(string $postDateGMT)
    {
        $this->postDateGMT = $postDateGMT;
    }

    public function getPostModified(): string
    {
        return $this->postModified;
    }
    public function setPostModified(string $postModified)
    {
        $this->postModified = $postModified;
    }

    public function getPostModifiedGMT(): string
    {
        return $this->postModifiedGMT;
    }
    public function setPostModifiedGMT(string $postModifiedGMT)
    {
        $this->postModifiedGMT = $postModifiedGMT;
    }

    public function getPostAuthorID(): int
    {
        return $this->postAuthorID;
    }
    public function setPostAuthorID(number $postAuthorID)
    {
        $this->postAuthorID = $postAuthorID;
    }

    public function getPostAuthorName(): string
    {
        return $this->postAuthorName;
    }
    public function setPostAuthorName(string $postAuthorName)
    {
        $this->postAuthorName = $postAuthorName;
    }

    public function getPostAuthorEmail(): string
    {
        return $this->postAuthorEmail;
    }
    public function setPostAuthorEmail(string $postAuthorEmail)
    {
        $this->postAuthorEmail = $postAuthorEmail;
    }

    public function getPostTitle(): string
    {
        return $this->postTitle;
    }
    public function setPostTitle(string $postTitle)
    {
        $this->postTitle = $postTitle;
    }

    public function getPostContent(): string
    {
        return $this->postContent;
    }
    public function setPostContent(string $postContent)
    {
        $this->postContent = $postContent;
    }

    public function getPostExcerpt(): string
    {
        return $this->postExcerpt;
    }
    public function setPostExcerpt(string $postExcerpt)
    {
        $this->postExcerpt = $postExcerpt;
    }

    public function getPostParent(): int
    {
        return $this->postParent;
    }
    public function setPostParent(number $postParent)
    {
        $this->postParent = $postParent;
    }

    public function getPostMenuOrder(): int
    {
        return $this->postMenuOrder;
    }
    public function setPostMenuOrder(int $postMenuOrder)
    {
        $this->postMenuOrder = $postMenuOrder;
    }

    public function getCommentStatus(): string
    {
        return $this->commentStatus;
    }
    public function setCommentStatus(string $commentStatus)
    {
        $this->commentStatus = $commentStatus;
    }

    public function getCommentCount(): int
    {
        return $this->commentCount;
    }
    public function setCommentCount(int $commentCount)
    {
        $this->commentCount = $commentCount;
    }

    public function getPingStatus(): string
    {
        return $this->pingStatus;
    }
    public function setPingStatus(string $pingStatus)
    {
        $this->pingStatus = $pingStatus;
    }

    public function getPinged(): array
    {
        return $this->pinged;
    }
    public function setPinged(array $pinged)
    {
        $this->pinged = $pinged;
    }

    public function getToPing(): array
    {
        return $this->toPing;
    }
    public function setToPing(array $toPing)
    {
        $this->toPing = $toPing;
    }

    public function getPostFilter(): string
    {
        return $this->postFilter;
    }
    public function setPostFilter(string $postFilter)
    {
        $this->postFilter = $postFilter;
    }

    public function getPostContentFiltered(): string
    {
        return $this->postContentFiltered;
    }
    public function setPostContentFiltered(string $postContentFiltered)
    {
        $this->postContentFiltered = $postContentFiltered;
    }

    public function getPostMimeType(): string
    {
        return $this->postMimeType;
    }
    public function setPostMimeType(string $postMimeType)
    {
        $this->postMimeType = $postMimeType;
    }

    public function getPostCategories(): array
    {
        return $this->postCategories;
    }
    public function setPostCategories(array $postCategories)
    {
        $this->postCategories = $postCategories;
    }

    public function getPostTags(): array
    {
        return $this->postTags;
    }
    public function setPostTags(array $postTags)
    {
        $this->postTags = $postTags;
    }

    public function getLamsonSendNotification(): boolean
    {
        return $this->lamsonSendNotification;
    }
    public function setLamsonSendNotification(bool $lamsonSendNotification)
    {
        $this->lamsonSendNotification = $lamsonSendNotification;
    }

    public function getLamsonSyndicationTargets(): array
    {
        return $this->lamsonSyndicationTargets;
    }
    public function setLamsonSyndicationTargets(array $lamsonSyndicationTargets)
    {
        $this->lamsonSyndicationTargets = $lamsonSyndicationTargets;
    }
}
