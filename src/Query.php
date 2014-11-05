<?php

namespace PlugQuery;

use PlugQuery\Query\Select;
use PlugQuery\Query\Insert;
use PlugQuery\Query\Update;

class Query {

	const PERMUTATION_EQUAL = 1;
	const PERMUTATION_LESS = 2;
	const PERMUTATION_MORE = 3;

	public static function select($table, $id = null) {
		$select = new Select();
		$select->table($table);
		if($id) $select->where('id', $id);
		return $select;
	}

	public static function insert($table, $data) {
		$insert = new Insert();
		$insert->table($table, $data);
		return $insert;
	}

	public static function update($table, $data) {
		$insert = new Update();
		$insert->table($table, $data);
		return $insert;
	}

}