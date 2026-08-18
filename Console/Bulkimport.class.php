<?php
namespace FreePBX\Console\Command;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
class Bulkimport extends Command {
	protected function configure(): void {
		$mod_info = module_getinfo('callaccounting', MODULE_STATUS_ENABLED);
		if (!isset($mod_info['callaccounting'])) {
			$helptext = _('Import a file: fwconsole bulkimport --type=[extensions|dids] filename.csv --replace(Replace the existing values)');
		} else {
			$helptext = _('Import a file: fwconsole bulkimport --type=[extensions|dids|callaccounting] filename.csv --replace(Replace the existing values)');
		}
		$this->setName('bulkimport')
			->setAliases(['bi'])
			->setDescription(_('This command is used to import extensions and dids'))
			->setDefinition([
				new InputOption('type', 't', InputOption::VALUE_REQUIRED, _('Type of file')),
				new InputArgument('filename', InputArgument::REQUIRED, _('Filename')),
				new InputOption('replace', null, InputOption::VALUE_NONE, _('To replace existing values')),
			])
			->setHelp($helptext);
	}
	protected function execute(InputInterface $input, OutputInterface $output): int {
		$filename = $input->getArgument('filename');
		$type = $input->getOption('type');
		$replace = (bool) $input->getOption('replace');
		if (file_exists($filename)) {
			$data = \FreePBX::Bulkhandler()->fileToArray($filename);
		} else {
			$output->writeln(_('<error>The specified file does not exist or we cannot read it</error>'));
			return Command::FAILURE;
		}
		if (!$data) {
			$output->writeln(_('<error>The file provided did not process properly. Check the file formatting</error>'));
			return Command::FAILURE;
		}

		switch ($type) {
			case 'dids':
				$output->writeln(_('Importing bulk dids'));
				$ret = \FreePBX::Bulkhandler()->import('dids', $data, $replace);
			break;
			case 'extensions':
				$output->writeln(_('Importing bulk extensions'));
				$ret = \FreePBX::Bulkhandler()->import('extensions', $data, $replace);
			break;
			case 'callaccounting':
				$output->writeln(_('Importing CallDECK Ratepatterns'));
				$ret = \FreePBX::Bulkhandler()->import('callaccounting', $data, $replace);
			break;
			default:
				$output->writeln(_('<error>You must specify the file type of --type=dids or --type=extensions</error>'));
				return Command::FAILURE;
		}
		if (!$ret) {
			$output->writeln(_('<error>The import failed</error>'));
			return Command::FAILURE;
		}
		return Command::SUCCESS;
	}
}
