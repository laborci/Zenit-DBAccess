<?php namespace Zenit\Bundle\DBAccess\Component;

use Zenit\Bundle\DBAccess\Component\PDOConnection\MysqlPDOConnection;
use Zenit\Bundle\DBAccess\Config;
use Zenit\Bundle\DBAccess\Interfaces\PDOConnectionInterface;
use Zenit\Bundle\DBAccess\Interfaces\SqlLogHookInterface;
use Zenit\Core\ServiceManager\Component\ServiceContainer;
use PDO;

class ConnectionFactory{

	static $connections = [];

	static public function get($name): PDOConnectionInterface{
		if (array_key_exists($name, self::$connections)) return self::$connections[$name];
		$databases = Config::Service()->databases;
		if (array_key_exists($name, $databases)){
			$defaults = Config::Service()->defaults;
			$settings = array_merge($defaults, $databases[$name]);
			$connection = static::factory($settings);
			return $connection;
		}
		return null;
	}

	static protected function factory($settings): PDOConnectionInterface{
		switch ($settings['scheme']){
			case 'mysql':
				$connection = static::mysql($settings);
				break;
			default:
				$connection = null;
		}

		$sqlHook = ServiceContainer::get(SqlLogHookInterface::class, true);
		if (!is_null($sqlHook)) $connection->setSqlLogHook($sqlHook);

		return $connection;
	}

	static protected function mysql($settings): PDOConnectionInterface{

		$host = $settings['host'];
		$database = $settings['database'];
		$user = $settings['user'];
		$password = $settings['password'];
		$port = $settings['port'];
		$charset = $settings['charset'];

		$dsn = 'mysql:host=' . $host . ';dbname=' . $database . ';port=' . $port . ';charset=' . $charset;

		$connection = new MysqlPDOConnection($dsn, $user, $password);

		$connection->setAttribute(PDO::ATTR_PERSISTENT, true);
		$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$connection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		$connection->query("SET CHARACTER SET $charset");

		return $connection;
	}

}