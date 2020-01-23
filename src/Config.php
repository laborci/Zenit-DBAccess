<?php namespace Zenit\Bundle\DBAccess;

use Zenit\Core\Env\Component\ConfigReader;

class Config extends ConfigReader{

	/** @var array */
	public $defaults = "bundle.dbaccess.defaults";
	/** @var array */
	public $databases = "bundle.dbaccess.databases";
	public $defaultDatabase = "bundle.dbaccess.default-database";

	public $pathTmp = "path.tmp";
	public $pathDev = "path.dev";

}