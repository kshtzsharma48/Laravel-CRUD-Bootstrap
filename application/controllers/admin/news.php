<?php

class Admin_News_Controller extends Admin_Controller {

	public $restful = true;
	protected $className = 'news';

	protected $rules = array(
	    'title'  => 'required|max:2',
	);

	protected $gridFields = array(
		'title'
	);

	protected $sortField = array('sortorder', 'desc');


	protected function generate_form()
	{
		$form = '<h1>Nieuwsitem</h1>';
		$form .= Former::horizontal_open();
		$form .= Former::legend('Wijzig uw nieuwsitem');
		$form .= Former::xlarge_text('title');
		$form .= Former::textarea('intro')->rows(10)->cols(70);
		$form .= Former::close();

		return $form;
	}

}