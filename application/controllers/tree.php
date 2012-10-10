<?php

class Tree extends Admin_Controller {
	

	public function get_index() {

		// Let's add to the previous setup
		$items = array(
		    array(
		        'id'   => 2,
		        'name' => 'Holden',
		    ),
		    array(
		        'id'   => 3,
		        'name' => 'Ford',
		    ),
		    array(
		        'name' => 'Toyota',
		        // Nest children within this reserved
		        // key name
		        'children' => array(
		            array(
		                'name' => 'Prius',
		            ),
		            array(
		                'name' => 'Camry',
		            ),
		            array(
		                'name' => 'Celica',
		            ),
		            array(
		                'name' => 'Hilux',
		            ),
		        ),
		    ),
		);
		$tree = Nesty::from_hierarchy_array(1, $items);
		
		echo '<pre>';
		print_r($tree);
		die();
		
		$grid = View::make('admin.tree', $this->_data)->__toString();

		$this->_data['view'] = $grid;

		return View::make('admin.admin', $this->_data);	
	}

}