<?php

namespace PlugQuery;

use PlugQuery\Connection\Manager;

class Model implements \Iterator {

	public static $schema;

	public $values = array();

	protected $name;

	public function __construct() {
		$this->name = self::getName();
	}

	public static function getName() {
		return strtolower(get_called_class());
	}

	public function save() {
		if($this->values['id'] === null) { 
			self::insert($this);
		} else {
			self::update($this);
		}
	}

	public static function insert($model) {
		$success = Query::insert($model->name, $model->values)->execute();
		if($success) $model->id = $success;
	}

	public static function update($model) {
		Query::update($model->name, $model->values)->where('id', $model->id)->execute();
	}

	public static function id($id) {
		$name = self::getName();
		$data = Query::select($name, $id)->findOne();
		$object = get_called_class();
		$new = new $object;
		$new->values = $data;
		return $new;
	}

	public static function find() {
		$name = self::getName();
		$rows = Query::select($name, $id)->find();
		$results = array();

		foreach($rows as $data) {
			$object = get_called_class();
			$new = new $object;
			$new->values = (array) $data;
			$results[] = $new;
		}

		return $results;
	}

	protected function hasOne($table) {
		$result = Query::select($table, $this->values["{$table}_id"])->findOne();
		$object = new $table;
		$object->values = $result;
		$this->values[$table] = $object;
		return $this;
	}

	public function __get($name) {
		if(isset($this->values[$name])) return $this->values[$name];
	}

	public function __set($name, $value) {
		$this->values[$name] = $value;
	}

	////
	public function rewind() {
        reset($this->values);
    }
  
    public function current() {
        $var = current($this->values);
        return $var;
    }
  
    public function key() {
        $var = key($this->values);
        return $var;
    }
  
    public function next() {
        $var = next($this->values);
        return $var;
    }
  
    public function valid() {
        $key = key($this->values);
        $var = ($key !== NULL && $key !== FALSE);
        return $var;
    }

}