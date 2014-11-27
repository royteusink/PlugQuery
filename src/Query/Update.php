<?php

namespace PlugQuery\Query;

use PlugQuery\Query;
use PlugQuery\Query\Method as QueryMethod;

class Update extends QueryMethod {

	public $table;
	public $data;
	public $wheres = array();

	public function table($table, $data) {
		$this->table = $table;
		$this->data = $data;
		return $this;
	}

	protected function baseWhere($column, $value, $permutation, $operator) {
		$where = new \StdClass;
		$where->column = $column;
		$where->permutation = $permutation;
		$where->operator = $operator;
		$where->value = $value;
		return $where;
	}

	public function where($column, $value) {
		$this->wheres[] = $this->baseWhere($column, $value, Query::PERMUTATION_EQUAL, Query::BOOLEAN_AND);
		return $this;
	}

	public function orWhere($column, $value) {
		$this->wheres[] = $this->baseWhere($column, $value, Query::PERMUTATION_EQUAL, Query::BOOLEAN_OR);
		return $this;
	}

}