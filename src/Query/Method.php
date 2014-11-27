<?php

namespace PlugQuery\Query;

use PlugQuery\Query\Builder;
use PlugQuery\Connection;
use PlugQuery\Connection\Manager;

abstract class Method {

	public function toSql() {
		$builder = new Builder();
		return $builder->build($this);
	}

	public function __toString() {
		return $this->toSql();
	}

	private function prepareExecute() {

		$dbh = Manager::getInstance()->getActive();
		$sth = $dbh->prepare($this->toSql());
		$index = 1;

		if($this->data) {
			foreach($this->data as $value) {
				$sth->bindParam($index, $value);
				$index++;
			}
		}

		if($this->wheres) {
			foreach($this->wheres as $item) {
				$sth->bindParam($index, $item->value);
				$index++;
			}
		}		
		
		$sth->execute();
		return $sth;
	}

	public function find() {
		return $this->prepareExecute()->fetchAll();
	}

	public function findOne() {
		return $this->prepareExecute()->fetch();
	}

	public function execute() {
		$this->prepareExecute();
		$dbh = Manager::getInstance()->getActive();
		return $dbh->lastInsertId();
	}

}