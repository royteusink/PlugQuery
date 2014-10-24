<?php

namespace PlugQuery\Connection;

use PlugQuery\Connection;

class Manager {

	protected $connections = array();

	public function addConnection($name, Connection $connection) {
		$this->connections[$name] = $connection;
	}

	public function getConnection($name) {
		return $this->connections[$name];
	}

	public function getConnections() {
		return $this->connections;
	}

}