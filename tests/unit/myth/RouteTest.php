<?php

namespace Myth;

class RouteTest extends \Codeception\TestCase\Test
{
   /**
    * @var \UnitTester
    */
    protected $tester;

    protected function _before()
    {
        $this->route = new Route();
    }

    protected function _after()
    {
    }

    // tests
    public function testCanAccessClass()
    {
        $this->assertTrue(class_exists('Myth\Route'));
    }

    //--------------------------------------------------------------------

    public function testMapLeavesPassedRoutesIntact ()
    {
        $routes = [
            'from' => 'to'
        ];

        $this->assertEquals($routes, $this->route->map($routes));
    }

    //--------------------------------------------------------------------

    public function testMapReplacesDefaultController ()
    {
        $routes = [
            'from' => '{default_controller}/to'
        ];

        $final = [
            'from' => 'home/to'
        ];

        $this->assertEquals($final, $this->route->map($routes));
    }

    //--------------------------------------------------------------------

    public function testAnyBasics ()
    {
        $final = [
            'from' => 'home/to'
        ];

        $this->route->any('from', 'home/to');

        $this->assertEquals($final, $this->route->map( [] ));
    }

    //--------------------------------------------------------------------

    //--------------------------------------------------------------------
    // HTTP Verb-based Routes
    //--------------------------------------------------------------------

    public function testGetBasics ()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $final = [
            'get' => 'from/to'
        ];

        $this->route->get('get', 'from/to');
        $this->route->post('post', 'from/to');
        $this->route->put('put', 'from/to');
        $this->route->delete('delete', 'from/to');
        $this->route->head('head', 'from/to');
        $this->route->patch('patch', 'from/to');
        $this->route->options('options', 'from/to');

        $this->assertEquals($final, $this->route->map( [] ));
    }

    //--------------------------------------------------------------------

    public function testPostBasics ()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';

        $final = [
            'post' => 'from/to'
        ];

        $this->route->get('get', 'from/to');
        $this->route->post('post', 'from/to');
        $this->route->put('put', 'from/to');
        $this->route->delete('delete', 'from/to');
        $this->route->head('head', 'from/to');
        $this->route->patch('patch', 'from/to');
        $this->route->options('options', 'from/to');

        $this->assertEquals($final, $this->route->map( [] ));
    }

    //--------------------------------------------------------------------

    public function testPutBasics ()
    {
        $_SERVER['REQUEST_METHOD'] = 'PUT';

        $final = [
            'put' => 'from/to'
        ];

        $this->route->get('get', 'from/to');
        $this->route->post('post', 'from/to');
        $this->route->put('put', 'from/to');
        $this->route->delete('delete', 'from/to');
        $this->route->head('head', 'from/to');
        $this->route->patch('patch', 'from/to');
        $this->route->options('options', 'from/to');

        $this->assertEquals($final, $this->route->map( [] ));
    }

    //--------------------------------------------------------------------

    public function testDeleteBasics ()
    {
        $_SERVER['REQUEST_METHOD'] = 'DELETE';

        $final = [
            'delete' => 'from/to'
        ];

        $this->route->get('get', 'from/to');
        $this->route->post('post', 'from/to');
        $this->route->put('put', 'from/to');
        $this->route->delete('delete', 'from/to');
        $this->route->head('head', 'from/to');
        $this->route->patch('patch', 'from/to');
        $this->route->options('options', 'from/to');

        $this->assertEquals($final, $this->route->map( [] ));
    }

    //--------------------------------------------------------------------

    public function testHeadBasics ()
    {
        $_SERVER['REQUEST_METHOD'] = 'HEAD';

        $final = [
            'head' => 'from/to'
        ];

        $this->route->get('get', 'from/to');
        $this->route->post('post', 'from/to');
        $this->route->put('put', 'from/to');
        $this->route->delete('delete', 'from/to');
        $this->route->head('head', 'from/to');
        $this->route->patch('patch', 'from/to');
        $this->route->options('options', 'from/to');

        $this->assertEquals($final, $this->route->map( [] ));
    }

    //--------------------------------------------------------------------

    public function testPatchBasics ()
    {
        $_SERVER['REQUEST_METHOD'] = 'PATCH';

        $final = [
            'patch' => 'from/to'
        ];

        $this->route->get('get', 'from/to');
        $this->route->post('post', 'from/to');
        $this->route->put('put', 'from/to');
        $this->route->delete('delete', 'from/to');
        $this->route->head('head', 'from/to');
        $this->route->patch('patch', 'from/to');
        $this->route->options('options', 'from/to');

        $this->assertEquals($final, $this->route->map( [] ));
    }

    //--------------------------------------------------------------------

    public function testOptionsBasics ()
    {
        $_SERVER['REQUEST_METHOD'] = 'OPTIONS';

        $final = [
            'options' => 'from/to'
        ];

        $this->route->get('get', 'from/to');
        $this->route->post('post', 'from/to');
        $this->route->put('put', 'from/to');
        $this->route->delete('delete', 'from/to');
        $this->route->head('head', 'from/to');
        $this->route->patch('patch', 'from/to');
        $this->route->options('options', 'from/to');

        $this->assertEquals($final, $this->route->map( [] ));
    }

    //--------------------------------------------------------------------

    //--------------------------------------------------------------------
    // Resources
    //--------------------------------------------------------------------

    public function testResourcesGet ()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $final = [
            'posts'                 => 'posts/list_all',            // GET
            'posts/new'             => 'posts/creation_form',       // GET
            'posts/(:any)/edit'     => 'posts/editing_form/$1',     // GET
            'posts/(:any)'          => 'posts/show/$1',             // GET
//            'posts'                 => 'posts/create',              // POST
//            'posts/(:any)'          => 'posts/edit/$1',             // PUT?
//            'posts/(:any)'          => 'posts/delete/$1'            // DELETE
        ];

        $this->route->resources('posts');

        $this->assertEquals($final, $this->route->map( [] ));
    }

    //--------------------------------------------------------------------

    public function testResourcesPost ()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';

        $final = [
//            'posts'                 => 'posts/list_all',            // GET
//            'posts/new'             => 'posts/creation_form',       // GET
//            'posts/(:any)/edit'     => 'posts/editing_form/$1',     // GET
//            'posts/(:any)'          => 'posts/show/$1',             // GET
              'posts'                 => 'posts/create',              // POST
            //            'posts/(:any)'          => 'posts/edit/$1',             // PUT?
            //            'posts/(:any)'          => 'posts/delete/$1'            // DELETE
        ];

        $this->route->resources('posts');

        $this->assertEquals($final, $this->route->map( [] ));
    }

    //--------------------------------------------------------------------

    public function testResourcesPut ()
    {
        $_SERVER['REQUEST_METHOD'] = 'PUT';

        $final = [
//            'posts'                 => 'posts/list_all',            // GET
//            'posts/new'             => 'posts/creation_form',       // GET
//            'posts/(:any)/edit'     => 'posts/editing_form/$1',     // GET
//            'posts/(:any)'          => 'posts/show/$1',             // GET
//            'posts'                 => 'posts/create',              // POST
              'posts/(:any)'          => 'posts/update/$1',           // PUT?
//            'posts/(:any)'          => 'posts/delete/$1'            // DELETE
        ];

        $this->route->resources('posts');

        $this->assertEquals($final, $this->route->map( [] ));
    }

    //--------------------------------------------------------------------

    public function testResourcesDelete ()
    {
        $_SERVER['REQUEST_METHOD'] = 'DELETE';

        $final = [
//            'posts'                 => 'posts/list_all',            // GET
//            'posts/new'             => 'posts/creation_form',       // GET
//            'posts/(:any)/edit'     => 'posts/editing_form/$1',     // GET
//            'posts/(:any)'          => 'posts/show/$1',             // GET
//            'posts'                 => 'posts/create',              // POST
//            'posts/(:any)'          => 'posts/edit/$1',             // PUT?
              'posts/(:any)'          => 'posts/delete/$1'            // DELETE
        ];

        $this->route->resources('posts');

        $this->assertEquals($final, $this->route->map( [] ));
    }

    //--------------------------------------------------------------------

    public function testResourcesOptions ()
    {
        $_SERVER['REQUEST_METHOD'] = 'OPTIONS';

        $final = [
//            'posts'                 => 'posts/list_all',            // GET
//            'posts/new'             => 'posts/creation_form',       // GET
//            'posts/(:any)/edit'     => 'posts/editing_form/$1',     // GET
//            'posts/(:any)'          => 'posts/show/$1',             // GET
//            'posts'                 => 'posts/create',              // POST
//            'posts/(:any)'          => 'posts/edit/$1',             // PUT?
//            'posts/(:any)'          => 'posts/delete/$1'            // DELETE
              'posts'                 => 'posts/index'                // options
        ];

        $this->route->resources('posts');

        $this->assertEquals($final, $this->route->map( [] ));
    }

    //--------------------------------------------------------------------

    public function testResourcesConstraint ()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $final = [
            'posts'                 => 'posts/list_all',            // GET
            'posts/new'             => 'posts/creation_form',       // GET
            'posts/(:num)/edit'     => 'posts/editing_form/$1',     // GET
            'posts/(:num)'          => 'posts/show/$1',             // GET
        ];

        $this->route->resources('posts', ['constraint' => '(:num)']);

        $this->assertEquals($final, $this->route->map( [] ));
    }

    //--------------------------------------------------------------------

    public function testResourcesController ()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $final = [
            'posts'                 => 'blog/list_all',            // GET
            'posts/new'             => 'blog/creation_form',       // GET
            'posts/(:any)/edit'     => 'blog/editing_form/$1',     // GET
            'posts/(:any)'          => 'blog/show/$1',             // GET
        ];

        $this->route->resources('posts', ['controller' => 'blog']);

        $this->assertEquals($final, $this->route->map( [] ));
    }

    //--------------------------------------------------------------------

    public function testResourcesModule ()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $final = [
            'posts'                 => 'blog/posts/list_all',            // GET
            'posts/new'             => 'blog/posts/creation_form',       // GET
            'posts/(:any)/edit'     => 'blog/posts/editing_form/$1',     // GET
            'posts/(:any)'          => 'blog/posts/show/$1',             // GET
        ];

        $this->route->resources('posts', ['module' => 'blog']);

        $this->assertEquals($final, $this->route->map( [] ));
    }

    //--------------------------------------------------------------------

    public function testResourcesOffset ()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $final = [
            'posts'                 => 'posts/list_all',            // GET
            'posts/new'             => 'posts/creation_form',       // GET
            'posts/(:any)/edit'     => 'posts/editing_form/$3',     // GET
            'posts/(:any)'          => 'posts/show/$3',             // GET
        ];

        $this->route->resources('posts', ['offset' => 2]);

        $this->assertEquals($final, $this->route->map( [] ));
    }

    //--------------------------------------------------------------------

    public function testResourcesAllOptions ()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $final = [
            'posts'                 => 'blog/newposts/list_all',            // GET
            'posts/new'             => 'blog/newposts/creation_form',       // GET
            'posts/(:num)/edit'     => 'blog/newposts/editing_form/$2',     // GET
            'posts/(:num)'          => 'blog/newposts/show/$2',             // GET
        ];

        $this->route->resources('posts', ['module' => 'blog', 'controller' => 'newposts', 'constraint' => '(:num)', 'offset' => 1]);

        $this->assertEquals($final, $this->route->map( [] ));
    }

    //--------------------------------------------------------------------

    //--------------------------------------------------------------------
    // Areas
    //--------------------------------------------------------------------

    public function testAreaBasics ()
    {
        $final = [
            'admin/(:any)/(:any)/(:any)/(:any)/(:any)'  => '$1/admin/$2/$3/$4/$5',
            'admin/(:any)/(:any)/(:any)/(:any)'         => '$1/admin/$2/$3/$4',
            'admin/(:any)/(:any)/(:any)'                => '$1/admin/$2/$3',
            'admin/(:any)/(:any)'                       => '$1/admin/$2',
            'admin/(:any)'                              => '$1/admin'
        ];

        $this->route->area('admin', 'admin');

        $this->assertEquals($final, $this->route->map( [] ));
    }

    //--------------------------------------------------------------------

    public function testAreaOffset ()
    {
        $final = [
            'admin/(:any)/(:any)/(:any)/(:any)/(:any)'  => '$2/admin/$3/$4/$5/$6',
            'admin/(:any)/(:any)/(:any)/(:any)'         => '$2/admin/$3/$4/$5',
            'admin/(:any)/(:any)/(:any)'                => '$2/admin/$3/$4',
            'admin/(:any)/(:any)'                       => '$2/admin/$3',
            'admin/(:any)'                              => '$2/admin'
        ];

        $this->route->area('admin', 'admin', ['offset' => 1]);

        $this->assertEquals($final, $this->route->map( [] ));
    }

    //--------------------------------------------------------------------

    public function testGetAreaName ()
    {
        $this->route->area('admin', 'adminController');

        $this->assertEquals('admin', Route::getAreaName( 'adminController' ));
    }

    //--------------------------------------------------------------------

    //--------------------------------------------------------------------
    // Groups
    //--------------------------------------------------------------------

    public function testGroupingWorks ()
    {
        $final = [
            'group1/blog' => 'blog/index'
        ];

        $this->route->group('group1', function() {

            $this->route->any('blog', 'blog/index');

        });

        $this->assertEquals($final, $this->route->map( [] ));
    }

    //--------------------------------------------------------------------

    public function testMultipleGroupingWorks ()
    {
        $final = [
            'group1/blog' => 'blog/index',
            'group2/blog' => 'blog/index'
        ];

        $this->route->group('group1', function() {
            $this->route->any('blog', 'blog/index');
        });

        $this->route->group('group2', function() {
            $this->route->any('blog', 'blog/index');
        });

        $this->assertEquals($final, $this->route->map( [] ));
    }

    //--------------------------------------------------------------------

    public function testNestedGroupingWorks ()
    {
        $final = [
            'group1/blog'   => 'blog/index',
            'group1/group2/blog' => 'blog/index'
        ];

        $this->route->group('group1', function() {
           $this->route->any('blog', 'blog/index');

            $this->route->group('group2', function() {
                $this->route->any('blog', 'blog/index');
            });
        });

        $this->assertEquals($final, $this->route->map( [] ));
    }

    //--------------------------------------------------------------------

    public function testDeepNestedGroupingWorks ()
    {
        $final = [
            'group1/blog'   => 'blog/index',
            'group1/group2/blog' => 'blog/index',
            'group1/group2/group3/blog' => 'blog/index',
            'group1/group2/group3/group4/blog' => 'blog/index'
        ];

        $this->route->group('group1', function() {
            $this->route->any('blog', 'blog/index');

            $this->route->group('group2', function() {
                $this->route->any('blog', 'blog/index');

                $this->route->group('group3', function() {
                    $this->route->any('blog', 'blog/index');

                    $this->route->group('group4', function() {
                        $this->route->any('blog', 'blog/index');
                    });
                });
            });
        });

        $this->assertEquals($final, $this->route->map( [] ));
    }

    //--------------------------------------------------------------------

    //--------------------------------------------------------------------
    // Misc
    //--------------------------------------------------------------------

    public function testBlockSingle ()
    {
        $final = [
            'from' => ''
        ];

        $this->route->block('from');

        $this->assertEquals($final, $this->route->map( [] ));
    }

    //--------------------------------------------------------------------

    public function testBlockMultiple ()
    {
        $final = [
            'from' => '',
            'from2' => ''
        ];

        $this->route->block('from', 'from2');

        $this->assertEquals($final, $this->route->map( [] ));
    }

    //--------------------------------------------------------------------

    public function testBlockOverwritesPrevious ()
    {
        $final = [
            'from' => '',
            'from2' => ''
        ];

        $this->route->get('from', 'to');

        $this->route->block('from', 'from2');

        $this->assertEquals($final, $this->route->map( [] ));
    }

    //--------------------------------------------------------------------

    //--------------------------------------------------------------------
    // Named Routes
    //--------------------------------------------------------------------

    public function testNamedBasics ()
    {
        $this->route->any('from', 'to', ['as' => 'ginger']);

        $this->assertEquals('from', Route::named('ginger'));
    }

    //--------------------------------------------------------------------

    //--------------------------------------------------------------------
    // Constraints
    //--------------------------------------------------------------------

    public function testConstraintConversion ()
    {
        $final = [
            'users/(:any)'  => 'users/$1',
            'users/(:num)'  => 'users/$1',
            'users/(:num)'  => 'users/$1',
            "users/([a-zA-Z']+)"    => 'users/$1'
        ];

        $this->route->any('users/{any}', 'users/$1');
        $this->route->any('users/{num}', 'users/$1');
        $this->route->any('users/{id}', 'users/$1');
        $this->route->any('users/{name}', 'users/$1');

        $this->assertEquals($final, $this->route->map( [] ));
    }

    //--------------------------------------------------------------------

    public function testRegisterConstraintNoOverwrite ()
    {
        $final = [
            'users/(:num)'   => 'users/$1'
        ];

        $this->route->registerConstraint('id', '(^.*)');

        $this->route->any('users/{id}', 'users/$1');

        $this->assertEquals($final, $this->route->map( [] ));
    }

    //--------------------------------------------------------------------

    public function testRegisterConstraintWiteOverwrite ()
    {
        $final = [
            'users/(^.*)'   => 'users/$1'
        ];

        $this->route->registerConstraint('id', '(^.*)', true);

        $this->route->any('users/{id}', 'users/$1');

        $this->assertEquals($final, $this->route->map( [] ));
    }

    //--------------------------------------------------------------------

    //--------------------------------------------------------------------
    // Match
    //--------------------------------------------------------------------

    public function testMatchIncludesAllMethodsInGet ()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $final = [
            'users/(:num)'  => 'users/$1'
        ];

        $this->route->match(['GET', 'POST'], 'users/(:num)', 'users/$1');

        $this->assertEquals($final, $this->route->map( [] ));
    }

    //--------------------------------------------------------------------

    public function testMatchIncludesAllMethodsInPost ()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';

        $final = [
            'users/(:num)'  => 'users/$1'
        ];

        $this->route->match(['GET', 'POST'], 'users/(:num)', 'users/$1');

        $this->assertEquals($final, $this->route->map( [] ));
    }

    //--------------------------------------------------------------------

    public function testMatchIncludesAllMethodsWithDifferentCase ()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $final = [
            'users/(:num)'  => 'users/$1'
        ];

        $this->route->match(['get', 'post'], 'users/(:num)', 'users/$1');

        $this->assertEquals($final, $this->route->map( [] ));
    }

    //--------------------------------------------------------------------

    //--------------------------------------------------------------------
    // Subdomains
    //--------------------------------------------------------------------

    public function testSubdomainMatchesASubdomain ()
    {
        $_SERVER['HTTP_HOST'] = 'http://en.example.com';
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $final = [
            'users/(:num)'  => 'users/$1'
        ];

        $this->route->get('users/(:num)', 'users/$1', ['subdomain' => 'en']);
        $this->route->get('users/(:any)', 'users/$1', ['subdomain' => 'fr']);

        $this->assertEquals($final, $this->route->map( [] ));
    }

    //--------------------------------------------------------------------

    public function testSubdomainDoesNotMatchWithoutValidSubdomain ()
    {
        $_SERVER['HTTP_HOST'] = 'http://en.example.com';
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $final = [
            'users/(:num)'  => 'users/$1'
        ];

        $this->route->get('users/(:num)', 'users/$1', ['subdomain' => 'br']);
        $this->route->get('users/(:any)', 'users/$1', ['subdomain' => 'fr']);

        $this->assertEquals([], $this->route->map( [] ));
    }

    //--------------------------------------------------------------------

    public function testSubdomainMatchesWithMultipleSubdomains ()
    {
        $_SERVER['HTTP_HOST'] = 'http://fr.example.com';
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $final = [
            'users/(:num)'  => 'users/$1'
        ];

        $this->route->get('users/(:num)', 'users/$1', ['subdomain' => ['en', 'fr'] ]);

        $this->assertEquals($final, $this->route->map( [] ));
    }

    //--------------------------------------------------------------------

    public function testSubdomainMatchesFirstSubdomainWhenMultipleExist ()
    {
        $_SERVER['HTTP_HOST'] = 'http://en.example.co.uk';
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $final = [
            'users/(:num)'  => 'users/$1'
        ];

        $this->route->get('users/(:num)', 'users/$1', ['subdomain' => 'en']);
        $this->route->get('users/(:any)', 'users/$1', ['subdomain' => 'fr']);

        $this->assertEquals($final, $this->route->map( [] ));
    }

    //--------------------------------------------------------------------

    public function testSubdomainMatchesASubdomainWithWildcard ()
    {
        $_SERVER['HTTP_HOST'] = 'http://en.example.co.uk';
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $final = [
            'users/(:num)'  => 'users/$1'
        ];

        $this->route->get('users/(:num)', 'users/$1', ['subdomain' => '*']);

        $this->assertEquals($final, $this->route->map( [] ));
    }

    //--------------------------------------------------------------------

    public function testSubdomainDoesNotMatchWhenNoSubdomainWithWildcard ()
    {
        $_SERVER['HTTP_HOST'] = 'http://example.com';
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $final = [
            'users/(:num)'  => 'users/$1'
        ];

        $this->route->get('users/(:num)', 'users/$1', ['subdomain' => '*']);

        $this->assertEquals($final, $this->route->map( [] ));
    }

    //--------------------------------------------------------------------

    public function testCanCallNamedRouteWhenSubdomainDoesNotMatch ()
    {
        $_SERVER['HTTP_HOST'] = 'http://en.example.com';
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $this->route->get('users/(:num)', 'users/$1', ['subdomain' => 'fr', 'as' => 'tester']);

        $this->assertEquals(Route::named('tester'), 'users/(:num)');
    }

    //--------------------------------------------------------------------

}