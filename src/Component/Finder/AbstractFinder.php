<?php namespace Zenit\Bundle\DBAccess\Component\Finder;

use Zenit\Bundle\DBAccess\Component\PDOConnection\AbstractPDOConnection;
use Zenit\Bundle\DBAccess\Component\Filter\Filter;

abstract class AbstractFinder {

	/** @var AbstractPDOConnection */
	protected $connection;
	/** @var callable */
	protected $converter;
	protected $select;
	/** @var Filter */
	protected $filter;
	protected $from;
	protected $limit;
	protected $offset;
	protected $order = [];

	public function __construct(AbstractPDOConnection $connection) {
		$this->connection = $connection;
	}

	/**
	 * @param callable|null $converter
	 * @return $this
	 */
	public function setConverter(callable $converter = null) {
		$this->converter = $converter;
		return $this;
	}

	/**
	 * @param string $sql
	 * @param array  $sqlParams
	 * @return $this
	 */
	public function select(string $sql, ...$sqlParams) {
		$this->select = $this->connection->applySQLParameters($sql, $sqlParams);
		return $this;
	}

	/**
	 * @param string $sql
	 * @param array  $sqlParams
	 * @return $this
	 */
	public function from(string $sql, ...$sqlParams) {
		$this->from = $this->connection->applySQLParameters($sql, $sqlParams);
		return $this;
	}

	/**
	 * @param \Zenit\Bundle\DBAccess\Component\Filter\Filter|null $filter
	 * @return $this
	 */
	public function where(Filter $filter = null) {
		$this->filter = $filter;
		return $this;
	}

	#region ORDER
	/**
	 * @param $order
	 * @return $this
	 */
	public function order($order) {
		if (is_array($order)) foreach ($order as $field => $dir) $this->order[] = $this->connection->escapeSQLEntity($field) . ' ' . $dir;
		else $this->order[] = $order;
		return $this;
	}

	/**
	 * @param $field
	 * @return $this
	 */
	public function asc($field) { return $this->order($this->connection->escapeSQLEntity($field) . ' ASC'); }

	/**
	 * @param $field
	 * @return $this
	 */
	public function desc($field) { return $this->order($this->connection->escapeSQLEntity($field) . ' DESC'); }

	/**
	 * @param bool   $cond
	 * @param string $field
	 * @return $this
	 */
	public function ascIf(bool $cond, string $field) { return $cond ? $this->asc($field) : $this; }

	/**
	 * @param bool   $cond
	 * @param string $field
	 * @return $this
	 */
	public function descIf(bool $cond, string $field) { return $cond ? $this->desc($field) : $this; }

	/**
	 * @param bool $cond
	 * @param      $order
	 * @return $this
	 */
	public function orderIf(bool $cond, $order) { return $cond ? $this->order($order) : $this; }
	#endregion

	public function collect($limit = null, $offset = null, &$count = null): array {
		$records = $this->collectRecords($limit, $offset, $count);
		$records = $this->convertRecords($records);
		return $records;
	}

	public function pick() { return $this->convertRecord($this->pickRecord()); }

	public function collectPage($pageSize, $page, &$count = 0): array {
		$records = $this->collectPageRecords($pageSize, $page, $count);
		$records = $this->convertRecords($records);
		return $records;
	}

	abstract public function fetch($fetchmode = \PDO::FETCH_ASSOC):array ;
	abstract public function fetchAll($fetchmode = \PDO::FETCH_ASSOC):array ;

	protected function pickRecord() {
		$records = $this->collectRecords(1, null);
		if ($records) {
			return array_shift($records);
		} else return null;
	}

	protected function collectPageRecords($pageSize, $page, &$count = 0): array {
		$pageSize = abs(intval($pageSize));
		$page = abs(intval($page));
		return $this->collectRecords($pageSize, $pageSize * ($page - 1), $count);
	}

	abstract protected function collectRecords($limit = null, $offset = null, &$count = false): array;

	abstract public function count(): int;

	abstract public function buildSQL($doCounting = false): string;

	protected function convertRecord($record) {
		$converter = $this->converter;
		return is_null($converter) || is_null($record) ? $record : $converter($record);
	}

	protected function convertRecords($records) {
		if (!is_null($this->converter)) {
			foreach ($records as $key => $record) {
				$converter = $this->converter;
				$records[$key] = $converter($record);
			}
		}
		return $records;
	}
}
