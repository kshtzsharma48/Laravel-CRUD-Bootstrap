<?php

class InputFactory
{
	public static function build($id, $attributes, $modelName, $model)
	{
		$name = static::getName($id, $attributes);
		$title = static::getTitle($id, $attributes);
		$label = static::generateLabel($name, $title);
		$input = static::generateInput($name, $attributes, $modelName, $model);

		return compact("label", "input");
	}

	public static function getName($id, $attributes)
	{
		$name = $id;
		if (is_numeric($id)) {
			$name = $attributes;
		}

		return $name;
	}

	public static function isFile($attributes)
	{
		return isset($attributes['type']) && $attributes['type'] === 'file';
	}

	public static function getTitle($id, $attributes)
	{
		$title = $id;
		if (is_numeric($id)) {
			$title = $attributes;
		}

		if (is_array($attributes) && isset($attributes["title"])) {
			$title = $attributes["title"];
		}

		if (!is_numeric($id) && isset($attributes["required"]) && $attributes["required"] == true) {
			$title .= '<abbr title=required">*</abbr>';
		}

		return $title;
	}

	private static function generateLabel($name, $title)
	{
		return "<label for='$name'>" . ucwords(str_replace("_", " ", $title)) . "</label>";
	}

	private static function generateInput($id, $attributes, $modelName, $model)
	{
		$field = "";
		$attributes_input = static::getAttributesInput(strtolower($id), $attributes, $modelName);

		switch ($attributes_input["type"]) {
			case 'checkbox':
				$field = static::generateCheckbox($id, $attributes_input, $model);
				break;
			case 'dropDown':
				$field = static::generateDropDown($id, $attributes_input, $model, $attributes);
				break;
			case 'file':
				$field = static::generateFile($id, $attributes_input, $model);
				break;
			case 'password':
				$field = static::generatePassword($id, $attributes_input, $model, $attributes);
				break;
			case 'textarea':
				$field = static::generateTextArea($id, $attributes_input, $model, $attributes);
				break;
			case 'radioButton':
				$field = static::generateRadioButton($id, $attributes_input, $model, $attributes);
				break;
			default:
				$field = static::generateText($id, $attributes_input, $model, $attributes);
				break;
		}

		return $field;
	}

	private static function getAttributesInput($attribute_name, $attributes, $modelName)
	{
		$attributeInput = array();
		$attributeInput["name"] = $modelName . "[" . $attribute_name . "]";
		$attributeInput["id"] = strtolower($modelName) . "_" . strtolower($attribute_name);

		if ( ! is_array($attributes)) {
			return array_merge($attributeInput, array("type" => "text"));
		}

		$attributeInput["type"] = (isset($attributes["type"]) && !empty($attributes["type"])) ? $attributes["type"] : "text";

		return $attributeInput;
	}


//CAMBIO
	private static function generateText($id, $attributes_input, $model, $attributes)
	{
		if (is_string($attributes)) {
			$attributes = $attributes_input;
		}

		if (isset($attributes["dateOptions"])) {
			unset($attributes["dateOptions"]);
		}

		$field = array();
		$field[] = Form::input($attributes_input["type"], $attributes_input["name"], $model->$id, $attributes);
		$field[] = '<span class="error"> ' . $model->first_error(strtolower($id)) . '</span>';

		return join($field);
	}

	private static function generatePassword($id, $attributes_input, $model, $attributes)
	{
		if (is_string($attributes)) {
			$attributes = $attributes_input;
		}

		$field = array();
		$field[] = Form::password($attributes_input["name"], $attributes);
		$field[] = '<span class="error"> ' . $model->first_error(strtolower($id)) . '</span>';

		return join($field);
	}

	private static function  generateRadioButton($id, $attributes_input, $model, $attributes)
	{
		$radioOptions = array();

		if ( ! isset($attributes["radioButtonOption"])) {
			throw new Exception("radioButtonOption is required if you want to create a radioButton input", 1);
		}

		$radioButtonOption = $attributes["radioButtonOption"];

		if (is_array($radioButtonOption["options"])) {
			foreach ($radioButtonOption["options"] as $keyOption => $option) {
				$value = $keyOption;
				$title = $option;

				unset($attributes["radioButtonOption"]);

				if ($value == $model->$id) {
					$activeOption = true;
				} else {
					$activeOption = false;
				}

				$test = Form::radio(
					$attributes_input["name"],
					$value,
					$activeOption,
					$attributes
				) . "<span> $title </span>";
				array_push($radioOptions, $test);
			}
		}

		return implode(" ", $radioOptions);
	}

	private static function generateTextArea($id, $attributes_input, $model, $attributes)
	{
		if (is_string($attributes)) {
			$attributes = $attributes_input;
		}

		$field = array();
		$field[] = Form::textarea($attributes_input["name"], $model->$id, $attributes);
		$field[] = '<span class="error"> ' . $model->first_error(strtolower($id)) . '</span>';

		return join($field);
	}

	private static function  generateCheckbox($id, $attributes_input, $model)
	{
		$checkboxInput[] = "<input name='" . $attributes_input["name"] . "' type='hidden' value='0' />";
		$checkboxInput[] = Form::checkbox($attributes_input["name"], 1, $model->$id, $attributes_input);
		$checkboxInput[] = "<span class='error'>" . $model->first_error(strtolower($id)) . "</span>";

		return join($checkboxInput);
	}

	private static function generateFile($id, $attributes_input, $model)
	{
		$field = array();
		unset($attributes_input["name"]);
		$field[] = Form::file($id, $attributes_input);
		$field[] = '<span class="error"> ' . $model->first_error(strtolower($id)) . '</span>';

		return join($field);
	}


	private static function  generateDropDown($id, $attributes_input, $model, $attributes)
	{
		$blankTitle = null;
		$valueField = "id";
		$textField = null;

		if (!isset($attributes["dropDownOptions"])) {
			throw new Exception("DropDownOption is required if you want to create a dropDown input", 1);
		}

		$dropDownOptions = $attributes["dropDownOptions"];

		if (isset($dropDownOptions["blankLabel"])) {
			$blankTitle = $dropDownOptions["blankLabel"];
		}

		if (array_key_exists("options", $dropDownOptions) && is_array($dropDownOptions["options"])) {
			$listOptions = array();
			if (isset($blankTitle)) {
				$listOptions[""] = $blankTitle;
			}
			$listOptions = array_merge($listOptions, $dropDownOptions["options"]);
		} elseif (array_key_exists("optionsFromMethod", $dropDownOptions) && is_array($dropDownOptions["optionsFromMethod"])) {
			$optionCustom = $dropDownOptions["optionsFromMethod"];
			$className = $optionCustom[0];
			$functionName = $optionCustom[1];

			$definitionClassname = "Admin\\$className";
			$modelDropDown = new $definitionClassname();

			$listOptions = $modelDropDown->$functionName();
		} else {
			static::validateDataForDropDown($attributes);

			if (isset($dropDownOptions["valueField"]) && !empty($dropDownOptions["valueField"])) {
				$valueField = $dropDownOptions["valueField"];
			}

			$textField = $dropDownOptions["titleField"];
			$dropDownClassName = $dropDownOptions["class"];

			$definitionClassname = "Admin\\$dropDownClassName";
			$modelDropDown = new $definitionClassname();

			if (isset($dropDownOptions["filters"])) {
				$modelDropDown = $modelDropDown->addFilters($dropDownOptions["filters"]);
			}

			$listOptions = SelectHelper::generateOptions($modelDropDown->get(), $textField, $valueField, $blankTitle);
		}

		return Form::select($attributes_input["name"], $listOptions, $model->$id);
	}

	private static function  validateDataForDropDown($attributes)
	{
		if (!isset($attributes["dropDownOptions"])) {
			throw new Exception("DropDownOption is required if you want to create a dropDown input", 1);
		}

		if (!isset($attributes["dropDownOptions"]["class"])) {
			throw new Exception("Not Found Property dropDownClass", 1);
		}

		if (!isset($attributes["dropDownOptions"]["titleField"])) {
			throw new Exception("Not Found Property titleField", 1);
		}
	}

}