<?php

trait WhereTrait {
    
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

}