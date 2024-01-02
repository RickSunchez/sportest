<?php
defined('DELORIUS') or die('access denied');

$router['admin'] = array(
    '/',
    array('_controller' => 'CMS:Admin:Register:list')
);

$router['admin_register'] = array(
    '/register/{action}',
    array('_controller' => 'CMS:Admin:Register:{action}')
);

$router['admin_cache_clean'] = array(
    '/cache_clean/',
    array('_controller' => 'CMS:Admin:Home:cleanCache')
);

$router['admin_cache_clean_thumb'] = array(
    '/cache_clean_thumb/',
    array('_controller' => 'CMS:Admin:Home:cleanCacheThumb')
);

$router['admin_cache_clean_theme'] = array(
    '/cache_clean_theme/',
    array('_controller' => 'CMS:Admin:Home:cleanCacheTheme')
);

$router['admin_sitemaps_create'] = array(
    '/sitemaps_create/',
    array('_controller' => 'CMS:Admin:Home:createSitemaps')
);

$router['admin_sitemaps_clear'] = array(
    '/sitemaps_clear/',
    array('_controller' => 'CMS:Admin:Home:clearSitemaps')
);

$router['admin_logout'] = array(
    '/logout',
    array('_controller' => 'CMS:Admin:Authorized:logout')
);

$router['admin_login_data'] = array(
    '/login_admin',
    array('_controller' => 'CMS:Admin:Authorized:loginData')
);

$router['admin_root_data'] = array(
    '/root/{action}.data',
    array('_controller' => 'CMS:Admin:Root:{action}Data', 'action' => 'list'),
    array('action' => '\w+')
);

$router['admin_root'] = array(
    '/root/{action}',
    array('_controller' => 'CMS:Admin:Root:{action}', 'action' => 'list'),
    array('action' => '\w+')
);

$router['admin_page_data'] = array(
    '/pages/{action}.data',
    array('_controller' => 'CMS:Admin:Page:{action}Data'),
    array('action' => '\w+')
);

$router['admin_page'] = array(
    '/pages/{action}',
    array('_controller' => 'CMS:Admin:Page:{action}', 'action' => 'list'),
    array('action' => '\w+')
);

$router['admin_user_data'] = array(
    '/users/{action}.data',
    array('_controller' => 'CMS:Admin:Users:{action}Data'),
    array('action' => '\w+')
);

$router['admin_user'] = array(
    '/users/{action}',
    array('_controller' => 'CMS:Admin:Users:{action}', 'action' => 'list'),
    array('action' => '\w+')
);

$router['admin_attr_data'] = array(
    '/user_attr/{action}.data',
    array('_controller' => 'CMS:Admin:Attr:{action}Data'),
    array('action' => '\w+')
);

$router['admin_attr'] = array(
    '/user_attr/{action}',
    array('_controller' => 'CMS:Admin:Attr:{action}', 'action' => 'list'),
    array('action' => '\w+')
);

$router['admin_go_data'] = array(
    '/go/{action}.data',
    array('_controller' => 'CMS:Admin:Go:{action}Data'),
    array('action' => '\w+')
);

$router['admin_go'] = array(
    '/go/{action}',
    array('_controller' => 'CMS:Admin:Go:{action}', 'action' => 'list'),
    array('action' => '\w+')
);

$router['admin_delivery_data'] = array(
    '/delivery/{action}.data',
    array('_controller' => 'CMS:Admin:Delivery:{action}Data'),
    array('action' => '\w+')
);

$router['admin_delivery'] = array(
    '/delivery/{action}',
    array('_controller' => 'CMS:Admin:Delivery:{action}', 'action' => 'list'),
    array('action' => '\w+')
);

$router['admin_subscriber_data'] = array(
    '/subscriber/{action}.data',
    array('_controller' => 'CMS:Admin:Subscriber:{action}Data'),
    array('action' => '\w+')
);

$router['admin_subscriber'] = array(
    '/subscriber/{action}',
    array('_controller' => 'CMS:Admin:Subscriber:{action}', 'action' => 'list'),
    array('action' => '\w+')
);

$router['admin_subscription_data'] = array(
    '/subscription/{action}.data',
    array('_controller' => 'CMS:Admin:Subscription:{action}Data'),
    array('action' => '\w+')
);

$router['admin_subscription'] = array(
    '/subscription/{action}',
    array('_controller' => 'CMS:Admin:Subscription:{action}', 'action' => 'list'),
    array('action' => '\w+')
);

$router['admin_event_data'] = array(
    '/event/{action}.data',
    array('_controller' => 'CMS:Admin:Event:{action}Data'),
    array('action' => '\w+')
);

$router['admin_event'] = array(
    '/event/{action}',
    array('_controller' => 'CMS:Admin:Event:{action}', 'action' => 'list'),
    array('action' => '\w+')
);

$router['admin_video_data'] = array(
    '/video/{action}.data',
    array('_controller' => 'CMS:Admin:Video:{action}Data'),
    array('action' => '\w+')
);

$router['admin_video'] = array(
    '/video/{action}',
    array('_controller' => 'CMS:Admin:Video:{action}', 'action' => 'list'),
    array('action' => '\w+')
);

$router['admin_news_data'] = array(
    '/news/{action}.data',
    array('_controller' => 'CMS:Admin:News:{action}Data'),
    array('action' => '\w+')
);

$router['admin_news'] = array(
    '/news/{action}',
    array('_controller' => 'CMS:Admin:News:{action}', 'action' => 'list'),
    array('action' => '\w+')
);

$router['admin_question_data'] = array(
    '/question/{action}.data',
    array('_controller' => 'CMS:Admin:Question:{action}Data'),
    array('action' => '\w+')
);

$router['admin_question'] = array(
    '/question/{action}',
    array('_controller' => 'CMS:Admin:Question:{action}', 'action' => 'list'),
    array('action' => '\w+')
);

$router['admin_review_data'] = array(
    '/review/{action}.data',
    array('_controller' => 'CMS:Admin:Review:{action}Data'),
    array('action' => '\w+')
);

$router['admin_review'] = array(
    '/review/{action}',
    array('_controller' => 'CMS:Admin:Review:{action}', 'action' => 'list'),
    array('action' => '\w+')
);

$router['admin_article_data'] = array(
    '/article/{action}.data',
    array('_controller' => 'CMS:Admin:Article:{action}Data'),
    array('action' => '\w+')
);

$router['admin_article'] = array(
    '/article/{action}',
    array('_controller' => 'CMS:Admin:Article:{action}', 'action' => 'list'),
    array('action' => '\w+')
);

$router['admin_doc_data'] = array(
    '/doc/{action}.data',
    array('_controller' => 'CMS:Admin:Document:{action}Data'),
    array('action' => '\w+')
);

$router['admin_doc'] = array(
    '/doc/{action}',
    array('_controller' => 'CMS:Admin:Document:{action}', 'action' => 'list'),
    array('action' => '\w+')
);

$router['admin_gallery_data'] = array(
    '/gallery/{action}.data',
    array('_controller' => 'CMS:Admin:Gallery:{action}Data'),
    array('action' => '\w+')
);

$router['admin_gallery'] = array(
    '/gallery/{action}',
    array('_controller' => 'CMS:Admin:Gallery:{action}', 'action' => 'list'),
    array('action' => '\w+')
);

$router['admin_analytics_data'] = array(
    '/analytics/{action}.data',
    array('_controller' => 'CMS:Admin:Generator:{action}AnalyticsData'),
    array('action' => '\w+')
);

$router['admin_analytics'] = array(
    '/analytics/{action}',
    array('_controller' => 'CMS:Admin:Generator:{action}Analytics', 'action' => 'list'),
    array('action' => '\w+')
);

$router['admin_robots_data'] = array(
    '/robots/{action}.data',
    array('_controller' => 'CMS:Admin:Generator:{action}RobotsData'),
    array('action' => '\w+')
);

$router['admin_robots'] = array(
    '/robots/{action}',
    array('_controller' => 'CMS:Admin:Generator:{action}Robots', 'action' => 'list'),
    array('action' => '\w+')
);

$router['admin_callback_data'] = array(
    '/callback/{action}.data',
    array('_controller' => 'CMS:Admin:Callback:{action}Data'),
    array('action' => '\w+')
);

$router['admin_callback'] = array(
    '/callback/{action}',
    array('_controller' => 'CMS:Admin:Callback:{action}', 'action' => 'list'),
    array('action' => '\w+')
);

$router['admin_migration'] = array(
    '/migration/{action}',
    array('_controller' => 'CMS:Admin:Migration:{action}', 'action' => 'index'),
    array('action' => '\w+')
);

$router['admin_acl_data'] = array(
    '/security/{action}.data',
    array('_controller' => 'CMS:Admin:Security:{action}Data'),
    array('action' => '\w+')
);

$router['admin_acl'] = array(
    '/security/{action}',
    array('_controller' => 'CMS:Admin:Security:{action}', 'action' => 'list'),
    array('action' => '\w+')
);

$router['admin_help_im_data'] = array(
    '/help/im/{action}.data',
    array('_controller' => 'CMS:Admin:HelpDesk:{action}Data'),
    array('action' => '\w+')
);

$router['admin_help_im'] = array(
    '/help/im/{action}',
    array('_controller' => 'CMS:Admin:HelpDesk:{action}', 'action' => 'list'),
    array('action' => '\w+')
);

$router['admin_banner_data'] = array(
    '/banners/{action}.data',
    array('_controller' => 'CMS:Admin:Banners:{action}Data'),
    array('action' => '\w+')
);

$router['admin_banner'] = array(
    '/banners/{action}',
    array('_controller' => 'CMS:Admin:Banners:{action}', 'action' => 'list'),
    array('action' => '\w+')
);

$router['admin_cms_category_data'] = array(
    '/category/{action}.data',
    array('_controller' => 'CMS:Admin:Category:{action}Data'),
    array('action' => '\w+')
);

$router['admin_cms_category'] = array(
    '/category/{action}',
    array('_controller' => 'CMS:Admin:Category:{action}'),
    array('action' => '\w+')
);

$router['admin_poll_data'] = array(
    '/polls/{action}.data',
    array('_controller' => 'CMS:Admin:Polls:{action}Data'),
    array('action' => '\w+')
);

$router['admin_poll'] = array(
    '/polls/{action}',
    array('_controller' => 'CMS:Admin:Polls:{action}', 'action' => 'list'),
    array('action' => '\w+')
);

$router['admin_image_data'] = array(
    '/images/{action}.data',
    array('_controller' => 'CMS:Admin:Image:{action}Data'),
    array('action' => '\w+')
);

$router['admin_image'] = array(
    '/images/{action}',
    array('_controller' => 'CMS:Admin:Image:{action}', 'action' => 'list'),
    array('action' => '\w+')
);

$router['admin_file_data'] = array(
    '/file/{action}.data',
    array('_controller' => 'CMS:Admin:File:{action}Data'),
    array('action' => '\w+')
);

$router['admin_comment_data'] = array(
    '/comment/{action}.data',
    array('_controller' => 'CMS:Admin:Comment:{action}Data'),
    array('action' => '\w+')
);

$router['admin_fileIndex_data'] = array(
    '/fileIndex/{action}.data',
    array('_controller' => 'CMS:Admin:FileIndex:{action}Data'),
    array('action' => '\w+')
);

$router['admin_fileIndex'] = array(
    '/fileIndex/{action}',
    array('_controller' => 'CMS:Admin:FileIndex:{action}', 'action' => 'list'),
    array('action' => '\w+')
);

$router['admin_slider_data'] = array(
    '/slider/{action}.data',
    array('_controller' => 'CMS:Admin:Slider:{action}Data'),
    array('action' => '\w+')
);

$router['admin_slider'] = array(
    '/slider/{action}',
    array('_controller' => 'CMS:Admin:Slider:{action}'),
    array('action' => '\w+')
);

$router['admin_menu_data'] = array(
    '/menu/{action}.data',
    array('_controller' => 'CMS:Admin:Menu:{action}Data'),
    array('action' => '\w+')
);

$router['admin_menu'] = array(
    '/menu/{action}',
    array('_controller' => 'CMS:Admin:Menu:{action}', array('action' => 'list')),
    array('action' => '\w+')
);

$router['admin_country_data'] = array(
    '/country/{action}.data',
    array('_controller' => 'CMS:Admin:Country:{action}Data'),
    array('action' => '\w+')
);

$router['admin_country'] = array(
    '/country/{action}',
    array('_controller' => 'CMS:Admin:Country:{action}', array('action' => 'list')),
    array('action' => '\w+')
);

$router['admin_city_data'] = array(
    '/city/{action}.data',
    array('_controller' => 'CMS:Admin:City:{action}Data'),
    array('action' => '\w+')
);

$router['admin_city'] = array(
    '/city/{action}',
    array('_controller' => 'CMS:Admin:City:{action}', 'action' => 'list'),
    array('action' => '\w+')
);

$router['admin_tags_data'] = array(
    '/tags/{action}.data',
    array('_controller' => 'CMS:Admin:Tags:{action}Data'),
    array('action' => '\w+')
);

$router['admin_tags'] = array(
    '/tags/{action}',
    array('_controller' => 'CMS:Admin:Tags:{action}', 'action' => 'list'),
    array('action' => '\w+')
);

$router['admin_tmp_data'] = array(
    '/seo/template/{action}.data',
    array('_controller' => 'CMS:Admin:TemplateSEO:{action}Data'),
    array('action' => '\w+')
);

$router['admin_tmp'] = array(
    '/seo/template/{action}',
    array('_controller' => 'CMS:Admin:TemplateSEO:{action}', array('action' => 'list')),
    array('action' => '\w+')
);

$router['admin_search'] = array(
    '/seo/search/{action}',
    array('_controller' => 'CMS:Admin:SearchSEO:{action}', array('action' => 'list')),
    array('action' => '\w+')
);

return $router;
