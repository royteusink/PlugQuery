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

	public function getValues() {

		$values = array();

		if($this->data) {
			foreach($this->data as $value) {
				$values[] = $value;
			}
		}

		if($this->wheres) {
			foreach($this->wheres as $item) {
				if(is_array($item->value)) {
					foreach ($item->value as $value) {
						$values[] = $value;
					}
				} else {
					$values[] = $item->value;
				}
			}
		}

		return $values;
	}

	private function prepareExecute() {

		$dbh = Manager::getInstance()->getActive();
		$sth = $dbh->prepare($this->toSql());
		
		foreach($this->getValues as $index => $value) {
			$sth->bindParam($index + 1, $value);
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