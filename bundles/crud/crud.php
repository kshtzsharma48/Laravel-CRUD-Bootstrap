<?php
/**
 * Part of the Crud bundle for Laravel.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the 3-clause BSD License.
 *
 * This source file is subject to the 3-clause BSD License that is
 * bundled with this package in the LICENSE file.  It is also available at
 * the following URL: http://www.opensource.org/licenses/BSD-3-Clause
 *
 * @package    Sentry
 * @version    1.0
 * @author     Cartalyst LLC
 * @license    BSD License (3-clause)
 * @copyright  (c) 2011 - 2012, Cartalyst LLC
 * @link       http://cartalyst.com
 */

namespace Crud;

use ArrayAccess;
use Closure;
use DB;
use Event;
use Exception;
use Str;
use Validator;

/**
 * Crud model class.
 *
 * @author  Daniel Petrie, Ben Corlett
 */
class Crud implements ArrayAccess
{

	/**
	 * The primary key for the model on the database table.
	 *
	 * @var string
	 */
	protected static $_key = 'id';

	/**
	 * The name of the table associated with the model.
	 * If left null, the table name will become the the plural of
	 * the class name: user => users
	 *
	 * @var string
	 */
	protected static $_table = null;

	/**
	 * The name of the database connection that should be used for the model.
	 *
	 * @var string
	 */
	protected static $_connection = null;

	/**
	 * The name of the sequence associated with the model.
	 *
	 * @var string
	 */
	protected static $_sequence = null;

	/**
	 * Indicates if the model has update and creation timestamps.
	 *
	 * @var bool
	 */
	protected static $_timestamps = false;

	/**
	 * The timestamp format for the model. Only available if the
	 * timestamps are set to true.
	 *
	 * SQL timestamps are converted to the correct format based
	 * on the connection used in the model (which are defined in
	 * each Laravel driver's Grammar class).
	 *
	 * Possible values:
	 *	- 'sql'
	 *	- 'unix'
	 *
	 * If the persist format is left NULL then 'sql' is used.
	 * If the instance format is left NULL then the persiste
	 * format is used.
	 *
	 * @var string
	 */
	protected static $_timestamp_persist_format  = 'sql';
	protected static $_timestamp_instance_format = 'unix';

	/**
	 * Indicates if the model should use events
	 *
	 * @var bool
	 */
	protected static $_events = true;

	/**
	 * Validation rules for model attributes.
	 *
	 * @var array
	 */
	protected static $_rules = array();

	/**
	 * Indicates if the model is new or not (insert vs update).
	 *
	 * @var  bool
	 */
	protected $_is_new;

	/**
	 * @var  validation object
	 */
	protected $_validation;

	/*
	|--------------------------------------------------------------------------
	| Object Usage
	|--------------------------------------------------------------------------
	*/

	/**
	 * Create a new Crud model instance.
	 *
	 * @param   array  $attributes
	 * @param   bool   $is_new
	 * @return  void
	 */
	public function __construct($attributes = array(), $is_new = null)
	{
		// Hydrate our model
		$this->fill((array) $attributes);

		// Set the $is_new flag
		$this->_is_new = ($is_new === null) ? ( ! array_key_exists(static::key(), $attributes)) : (bool) $is_new;
	}

	/**
	 * Save the model instance to the database.
	 *
	 * @return  int
	 */
	public function save($events = array('before', 'after'))
	{
		// first check if we want timestamps as this will append to attributes
		if (static::$_timestamps === true)
		{
			$this->timestamp();
		}

		// now we grab the attributes
		$attributes = $this->attributes();

		// run - if rules are set
		if ( ! empty(static::$_rules))
		{
			$validated = $this->run_validation($attributes, static::$_rules);

			if ( ! $validated)
			{
				return false;
			}
		}

		// prep attribute values after validation is done
		$attributes = $this->prep_attributes($attributes);

		// If the model is not new, we only need to update it in the database, and the update
		// will be considered successful if there is one affected row returned from the
		// fluent query instance. We'll set the where condition automatically.
		if ( ! $this->is_new())
		{
			// make sure a key is set then grab and remove it from the attributes array
			if ( ! isset($attributes[static::key()]) or empty($attributes[static::key()]))
			{
				// the key is not set or empty, throw an exception
				throw new Exception('A primary key is required to update.');
			}

			$key = $attributes[static::key()];
			unset($attributes[static::key()]);

			$query = $this->query()->where(static::table().'.'.static::key(), '=', $key);

			if (in_array('before', $events))
			{
				list($query, $attributes) = $this->before_update($query, $attributes);
			}

			$result = $query->update($attributes);

			if (in_array('after', $events))
			{
				$result = $this->after_update($result);
			}

			if (static::$_events)
			{
				// fire update event
				Event::fire(static::event().'.update', array($this));
			}
		}

		// If the model is new, we will insert the record and retrieve the last
		// insert ID that is associated with the model. If the ID returned is numeric
		// then we can consider the insert successful.
		else
		{
			$query = $this->query();

			if (in_array('before', $events))
			{
				list($query, $attributes) = $this->before_insert($query, $attributes);
			}

			$key = $this->query()->insert_get_id($attributes, static::$_sequence);

			if (in_array('after', $events))
			{
				$key = $this->after_insert($key);
			}

			// Workaound for PDO connections not returning
			// the key upon insert.
			if (isset($this->{static::key()}) and $key === 0)
			{
				$key = $this->{static::key()};
				$this->is_new(false);
			}
			else
			{
				// If we didn't already have a primary
				// key, assign what is returned from
				// the database insert
				if ( ! isset($this->{static::key()}))
				{
					$this->{static::key()} = $key;
				}

				$this->is_new( ! (bool) $key);
			}

			$this->fill($attributes);

			if (static::$_events)
			{
				// fire create event
				Event::fire(static::event().'.create', array($this));
			}
		}

		return $key;
	}

	/**
	 * Delete a model from the datatabase
	 *
	 * @return  int
	 */
	public function delete($events = array('before', 'after'))
	{
		// make sure a key is set then grab and remove it from the attributes array
		if ( ! isset($this->{static::key()}) or empty($this->{static::key()}))
		{
			// the key is not set or empty, throw an exception
			throw new Exception('A primary key is required to delete.');
		}

		$query = $this->query()->where(static::table().'.'.static::key(), '=', $this->{static::key()});

		if (in_array('before', $events))
		{
			$query = $this->before_delete($query);
		}

		$result = $query->delete();
		
		if (in_array('after', $events))
		{
			$result = $this->after_delete($result);
		}

		if (static::$_events)
		{
			// fire delete event
			Event::fire(static::event().'.delete', array($this));
		}

		return $result;
	}

	/**
	 * Hydrate the model with an array of attributes.
	 *
	 * @param   array  $attributes
	 * @return  Crud
	 */
	public function fill($attributes = array())
	{
		foreach ($attributes as $key => $value)
		{
			$this->{$key} = $value;
		}

		return $this;
	}

	/**
	 * Get all the attributes of the model.
	 *
	 * @return  array
	 */
	public function attributes()
	{
		return get_object_public_vars($this);
	}

	/**
	 * Returns the a validation object for the model.
	 *
	 * @return  Validation
	 */
	public function validation()
	{
		return $this->_validation;
	}

	/**
	 * Dynamically set the value of an attribute.
	 *
	 * @param   string  $key
	 * @param   mixed   $value
	 * @return  void
	 */
	public function __set($key, $value)
	{
		$this->{$key} = $value;
	}

	/**
	 * Dynamically check if an attribute is set.
	 *
	 * @param   string  $key
	 * @return  bool
	 */
	public function __isset($key)
	{
		return isset($this->{$key});
	}

	/**
	 * Dynamically unset an attribute.
	 *
	 * @param   string  $key
	 * @return  void
	 */
	public function __unset($key)
	{
		unset($this->{$key});
	}

	/**
	 * Set or Determine if the model is new or not
	 *
	 * @param   bool  $is_new
	 * @return  bool|Crud
	 */
	public function is_new($is_new = null)
	{
		if ($is_new === null)
		{
			return $this->_is_new;
		}

		$this->_is_new = (bool) $is_new;

		return $this;
	}

	/**
	 * Set the update and creation timestamps on the model.
	 *
	 * @return void
	 */
	protected function timestamp()
	{
		// Get the timestamp
		$timestamp = time();

		// Default the timestamp format
		$format = static::$_timestamp_persist_format ?: 'sql';

		// Using SQL timestamps?
		if ($format === 'sql')
		{
			$timestamp = date(static::connection()->grammar()->grammar->datetime, $timestamp);
		}

		$this->updated_at = $timestamp;

		if ($this->is_new())
		{
			$this->created_at = $this->updated_at;
		}
	}

	/**
	 * Run validation
	 *
	 * @param   array  $attributes
	 * @param   array  $rultes
	 * @return  bool
	 */
	protected function run_validation($attributes, $rules)
	{
		list($attributes, $rules) = $this->before_validation($attributes, $rules);

		$this->_validation = Validator::make($attributes, $rules);

		$result = $this->after_validation($this->_validation->fails());

		return ( ! (bool) $result);
	}

	/**
	 * Gets called before the validation is ran.
	 *
	 * @param   array  $data
	 * @param   array  $rults
	 * @return  array
	 */
	protected function before_validation($data, $rules)
	{
		return array($data, $rules);
	}

	/**
	 * Called right after the validation is ran.
	 *
	 * @param   bool  $result
	 * @return  bool
	 */
	protected function after_validation($result)
	{
		return $result;
	}

	/**
	 * Called right after validation before inserting/updating to the database
	 *
	 * @param   array  $attributes
	 * @return  array
	 */
	protected function prep_attributes($attributes)
	{
		return $attributes;
	}

	/**
	 * Gets called before insert() is executed to modify the query
	 * Must return an array of the query object and columns array($query, $columns)
	 *
	 * @param   Query  $query
	 * @param   array  $columns
	 * @return  array
	 */
	protected function before_insert($query, $columns)
	{
		return array($query, $columns);
	}

	/**
	 * Gets call after the insert() query is exectuted to modify the result
	 * Must return a proper result
	 *
	 * @param   array  $result
	 * @return  array  $result
	 */
	protected function after_insert($result)
	{
		return $result;
	}

	/**
	 * Gets called before update() is executed to modify the query
	 * Must return an array of the query object and columns array($query, $columns)
	 *
	 * @param   Query  $query
	 * @param   array  $columns
	 * @return  array
	 */
	protected function before_update($query, $columns)
	{
		return array($query, $columns);
	}

	/**
	 * Gets call after the update() query is exectuted to modify the result
	 * Must return a proper result
	 *
	 * @return  array
	 */
	protected function after_update($result)
	{
		return $result;
	}

	/**
	 * Gets called before delete() is executed to modify the query
	 * Must return an array of the query object and columns array($query, $columns)
	 *
	 * @param   Query  $query
	 * @return  Query  $query
	 */
	protected function before_delete($query)
	{
		return $query;
	}

	/**
	 * Gets call after the delete() query is exectuted to modify the result
	 * Must return a proper result
	 *
	 * @param   int  $result
	 * @return  int  $result
	 */
	protected function after_delete($result)
	{
		return $result;
	}

	/**
	 * Gets called before find() is executed to modify the query
	 * Must return an array of the query object and columns array($query, $columns)
	 *
	 * @param   Query  $query
	 * @param   array  $columns
	 * @return  array
	 */
	protected function before_find($query, $columns)
	{
		return array($query, $columns);
	}

	/**
	 * Gets call after the find() query is exectuted to modify the result
	 * Must return a proper result
	 *
	 * @param   Query  $query
	 * @param   array  $columns
	 * @return  array
	 */
	protected function after_find($result)
	{
		return $result;
	}

	/*
	|--------------------------------------------------------------------------
	| ArrayAccess Implementation
	|--------------------------------------------------------------------------
	*/

	/**
	 * Sets the value of the given offset (class property).
	 *
	 * @param   string  $key
	 * @param   string  $value
	 * @return  void
	 */
	public function offsetSet($key, $value)
	{
		$this->{$key} = $value;
	}

	/**
	 * Checks if the given offset (class property) exists.
	 *
	 * @param   string  $key
	 * @return  bool
	 */
	public function offsetExists($key)
	{
		return isset($this->{$key});
	}

	/**
	 * Unsets the given offset (class property).
	 *
	 * @param   string  $key
	 * @return  void
	 */
	public function offsetUnset($key)
	{
		unset($this->{$key});
	}

	/**
	 * Gets the value of the given offset (class property).
	 *
	 * @param   string  $key
	 * @return  mixed
	 */
	public function offsetGet($key)
	{
		if (isset($this->{$key}))
		{
			return $this->{$key};
		}

		throw new Exception('Property "'.$key.'" not found for '.get_called_class().'.');
	}

	/*
	|--------------------------------------------------------------------------
	| Static Usage
	|--------------------------------------------------------------------------
	*/

	/**
	 * Get the key of the table
	 *
	 * @return  string
	 */
	public static function key()
	{
		return static::$_key;
	}

	/**
	 * Get the name of the table associated with the model.
	 *
	 * @return  string
	 */
	public static function table()
	{
		return static::$_table ?: Str::lower(Str::plural(class_basename(new static)));
	}

	/**
	 * Get the event name associated with the model
	 *
	 * @return  string
	 */
	public static function event()
	{
		return Str::lower(class_basename(new static));
	}

	/**
	 * Get the database connection.
	 *
	 * @return  Laravel\Database\Connection
	 */
	public static function connection()
	{
		return DB::connection(static::$_connection);
	}

	/**
	 * Find a model by either it's primary key
	 * or a condition that modifies the query object.
	 *
	 * @param   string  $condition
	 * @param   array   $columns
	 * @return  Crud
	 */
	public static function find($condition = 'first', $columns = array('*'), $events = array('before', 'after'))
	{
		$model = new static;
		$query = $model->query();

		// User has a closure of the query
		// as the condition
		if ($condition instanceof Closure)
		{
			$query = $condition($query);
		}

		elseif ($condition == 'first')
		{
			$query->order_by(static::table().'.'.static::key(), 'asc');
		}

		// After last result
		elseif ($condition == 'last')
		{
			$query->order_by(static::table().'.'.static::key(), 'desc');
		}

		// Providing either an int or string for
		// the primary key
		else
		{
			$query = $query->where(static::table().'.'.static::key(), '=', $condition);
		}

		if (in_array('before', $events))
		{
			list($query, $columns) = $model->before_find($query, $columns);
		}

		$result = $query->first($columns);

		// Flag for convert timestamps
		$convert_timestamps = (static::$_timestamps == true and ! is_null(static::$_timestamp_instance_format) and static::$_timestamp_instance_format !== static::$_timestamp_persist_format) ? static::$_timestamp_instance_format : false;

		if ($convert_timestamps)
		{
			if (isset($result->created_at))
			{
				if ($convert_timestamps === 'unix')
				{
					$result->created_at = strtotime($result->created_at);
				}
			}
			if (isset($result->updated_at))
			{
				if ($convert_timestamps === 'unix')
				{
					$result->updated_at = strtotime($result->updated_at);
				}
			}
		}

		if (in_array('after', $events))
		{
			$result = $model->after_find($result);
		}

		if (count($result) > 0)
		{
			return $model->is_new(false)
			             ->fill($result);
		}

		return null;
	}

	/**
	 * Get all of the models in the database.
	 *
	 * @param   Closure      $condition
	 * @param   String|Array columns to select
	 * @return  array
	 */
	public static function all($condition = null, $columns = '*', $events = array('before', 'after'))
	{
		$query = with(new static)->query();

		if ($condition instanceof Closure)
		{
			$query = $condition($query, $columns);
		}

		if (in_array('before', $events))
		{
			list($query, $columns) = static::before_all($query, $columns);
		}

		$results = $query->get($columns);

		if (in_array('after', $events))
		{
			$results = static::after_all($results);
		}

		$models  = array();

		// Flag for convert timestamps
		$convert_timestamps = (static::$_timestamps == true and ! is_null(static::$_timestamp_instance_format) and static::$_timestamp_instance_format !== static::$_timestamp_persist_format) ? static::$_timestamp_instance_format : false;

		foreach ($results as $result)
		{
			if ($convert_timestamps)
			{
				if (isset($result->created_at))
				{
					if ($convert_timestamps === 'unix')
					{
						$result->created_at = strtotime($result->created_at);
					}
				}
				if (isset($result->updated_at))
				{
					if ($convert_timestamps === 'unix')
					{
						$result->updated_at = strtotime($result->updated_at);
					}
				}
			}

			$models[] = new static($result);
		}

		return $models;
	}

	/**
	 * Returns the number of records in the table.
	 *
	 * @param   string  $column
	 * @param   Closure $condition
	 * @return  int
	 */
	public static function count($column = null, $condition = null)
	{
		$query = static::query();

		if ($condition instanceof Closure)
		{
			$query = $condition($query);
		}

		return $query->count($column ?: static::key());
	}

	/**
	 * Returns the number of distinct records in the table.
	 *
	 * @param   string  $column
	 * @param   Closure $condition
	 * @return  int
	 */
	public static function count_distinct($column = null, $condition = null)
	{
		$query = static::query();

		if ($condition instanceof Closure)
		{
			$query = $condition($query);
		}

		return $query->distinct()->count($column ?: static::key());
	}

	/**
	 * Get a new fluent query builder instance for the model.
	 *
	 * @return  Query
	 */
	public static function query()
	{
		return static::connection()->table(static::table());
	}

	/**
	 * Gets called before all() is executed to modify the query
	 * Must return an array of the query object and columns array($query, $columns)
	 *
	 * @param   Query  $query
	 * @param   array  $columns
	 * @return  array
	 */
	protected static function before_all($query, $columns)
	{
		return array($query, $columns);
	}

	/**
	 * Gets called after the all() query is exectuted to modify the result
	 * Must return a proper result
	 *
	 * @param   array  $results
	 * @return  array  $results
	 */
	protected static function after_all($results)
	{
		return $results;
	}

}
