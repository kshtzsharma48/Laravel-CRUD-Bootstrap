<?php


class Just {
	
	public $form = '';

	public function __construct()
	{
		$this->form .= Former::horizontal_open();

	}
	public function generateForm()
	{
	    $arg_list = func_get_args();



	    foreach ($arg_list as $item) {
	    	$this->form .= $item;
	    }

	 
	    
	}

	public function closeForm($name) {
		$closeForm = '<div class="control-group"><div class="controls">
	    	<button class="btn btn btn-primary" type="submit">Opslaan</button>  <button type="button" class="btn">Terug</button>

	    </div></div>';

	    $closeForm .= Former::close();

	    return $closeForm;
	}
	public function __toString()
	{
		return $this->form;
	}
}