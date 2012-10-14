<?php

class Tree extends Admin_Controller {
	

	public function get_index() {

		// Get ford
		$pages = Pages::find(function($query)
		{
		    return $query->where('id', '=', 1);
		});


		$this->_data['list'] = $pages->dump_children_as('ol', 'name');



		$grid = View::make('admin.tree', $this->_data)->__toString();

		
		$this->_data['view'] = $grid;

		return View::make('admin.admin', $this->_data);	
	}

}