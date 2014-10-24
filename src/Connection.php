<?php

namespace PlugQuery;

use \PDO as PDO;

abstract class Connection extends PDO {

	public abstract function getDSN();

}