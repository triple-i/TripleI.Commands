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
     * コマンドファイルが作成されたかどうかのフラグ
     *
     * @var boolean
     **/
    private $command_file_flag = false;


    /**
     * テストファイルが作成されたかどうかのフラグ
     *
     * @var boolean
     **/
    private $test_file_flag = false;


    /**
     * Applicationクラスを書き換えたかどうかのフラグ
     *
     * @var boolean
     **/
    private $update_app_flag = false;


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
        try {
            $this->c_name = ucfirst($input->getArgument('command_name'));
            $this->c_path = sprintf(SRC.'/TripleI/Command/%s.php', $this->c_name);

            $this->_validateCommand();
            $this->_generateCommandFile();
            $this->_generateCommandTestFile();
            $this->_updateApplicationClass();

            $output->writeln(sprintf('<info>Generated %s command!</info>', $this->c_name));

        } catch (\Exception $e) {
            $this->_rollBackFiles();
            throw $e;
        }
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
     * @return void
     **/
    private function _generateCommandFile ()
    {
        try {
            $skeleton = file_get_contents(ROOT.'/data/skeleton/CommandSkeleton.php');
            $skeleton = str_replace('${name}', $this->c_name, $skeleton);
            $skeleton = str_replace('${c_name}', strtolower($this->c_name), $skeleton);

            file_put_contents($this->c_path, $skeleton);
            $this->command_file_flag = true;

        } catch (\RuntimeException $e) {
            throw $e;
        }
    }


    /**
     * @return void
     **/
    private function _generateCommandTestFile ()
    {
        try {
            $skeleton = file_get_contents(ROOT.'/data/skeleton/CommandTestSkeleton.php');
            $skeleton = str_replace('${name}', $this->c_name, $skeleton);
            $skeleton = str_replace('${group_name}', strtolower($this->c_name), $skeleton);

            file_put_contents(ROOT.'/tests/TripleI/Command/'.$this->c_name.'Test.php', $skeleton);
            $this->test_file_flag = true;

        } catch (\Exception $e) {
            throw $e;
        }
    }


    /**
     * @return void
     **/
    private function _updateApplicationClass ()
    {
        try {
            $replace_text = sprintf('$this->add(new Command\%s());'.PHP_EOL.
                '        /* TripleI Commands List */',
                $this->c_name);

            // ユニットテスト時は書き換えを行わない
            if (defined('TEST')) return false;

            $app_path = SRC.'/TripleI/Console/TripleIApplication.php';
            $app = file_get_contents($app_path);
            $app = str_replace('/* TripleI Commands List */', $replace_text, $app);

            file_put_contents($app_path, $app);
            $this->update_app_flag = true;

        } catch (\Exception $e) {
            throw $e;
        }
    }


    /**
     * 生成してしまったファイルを削除する
     *
     * @return void
     **/
    private function _rollBackFiles ()
    {
        // コマンドファイルの削除
        if ($this->command_file_flag) {
            if (file_exists($this->c_path)) unlink($this->c_path);
        }

        // テストファイルの削除
        if ($this->test_file_flag) {
            $path = sprintf(ROOT.'/tests/TripleI/Command/%sTest.php', $this->c_name);
            if (file_exists($path)) unlink($path);
        }
    }
}

