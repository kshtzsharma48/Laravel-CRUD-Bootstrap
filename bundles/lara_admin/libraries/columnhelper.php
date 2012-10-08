<?php

class ColumnHelper
{
	public $key;
	public $title;
	public $caption;

	public function __construct($key, $title = null, $descripcion = "")
	{
		$this->caption = $descripcion;
		$this->title = $title;
		$this->key = $key;

		if (!isset($title)) {
			$this->title = $key;
		}
	}

}