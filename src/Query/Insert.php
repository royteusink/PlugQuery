<?php

namespace PlugQuery\Query;

use PlugQuery\Query;
use PlugQuery\Query\Method as QueryMethod;

class Insert extends QueryMethod {

	public $table;
	public $data;

	public function table($table, $data) {
		$this->table = $table;
		$this->data = $data;
		return $this;
	}

}