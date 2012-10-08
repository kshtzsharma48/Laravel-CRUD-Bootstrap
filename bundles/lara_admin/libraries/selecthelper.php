<?php

class SelectHelper
{
	public static function generateOptions($models, $keyValue, $keyIndex = "id", $emptyTitle = null)
	{
		$options = array();

		if (!isset($models) && count($models) == 0) {
			throw new  Exception("Error: Empty List", 1);
		}

		if (isset($emptyTitle)) {
			$options[""] = $emptyTitle;
		}

		foreach ($models as $key => $model) {
			$options[$model->$keyIndex] = $model->$keyValue;
		}

		return $options;
	}

}