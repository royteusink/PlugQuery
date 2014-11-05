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

	public function find() {

		$dbh = Manager::getInstance()->getActive();
		$sth = $dbh->prepare($this->toSql());

		$i = 1;
		if($this->wheres) {
			foreach($this->wheres as $index => $where) {
				$type = Connection::PARAM_STR;
				if(is_int($where['value'])) {
					$type = Connection::PARAM_INT;
				}
				$sth->bindValue($i, $where['value'], $type);
				$i++;
			}
		}

		$sth->execute();
		return $sth->fetchAll();

	}

	public function findOne() {

		$dbh = Manager::getInstance()->getActive();
		$sth = $dbh->prepare($this->toSql());

		$i = 1;
		if($this->wheres) {
			foreach($this->wheres as $index => $where) {
				$type = Connection::PARAM_STR;
				if(is_int($where['value'])) {
					$type = Connection::PARAM_INT;
				}
				$sth->bindValue($i, $where['value'], $type);
				$i++;
			}
		}

		$sth->execute();
		return $sth->fetch();

	}

	public function execute() {
		$dbh = Manager::getInstance()->getActive();
		$sth = $dbh->prepare($this->toSql());

		$i = 1;
		if($this->data) {
			foreach($this->data as $index => $value) {
				$type = Connection::PARAM_STR;
				if(is_int($where['value'])) {
					$type = Connection::PARAM_INT;
				}
				$sth->bindValue($i, $value, $type);
				$i++;
			}
		}

		if($this->wheres) {
			foreach($this->wheres as $index => $where) {
				$type = Connection::PARAM_STR;
				if(is_int($where['value'])) {
					$type = Connection::PARAM_INT;
				}
				$sth->bindValue($i, $where['value'], $type);
				$i++;
			}
		}

		$sth->execute();
		return $dbh->lastInsertId();
	}

}