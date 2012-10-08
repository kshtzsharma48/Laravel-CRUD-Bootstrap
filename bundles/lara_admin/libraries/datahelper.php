<?php

class DataHelper
{
	public static function get($model, $key, $display = "both")
	{
		$data = $model->$key;
		$data = static::addBelongsToInformation($model, $key, $display, $data);
		$data = static::addEllipsisStyle($model, $key, $display, $data);

		return $data;
	}

	public static function addBelongsToInformation($model, $key, $display, $data)
	{
		if (isset($model->belongModels) && array_key_exists($key, $model->belongModels)) {
			$belongData = $model->belongModels[$key];

			if ( ! isset($belongData["showon"]) || static::isValidView($belongData, $display)) {
				if ( ! isset($belongData["model"])) {
					throw new Exception("Model Attribute not Found in belongsModels for $key", 1);
				}
				if ( ! isset($belongData["column"])) {
					throw new Exception("Column Attribute not Found in belongsModels for $key", 1);
				}

				$column = $belongData["column"];
				$modelName = ucwords($belongData["model"]);
				$belongModel = $model->belongs_to("Admin\\$modelName", $key)->first();

				if (isset($belongModel)) {
					$data = $belongModel->$column;
				} else {
					if (isset($belongData["alternative_text"])) {
						$data = $belongData["alternative_text"];
					}
				}
			}
		}

		return $data;
	}

	public static function addEllipsisStyle($model, $key, $display, $data)
	{
		if (isset($model->ellipsisTextColumns[$key])) {
			$ellipsisOptions = $model->ellipsisTextColumns[$key];
			$widthEllipsis = "200px";

			if (isset($ellipsisOptions["width"])) {
				$widthEllipsis = $ellipsisOptions["width"];
			}

			if (!isset($ellipsisOptions["showon"]) || static::isValidView($ellipsisOptions, $display)) {
				$data = "<p class='ellipsis' style='width:$widthEllipsis;'> $data </p>";
			}
		}

		return $data;
	}

	public static function isValidView($configOptions, $option)
	{
		return (isset($configOptions["showon"]) && ($configOptions["showon"] == "both" || $configOptions["showon"] == $option));
	}

}