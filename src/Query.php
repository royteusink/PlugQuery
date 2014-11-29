<?php

namespace PlugQuery;

use PlugQuery\Query\Select;
use PlugQuery\Query\Insert;
use PlugQuery\Query\Update;
use PlugQuery\Query\Delete;
use PlugQuery\Query\Create;

class Query {

	const BOOLEAN_OR = 1;
	const BOOLEAN_AND = 2;
	
	const PERMUTATION_EQUAL = 1;
	const PERMUTATION_LESS = 2;
	const PERMUTATION_MORE = 3;
	const PERMUTATION_NOTEQUAL = 4;
	const PERMUTATION_LESSEQUAL = 5;
	const PERMUTATION_MOREEQUAL = 6;
	const PERMUTATION_IN = 7;
	const PERMUTATION_LIKE = 8;
	const PERMUTATION_NOTLIKE = 9;

	// (string) = $table
	// (string, int) = $table, $id
	// (string, array) = $table, $columns
	// (string, array, int) = $table, $columns, $id
	public static function select($table, $arg1 = null, $arg2 = null) {
		$select = new Select();
		
		$id = null;
		$columns = null;

		if(is_int($arg1)) $id = $arg1;
		if(is_int($arg2)) $id = $arg2;
		if(is_array($arg1)) $columns = $arg1;

		$select->table($table, $columns);
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

	public static function delete($table, $id = null) {
		$delete = new Delete();
		$delete->table($table);
		if($id) $delete->where('id', $id);
		return $delete;
	}

	public static function create($table) {
		$create = new Create();
		$create->table($table);
		return $create;
	}

}