<?php


namespace TripleI\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ${name} extends Command
{

    /**
     * @return void
     **/
    protected function configure ()
    {
        $this->setName('generate')
            ->setDescription('コマンドの概要');

        /* 引数の記載
            ->addArgument(
                'argument_name',
                InputArgument::REQUIRED,
                '引数の説明'
            )
         */

        /* オプションの記載
            ->addOption(
                'option_name',
                null,
                InputerOption::VALUE_REQUIRED
            ) */
    }


    /**
     * @param  InputInterface $input
     * @param  OutputInterface $output
     * @return void
     **/
    protected function execute (InputInterface $input, OutputInterface $output)
    {
        // コマンドの実行内容を記載
    }
}

