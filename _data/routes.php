<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|   example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|   http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|   $route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|   $route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|   $route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples: my-controller/index -> my_controller/index
|       my-controller/my-method -> my_controller/my_method
*/
$route['default_controller']    = 'home';
$route['404_override']          = '';
$route['translate_uri_dashes']  = TRUE;

// @[username]
$route['^@([0-9a-zA-Z-_]+)']    = 'user/index/$1';
// %40[username] email escapes
$route['^\%40([0-9a-zA-Z-_]+)'] = 'user/index/$1';

// pages
$route['about']                 = 'pages/index/about';
$route['about/(:any)']          = 'pages/index/about/$1';
$route['ai']                    = 'pages/index/ai';
$route['features']              = 'pages/index/features';
$route['jobs']                  = 'pages/index/jobs';
$route['legal']                 = 'pages/index/legal';
$route['legal/(:any)']          = 'pages/index/legal/$1';
$route['support']               = 'pages/index/support';
$route['support/(:any)']        = 'pages/index/support/$1';
$route['plans']                 = 'pages/index/plans';
$route['plans/(:any)']          = 'pages/index/plans/$1';
$route['press']                 = 'pages/index/press';
$route['press/(:any)']          = 'pages/index/press/$1';

// customize
$route['customize/logo']        = 'customize/upload/logo';
$route['customize/icon']        = 'customize/upload/icon';
$route['customize/title']       = 'customize/upload/title';
// $route['customize/domain']      = 'customize/domain';

// weblog
$route['weblog/(:any)']                             = 'weblog/index/$1';
$route['weblog/(:any)/rss']                         = 'weblog/rss/$1';
$route['weblog/(:any)/collections']                 = 'weblog/collection_navigation/$1';
$route['weblog/(:any)/collections/(:any)']          = 'weblog/collection_landing/$1/$2';
$route['weblog/(:any)/collections/(:any)/rss']      = 'weblog/rss/$1/collections/$2';
$route['weblog/(:any)/topics']                      = 'weblog/topic_navigation/$1';
$route['weblog/(:any)/topics/(:any)']               = 'weblog/topic_landing/$1/$2';
$route['weblog/(:any)/topics/(:any)/rss']           = 'weblog/rss/$1/topics/$2';
// $route['weblog/(:any)/post/(:any)']              = 'weblog/post/$1/$2';
// $route['weblog/(:any)/(:any)/(:num)']            = 'weblog/post/$1/$3';
$route['weblog/(:any)/(:any)~(:num)']               = 'weblog/post/$1/$3';
// $route['weblog/(:any)/amp/(:any)']               = 'weblog/amp/$1/$2';
$route['weblog/(:any)/(:any)~(:num)/amp']           = 'weblog/amp/$1/$3';
$route['weblog/(:any)/search']                      = 'weblog/search/$1';
$route['weblog/(:any)/random']                      = 'weblog/random/$1';
$route['weblog/(:any)/about']                       = 'weblog/about/$1';

// api/v1/pages/latest/:username?paginate_by=10&offset=5&page=1
// all, collection, topic
$route['api/v1/pages/(:any)/(:any)']                = 'api/pages/$1/$2';
$route['api/v1/pages/(:any)/(:any)/(:any)']         = 'api/pages/$1/$2/$3';
$route['api/v1/type-ahead/(:any)']                  = 'api/search/type_ahead/$1';
// api/v1/search/?q=home&paginate_by=12&offset=0&page=1
$route['api/v1/search/(:any)']                      = 'api/search/get/$1';

// SEO
// $route['sitemap\.xml']      = "seo/sitemap";


// custom domain

if (!is_cli())
{
    $domain = $_SERVER['HTTP_HOST']; // suvozit.com
    
    if (ENVIRONMENT === 'production' AND $domain != 'rime.co')
    // if ($domain == 'localhost'):
    {
        $route = [];

        $route['default_controller']    = 'weblog';
        $route['404_override']          = '';
        $route['translate_uri_dashes']  = TRUE;

        $route['rss']                                       = 'weblog/rss';
        $route['collections']                               = 'weblog/collection_navigation';
        $route['collections/(:any)']                        = 'weblog/collection_landing//$1';
        $route['collections/(:any)/rss']                    = 'weblog/rss//collections/$1';
        $route['topics']                                    = 'weblog/topic_navigation';
        $route['topics/(:any)']                             = 'weblog/topic_landing//$1';
        $route['topics/(:any)/rss']                         = 'weblog/rss//topics/$1';
        // $route['post/(:any)']                            = 'weblog/post//$1';
        // $route['(:any)/(:num)']                          = 'weblog/post//$2';
        $route['(:any)~(:num)']                             = 'weblog/post//$2';
        // $route['amp/(:any)']                             = 'weblog/amp//$1';
        $route['(:any)~(:num)/amp']                         = 'weblog/amp//$2';
        $route['search']                                    = 'weblog/search';
        $route['random']                                    = 'weblog/random';
        $route['about']                                     = 'weblog/about';

        $route['api/v1/pages/(:any)/(:any)']                = 'api/pages/$1/$2';
        $route['api/v1/pages/(:any)/(:any)/(:any)']         = 'api/pages/$1/$2/$3';
        $route['api/v1/type-ahead/(:any)']                  = 'api/search/type_ahead/$1';
        // api/v1/search/?q=home&paginate_by=12&offset=0&page=1
        $route['api/v1/search/(:any)']                      = 'api/search/get/$1';
    }
}