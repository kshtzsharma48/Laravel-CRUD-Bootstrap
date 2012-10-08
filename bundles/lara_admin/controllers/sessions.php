<?php

class lara_admin_sessions_controller extends Lara_admin_Controller
{
	var $layout = "lara_admin::layouts.sessions.default";

	public function __construct()
	{
		parent::__construct(); // Our layout will still be instantiated now.
		$this->filter('before', 'auth')->except(array("index", "destroy", "create"));
	}

	public function action_index()
	{
		$view = View::make("lara_admin::sessions.index");
		$this->layout->content = $view;

		return $this->layout;
	}

	public function action_destroy()
	{
		Session::forget("token_user");

		return Redirect::to("/lara_admin/login");
	}

	public function action_create()
	{
		$login = Input::get("login");
		$password = md5(Input::get("password"));

		if ($login == Config::get("lara_admin::lara_admin.user") && $password == Config::get("lara_admin::lara_admin.password")) {
			Session::put('token_user', md5("12324564567wefsdfsdf" . Config::get("bundle::lara_admin.user")));

			return Redirect::to("/lara_admin/models");
		} else {
			return Redirect::to("/lara_admin/login");
		}
	}

}