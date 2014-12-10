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
        $command_name = ucfirst($input->getArgument('command_name'));
        $command_path = sprintf(SRC.'/TripleI/Command/%s.php', $command_name);

        $this->_validateCommand($command_path);
    }


    /**
     * @param  string $command_path
     * @return void
     **/
    private function _validateCommand ($command_path)
    {
        if (file_exists($command_path)) {
            $info = pathinfo($command_path);
            throw new \RuntimeException(sprintf('既に%sコマンドは存在しています'));
        }
    }
}

