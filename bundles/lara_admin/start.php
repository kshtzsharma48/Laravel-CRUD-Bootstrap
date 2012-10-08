<?php


/*
|--------------------------------------------------------------------------
| Admin Models
|--------------------------------------------------------------------------
|
| Map Models  using PSR-0 standard namespace. 
 */
Autoloader::directories(array(
    path('bundle').'lara_admin/libraries',
));

Autoloader::namespaces(array(
	'Admin'   => Bundle::path('lara_admin').'models',
));


Autoloader::map(array(
	'Lara_admin_Controller' => path('bundle').'/lara_admin/controllers/lara_admin.php',
	'LaraAdmin' => path('bundle').'/lara_admin/lara_admin.php'
));

LaraAdmin::make();