<?php
namespace FreePBX\Console\Command;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
class Bulkimport extends Command {
    protected function configure(){
        $this->setName('bulkimport')
        ->setAliases(array('bi'))
        ->setDescription('This command is used to import extensions and dids')
        ->setDefinition(array(
            new InputOption('replace', '-r', InputOption::VALUE_NONE, 'Overwrite Existing Data'),
            new InputOption('type', 't', InputOption::VALUE_REQUIRED, 'Type of file'),
            new InputArgument('filename', InputArgument::REQUIRED, 'Filename', null),))
        ->setHelp('Import a file: fwconsole bulkimport [--replace] --type={type} filename.csv');
    }
    protected function execute(InputInterface $input, OutputInterface $output){
        $filename = $input->getArgument('filename');
        $type = strtolower($input->getOption('type'));
        $valid_types = \FreePBX::Bulkhandler()->getTypes('import');
        $validtype = false;
        $typetext = '';
        $typelist = array();
        foreach ($valid_types as $key => $value) {
          $typelist[] = $value['type'];
          if($value['type'] == $type){
            $validtype = true;
            $typetext = $value['description'];
          }
        }
        $replace = ($input->getOption('replace'))?true:false;
        if(file_exists($filename)){
          $data = \FreePBX::Bulkhandler()->fileToArray($filename);
        }else{
          $output->writeln('<error>The specified file does not exist or we cannot read it</error>');
          return false;
        }
        if(!$data){
          $output->writeln('<error>The file provided did not process properly. Check the file formatting</error>');
          return false;
        }
        if($validtype){
            $output->writeln(sprintf(_('Importing %s'),$typetext));
            if($replace){
              $output->writeln(_('Replace flag set, overwriting existing data'));
            }
            $ret = \FreePBX::Bulkhandler()->import($type, $data, $replace);
        }else{
          $output->writeln('<error>You must specify the file type of --type=type</error>');
          $output->writeln('<error>Valid types are:</error>');
          foreach($typelist as $t){
            $output->writeln('<error>'.$t.'</error>');
          }
          return false;
        }
        if(!$ret){
          $output->writeln('<error>The import failed</error>');
          return false;
        }else{
          return true;
        }
    }
}
