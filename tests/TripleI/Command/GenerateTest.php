<?php


use TripleI\Command\Generate;
use TripleI\Console\TripleIApplication;
use Symfony\Component\Console\Tester\CommandTester;

class GenerateTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var TripleIApplication
     **/
    private $app;


    /**
     * コマンドの名称

     * @var string
     **/
    private $c_name;


    /**
     * コマンドクラスへのパス
     *
     * @var string
     **/
    private $c_path;


    /**
     * @return void
     **/
    public function setUp ()
    {
        $this->app = new TripleIApplication();
        $this->app->add(new Generate());
    }


    /**
     * @return void
     **/
    public function tearDown ()
    {
        if (file_exists($this->c_path)) {
            unlink($this->c_path);
        }

        $test_path = sprintf(ROOT.'/tests/TripleI/Command/%sTest.php', $this->c_name);
        if (file_exists($test_path)) {
            unlink($test_path);
        }
    }


    /**
     * @test
     * @expectedException          RuntimeException
     * @expectedExceptionMessageRegExp   /既に[0-9a-zA-Z]{5}コマンドが存在しています/
     * @group generate-already-exists-command
     * @group generate
     **/
    public function 指定コマンドが既に存在している場合 ()
    {
        $this->_generateCommandName();
        file_put_contents($this->c_path, '');

        $command = $this->app->find('generate');
        $tester  = new CommandTester($command);
        $tester->execute(array(
            'command' => $command->getName(),
            'command_name' => $this->c_name
        ));
    }


    /**
     * @test
     * @group generate-execute
     * @group generate
     **/
    public function 正常な処理 ()
    {
        $this->_generateCommandName();

        $command = $this->app->find('generate');
        $tester  = new CommandTester($command);
        $tester->execute(array(
            'command'  => $command->getName(),
            'command_name' => $this->c_name
        ));

        $this->assertTrue(file_exists($this->c_path));
        $this->assertEquals(
            sprintf('Generated %s command!'.PHP_EOL, $this->c_name),
            $tester->getDisplay()
        );
    }


    /**
     * テスト用のコマンドファイル名を生成する
     *
     * @return void
     **/
    private function _generateCommandName ()
    {
        $this->c_name = ucfirst(substr(md5(uniqid()), 0, 5));
        $this->c_path = sprintf(SRC.'/TripleI/Command/%s.php', $this->c_name);
    }
}

