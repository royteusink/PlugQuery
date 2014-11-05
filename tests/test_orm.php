<?php

include '../vendor/autoload.php';

use PlugQuery\Connection\Manager;
use PlugQuery\Connection\Mysql;
use PlugQuery\Model;
use PlugQuery\Query;

echo '<pre>';

$manager = new Manager();
$manager->addConnection('site', new Mysql('root', 'admin', 'test'));

class Account extends Model {
	public static $schema = array(
		'username' => 'string'
	);
}

echo '<h3>CRUD</h3>';

$all = Account::find();
echo '<table>';
echo '<tr><th>ID</th><th>USERNAME</th></tr>';
foreach($all as $a) {
	echo '<tr><td>' . $a->id . '</td><td>' . $a->username . '</td></tr>';
}
echo '</table>';

echo '<h3>Create a new Account and save</h3>';
$account = new Account();
$account->username = 'admin';
$account->save();
print_r($account);

echo '<h3>Iterate over object as array</h3>';
foreach ($account as $key => $value) {
	echo $key . ' -> ' . $value . '<BR>';
}

echo '<h3>Change account and update</h3>';
$account->username = 'demo';
$account->save();
var_dump($account);

echo '<h3>List all values</h3>';
var_dump($account->values);

echo '<h3>Print a value</h3>';
var_dump($account->username);

echo '<h3>Select a Account by id</h3>';
$one_account = Account::id(1);
var_dump($one_account);