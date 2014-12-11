<?php


namespace TripleI\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Generate extends Command
{

    /**
     * @var string
     **/
    private $c_name;


    /**
     * @var string
     **/
    private $c_path;


    /**
     * @return void
     **/
    protected function configure ()
    {
        $this->setName('generate')
            ->setDescription('新しいコマンドを生成します')
            ->addArgument(
                'command_name',
                InputArgument::REQUIRED,
                '生成したいコマンド名'
            );
    }


    /**
     * @param  InputInterface $input
     * @param  OutputInterface $output
     * @return void
     **/
    protected function execute (InputInterface $input, OutputInterface $output)
    {
        $this->c_name = ucfirst($input->getArgument('command_name'));
        $this->c_path = sprintf(SRC.'/TripleI/Command/%s.php', $this->c_name);

        $this->_validateCommand();
        $command_data = $this->_buildCommandData();
        file_put_contents($this->c_path, $command_data);

        $output->writeln(sprintf('<info>Generated %s command!</info>', $this->c_name));
    }


    /**
     * @return void
     **/
    private function _validateCommand ()
    {
        if (file_exists($this->c_path)) {
            throw new \RuntimeException(sprintf('既に%sコマンドは存在しています', $this->c_name));
        }
    }


    /**
     * @return string
     **/
    private function _buildCommandData ()
    {
        $skeleton = file_get_contents(ROOT.'/data/skeleton/CommandSkeleton.php');
        $skeleton = str_replace('${name}', $this->c_name, $skeleton);

        return $skeleton;
    }
}

