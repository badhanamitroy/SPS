<?php

/** @var \App\Core\Router $router */

// Language-prefixed routes
$router->get('/{lang}', 'HomeController@index', 'home');
$router->get('/{lang}/components', 'HomeController@componentsShowcase', 'components');

$router->get('/{lang}/about', 'SectionController@about', 'about');
$router->get('/{lang}/activities', 'SectionController@activities', 'activities');
$router->get('/{lang}/knowledge', 'SectionController@knowledge', 'knowledge');
$router->get('/{lang}/library', 'SectionController@library', 'library');
$router->get('/{lang}/blog', 'SectionController@blog', 'blog');
$router->get('/{lang}/get-involved', 'SectionController@getInvolved', 'get-involved');
$router->get('/{lang}/transparency', 'SectionController@transparency', 'transparency');
$router->get('/{lang}/contact', 'SectionController@contact', 'contact');
