<?php
$map = array();

// named routes
$map['root'] = array (
	'controller' => 'posts'
);
$map['login'] = array (
	'controller' => 'admin',
	'action' => 'login'
);
$map['logout'] = array (
	'controller' => 'admin',
	'action' => 'logout'
);

// temporary redirect routes.
$map['/login2'] = array (
	'location' => '/admin/login'
);

// default route.
$map['/:controller/:action/:id'] = array ();

// for paginate
$map['/:controller/:action/page/:page'] = array ('page' => '/^\d+$/');

// for search action of admin controller
$map['/:controller/search/:q/page/:page'] = array ('action' => 'search', 'page' => '/^\d+$/');

// unknow request.
$map['*path'] = array (
	'controller' => 'admin',
	'action' => 'test'
);
?>
