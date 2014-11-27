<?php

namespace PlugQuery\Query;

use PlugQuery\Query;
use PlugQuery\Query\Method as QueryMethod;

class Create extends QueryMethod {

	public $table;
	public $columns = array();

	public function table($table) {
		$this->table = $table;
		return $this;
	}

	public function addColumn($name, $type, $size) {
		$create = new \StdClass;
		$create->name = $name;
		$create->type = $type;
		$create->size = $size;
		$this->columns[] = $create;
		return $this;
	}

}