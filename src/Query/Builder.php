<?php

namespace PlugQuery\Query;

use PlugQuery\Query;
use PlugQuery\Query\Method as QueryMethod;
use PlugQuery\Query\Select;
use PlugQuery\Query\Insert;
use PlugQuery\Query\Update;
use PlugQuery\Query\Create;

class Builder {

	public function build(QueryMethod $query) {

		if($query instanceOf Select) {
			return $this->buildSelectQuery($query);
		}

		if($query instanceOf Insert) {
			return $this->buildInsertQuery($query);
		}

		if($query instanceOf Update) {
			return $this->buildUpdateQuery($query);
		}

		if($query instanceOf Delete) {
			return $this->buildDeleteQuery($query);
		}

		if($query instanceOf Create) {
			return $this->buildCreateQuery($query);
		}

		return null;
	}

	protected function buildInsertQuery(Insert $query) {

		$statement = "INSERT INTO " . $query->table;
		$statement .= " (" . join(', ', array_keys($query->data)) . ")";
		$statement .= " VALUES (";
		$statement .= join(', ', array_fill(1, count($query->data), '?')) . ")";

		return $statement;
	}

	protected function buildUpdateQuery(Update $query) {

		$statement = "UPDATE " . $query->table . " ";

		$columns = join(', ', 
			array_map(
				function($key) {
					return $key . " = ?";
				}, 
				array_keys($query->data)
			)
		);

		$statement .= "SET " . $columns . " ";
		$statement .= $this->processWheres($query);

		return $statement;
	}

	protected function buildDeleteQuery(Delete $query) {

		if(empty($query->wheres)) throw new \Exception("delete requires at least one where.");

		$processes = array_filter(array(
			"DELETE FROM {$query->table}",
			$this->processWheres($query)
		));

		return implode(" ", $processes);
	}

	protected function buildCreateQuery(Create $query) {

		$processes = array_filter(array(
			"CREATE TABLE {$query->table} (",
			$this->processColumns($query),
			")"
		));

		return implode(" ", $processes);
	}

	protected function processColumns($query) {

		$columns = array_map(function($column) {
			return "{$column->name} {$column->type}({$column->size})";
		}, $query->columns);
		
		return implode(", ", $columns);
	}

	protected function buildSelectQuery(Select $query) {

		$processes = array_filter(array(
			$this->processSelects($query),
			$this->processJoins($query),
			$this->processWheres($query),
			$this->processOrder($query),
			$this->processLimits($query),
			$this->processGroupBy($query),
			// $this->processHaving($query)
		));

		return implode(" ", $processes);
	}

	/**
	 * SELECT t1.* FROM t1
	 * SELECT t1.*, t2.* FROM t1
	 */
	protected function processSelects($query) {

		$jointables = array_map(function($join) {
			return "{$join->table}.*";
		}, $query->joins);

		array_unshift($jointables, "{$query->table}.*");
		
		$output  = "SELECT ";
		$output .= implode(", ", $jointables);
		$output .= " FROM {$query->table}";

		return $output;
	}

	/**
	 * WHERE a = ?
	 * WHERE a = ? AND b = ?
	 * WHERE a = ? OR b = ?
	 * WHERE a = ? AND b = ? OR c < 1
	 * WHERE a IS NOT NULL
	 * WHERE t1.a = ? AND t2.b = ?
	 */
	protected function processWheres($query) {
		if(!empty($query->wheres)) {

			$statement = array("WHERE");

			foreach($query->wheres as $index => $item) {

				// prefix column with tablename if not specified by the user
				$column = $item->column;
				if(strpos($item->column, ".") === false) {
					$column = "{$query->table}.{$item->column}";
				}

				// determine is data is nullable
				$is_null = is_null($item->value);
				$value = $is_null ? "NULL" : "?";

				// add operator, but not before the first statement
				if($index > 0) {
					switch($item->operator) {
						case Query::BOOLEAN_OR:
							$statement[] = "OR";
							break;
						case Query::BOOLEAN_AND:
							$statement[] = "AND";
							break;
					}
				}

				// set permutation
				switch($item->permutation) {
					case Query::PERMUTATION_EQUAL:
						$permutation = $is_null ? "IS" : "=";
						break;
					case Query::PERMUTATION_NOTEQUAL:
						$permutation = $is_null ? "IS NOT" : "<>";
						break;
					case Query::PERMUTATION_LESS:
						$permutation = "<";
						break;
					case Query::PERMUTATION_MORE:
						$permutation = ">";
						break;
					case Query::PERMUTATION_LESSEQUAL:
						$permutation = "<=";
						break;
					case Query::PERMUTATION_MOREEQUAL:
						$permutation = ">=";
						break;
					case Query::PERMUTATION_LIKE:
						$permutation = "LIKE";
						break;
					case Query::PERMUTATION_NOTLIKE:
						$permutation = "NOT LIKE";
						break;
					case Query::PERMUTATION_IN:
						$permutation = "IN";
						$value = "(" . join(', ', array_fill(1, count($item->value), '?')) . ")";
						break;
				}

				$statement[] = "{$column} {$permutation} {$value}";
			}
			return implode(" ", $statement);
		}
		return null;
	}

	/**
	 * ORDER BY t1.a DESC
	 * ORDER BY t1.a ASC
	 * ORDER BY t1.a DESC, t2.a ASC
	 */
	protected function processOrder($query) {
		if(!empty($query->orders)) {
			return 'ORDER BY ' . implode(', ', array_map(function($item) use ($query) {
				$column = $item->column;
				if(strpos($item->column, ".") === false) {
					$column = "{$query->table}.{$item->column}";
				}
				$direction = strtoupper($item->direction);
				return "{$column} {$direction}";
			}, $query->orders));
		}
		return null;
	}

	/**
	 * LEFT JOIN t2 ON t1.t2_id = t2.id
	 */
	protected function processJoins($query) {
		if(!empty($query->joins)) {
			$statement = array();
			foreach($query->joins as $item) {
				$statement[] = "LEFT JOIN {$item->table} ON {$query->table}.{$item->table}_id = {$item->table}.id";
			}
			return implode(" ", $statement);
		}
		return null;
	}

	/**
	 * LIMIT 1
	 * LIMIT 1, 5
	 */
	protected function processLimits($query) {
		if($query->limit) {
			$output = "LIMIT {$query->limit->start}";
			if($query->limit->count) {
				$output .= ", {$query->limit->count}";
			}
			return $output;
		}
		return null;
	}

	/**
	 * GROUP BY id
	 * GROUP BY id, val;
	 */
	protected function processGroupBy($query) {
		if(!empty($query->groupby)) {

			$groupbys = array_map(function($item) use ($query) {
				$column = $item->column;
				if(strpos($item->column, ".") === false) {
					$column = "{$query->table}.{$item->column}";
				}
				return $column;
			}, $query->groupby);
			
			$output  = "GROUP BY ";
			$output .= implode(", ", $groupbys);

			return $output;
		}
		return null;
	}

	/**
	 * HAVING a > 1
	 * HAVING a < 1 AND b > 2 OR c = 1
	 */
	protected function processHaving($query) {
		if(!empty($query->having)) {
			throw new Exception("TODO: HAVING");
		}
		return null;
	}

}