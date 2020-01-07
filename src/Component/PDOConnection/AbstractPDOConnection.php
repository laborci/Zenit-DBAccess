<?php namespace Zenit\Bundle\DBAccess\Component\PDOConnection;

use Zenit\Bundle\DBAccess\Interfaces\PDOConnectionInterface;

abstract class AbstractPDOConnection extends \PDO implements PDOConnectionInterface{

	/** @var \Zenit\Bundle\DBAccess\Interfaces\SqlLogHookInterface */
	protected $sqlLogHook;
	public function setSqlLogHook($hook){$this->sqlLogHook = $hook;}

	public function query($statement, $mode = \PDO::ATTR_DEFAULT_FETCH_MODE, $arg3 = null, array $ctorargs = []) {
		if(!is_null($this->sqlLogHook))$this->sqlLogHook->log($statement);
		return parent::query($statement);
	}

}