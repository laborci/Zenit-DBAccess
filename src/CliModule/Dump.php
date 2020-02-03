<?php namespace Zenit\Bundle\DBAccess\CliModule;

use Rah\Danpu\Export;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Zenit\Bundle\DBAccess\Config;
use Zenit\Bundle\Mission\Component\Cli\CliModule;

class Dump extends CliModule{

	protected function configure(){
		$this->setName('dump');
		$this->addOption("structure", "s", InputOption::VALUE_NONE, "Dump structure");
		$this->addOption("data", "d", InputOption::VALUE_NONE, "Dump data");
		$this->addOption("database", "db", InputOption::VALUE_REQUIRED, "Database name", null);
	}

	protected function execute(InputInterface $input, OutputInterface $output){

		$this->config = Config::Service();

		$style = new SymfonyStyle($input, $output);

		$dbName = $input->getOption('database') ?: $this->config->defaultDatabase;
		$database = $this->config->databases[$dbName];
		$defaults = $this->config->defaults;
		$database = array_merge($defaults, $database);

		$dsn = $database['scheme'] . ':dbname=' . $database['database'] . ';host=' . $database['host'];
		
		$dump = new \Rah\Danpu\Dump();
		$dump
			->dsn($dsn)
			->user($database['user'])
			->pass($database['password'])
			->tmp($this->config->pathTmp)
		;

		if ($input->getOption('structure') === false && $input->getOption('data') === false){
			$file = $this->config->pathDev . 'dump/snapshot.' .$dbName.'.' .time() . '.sql';
			$style->title('dumping snapshot: ' . $file);
			$dump->structure(true)->disableForeignKeyChecks(true)->data(true)->file($file);
			new Export($dump);
			$style->success('done');
		}

		if ($input->getOption('structure') !== false){
			$file = $this->config->pathDev . 'dump/structure.' .$dbName.'.'. time() . '.sql';
			$style->title('dumping structure: ' . $file);
			$dump->structure(true)->disableForeignKeyChecks(true)->data(false)->file($file);
			new Export($dump);
			$style->success('done');
		}

		if ($input->getOption('data') !== false){
			$file = $this->config->pathDev . 'dump/data.' .$dbName.'.' .time() . '.sql';
			$style->title('dumping data' . $file);
			$dump->structure(false)->disableForeignKeyChecks(true)->data(true)->file($file);
			new Export($dump);
			$style->success('done');
		}

	}

}
