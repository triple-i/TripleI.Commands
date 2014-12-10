<?php


namespace TripleI\Console;

use Symfony\Component\Console\Application;
use TripleI\Command;

class TripleIApplication extends Application
{

    /**
     * コマンドの初期化を行う
     *
     * @return void
     **/
    public function __construct ()
    {
        parent::__construct('TripleI Commands -- '.VERSION);

        $this->add(new Command\Generate());
        /* TripleI Commands List */
    }
}

