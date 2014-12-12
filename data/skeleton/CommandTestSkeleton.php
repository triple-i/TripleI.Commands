<?php


use TripleI\Command\${name};
use TripleI\Console\TripleIApplication;
use Symfony\Component\Console\Tester\CommandTester;

class ${name}Test extends PHPUnit_Framework_TestCase
{

    /**
     * @var TripleIApplication
     **/
    private $app;


    /**
     * @return void
     **/
    public function setUp ()
    {
        $this->app = new TripleIApplication();
        $this->app->add(new ${name}());
    }


    /**
     * @return void
     **/
    public function tearDown ()
    {
    }


    /**
     * @test
     * @group ${group_name}-execute
     * @group ${group_name}
     **/
    public function 正常な処理 ()
    {
        $command = $this->app->find('${group_name}');
        $tester  = new CommandTester($command);
        $tester->execute(array(
            'command'  => $command->getName(),
            'argument' => 'hoge'
        ));
    }
}

