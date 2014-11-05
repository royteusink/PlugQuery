<?php

namespace PlugQuery\Connection;

use PlugQuery\Connection;

class Mysql extends Connection {

	private $database;
	private $hostname;
	private $port;

	public function __construct($username, $password, $database, $hostname = 'localhost', $port = null) {

		$this->database = $database;
		$this->hostname = $hostname;
		$this->port = $port;

		$options = array(
			self::ATTR_DEFAULT_FETCH_MODE => self::FETCH_OBJ
		);

		parent::__construct($this->getDSN(), $username, $password, $options);
	}

	public function getDSN() {
		return "mysql:"
			. ($this->hostname ? "host={$this->hostname};" : null)
			. ($this->database ? "dbname={$this->database};" : null)
			. ($this->port ? "port={$this->port};" : null);
	}
}