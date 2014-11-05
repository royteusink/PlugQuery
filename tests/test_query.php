<?php

include '../vendor/autoload.php';

use PlugQuery\Connection\Manager;
use PlugQuery\Connection\Mysql;
use PlugQuery\Model;
use PlugQuery\Query;

$manager = new Manager();
$manager->addConnection('site', new Mysql('root', 'admin', 'test'));

echo '<pre>';

echo '<h3>Query::select by id</h3>';
$a1 = Query::select('account', 1);
echo $a1 . "\n\n"; print_r($a1);
print_r($a1->findOne());

echo '<h3>Query::insert</h3>';
$a2 = Query::insert('account', array( 'username' => 'inserted' ))->execute();
echo $a2 . "\n\n"; print_r($a2);

echo '<h3>Query::update by id</h3>';
$a3 = Query::update('account', array( 'username' => 'updated' ))->where('id', '1')->execute();
echo $a3 . "\n\n"; print_r($a3);

echo '<h3>Query::select by id</h3>';
$a1 = Query::select('account', 2);
echo $a1 . "\n\n"; print_r($a1);
print_r($a1->findOne());