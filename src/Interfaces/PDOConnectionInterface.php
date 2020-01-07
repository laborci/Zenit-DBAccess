<?php

namespace Zenit\Bundle\DBAccess\Interfaces;

use Zenit\Bundle\DBAccess\Component\Filter\AbstractFilterBuilder;
use Zenit\Bundle\DBAccess\Component\Finder\AbstractFinder;
use Zenit\Bundle\DBAccess\Component\Repository\AbstractRepository;
use Zenit\Bundle\DBAccess\Component\SmartAccess\AbstractSmartAccess;
use PDO;

interface PDOConnectionInterface {
	public function quoteValue($subject, bool $addQuotationMarks = true): string;
	public function quoteArray(array $array, bool $addQuotationMarks = true): array;
	public function escapeSQLEntity($subject): string;
	public function escapeSQLEntities(array $array): array;
	public function applySQLParameters(string $sql, array $sqlParams = []): string;
	public function createFinder(): AbstractFinder;
	public function createSmartAccess(): AbstractSmartAccess;
	public function createRepository(string $table): AbstractRepository;
	public function createFilterBuilder(): AbstractFilterBuilder;
	public function setSqlLogHook($hook);
	public function query($statement, $mode = PDO::ATTR_DEFAULT_FETCH_MODE, $arg3 = null, array $ctorargs = []);
}