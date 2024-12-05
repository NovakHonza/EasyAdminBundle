<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Tests\Controller\PrettyUrls;

use EasyCorp\Bundle\EasyAdminBundle\Tests\PrettyUrlsTestApplication\Kernel;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @group pretty_urls
 */
class PrettyUrlsControllerTest extends WebTestCase
{
    public static function getKernelClass(): string
    {
        return Kernel::class;
    }

    public function testGeneratedRoutes()
    {
        // the generated routes are:
        //   * `admin_pretty_*`: the default routes of DashboardController (which doesn't customize anything about them)
        //   * `admin_pretty_external_user_editor_*`: these are the routes related to the User entity; this is not used by DashboardController, but they are created
        //                                            anyways because EasyAdmin creates routes for all dashboards + CRUD controllers by default (this can be avoided
        //                                            by setting the 'allowedControllers' option in the #[AdminDashboard] attribute)
        //   * `second_dashboard_*`: the fully-customized routes of the SecondDashboardController; EasyAdmin only generates routes for the User
        //                           entity because it's the only one allowed by the 'allowedControllers' option in the #[AdminDashboard] attribute
        // note: don't sort or reorder these routes in any way; this is the expected order in which
        //       they are generated by EasyAdmin and it's important to test that too
        $expectedRoutes = [];
        $expectedRoutes['admin_pretty'] = '/admin/pretty/urls';
        $expectedRoutes['second_dashboard'] = '/second/dashboard';
        $expectedRoutes['admin_pretty_blog_post_index'] = '/admin/pretty/urls/blog-post';
        $expectedRoutes['admin_pretty_blog_post_new'] = '/admin/pretty/urls/blog-post/new';
        $expectedRoutes['admin_pretty_blog_post_batch_delete'] = '/admin/pretty/urls/blog-post/batch-delete';
        $expectedRoutes['admin_pretty_blog_post_autocomplete'] = '/admin/pretty/urls/blog-post/autocomplete';
        $expectedRoutes['admin_pretty_blog_post_render_filters'] = '/admin/pretty/urls/blog-post/render-filters';
        $expectedRoutes['admin_pretty_blog_post_edit'] = '/admin/pretty/urls/blog-post/{entityId}/edit';
        $expectedRoutes['admin_pretty_blog_post_delete'] = '/admin/pretty/urls/blog-post/{entityId}/delete';
        $expectedRoutes['admin_pretty_blog_post_detail'] = '/admin/pretty/urls/blog-post/{entityId}';
        $expectedRoutes['admin_pretty_category_index'] = '/admin/pretty/urls/category';
        $expectedRoutes['admin_pretty_category_new'] = '/admin/pretty/urls/category/new';
        $expectedRoutes['admin_pretty_category_batch_delete'] = '/admin/pretty/urls/category/batch-delete';
        $expectedRoutes['admin_pretty_category_autocomplete'] = '/admin/pretty/urls/category/autocomplete';
        $expectedRoutes['admin_pretty_category_render_filters'] = '/admin/pretty/urls/category/render-filters';
        $expectedRoutes['admin_pretty_category_edit'] = '/admin/pretty/urls/category/{entityId}/edit';
        $expectedRoutes['admin_pretty_category_delete'] = '/admin/pretty/urls/category/{entityId}/delete';
        $expectedRoutes['admin_pretty_category_detail'] = '/admin/pretty/urls/category/{entityId}';
        $expectedRoutes['admin_pretty_external_user_editor_custom_route_for_index'] = '/admin/pretty/urls/user-editor/custom/path-for-index';
        $expectedRoutes['admin_pretty_external_user_editor_custom_route_for_new'] = '/admin/pretty/urls/user-editor/new';
        $expectedRoutes['admin_pretty_external_user_editor_batch_delete'] = '/admin/pretty/urls/user-editor/batch-delete';
        $expectedRoutes['admin_pretty_external_user_editor_autocomplete'] = '/admin/pretty/urls/user-editor/autocomplete';
        $expectedRoutes['admin_pretty_external_user_editor_render_filters'] = '/admin/pretty/urls/user-editor/render-filters';
        $expectedRoutes['admin_pretty_external_user_editor_edit'] = '/admin/pretty/urls/user-editor/{entityId}/edit';
        $expectedRoutes['admin_pretty_external_user_editor_delete'] = '/admin/pretty/urls/user-editor/{entityId}/delete';
        $expectedRoutes['admin_pretty_external_user_editor_detail'] = '/admin/pretty/urls/user-editor/custom/path-for-detail/{entityId}';
        $expectedRoutes['admin_pretty_external_user_editor_foobar'] = '/admin/pretty/urls/user-editor/bar/foo';
        $expectedRoutes['admin_pretty_external_user_editor_foofoo'] = '/admin/pretty/urls/user-editor/bar/bar';
        $expectedRoutes['second_dashboard_external_user_editor_custom_route_for_index'] = '/second/dashboard/user-editor/custom/path-for-index';
        $expectedRoutes['second_dashboard_external_user_editor_custom_route_for_new'] = '/second/dashboard/user-editor/add-new';
        $expectedRoutes['second_dashboard_external_user_editor_batch_delete'] = '/second/dashboard/user-editor/batch-delete';
        $expectedRoutes['second_dashboard_external_user_editor_autocomplete'] = '/second/dashboard/user-editor/autocomplete';
        $expectedRoutes['second_dashboard_external_user_editor_render_filters'] = '/second/dashboard/user-editor/render-filters';
        $expectedRoutes['second_dashboard_external_user_editor_change'] = '/second/dashboard/user-editor/edit/---{entityId}---';
        $expectedRoutes['second_dashboard_external_user_editor_delete_this_now'] = '/second/dashboard/user-editor/{entityId}/delete';
        $expectedRoutes['second_dashboard_external_user_editor_detail'] = '/second/dashboard/user-editor/custom/path-for-detail/{entityId}';
        $expectedRoutes['second_dashboard_external_user_editor_foobar'] = '/second/dashboard/user-editor/bar/foo';
        $expectedRoutes['second_dashboard_external_user_editor_foofoo'] = '/second/dashboard/user-editor/bar/bar';

        self::bootKernel();
        $container = static::getContainer();
        $router = $container->get('router');
        $generatedRoutes = [];
        foreach ($router->getRouteCollection() as $name => $route) {
            $generatedRoutes[$name] = $route->getPath();
        }

        $this->assertSame($expectedRoutes, $generatedRoutes);
    }

    public function testDefaultWelcomePage()
    {
        $client = static::createClient();
        $client->followRedirects();

        $client->request('GET', '/admin/pretty/urls');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Welcome to EasyAdmin 4');
    }

    public function testCusomizedWelcomePage()
    {
        $client = static::createClient();
        $client->followRedirects();

        $client->request('GET', '/second/dashboard');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Welcome to EasyAdmin 4');
    }

    public function testDefaultCrudController()
    {
        $client = static::createClient();
        $client->followRedirects();

        $client->request('GET', '/admin/pretty/urls/blog-post');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1.title', 'BlogPost');
    }

    public function testCustomizedCrudController()
    {
        $client = static::createClient();
        $client->followRedirects();

        $client->request('GET', '/second/dashboard/user-editor/custom/path-for-index');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1.title', 'User');
    }

    public function testDefaultMainMenuUsesPrettyUrls()
    {
        $client = static::createClient();
        $client->followRedirects();

        $crawler = $client->request('GET', '/admin/pretty/urls/blog-post');

        $this->assertSame('/admin/pretty/urls', $crawler->filter('#header-logo a.logo')->attr('href'), 'The main Dashboard logo link points to the dashboard entry URL');
        $this->assertSame('/admin/pretty/urls', $crawler->filter('li.menu-item a:contains("Dashboard")')->attr('href'), 'The Dashboard link inside the menu points to the dashboard entry URL (even if it later redirects to some other entity)');
        $this->assertSame('http://localhost/admin/pretty/urls/blog-post', $crawler->filter('li.menu-item a:contains("Blog Posts")')->attr('href'));
        $this->assertSame('http://localhost/admin/pretty/urls/category', $crawler->filter('li.menu-item a:contains("Categories")')->attr('href'));
    }

    public function testCustomMainMenuUsesPrettyUrls()
    {
        $client = static::createClient();
        $client->followRedirects();

        $crawler = $client->request('GET', '/second/dashboard/user-editor/custom/path-for-index');

        $this->assertSame('/second/dashboard', $crawler->filter('#header-logo a.logo')->attr('href'), 'The main Dashboard logo link points to the dashboard entry URL');
        $this->assertSame('/second/dashboard', $crawler->filter('li.menu-item a:contains("Dashboard")')->attr('href'), 'The Dashboard link inside the menu points to the dashboard entry URL (even if it later redirects to some other entity)');
        $this->assertSame('http://localhost/second/dashboard/user-editor/custom/path-for-index', $crawler->filter('li.menu-item a:contains("Users")')->attr('href'));
    }

    /**
     * @dataProvider provideActiveMenuUrls
     */
    public function testMainMenuActiveItemWithPrettyUrls(string $url)
    {
        $client = static::createClient();
        $client->followRedirects();

        $crawler = $client->request('GET', $url);

        $this->assertSame('Categories', trim($crawler->filter('.menu-item.active')->text()));
    }

    public function testDefaultActionsUsePrettyUrls()
    {
        $client = static::createClient();
        $client->followRedirects();

        $crawler = $client->request('GET', '/admin/pretty/urls/blog-post');

        $this->assertSame('http://localhost/admin/pretty/urls/blog-post?page=1', $crawler->filter('form.form-action-search')->attr('action'));
        $this->assertSame('http://localhost/admin/pretty/urls/blog-post/new', $crawler->filter('.global-actions a.action-new')->attr('href'));
        $this->assertSame('http://localhost/admin/pretty/urls/blog-post/1/edit', $crawler->filter('td a.action-edit')->attr('href'));
        $this->assertSame('http://localhost/admin/pretty/urls/blog-post/1/delete', $crawler->filter('td a.action-delete')->attr('href'));
        $this->assertMatchesRegularExpression('#http://localhost/admin/pretty/urls/blog-post/1/edit\?csrfToken=.*&fieldName=content#', $crawler->filter('td.field-boolean input[type="checkbox"]')->attr('data-toggle-url'));
    }

    /**
     * @dataProvider provideDefaultPageUrls
     */
    public function testDefaultPagesWithPrettyUrls(string $uri)
    {
        $client = static::createClient();
        $client->followRedirects();

        $client->request('GET', $uri);

        $this->assertResponseIsSuccessful();
    }

    public function testCustomActionsUsePrettyUrls()
    {
        $client = static::createClient();
        $client->followRedirects();

        $crawler = $client->request('GET', '/second/dashboard/user-editor/custom/path-for-index');

        $this->assertSame('http://localhost/second/dashboard/user-editor/custom/path-for-index?page=1', $crawler->filter('form.form-action-search')->attr('action'));
        $this->assertSame('http://localhost/second/dashboard/user-editor/add-new', $crawler->filter('.global-actions a.action-new')->attr('href'));
        $this->assertSame('http://localhost/second/dashboard/user-editor/edit/---1---', $crawler->filter('td a.action-edit')->attr('href'));
        $this->assertSame('http://localhost/second/dashboard/user-editor/1/delete', $crawler->filter('td a.action-delete')->attr('href'));
    }

    public function testDefaultSortLinksUsePrettyUrls()
    {
        $client = static::createClient();
        $client->followRedirects();

        $crawler = $client->request('GET', '/admin/pretty/urls/blog-post');

        $this->assertSame('http://localhost/admin/pretty/urls/blog-post?page=1&sort%5Bid%5D=DESC', $crawler->filter('th.searchable a')->eq(0)->attr('href'));
        $this->assertSame('http://localhost/admin/pretty/urls/blog-post?page=1&sort%5Btitle%5D=DESC', $crawler->filter('th.searchable a')->eq(1)->attr('href'));
        $this->assertSame('http://localhost/admin/pretty/urls/blog-post?page=1&sort%5Bslug%5D=DESC', $crawler->filter('th.searchable a')->eq(2)->attr('href'));
        $this->assertSame('http://localhost/admin/pretty/urls/blog-post?page=1&sort%5Bcontent%5D=DESC', $crawler->filter('th.searchable a')->eq(3)->attr('href'));
        $this->assertSame('http://localhost/admin/pretty/urls/blog-post?page=1&sort%5Bauthor%5D=DESC', $crawler->filter('th.searchable a')->eq(4)->attr('href'));
    }

    public function testCustomSortLinksUsePrettyUrls()
    {
        $client = static::createClient();
        $client->followRedirects();

        $crawler = $client->request('GET', '/second/dashboard/user-editor/custom/path-for-index');

        $this->assertSame('http://localhost/second/dashboard/user-editor/custom/path-for-index?page=1&sort%5Bid%5D=DESC', $crawler->filter('th.searchable a')->eq(0)->attr('href'));
        $this->assertSame('http://localhost/second/dashboard/user-editor/custom/path-for-index?page=1&sort%5Bname%5D=DESC', $crawler->filter('th.searchable a')->eq(1)->attr('href'));
        $this->assertSame('http://localhost/second/dashboard/user-editor/custom/path-for-index?page=1&sort%5Bemail%5D=DESC', $crawler->filter('th.searchable a')->eq(2)->attr('href'));
    }

    public static function provideActiveMenuUrls(): iterable
    {
        yield ['/admin/pretty/urls/category'];
        yield ['/admin/pretty/urls/category/new'];
        yield ['/admin/pretty/urls/category/1'];
        yield ['/admin/pretty/urls/category/1/edit'];
    }

    public static function provideDefaultPageUrls(): iterable
    {
        yield 'Create' => ['/admin/pretty/urls/category/new'];
        yield 'Read' => ['/admin/pretty/urls/category/1'];
        yield 'Update' => ['/admin/pretty/urls/category/1/edit'];
    }
}
