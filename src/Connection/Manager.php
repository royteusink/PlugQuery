<?php

namespace PlugQuery\Connection;

use PlugQuery\Connection;

class Manager {

	public static $instance;

	protected $connections = array();
	protected $active;

	public function __construct() {
		self::$instance = $this;
	}

	public function addConnection($name, Connection $connection) {
		$this->connections[$name] = $connection;
	}

	public function getConnection($name) {
		return $this->connections[$name];
	}

	public function getConnections() {
		return $this->connections;
	}

	public function setActive($name) {
		$this->active = $this->getConnection($name);
	}

	public function getActive() {
		return $this->active ? $this->active : reset($this->connections);
	}

	public static function getInstance() {
		return self::$instance;
	}

}