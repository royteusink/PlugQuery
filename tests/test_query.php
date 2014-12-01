<?php

include '../vendor/autoload.php';

use PlugQuery\Connection\Manager;
use PlugQuery\Connection\Mysql;
use PlugQuery\Model;
use PlugQuery\Query;

echo '<pre>';
echo phpversion();

$manager = new Manager();
$manager->addConnection('site', new Mysql('root', 'admin', 'test'));


$test = Query::select('account')->where('username', 'admin')->or->where('username','visitor')->where('role_id', 1);
print_r($test->getValues());
print_r($test->toSql());


// echo '<h3>Query::select by id</h3>';
// $a1 = Query::select('account', 1);
// echo $a1 . "\n\n"; print_r($a1);
// print_r($a1->findOne());

// echo '<h3>Query::insert</h3>';
// $id = Query::insert('account', array( 'username' => 'inserted' ))->execute();
// echo $id . "\n\n"; print_r($id);

// echo '<h3>Query::update by id</h3>';
// $updated = Query::update('account', array( 'username' => 'updated' ))->where('id', 2);
// var_dump($updated->toSql());
// var_dump(implode(', ', $updated->data));
// print_r($updated->execute());

// echo '<h3>Query::select all</h3>';
// $accounts = Query::select('account')->orderBy('id', 'desc');
// var_dump($accounts->toSql());
// print_r($accounts->find());