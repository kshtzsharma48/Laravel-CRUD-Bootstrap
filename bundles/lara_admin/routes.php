<?php

//REST MODELS
Route::get(array('(:bundle)/models/(:any).(json|xml)', '(:bundle)/models/(:any)'), 'lara_admin::models@index');
Route::get('(:bundle)/models/(:any)/(:num)/edit', 'lara_admin::models@edit');
Route::get('(:bundle)/models/(:any)/(:num)', 'lara_admin::models@show');
Route::get('(:bundle)/models/(:any)/new', 'lara_admin::models@new');
Route::get('(:bundle)/models', 'lara_admin::models@dashboard');

Route::post('(:bundle)/models/(:any)/(:num)', 'lara_admin::models@update');
Route::post('(:bundle)/models/(:any)', 'lara_admin::models@create');
Route::post('(:bundle)/models/(:any)/(:num)/delete', 'lara_admin::models@destroy');

//USERS
Route::get('(:bundle)/login', 'lara_admin::sessions@index');
Route::post('(:bundle)/sign_in', 'lara_admin::sessions@create');
Route::get('(:bundle)/logout', 'lara_admin::sessions@destroy');

//HOME
Route::get('(:bundle)', 'lara_admin::models@dashboard');

Route::filter(
	'validate_admin',
	function () {
		if (Session::has("token_user") == false) {
			return Redirect::to("/lara_admin/login");
		}
	}
);