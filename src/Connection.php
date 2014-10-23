<?php

namespace PlugQuery;

abstract class Connection extends \PDO {

	public abstract function getDSN();

}