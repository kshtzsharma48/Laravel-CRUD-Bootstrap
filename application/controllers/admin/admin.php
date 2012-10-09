<?php

/**
* Admin_Controller
*
* @uses     Base_Controller
*
* @category Category
* @package  Package
* @author    <>
* @license  
* @link     
*/
class Admin_Controller extends Base_Controller {

    /**
     * $_data
     *
     * @var mixed
     *
     * @access protected
     */
	protected $_data;

    /**
     * __construct
     * 
     * @access public
     * @return mixed Value.
     */
	public function __construct()
	{
		$this->_data = array();
		$this->_data['gridFields'] = $this->gridFields;
		$this->_data['className']  = $this->className;
		$this->_data['allClasses'] = $this->scandir_only_files();	
			
	}

    /**
     * get_index
     * 
     * @access public
     * @return mixed Value.
     */
	public function get_index()
	{
		$class = $this->className;
		$this->_data['items'] = $class::query()->order_by( reset($this->sortField), end($this->sortField) )->get();

		$grid = View::make('admin.grid', $this->_data)->__toString();

		$this->_data['view'] = $grid;
		
		$status = Session::get('status');

		if ($status) 
			$this->_data['status'] = $status;

		return View::make('admin.admin', $this->_data);
	}	

	public function post_index()
	{

		echo '<pre>';
		print_r($_POST);
		die();
		

	}
    /**
     * post_form
     * 
     * @param mixed $id Description.
     *
     * @access public
     * @return mixed Value.
     */
	public function post_form($id = null)
	{
		$class = $this->className;

		$data = new $class();
		$data->fill($_POST);

		if (!is_null($id)) {
			$data->id = $id;
			$data->is_new( false );
		}
		
		$validation = Validator::make($_POST, $this->rules);

		if ( !$data->save() || $validation->fails() ) {
			
			if ( !$validation->fails() ) {
				$this->_data['status'] = array(
					'type' => 'error',
					'message' => 'Dit item kan niet worden verwijderd. Neem contact op met Just.'
				);
			}

			Former::withErrors($validation);
			Former::populate( $_POST );
			
			$form = $this->generate_form();
			$this->_data['view'] = $form;		

			return View::make('admin.admin', $this->_data);
		}
		
		return Redirect::to_action('admin/' . $class . '@index')
			->with('status', array(
				'type' => 'success',
				'message' => 'Uw item is succesvol opgeslagen',
			));
	}


    /**
     * get_form
     * 
     * @param mixed $id Description.
     *
     * @access public
     * @return mixed Value.
     */
	public function get_form($id = null)
	{
		$class = $this->className;
		if (!is_null($id))
			Former::populate( $class::find( $id ) );

		$form = $this->generate_form();
		$this->_data['view'] = $form;		
		
		return View::make('admin.admin', $this->_data);
	}

    /**
     * get_delete
     * 
     * @param mixed $id Description.
     *
     * @access public
     * @return mixed Value.
     */
	public function get_delete($id = null) 
	{
		$class = $this->className;
		if ( is_null( $id )) 
			return;
		
		$item = $class::find( $id );

		if (!$item) {
			return Redirect::to_action('admin/' . $class . '@index')
				->with('status', array(
					'type' => 'error',
					'message' => 'Dit item kan niet worden verwijderd. Neem contact op met Just.'
				));
		}
		
		$first   = reset($this->gridFields);
		$special = $item->$first;

		//$user->delete();	

		return Redirect::to_action('admin/' . $class . '@index')
			->with('status', array(
				'type' => 'success',
				'message' => '<strong>' . $special . '</strong> is succesvol verwijderd'
			));
	}

    /**
     * scandir_only_files
     * 
     * @param mixed $dir Description.
     *
     * @access public
     * @return mixed Value.
     */
	public function scandir_only_files( ) {

		$thelist = array();
   		if ($handle = opendir( realpath( dirname(__FILE__)) )) {
   			while (false !== ($file = readdir($handle)))
      		{
          		if ($file != "." && $file != ".." && $file != 'admin.php') {	
          			$file = explode('.php', $file);
          			$thelist[] = $file[0];
          		}
       		}
  			closedir($handle);
 	 	}	

 	 	return $thelist;
	}

}