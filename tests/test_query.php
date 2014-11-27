<?php

include '../vendor/autoload.php';

use PlugQuery\Connection\Manager;
use PlugQuery\Connection\Mysql;
use PlugQuery\Model;
use PlugQuery\Query;


echo phpversion();

$manager = new Manager();
$manager->addConnection('site', new Mysql('root', 'admin', 'test'));

echo '<pre>';

echo '<h3>Query::select by id</h3>';
$a1 = Query::select('account', 1);
echo $a1 . "\n\n"; print_r($a1);
print_r($a1->findOne());

echo '<h3>Query::insert</h3>';
$id = Query::insert('account', array( 'username' => 'inserted' ))->execute();
echo $id . "\n\n"; print_r($id);

echo '<h3>Query::update by id</h3>';
$updated = Query::update('account', array( 'username' => 'updated' ))->where('id', 2);
var_dump($updated->toSql());
var_dump(implode(', ', $updated->data));
print_r($updated->execute());

echo '<h3>Query::select all</h3>';
$accounts = Query::select('account')->orderBy('id', 'desc');
var_dump($accounts->toSql());
print_r($accounts->find());