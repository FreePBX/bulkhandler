<?php
namespace FreePBX\Console\Command;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
class Bulkimport extends Command {
	protected function configure(){
		$mod_info = module_getinfo('callaccounting', MODULE_STATUS_ENABLED);
		if (!isset($mod_info['callaccounting'])) {
			$helptext = 'Import a file: fwconsole bulkimport --type=[extensions|dids] filename.csv --replace(Replace the existing values)';
		} else {
			$helptext = 'Import a file: fwconsole bulkimport --type=[extensions|dids|callaccounting] filename.csv --replace(Replace the existing values)';
		}
		$this->setName('bulkimport')
		->setAliases(array('bi'))
		->setDescription('This command is used to import extensions and dids')
		->setDefinition(array(
			new InputOption('type', 't', InputOption::VALUE_REQUIRED, 'Type of file'),
			new InputArgument('filename', InputArgument::REQUIRED, 'Filename', null),
			new InputOption('replace', null, InputOption::VALUE_NONE, 'To replace existing values'),))
		->setHelp($helptext);
	}
	protected function execute(InputInterface $input, OutputInterface $output){
		$filename = $input->getArgument('filename');
		$type = $input->getOption('type');
		if ($input->getOption('replace')) {
			$replace = true;
		}
		if (file_exists($filename)) {
			$data = \FreePBX::Bulkhandler()->fileToArray($filename);
		} else {
			$output->writeln('<error>The specified file does not exist or we cannot read it</error>');
			return false;
		}
		if (!$data) {
			$output->writeln('<error>The file provided did not process properly. Check the file formatting</error>');
			return false;
		}

		switch ($type) {
			case 'dids':
				$output->writeln('Importing bulk dids');
				$ret = \FreePBX::Bulkhandler()->import('dids', $data, $replace);
			break;
			case 'extensions':
				$output->writeln('Importing bulk extensions');
				$ret = \FreePBX::Bulkhandler()->import('extensions', $data, $replace);
			break;
			case 'callaccounting':
				$output->writeln('Importing CallDECK Ratepatterns');
				$ret = \FreePBX::Bulkhandler()->import('callaccounting', $data, $replace);
			 break;
			default:
				$output->writeln('<error>You must specify the file type of --type=dids or --type=extensions</error>');
			return false;
			break;
		}
		if (!$ret) {
			$output->writeln('<error>The import failed</error>');
			return false;
		} else {
			return true;
		}
	}
}
