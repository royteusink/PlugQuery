<?php

trait WhereTrait {
    
    protected $operator = Query::BOOLEAN_AND;

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

}