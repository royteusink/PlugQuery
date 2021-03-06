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

		if(empty($query->columns)) throw new \Exception("create requires at least one column.");

		$processes = array_filter(array(
			"CREATE TABLE IF NOT EXISTS {$query->table} (",
			$this->processColumns($query),
			")"
		));

		return implode(" ", $processes);
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
	 * Used for create table
	 * name varchar(200)
	 */
	protected function processColumns($query) {

		$columns = array_map(function($column) {
			return "{$column->name} {$column->type}({$column->size})";
		}, $query->columns);

		array_unshift($columns, "id INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT");
		
		return implode(", ", $columns);
	}

	/**
	 * SELECT t1.* FROM t1
	 * SELECT t1.*, j1.*, j2.* FROM t1
	 * SELECT t1.id, t1.name, j1.id, j1.name, j2.id FROM t1
	 */
	protected function processSelects($query) {

		$columns = array();

		if(empty($query->columns)) {
			$columns[] = "{$query->table}.*";
		} else {
			foreach($query->columns as $column) {
				$columns[] = "{$query->table}.{$column}";
			}
		}

		foreach($query->joins as $join) {
			if(empty($join->columns)) {
				$columns[] = "{$join->table}.*";
			} else {
				foreach($join->columns as $column) {
					$columns[] = "{$join->table}.{$column}";
				}
			}
		}
		
		$output  = "SELECT " . ($query->distinct ? "DISTINCT " : "");
		$output .= implode(", ", $columns);
		$output .= " FROM {$query->table}";

		return $output;
	}

	/**
	 * WHERE t1.a = ?
	 * WHERE t1.a = ? AND t1.b = ?
	 * WHERE t1.a = ? OR t1.b = ?
	 * WHERE t1.a = ? AND t1.b = ? OR t1.c < 1
	 * WHERE t1.a IS NOT NULL
	 * WHERE t1.a = ? AND t2.b = ?
	 * WHERE t1.a BETWEEN ? AND ?
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
				$value = "?";
				//$value = $is_null ? "NULL" : "?";

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
						$permutation = $is_null ? "IS NOT" : "!=";
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
					case Query::PERMUTATION_BETWEEN:
						$permutation = "BETWEEN";
						$value = "? AND ?";
						break;
					case Query::PERMUTATION_NOTBETWEEN:
						$permutation = "NOT BETWEEN";
						$value = "? AND ?";
						break;
					case Query::PERMUTATION_IN:
						$permutation = "IN";
						$value = "(" . join(', ', array_fill(1, count($item->value), '?')) . ")";
						break;
					case Query::PERMUTATION_REGEXP:
						$permutation = 'REGEXP';
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
				$on = is_null($item->on) ? $query->table : $item->on;
				$statement[] = "LEFT JOIN {$item->table} ON {$on}.{$item->table}_id = {$item->table}.id";
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
			throw new \Exception("TODO: HAVING");
		}
		return null;
	}

}