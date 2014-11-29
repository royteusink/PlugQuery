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

	public function table($table, $columns = null) {
		$this->table = $table;
		$this->columns = $columns == null ? array() : $columns;
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

	public function whereNot($column, $value) {
		$this->wheres[] = $this->baseWhere($column, $value, Query::PERMUTATION_NOTEQUAL, Query::BOOLEAN_AND);
		return $this;
	}

	public function orWhere($column, $value) {
		$this->wheres[] = $this->baseWhere($column, $value, Query::PERMUTATION_EQUAL, Query::BOOLEAN_OR);
		return $this;
	}

	public function whereIn($column, $value) {
		$this->wheres[] = $this->baseWhere($column, $value, Query::PERMUTATION_IN, Query::BOOLEAN_AND);
		return $this;
	}

	public function orWhereIn($column, $value) {
		$this->wheres[] = $this->baseWhere($column, $value, Query::PERMUTATION_IN, Query::BOOLEAN_OR);
		return $this;
	}

	public function wherelike($column, $value) {
		$this->wheres[] = $this->baseWhere($column, $value, Query::PERMUTATION_LIKE, Query::BOOLEAN_AND);
		return $this;
	}

	public function whereNotLike($column, $value) {
		$this->wheres[] = $this->baseWhere($column, $value, Query::PERMUTATION_NOTLIKE, Query::BOOLEAN_AND);
		return $this;
	}

	public function whereBetween($column, $from, $to) {
		$this->wheres[] = $this->baseWhere($column, array($from, $to), Query::PERMUTATION_BETWEEN, Query::BOOLEAN_AND);
		return $this;
	}

	public function whereNotBetween($column, $from, $to) {
		$this->wheres[] = $this->baseWhere($column, array($from, $to), Query::PERMUTATION_NOTBETWEEN, Query::BOOLEAN_AND);
		return $this;
	}

	/*
	public function orWherelike($column, $value) {
		$this->wheres[] = $this->baseWhere($column, $value, Query::PERMUTATION_LIKE, Query::BOOLEAN_OR);
		return $this;
	}

	public function orWhereNotLike($column, $value) {
		$this->wheres[] = $this->baseWhere($column, $value, Query::PERMUTATION_NOTLIKE, Query::BOOLEAN_OR);
		return $this;
	}
	*/

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