<?php

namespace PlugQuery\Query;

use PlugQuery\Query;
use PlugQuery\Query\Method as QueryMethod;

// treat PlugQuery\Query\Parts\Where;

class Update extends QueryMethod {

	public $table;
	public $data;
	public $wheres = array();

	public function table($table, $data) {
		$this->table = $table;
		$this->data = $data;
		return $this;
	}

	public function where($column, $value) {
		$this->wheres[] = array(
			'column' => $column,
			'permutation' => Query::PERMUTATION_EQUAL,
			'value' => $value
		);
		return $this;
	}

	// public function whereLess() {}
	// public function whereMore() {}
	// public function whereLessOrEqual() {}
	// public function whereMoreOrEqual() {}
	// public function whereNot() {}

}