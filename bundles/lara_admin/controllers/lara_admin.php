<?php

class Lara_admin_Controller extends Controller
{
	var $layout = "lara_admin::layouts.default";

	/**
	 * Catch-all method for requests that can't be matched.
	 *
	 * @param  string    $method
	 * @param  array     $parameters
	 * @return Response
	 */
	public function __call($method, $parameters)
	{
		return Response::error('404');
	}

	public function __construct()
	{
		parent::__construct(); // Our layout will still be instantiated now.
	}


	public function collectionToArray($collection)
	{
		$list = array();
		foreach ($collection as $key => $model) {
			$list[get_class($model) . $key] = $model->to_array();
		}

		return $list;
	}

	public function response_with($formats, $content, $isDownload = null)
	{
		if (Input::get("format") != null) {
			if (in_array(Input::get("format"), $formats)) {
				switch (Input::get("format")) {
					case 'json':
						return View::make(
							"lara_admin::exporters.json",
							array("isDownload" => $isDownload, "content" => json_encode($content))
						);
						break;
					case 'xml':
						return View::make(
							"lara_admin::exporters.xml",
							array("isDownload" => $isDownload, "content" => $this->toXml($content))
						);
						break;
					case 'csv':
						return View::make(
							"lara_admin::exporters.csv",
							array("isDownload" => $isDownload, "content" => $this->array_to_scv($content))
						);
						break;
				}
			}
		}

		return;
	}


	public function defaultAttrForLayout($modelName, $view)
	{
		$this->layout->title = $modelName;
		$this->layout->content = $view;
	}

	public function toXml($data, $rootNodeName = 'data', $xml = null)
	{
		// turn off compatibility mode as simple xml throws a wobbly if you don't.
		if (ini_get('zend.ze1_compatibility_mode') == 1) {
			ini_set('zend.ze1_compatibility_mode', 0);
		}

		if ($xml == null) {
			$xml = simplexml_load_string("<?xml version='1.0' encoding='utf-8'?><$rootNodeName />");
		}

		// loop through the data passed in.
		foreach ($data as $key => $value) {
			// no numeric keys in our xml please!
			if (is_numeric($key)) {
				// make string key...
				$key = "unknownNode_" . (string)$key;
			}

			// replace anything not alpha numeric
			$key = preg_replace('/[^a-z]/i', '', $key);

			// if there is another array found recrusively call this function
			if (is_array($value)) {
				$node = $xml->addChild($key);
				// recrusive call.
				$this->toXml($value, $rootNodeName, $node);
			} else {
				// add single node.
				$value = htmlentities($value);
				$xml->addChild($key, $value);
			}

		}

		// pass back as string. or simple xml object if you want!
		return $xml->asXML();
	}


	function array_to_scv($array, $header_row = true, $col_sep = ",", $row_sep = "\n", $qut = '"')
	{
		//if (!is_array($array) or !is_array($array[0])) return false;
		$array = array_values($array);
		$output = "";

		//Header row.
		if ($header_row) {
			foreach ($array[0] as $key => $val) {
				//Escaping quotes.
				$key = str_replace($qut, "$qut$qut", $key);
				$output .= "$col_sep$qut$key$qut";
			}
			$output = substr($output, 1) . "\n";
		}
		//Data rows.
		foreach ($array as $key => $val) {
			$tmp = '';
			foreach ($val as $cell_key => $cell_val) {
				//Escaping quotes.
				$cell_val = str_replace($qut, "$qut$qut", $cell_val);
				$tmp .= "$col_sep$qut$cell_val$qut";
			}
			$output .= substr($tmp, 1) . $row_sep;
		}

		return $output;
	}

}