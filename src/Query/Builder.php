<?php

namespace PlugQuery\Query;

use PlugQuery\Query;
use PlugQuery\Query\Method as QueryMethod;
use PlugQuery\Query\Select;
use PlugQuery\Query\Insert;
use PlugQuery\Query\Update;

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

		return 'Error';
	}

	public function buildSelectQuery(Select $query) {
		$statement = "SELECT * FROM " . $query->table . " ";
		$statement .= $this->processWheres($query);
		return $statement;
	}

	public function buildInsertQuery(Insert $query) {

		$statement = "INSERT INTO " . $query->table . " ";
		$statement .= "(" . join(', ', array_keys($query->data)) . ") ";
		$statement .= "VALUES (";
		$statement .= join(', ', array_fill(1, count($query->data), '?')) . ") ";

		return $statement;
	}

	public function buildUpdateQuery(Update $query) {

		$statement = "UPDATE " . $query->table . " ";

		$columns = join(', ', 
			array_map(function($key) {
					return $key . " = ?";
				}, 
				array_keys($query->data)
			)
		);

		$statement .= "SET " . $columns . " ";
		$statement .= $this->processWheres($query);

		return $statement;
	}

	private function processWheres($query) {
		if($query->wheres) {
			$statement .= "WHERE ";
			foreach($query->wheres as $where) {
				$statement .= $where['column'];
				switch($where['permutation']) {
					case Query::PERMUTATION_EQUAL:
						$statement .= " = ? ";
					break;
				}
			}
		}
		return $statement;
	}

}