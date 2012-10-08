<?php namespace Admin;

class Appmodel extends \Eloquent
{
	public $errors = array();
	public $save = array();
	public $filters = array();
	public $defaultFilters = array();
	public $validator;
	public $perPage = null;
	public $confirmationDeleteText = "Are you sure you want to delete it?";
	private $overloadingData = array();

	public function validate()
	{
		$this->validator = \Validator::make($this->attributes, $this->rules);

		return $this->validator->passes();
	}


	public function first_error($field)
	{
		return (isset($this->validator->errors)) ? $this->validator->errors->first($field) : "";
	}

	public function saveData()
	{
		$options = $this->edit;

		if (count($this->new) > 0) {
			$options = $this->new;
		}

		return $options;
	}

	public function columns()
	{
		$columns = array();

		if (count($this->index) <= 0) {
			$columnKeys = array_keys((array)$this->attributes);

			foreach ($columnKeys as $key) {
				array_push($columns, new \ColumnHelper($key));
			}
		} else {
			foreach ($this->index as $key => $options) {
				if (is_numeric($key)) {
					$column = new \ColumnHelper($options);
				} else {
					$caption = isset($options["caption"]) ? $options["caption"] : null;
					$title = isset($options["title"]) ? $options["title"] : null;
					$column = new \ColumnHelper($key, $title, $caption);
				}
				array_push($columns, $column);
			}

		}

		return $columns;
	}


	public function addFilters($filters)
	{
		$model = $this;

		foreach ($filters as $nameFilter) {
			$model = $this->matchFilter($nameFilter);
		}

		return $model;
	}

	private function matchFilter($nameFilter)
	{
		if (isset($this->filters[$nameFilter]) && !empty($this->filters[$nameFilter])) {
			$filter = $this->filters[$nameFilter];

			if (count($filter) != 3) {
				throw new Exception("The paramaters for filter must be 3 (like where function)", 1);
			}

			$column = $filter[0];
			$operator = $filter[1];
			$value = $filter[2];

			return $this->where($column, $operator, $value);
		}

		return $this;
	}

	public function __call($method, $arguments)
	{

		if (in_array($method, array_keys($this->filters))) {
			return $model = $this->matchFilter($method);
		}

		return parent::__call($method, $arguments);
	}

}
  