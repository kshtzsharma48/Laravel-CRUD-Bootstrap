<?php

class Lara_admin_LaraAdmin_Task
{
	public function run($arguments)
	{

	}

	public function setup($arguments)
	{
		$email = (isset($arguments[0])) ? $arguments[0] : "test@test.com";
		$password = (isset($arguments[1])) ? $arguments[1] : "password";
		$force = (isset($arguments[2])) ? true : false;

		$content = array();
		$content[] = "<?php ";
		$content[] = "return  array( ";
		$content[] = "'user'=>'" . $email . "', ";
		$content[] = "'password'=>'" . md5($password) . "' ";
		$content[] = "); ";
		$content[] = "?> ";
		$DS = DIRECTORY_SEPARATOR;

		$path = realpath(dirname(__FILE__) . $DS . ".." . $DS . "config") . $DS . "lara_admin.php";

		if ( ! file_exists($path)) {
			file_put_contents($path, implode(" \n ", $content));
		} else {
			if ($force == "force") {
				file_put_contents($path, implode(" \n ", $content));
			}
		}
	}

	public function resource($arguments)
	{
		$pluralize = new Laravel\Pluralizer(Laravel\Config::get('strings'));
		foreach ($arguments as $key => $model) {
			$content = array();
			$nameClass = ucwords($model);
			$content[] = "<?php namespace Admin;";
			$content[] = "class $nameClass extends Appmodel{";
			$content[] = "	public static \$table ='" . Str::plural($model) . "'; ";
			$content[] = "	public \$index= array(); ";
			$content[] = "	public \$new=array(); ";
			$content[] = "	public \$edit= array(); ";
			$content[] = "	public \$show= array(); ";
			$content[] = "	public \$rules= array(); ";
			$content[] = "}";

			$DS = DIRECTORY_SEPARATOR;
			$path = path('bundle') . $DS . "lara_admin" . $DS . "models" . $DS . strtolower($model) . ".php";

			if ( ! file_exists($path)) {
				file_put_contents($path, implode(" \n ", $content));
				echo "done";
			} else {
				echo " already exists. send force like third parameter";
			}
		}
	}

}