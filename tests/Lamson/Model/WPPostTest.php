<?php
namespace Lamson;

use Lamson\Model\WPPost;
use PHPUnit\Framework\TestCase;

class WPPostTest extends TestCase
{
    protected $post;

    protected function initSamplePost(): WPPost
    {
        $siteDomain = 'www.example.com';
        $siteID = 5;
        $postID = 234;

        $post = new WPPost($siteDomain, $siteID, $postID);
        return $post;
    }

    protected function setUp(): void
    {
        $this->post = $this->initSamplePost();
    }

    public function testCanBeInstantiated(): void
    {
        $siteDomain = 'www.example.com';
        $siteID = 5;
        $postID = 234;

        $post = new WPPost($siteDomain, $siteID, $postID);

        $this->assertInstanceOf(WPPost::class, $post);

        $this->assertObjectHasAttribute('_id', $post);
        $this->assertObjectHasAttribute('_siteDomain', $post);
        $this->assertObjectHasAttribute('_siteID', $post);
        $this->assertObjectHasAttribute('_postID', $post);

        $this->assertObjectHasAttribute('_siteDomain', $post);
        $this->assertObjectHasAttribute('_siteID', $post);
        $this->assertObjectHasAttribute('_postID', $post);
        $this->assertObjectHasAttribute('_slug', $post);
        $this->assertObjectHasAttribute('_siteName', $post);
        $this->assertObjectHasAttribute('_postSlug', $post);
        $this->assertObjectHasAttribute('_postPermalink', $post);
        $this->assertObjectHasAttribute('_postGUID', $post);
        $this->assertObjectHasAttribute('_postType', $post);
        $this->assertObjectHasAttribute('_postStatus', $post);
        $this->assertObjectHasAttribute('_postDate', $post);
        $this->assertObjectHasAttribute('_postDateGMT', $post);
        $this->assertObjectHasAttribute('_postModified', $post);
        $this->assertObjectHasAttribute('_postModifiedGMT', $post);
        $this->assertObjectHasAttribute('_postAuthorID', $post);
        $this->assertObjectHasAttribute('_postAuthorName', $post);
        $this->assertObjectHasAttribute('_postAuthorEmail', $post);
        $this->assertObjectHasAttribute('_postTitle', $post);
        $this->assertObjectHasAttribute('_postContent', $post);
        $this->assertObjectHasAttribute('_postExcerpt', $post);
        $this->assertObjectHasAttribute('_postParent', $post);
        $this->assertObjectHasAttribute('_postMenuOrder', $post);
        $this->assertObjectHasAttribute('_commentStatus', $post);
        $this->assertObjectHasAttribute('_commentCount', $post);
        $this->assertObjectHasAttribute('_pingStatus', $post);
        $this->assertObjectHasAttribute('_pinged', $post);
        $this->assertObjectHasAttribute('_toPing', $post);
        $this->assertObjectHasAttribute('_postFilter', $post);
        $this->assertObjectHasAttribute('_postContentFiltered', $post);
        $this->assertObjectHasAttribute('_postMimeType', $post);
        $this->assertObjectHasAttribute('_postCategories', $post);
        $this->assertObjectHasAttribute('_postTags', $post);
        $this->assertObjectHasAttribute('_lamsonSendNotification', $post);
        $this->assertObjectHasAttribute('_lamsonSyndicationTargets', $post);
    }

    public function testID(): void
    {
        $this->assertIsString($this->post->getID());
    }

    public function testSiteDomain(): void
    {
        $this->assertIsString($this->post->getSiteDomain());
    }

    public function testSiteID(): void
    {
        $this->assertIsInt($this->post->getSiteID());
    }

    public function testPostID(): void
    {
        $this->assertIsInt($this->post->getPostID());
    }

    public function testSiteSlug(): void
    {
        $this->post->setSiteSlug('slug');

        $this->assertIsString($this->post->getSiteSlug());
        $this->assertSame($this->post->getSiteSlug(), 'slug');
    }

    public function testSiteName(): void
    {
        $this->post->setSiteName('siteName');

        $this->assertIsString($this->post->getSiteName());
        $this->assertSame($this->post->getSiteName(), 'siteName');
    }

    public function testSiteURL(): void
    {
        $this->post->setSiteSlug('slug');

        $this->assertIsString($this->post->getSiteURL());
        $this->assertSame($this->post->getSiteURL(), "{$this->post->getSiteDomain}_{$this->post->getSiteSlug}");
    }

    public function testSiteLink(): void
    {
        $this->post->setSiteSlug('slug');

        $this->assertIsString($this->post->getSiteLink());
        $this->assertSame($this->post->getSiteLink(), "https://{$this->post->getSiteDomain}_{$this->post->getSiteSlug}/");
    }

    public function testPostSlug(): void
    {
        $this->post->setPostSlug('slug');

        $this->assertIsString($this->post->getPostSlug());
        $this->assertSame($this->post->getPostSlug(), 'slug');
    }

    public function testPostPermalink(): void
    {
        $this->post->setPostPermalink('permalink');

        $this->assertIsString($this->post->getPostPermalink());
        $this->assertSame($this->post->getPostPermalink(), 'permalink');
    }

    public function testPostGUID(): void
    {
        $this->post->setPostGUID('guid');

        $this->assertIsString($this->post->getPostGUID());
        $this->assertSame($this->post->getPostGUID(), 'guid');
    }

    public function testPostType(): void
    {
        $this->assertIsString($this->post->getPostType());
    }

    public function testPostStatus(): void
    {
        $this->assertIsString($this->post->getPostStatus());
    }

    public function testPostDate(): void
    {
        $this->assertIsString($this->post->getPostDate());
    }

    public function testPostDateGMT(): void
    {
        $this->assertIsString($this->post->getPostDateGMT());
    }

    public function testPostModified(): void
    {
        $this->assertIsString($this->post->getPostModified());
    }

    public function testPostModifiedGMT(): void
    {
        $this->assertIsString($this->post->getPostModifiedGMT());
    }

    public function testPostAuthorID(): void
    {
        $this->assertIsString($this->post->getPostAuthorID());
    }

    public function testPostAuthorName(): void
    {
        $this->assertIsString($this->post->getPostAuthorName());
    }

    public function testPostAuthorEmail(): void
    {
        $this->assertIsString($this->post->getPostAuthorEmail());
    }

    public function testPostTitle(): void
    {
        $this->assertIsString($this->post->getPostTitle());
    }

    public function testPostContent(): void
    {
        $this->assertIsString($this->post->getPostContent());
    }

    public function testPostExcerpt(): void
    {
        $this->assertIsString($this->post->getPostExcerpt());
    }

    public function testPostParent(): void
    {
        $this->assertIsString($this->post->getPostParent());
    }

    public function testPostMenuOrder(): void
    {
        $this->assertIsString($this->post->getPostMenuOrder());
    }

    public function testCommentStatus(): void
    {
        $this->assertIsString($this->post->getCommentStatus());
    }

    public function testCommentCount(): void
    {
        $this->assertIsString($this->post->getCommentCount());
    }

    public function testPingStatus(): void
    {
        $this->assertIsString($this->post->getPingStatus());
    }

    public function testPinged(): void
    {
        $this->assertIsString($this->post->getPinged());
    }

    public function testToPing(): void
    {
        $this->assertIsString($this->post->getToPing());
    }

    public function testPostFilter(): void
    {
        $this->assertIsString($this->post->getPostFilter());
    }

    public function testPostContentFiltered(): void
    {
        $this->assertIsString($this->post->getPostContentFiltered());
    }

    public function testPostMimeType(): void
    {
        $this->assertIsString($this->post->getPostMimeType());
    }

    public function testPostCategories(): void
    {
        $this->assertIsString($this->post->getPostCategories());
    }

    public function testTags(): void
    {
        $this->assertIsString($this->post->getPostTags());
    }

    public function testLamsonSendNotification(): void
    {
        $this->assertIsString($this->post->getLamsonSendNotification());
    }

    public function testLamsonSyndicationTargets(): void
    {
        $this->assertIsString($this->post->getLamsonSyndicationTargets());
    }

}
