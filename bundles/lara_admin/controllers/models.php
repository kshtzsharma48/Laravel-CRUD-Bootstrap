<?php

class lara_admin_models_controller extends Lara_admin_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->filter('before', 'validate_admin');
	}

	public function action_index($modelName)
	{
		$model = $this->getClassObject($modelName);
		$columnModel = $model::first();
		Input::flash();
		if (Input::get($modelName) != null && $this->addConditions($model, $modelName)->first() != null) {
			$columnModel = $this->addConditions($model, $modelName)->first();
		}

		if ($columnModel == null) {
			return Redirect::to("/lara_admin/models/$modelName/new");
		}

		$columns = $columnModel->columns();
		$sort_options = $this->setOrderOptions($columns);
		$models = $this->addConditions($model, $modelName)->order_by($sort_options["column_order"], $sort_options["sort_direction"])->paginate($model->perPage);

		$request_uri = Request::server("REQUEST_URI");
		$request_uri = preg_replace("/&order=[^&]*/", "", $request_uri);

		if (!preg_match("/\?/i", Request::server("REQUEST_URI"))) {
			$request_uri .= "?";
		}

		//TODO function getCustomAction
		$name_custom_action = "lara_admin::" . Str::plural(Str::lower($modelName)) . "." . preg_replace("/action_/", "", __FUNCTION__);

		if (View::exists($name_custom_action) != false) {
			$view = View::make(
				$name_custom_action,
				array(
					"sort_options" => $sort_options,
					"request_uri" => $request_uri,
					"modelName" => $modelName,
					"modelInstance" => $model,
					"models" => $models,
					"columns" => $columns
				)
			);
		} else {
			$view = View::make(
				"lara_admin::models.index",
				array(
					"sort_options" => $sort_options,
					"request_uri" => $request_uri,
					"modelName" => $modelName,
					"modelInstance" => $model,
					"models" => $models,
					"columns" => $columns
				)
			);
		}

		$this->defaultAttrForLayout($modelName, $view);

		return $this->response_with(array("xml", "json", "csv"), $this->collectionToArray($models->results), true);
	}

	public function action_edit($modelName, $model_id)
	{
		$definitionClassname = "Admin\\$modelName";
		$model = $definitionClassname::find($model_id);
		$view = View::make("lara_admin::models.edit", array("model" => $model, "modelName" => $modelName));
		$this->defaultAttrForLayout($modelName, $view);
	}

	public function action_new($modelName)
	{
		$model = $this->getClassObject($modelName);
		$view = View::make("lara_admin::models.new", array("model" => $model, "modelName" => $modelName));
		$this->defaultAttrForLayout($modelName, $view);

		return $this->layout;
	}

	public function action_create($modelName)
	{
		$definitionClassname = "Admin\\$modelName";
		$model = new $definitionClassname(Input::get($modelName));

		if (!$model->validate()) {
			$view = View::make("lara_admin::models.new", array("model" => $model, "modelName" => $modelName));
			$this->defaultAttrForLayout($modelName, $view);

			return $this->layout;
		}

		$model = $this->uploadFiles($model, $modelName);
		$model->save();

		return Redirect::to("/lara_admin/models/$modelName");
	}

	public function action_destroy($modelName, $model_id)
	{
		$definitionClassname = "Admin\\$modelName";
		$model = $definitionClassname::find($model_id);
		$model->delete();

		return Redirect::to("/lara_admin/models/$modelName");
	}


	public function action_show($modelName, $model_id)
	{
		$definitionClassname = "Admin\\$modelName";
		$model = $definitionClassname::find($model_id);
		$view = View::make("lara_admin::models.show", array("model" => $model, "modelName" => $modelName));
		$this->defaultAttrForLayout($modelName, $view);
	}

	public function action_update($modelName, $model_id)
	{
		$definitionClassname = "Admin\\$modelName";
		$model = $definitionClassname::find(strtolower($model_id));
		$model->fill(Input::get($modelName));

		if ( ! $model->validate()) {
			$view = View::make("lara_admin::models.edit", array("model" => $model, "modelName" => $modelName));
			$this->defaultAttrForLayout($modelName, $view);

			return $this->layout;
		}

		$model = $this->uploadFiles($model, $modelName);
		$model->save();

		return Redirect::to("/lara_admin/models/$modelName");
	}

	public function action_dashboard()
	{
		$view = View::make("lara_admin::models.dashboard");
		$this->defaultAttrForLayout("Dashboard", $view);

		return $this->layout;
	}


	/**
	+++Method Helpers
	 */

	private function uploadFiles($model, $modelName)
	{
		$attributes = $this->fileAttributes($model->edit);

		foreach ($attributes as $key => $attribute) {
			$name = InputFactory::getName($key, $attribute);
			$imagePath = FileHelper::upload($model, $modelName, $name, $attribute, true);
			if ($imagePath !== false) {
				$model->$name = $imagePath["fileName"];
			}
		}

		return $model;
	}

	private function fileAttributes($attributes)
	{
		$searchFunc = function ($value) {
			return isset($value['type']) && $value['type'] === 'file';
		};

		return array_filter($attributes, $searchFunc);
	}

	public function addConditions($model, $modelName)
	{
		if (Input::get($modelName) != null) {
			foreach (Input::get($modelName) as $key => $value) {
				if (!empty($value) && preg_match("/(_gte|_lte)/", $key) === false) {
					$model = $model->where($key, "like", "%$value%");
				} elseif (!empty($value) && preg_match("/(_gte|_lte)/", $key)) {
					$model = $this->createConditionRange($model, $key, $value);
				} elseif (!empty($value)) {
					$model = $model->where($key, "like", "%$value%");
				}
			}
		}

		if (isset($model->defaultFilters) && count($model->defaultFilters) > 0) {
			$model = $model->addFilters($model->defaultFilters);
		}

		if (Input::get("created_at_gte") != null) {
			$model = $model->where("created_at", ">=", Input::get("created_at_gte"));
		}
		if (Input::get("created_at_lte") != null) {
			$model = $model->where("created_at", "<=", Input::get("created_at_lte"));
		}

		return $model;
	}


	public function createConditionRange($model, $id, $value)
	{
		$condition = "<=";
		if (preg_match("/_gte/", $id)) {
			$condition = ">=";
		}

		return $model->where(preg_replace("/(_gte|_lte)/", "", $id), $condition, $value);
	}


	public function getClassObject($modelName)
	{
		$this->definitionClassname = "Admin\\$modelName";

		return new $this->definitionClassname();
	}


	public function setOrderOptions($columns)
	{
		$sort_options = array();
		if (Input::get("order") != null && Input::get("order") != "") {
			if (preg_match("/_desc/", Input::get("order"))) {
				$sort_options["sort_direction"] = "desc";
				$sort_options["sort_invert"] = "asc";
				$sort_options["column_order"] = str_replace("_desc", "", Input::get("order"));
			} else {
				$sort_options["sort_invert"] = "desc";
				$sort_options["sort_direction"] = "asc";
				$sort_options["column_order"] = str_replace("_asc", "", Input::get("order"));
			}
		} else {
			if (isset($columns) && isset($columns[0])) {

				$sort_options["column_order"] = $columns[0]->key;
				$sort_options["sort_direction"] = "asc";
				$sort_options["sort_invert"] = "desc";
			}
		}

		return $sort_options;
	}

}