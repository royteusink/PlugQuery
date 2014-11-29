<?php

//  ./vendor/phpunit/phpunit/phpunit.php --colors tests/QueryTest

use PlugQuery\Query;

class QueryTest extends PHPUnit_Framework_TestCase {
	
	public function testSelectAll() {
		$query = Query::select('account');
		echo "\n" . $query->toSql();
		$this->assertEquals("SELECT account.* FROM account", $query->toSql());
	}

	public function testSelectById() {
		$query = Query::select('account', 1);
		echo "\n" . $query->toSql();
		$this->assertEquals("SELECT account.* FROM account WHERE account.id = ?", $query->toSql());
	}

	public function testSelectWhere() {
		$query = Query::select('account')->where('id', 1);
		echo "\n" . $query->toSql();
		$this->assertEquals("SELECT account.* FROM account WHERE account.id = ?", $query->toSql());
	}

	public function testSelectWhereNot() {
		$query = Query::select('account')->whereNot('id', 1);
		echo "\n" . $query->toSql();
		$this->assertEquals("SELECT account.* FROM account WHERE account.id <> ?", $query->toSql());
	}

	public function testSelectWhereNull() {
		$query = Query::select('account')->where('username', null);
		echo "\n" . $query->toSql();
		$this->assertEquals("SELECT account.* FROM account WHERE account.username IS NULL", $query->toSql());
	}

	public function testSelectWhereNotNull() {
		$query = Query::select('account')->whereNot('username', null);
		echo "\n" . $query->toSql();
		$this->assertEquals("SELECT account.* FROM account WHERE account.username IS NOT NULL", $query->toSql());
	}

	public function testSelectWhereMultiple() {
		$query = Query::select('account')->where('id', 1)->where('username', 'test');
		echo "\n" . $query->toSql();
		$this->assertEquals("SELECT account.* FROM account WHERE account.id = ? AND account.username = ?", $query->toSql());
	}

	public function testSelectOrWhereMultiple() {
		$query = Query::select('account')->where('id', 1)->orWhere('username', 'test');
		echo "\n" . $query->toSql();
		$this->assertEquals("SELECT account.* FROM account WHERE account.id = ? OR account.username = ?", $query->toSql());
	}

	public function testSelectAllOrderOneDesc() {
		$query = Query::select('account', 1)->orderBy('id', 'desc');
		echo "\n" . $query->toSql();
		$this->assertEquals("SELECT account.* FROM account WHERE account.id = ? ORDER BY account.id DESC", $query->toSql());
	}

	public function testSelectAllOrderOneAsc() {
		$query = Query::select('account', 1)->orderBy('id', 'desc');
		echo "\n" . $query->toSql();
		$this->assertEquals("SELECT account.* FROM account WHERE account.id = ? ORDER BY account.id DESC", $query->toSql());
	}

	public function testSelectAllOrderMultiple() {
		$query = Query::select('account')->orderBy('id', 'desc')->orderBy('username', 'asc');
		echo "\n" . $query->toSql();
		$this->assertEquals("SELECT account.* FROM account ORDER BY account.id DESC, account.username ASC", $query->toSql());
	}

	public function testSelectWithJoin() {
		$query = Query::select('account')->join('role');
		echo "\n" . $query->toSql();
		$this->assertEquals("SELECT account.*, role.* FROM account LEFT JOIN role ON account.role_id = role.id", $query->toSql());
	}

	public function testSelectWithJoinSpecified() {
		$query = Query::select('account', array('id', 'username', 'password'))->join('role', array('id', 'name'));
		echo "\n" . $query->toSql();
		$this->assertEquals("SELECT account.id, account.username, account.password, role.id, role.name FROM account LEFT JOIN role ON account.role_id = role.id", $query->toSql());
	}

	public function testSelectWithJoinSpecifiedId() {
		$query = Query::select('account', array('id', 'username', 'password'), 22)->join('role', array('id', 'name'));
		echo "\n" . $query->toSql();
		$this->assertEquals("SELECT account.id, account.username, account.password, role.id, role.name FROM account LEFT JOIN role ON account.role_id = role.id WHERE account.id = ?", $query->toSql());
	}

	public function testSelectWithJoinWhere() {
		$query = Query::select('account')->join('role')->where('role.name', 'admin');
		echo "\n" . $query->toSql();
		$this->assertEquals("SELECT account.*, role.* FROM account LEFT JOIN role ON account.role_id = role.id WHERE role.name = ?", $query->toSql());
	}

	public function testSelectLimit() {
		$query = Query::select('account')->limit(3);
		echo "\n" . $query->toSql();
		$this->assertEquals("SELECT account.* FROM account LIMIT 3", $query->toSql());
	}

	public function testSelectLimitSecondParam() {
		$query = Query::select('account')->limit(3, 5);
		echo "\n" . $query->toSql();
		$this->assertEquals("SELECT account.* FROM account LIMIT 3, 5", $query->toSql());
	}

	public function testSelectAllGroupBy() {
		$query = Query::select('stats')->groupBy('sales');
		echo "\n" . $query->toSql();
		$this->assertEquals("SELECT stats.* FROM stats GROUP BY stats.sales", $query->toSql());
	}

	public function testSelectAllMultipleGroupBy() {
		$query = Query::select('stats')->groupBy('sales')->groupBy('name');
		echo "\n" . $query->toSql();
		$this->assertEquals("SELECT stats.* FROM stats GROUP BY stats.sales, stats.name", $query->toSql());
	}

	public function testSelectWhereIn() {
		$query = Query::select('stats')->whereIn('source', array('visitors','logins','clicks'));
		echo "\n" . $query->toSql();
		$this->assertEquals("SELECT stats.* FROM stats WHERE stats.source IN (?, ?, ?)", $query->toSql());
	}

	public function testSelectOrWhereIn() {
		$query = Query::select('stats')->where('times', 1)->orWhereIn('source', array('visitors'));
		echo "\n" . $query->toSql();
		$this->assertEquals("SELECT stats.* FROM stats WHERE stats.times = ? OR stats.source IN (?)", $query->toSql());
	}

	public function testSelectWhereLike() {
		$query = Query::select('account')->whereLike('name', '%roy');
		echo "\n" . $query->toSql();
		$this->assertEquals("SELECT account.* FROM account WHERE account.name LIKE ?", $query->toSql());
	}

	public function testSelectNotWhereLike() {
		$query = Query::select('account')->whereNotLike('name', '%roy');
		echo "\n" . $query->toSql();
		$this->assertEquals("SELECT account.* FROM account WHERE account.name NOT LIKE ?", $query->toSql());
	}

	public function testInsert() {
		$query = Query::insert('account', array('username' => 'a'));
		echo "\n" . $query->toSql();
		$this->assertEquals("INSERT INTO account (username) VALUES (?)", $query->toSql());
	}

	public function testInsertMultiple() {
		$query = Query::insert('account', array('username' => 'a', 'password' => 123));
		echo "\n" . $query->toSql();
		$this->assertEquals("INSERT INTO account (username, password) VALUES (?, ?)", $query->toSql());
	}

	public function testUpdate() {
		$query = Query::update('account', array('username' => 'a'))->where('id', 1);
		echo "\n" . $query->toSql();
		$this->assertEquals("UPDATE account SET username = ? WHERE account.id = ?", $query->toSql());
	}

	public function testUpdateMutiple() {
		$query = Query::update('account', array('username' => 'a', 'password' => 123))->where('id', 1);
		echo "\n" . $query->toSql();
		$this->assertEquals("UPDATE account SET username = ?, password = ? WHERE account.id = ?", $query->toSql());
	}

	public function testDeleteById() {
		$query = Query::delete('account', 1);
		echo "\n" . $query->toSql();
		$this->assertEquals("DELETE FROM account WHERE account.id = ?", $query->toSql());
	}

	public function testDeleteWhere() {
		$query = Query::delete('account')->where('username', 'admin')->where('role_id', 1);
		echo "\n" . $query->toSql();
		$this->assertEquals("DELETE FROM account WHERE account.username = ? AND account.role_id = ?", $query->toSql());
	}

	public function testCreateTable() {
		$query = Query::create('account')->addColumn('username', 'varchar', 255)->addColumn('password', 'varchar', 255);
		echo "\n" . $query->toSql();
		$this->assertEquals("CREATE TABLE IF NOT EXISTS account ( id INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT, username varchar(255), password varchar(255) )", $query->toSql());
	}

}