<?php

//  ./vendor/phpunit/phpunit/phpunit.php --colors tests/QueryTest

use PlugQuery\Query;

class QueryTest extends PHPUnit_Framework_TestCase {
	
	public function testSelectAll() {
		$query = Query::select('account');
		echo "\n" . $query->toSql();
		$this->assertEquals("SELECT account.* FROM account", $query->toSql());
		$this->assertEmpty($query->getValues());
	}

	public function testSelectWhereOrs() {
		$query = Query::select('account')->where('username', 'admin')->or->where('username','visitor')->where('role_id', 1);
		echo "\n" . $query->toSql();
		$this->assertEquals("SELECT account.* FROM account WHERE account.username = ? OR account.username = ? AND account.role_id = ?", $query->toSql());
		$this->assertEquals(array('admin', 'visitor', 1), $query->getValues());
	}

	public function testSelectDistinct() {
		$query = Query::select('customer', array('city'))->distinct();
		echo "\n" . $query->toSql();
		$this->assertEquals("SELECT DISTINCT customer.city FROM customer", $query->toSql());
		$this->assertEmpty($query->getValues());
	}

	public function testSelectById() {
		$query = Query::select('account', 1);
		echo "\n" . $query->toSql();
		$this->assertEquals("SELECT account.* FROM account WHERE account.id = ?", $query->toSql());
		$this->assertEquals(array(1), $query->getValues());
	}

	public function testSelectWhere() {
		$query = Query::select('account')->where('id', 1);
		echo "\n" . $query->toSql();
		$this->assertEquals("SELECT account.* FROM account WHERE account.id = ?", $query->toSql());
		$this->assertEquals(array(1), $query->getValues());
	}

	public function testSelectWhereLess() {
		$query = Query::select('account')->whereLess('visits', 10);
		echo "\n" . $query->toSql();
		$this->assertEquals("SELECT account.* FROM account WHERE account.visits < ?", $query->toSql());
		$this->assertEquals(array(10), $query->getValues());
	}

	public function testSelectWhereMore() {
		$query = Query::select('account')->whereMore('visits', 10);
		echo "\n" . $query->toSql();
		$this->assertEquals("SELECT account.* FROM account WHERE account.visits > ?", $query->toSql());
		$this->assertEquals(array(10), $query->getValues());
	}

	public function testSelectWhereLessEqual() {
		$query = Query::select('account')->whereLessEqual('visits', 10);
		echo "\n" . $query->toSql();
		$this->assertEquals("SELECT account.* FROM account WHERE account.visits <= ?", $query->toSql());
		$this->assertEquals(array(10), $query->getValues());
	}

	public function testSelectWhereMoreEqual() {
		$query = Query::select('account')->whereMoreEqual('visits', 10);
		echo "\n" . $query->toSql();
		$this->assertEquals("SELECT account.* FROM account WHERE account.visits >= ?", $query->toSql());
		$this->assertEquals(array(10), $query->getValues());
	}

	public function testSelectWhereNot() {
		$query = Query::select('account')->whereNot('id', 1);
		echo "\n" . $query->toSql();
		$this->assertEquals("SELECT account.* FROM account WHERE account.id != ?", $query->toSql());
		$this->assertEquals(array(1), $query->getValues());
	}

	public function testSelectWhereNull() {
		$query = Query::select('account')->where('username', null);
		echo "\n" . $query->toSql();
		$this->assertEquals("SELECT account.* FROM account WHERE account.username IS ?", $query->toSql());
		$this->assertEquals(array(null), $query->getValues());
	}

	public function testSelectWhereNotNull() {
		$query = Query::select('account')->whereNot('username', null);
		echo "\n" . $query->toSql();
		$this->assertEquals("SELECT account.* FROM account WHERE account.username IS NOT ?", $query->toSql());
		$this->assertEquals(array(null), $query->getValues());
	}

	public function testSelectWhereExpression() {
		$query = Query::select('account')->whereExp('username', '^te');
		echo "\n" . $query->toSql();
		$this->assertEquals("SELECT account.* FROM account WHERE account.username REGEXP ?", $query->toSql());
		$this->assertEquals(array('^te'), $query->getValues());
	}

	public function testSelectWhereMultiple() {
		$query = Query::select('account')->where('id', 1)->where('username', 'test');
		echo "\n" . $query->toSql();
		$this->assertEquals("SELECT account.* FROM account WHERE account.id = ? AND account.username = ?", $query->toSql());
		$this->assertEquals(array(1, 'test'), $query->getValues());
	}

	public function testSelectOrWhereMultiple() {
		$query = Query::select('account')->where('id', 1)->or->where('username', 'test');
		echo "\n" . $query->toSql();
		$this->assertEquals("SELECT account.* FROM account WHERE account.id = ? OR account.username = ?", $query->toSql());
		$this->assertEquals(array(1, 'test'), $query->getValues());
	}

	public function testSelectAllOrderOneDesc() {
		$query = Query::select('account', 1)->orderBy('id', 'desc');
		echo "\n" . $query->toSql();
		$this->assertEquals("SELECT account.* FROM account WHERE account.id = ? ORDER BY account.id DESC", $query->toSql());
		$this->assertEquals(array(1), $query->getValues());
	}

	public function testSelectAllOrderOneAsc() {
		$query = Query::select('account', 1)->orderBy('id', 'desc');
		echo "\n" . $query->toSql();
		$this->assertEquals("SELECT account.* FROM account WHERE account.id = ? ORDER BY account.id DESC", $query->toSql());
		$this->assertEquals(array(1), $query->getValues());
	}

	public function testSelectAllOrderMultiple() {
		$query = Query::select('account')->orderBy('id', 'desc')->orderBy('username', 'asc');
		echo "\n" . $query->toSql();
		$this->assertEquals("SELECT account.* FROM account ORDER BY account.id DESC, account.username ASC", $query->toSql());
		$this->assertEmpty($query->getValues());
	}

	public function testSelectWithJoin() {
		$query = Query::select('account')->join('role');
		echo "\n" . $query->toSql();
		$this->assertEquals("SELECT account.*, role.* FROM account LEFT JOIN role ON account.role_id = role.id", $query->toSql());
		$this->assertEmpty($query->getValues());
	}

	public function testSelectWithJoinOn() {
		$query = Query::select('account')->join('role')->join('permission', 'role');
		echo "\n" . $query->toSql();
		$this->assertEquals("SELECT account.*, role.*, permission.* FROM account LEFT JOIN role ON account.role_id = role.id LEFT JOIN permission ON role.permission_id = permission.id", $query->toSql());
		$this->assertEmpty($query->getValues());
	}

	public function testSelectWithJoinSpecified() {
		$query = Query::select('account', array('id', 'username', 'password'))->join('role', array('id', 'name'));
		echo "\n" . $query->toSql();
		$this->assertEquals("SELECT account.id, account.username, account.password, role.id, role.name FROM account LEFT JOIN role ON account.role_id = role.id", $query->toSql());
		$this->assertEmpty($query->getValues());
	}

	public function testSelectWithJoinOnSpecified() {
		$query = Query::select('account', array('id', 'username', 'password'))->join('role', array('id', 'name'))->join('permission','role', array('id', 'name'));
		echo "\n" . $query->toSql();
		$this->assertEquals("SELECT account.id, account.username, account.password, role.id, role.name, permission.id, permission.name FROM account LEFT JOIN role ON account.role_id = role.id LEFT JOIN permission ON role.permission_id = permission.id", $query->toSql());
		$this->assertEmpty($query->getValues());
	}

	public function testSelectWithJoinSpecifiedId() {
		$query = Query::select('account', array('id', 'username', 'password'), 22)->join('role', array('id', 'name'));
		echo "\n" . $query->toSql();
		$this->assertEquals("SELECT account.id, account.username, account.password, role.id, role.name FROM account LEFT JOIN role ON account.role_id = role.id WHERE account.id = ?", $query->toSql());
		$this->assertEquals(array(22), $query->getValues());
	}

	public function testSelectWithJoinWhere() {
		$query = Query::select('account')->join('role')->where('role.name', 'admin');
		echo "\n" . $query->toSql();
		$this->assertEquals("SELECT account.*, role.* FROM account LEFT JOIN role ON account.role_id = role.id WHERE role.name = ?", $query->toSql());
		$this->assertEquals(array('admin'), $query->getValues());
	}

	public function testSelectLimit() {
		$query = Query::select('account')->limit(3);
		echo "\n" . $query->toSql();
		$this->assertEquals("SELECT account.* FROM account LIMIT 3", $query->toSql());
		$this->assertEmpty($query->getValues());
	}

	public function testSelectLimitSecondParam() {
		$query = Query::select('account')->limit(3, 5);
		echo "\n" . $query->toSql();
		$this->assertEquals("SELECT account.* FROM account LIMIT 3, 5", $query->toSql());
		$this->assertEmpty($query->getValues());
	}

	public function testSelectAllGroupBy() {
		$query = Query::select('stats')->groupBy('sales');
		echo "\n" . $query->toSql();
		$this->assertEquals("SELECT stats.* FROM stats GROUP BY stats.sales", $query->toSql());
		$this->assertEmpty($query->getValues());
	}

	public function testSelectAllMultipleGroupBy() {
		$query = Query::select('stats')->groupBy('sales')->groupBy('name');
		echo "\n" . $query->toSql();
		$this->assertEquals("SELECT stats.* FROM stats GROUP BY stats.sales, stats.name", $query->toSql());
		$this->assertEmpty($query->getValues());
	}

	public function testSelectWhereIn() {
		$query = Query::select('stats')->whereIn('source', array('visitors','logins','clicks'));
		echo "\n" . $query->toSql();
		$this->assertEquals("SELECT stats.* FROM stats WHERE stats.source IN (?, ?, ?)", $query->toSql());
		$this->assertEquals(array('visitors', 'logins', 'clicks'), $query->getValues());
	}

	public function testSelectOrWhereIn() {
		$query = Query::select('stats')->where('times', 1)->or->whereIn('source', array('visitors'));
		echo "\n" . $query->toSql();
		$this->assertEquals("SELECT stats.* FROM stats WHERE stats.times = ? OR stats.source IN (?)", $query->toSql());
		$this->assertEquals(array(1, 'visitors'), $query->getValues());
	}

	public function testSelectWhereLike() {
		$query = Query::select('account')->whereLike('name', '%roy');
		echo "\n" . $query->toSql();
		$this->assertEquals("SELECT account.* FROM account WHERE account.name LIKE ?", $query->toSql());
		$this->assertEquals(array('%roy'), $query->getValues());
	}

	public function testSelectNotWhereLike() {
		$query = Query::select('account')->whereNotLike('name', '%roy');
		echo "\n" . $query->toSql();
		$this->assertEquals("SELECT account.* FROM account WHERE account.name NOT LIKE ?", $query->toSql());
		$this->assertEquals(array('%roy'), $query->getValues());
	}

	public function testSelectWhereBetween() {
		$query = Query::select('stats')->whereBetween('visits', 3, 10);
		echo "\n" . $query->toSql();
		$this->assertEquals("SELECT stats.* FROM stats WHERE stats.visits BETWEEN ? AND ?", $query->toSql());
		$this->assertEquals(array(3, 10), $query->getValues());
	}

	public function testSelectWhereNotBetween() {
		$query = Query::select('stats')->whereNotBetween('visits', 3, 10);
		echo "\n" . $query->toSql();
		$this->assertEquals("SELECT stats.* FROM stats WHERE stats.visits NOT BETWEEN ? AND ?", $query->toSql());
		$this->assertEquals(array(3, 10), $query->getValues());
	}

	public function testInsert() {
		$query = Query::insert('account', array('username' => 'a'));
		echo "\n" . $query->toSql();
		$this->assertEquals("INSERT INTO account (username) VALUES (?)", $query->toSql());
		$this->assertEquals(array('a'), $query->getValues());
	}

	public function testInsertMultiple() {
		$query = Query::insert('account', array('username' => 'a', 'password' => 123));
		echo "\n" . $query->toSql();
		$this->assertEquals("INSERT INTO account (username, password) VALUES (?, ?)", $query->toSql());
		$this->assertEquals(array('a', 123), $query->getValues());
	}

	public function testUpdate() {
		$query = Query::update('account', array('username' => 'a'))->where('id', 1);
		echo "\n" . $query->toSql();
		$this->assertEquals("UPDATE account SET username = ? WHERE account.id = ?", $query->toSql());
		$this->assertEquals(array('a', 1), $query->getValues());
	}

	public function testUpdateSetNull() {
		$query = Query::update('account', array('username' => null))->where('id', 1);
		echo "\n" . $query->toSql();
		$this->assertEquals("UPDATE account SET username = ? WHERE account.id = ?", $query->toSql());
		$this->assertEquals(array(null, 1), $query->getValues());
	}

	public function testUpdateMutiple() {
		$query = Query::update('account', array('username' => 'a', 'password' => 1))->where('id', 1);
		echo "\n" . $query->toSql();
		$this->assertEquals("UPDATE account SET username = ?, password = ? WHERE account.id = ?", $query->toSql());
		$this->assertEquals(array('a', 1, 1), $query->getValues());
	}

	public function testDeleteById() {
		$query = Query::delete('account', 1);
		echo "\n" . $query->toSql();
		$this->assertEquals("DELETE FROM account WHERE account.id = ?", $query->toSql());
		$this->assertEquals(array(1), $query->getValues());
	}

	public function testDeleteWhere() {
		$query = Query::delete('account')->where('username', 'admin')->where('role_id', 1);
		echo "\n" . $query->toSql();
		$this->assertEquals("DELETE FROM account WHERE account.username = ? AND account.role_id = ?", $query->toSql());
		$this->assertEquals(array('admin', 1), $query->getValues());
	}

	public function testCreateTable() {
		$query = Query::create('account')->addColumn('username', 'varchar', 255)->addColumn('password', 'varchar', 255);
		echo "\n" . $query->toSql();
		$this->assertEquals("CREATE TABLE IF NOT EXISTS account ( id INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT, username varchar(255), password varchar(255) )", $query->toSql());
		$this->assertEmpty($query->getValues());
	}

	public function testComplexExample() {
		$query = Query::select('user', array('id', 'firstname'))
			->join('account', array('username', 'password'))
			->join('permission', 'account', array('name'))
			->where('permission.name', 'administrator')
			->or
			->where('permission.name', 'visitor')
			->whereIN('username', array('patrick', 'roy'))
			->orderBy('username', 'desc')
			->orderBy('permission.name', 'asc')
			->limit(2, 10);
		echo "\n" . $query->toSql();
		$this->assertEquals("SELECT user.id, user.firstname, account.username, account.password, permission.name FROM user LEFT JOIN account ON user.account_id = account.id LEFT JOIN permission ON account.permission_id = permission.id WHERE permission.name = ? OR permission.name = ? AND user.username IN (?, ?) ORDER BY user.username DESC, permission.name ASC LIMIT 2, 10" , $query->toSql());
		$this->assertEquals(array('administrator', 'visitor', 'patrick', 'roy'), $query->getValues());
	}

}