<?php

namespace PlugQuery\Query;

use PlugQuery\Query;
use PlugQuery\Query\Method as QueryMethod;

class Select extends QueryMethod {

	public $table;
	public $columns = array();
	public $joins = array();
	public $wheres = array();
	public $orders = array();
	public $limit;
	public $groupby = array();
	public $distinct = false;
	public $operator = Query::BOOLEAN_AND;

	public function table($table, $columns = null) {
		$this->table = $table;
		$this->columns = is_null($columns) ? array() : $columns;
		return $this;
	}

	public function __get($name) {
		if($name == 'or') {
			$this->operator = Query::BOOLEAN_OR;
			return $this;
		}
	}

	protected function baseWhere($column, $value, $permutation) {
		$where = new \StdClass;
		$where->column = $column;
		$where->permutation = $permutation;
		$where->operator = $this->operator;
		$where->value = $value;

		// reset operator to AND
		$this->operator = Query::BOOLEAN_AND;

		return $where;
	}

	public function where($column, $value) {
		$this->wheres[] = $this->baseWhere($column, $value, Query::PERMUTATION_EQUAL);
		return $this;
	}

	public function whereLess($column, $value) {
		$this->wheres[] = $this->baseWhere($column, $value, Query::PERMUTATION_LESS);
		return $this;
	}

	public function whereMore($column, $value) {
		$this->wheres[] = $this->baseWhere($column, $value, Query::PERMUTATION_MORE);
		return $this;
	}

	public function whereNot($column, $value) {
		$this->wheres[] = $this->baseWhere($column, $value, Query::PERMUTATION_NOTEQUAL);
		return $this;
	}

	public function whereLessEqual($column, $value) {
		$this->wheres[] = $this->baseWhere($column, $value, Query::PERMUTATION_LESSEQUAL);
		return $this;
	}

	public function whereMoreEqual($column, $value) {
		$this->wheres[] = $this->baseWhere($column, $value, Query::PERMUTATION_MOREEQUAL);
		return $this;
	}

	public function whereIn($column, $value) {
		$this->wheres[] = $this->baseWhere($column, $value, Query::PERMUTATION_IN);
		return $this;
	}

	public function wherelike($column, $value) {
		$this->wheres[] = $this->baseWhere($column, $value, Query::PERMUTATION_LIKE);
		return $this;
	}

	public function whereNotLike($column, $value) {
		$this->wheres[] = $this->baseWhere($column, $value, Query::PERMUTATION_NOTLIKE);
		return $this;
	}

	public function whereBetween($column, $from, $to) {
		$this->wheres[] = $this->baseWhere($column, array($from, $to), Query::PERMUTATION_BETWEEN);
		return $this;
	}

	public function whereNotBetween($column, $from, $to) {
		$this->wheres[] = $this->baseWhere($column, array($from, $to), Query::PERMUTATION_NOTBETWEEN);
		return $this;
	}

	public function distinct() {
		$this->distinct = true;
		return $this;
	}

	public function orderBy($column, $direction) {
		$order = new \StdClass;
		$order->column = $column;
		$order->direction = $direction;
		$this->orders[] = $order;
		return $this;
	}

	public function join($table, $columns = null) {
		$join = new \StdClass;
		$join->table = $table;
		$join->direction = 'left';
		$join->columns = $columns;
		$this->joins[] = $join;
		return $this;
	}

	public function limit($start, $count = null) {
		$limit = new \StdClass;
		$limit->start = $start;
		$limit->count = $count;
		$this->limit = $limit;
		return $this;
	}

	public function groupBy($column) {
		$groupby = new \StdClass;
		$groupby->column = $column;
		$this->groupby[] = $groupby;
		return $this;
	}

}