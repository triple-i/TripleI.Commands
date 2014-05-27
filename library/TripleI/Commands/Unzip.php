<?php

use Emerald\Command\AbstractCommand;
use Emerald\Command\CommandInterface;

class Unzip extends AbstractCommand implements CommandInterface
{

    /**
     * @var array
     **/
    protected $params;


    /**
     * コマンドの実行
     *
     * @param Array $params  パラメータ配列
     * @return void
     **/
    public function execute (Array $params)
    {
        try {
            set_time_limit(0);

            $this->params = $params;
            $this->_validateParameters();

            $this->_unzip();
            $this->log('success!');

        } catch (\Exception $e) {
            $this->errorLog($e->getMessage());
        }
    }


    /**
     * パラメータのバリデート
     *
     * @return array
     **/
    private function _validateParameters ()
    {
        if (! isset($this->params[1])) {
            throw new \Exception('unzip化させるzipの入っているディレクトリを指定してください');
        }

        $path = $this->params[1];
        if (! is_dir($path)) {
            throw new \Exception('ディレクトリを指定してください');
        }
    }


    /**
     * 解凍処理を行う
     *
     * @return void
     **/
    private function _unzip ()
    {
        $current_dir = $this->_formatDirectoryPath();

        if ($dh = opendir($current_dir)) {
            chdir($current_dir);

            while ($entry = readdir($dh)) {
                if ($entry != '.' && $entry != '..' && preg_match('/\.zip$/', $entry)) {
                    $command = 'unzip '.$entry;
                    passthru($command);
                }
            }
            closedir($dh);
        }
    }


    /**
     * ディレクトリパスを整形する
     *
     * @return string
     **/
    private function _formatDirectoryPath ()
    {
        $dir = $this->params[1];
        $dir = preg_replace('/\/$/', '', $dir);
        $dir = preg_replace('/^\.\//', '', $dir);

        $user = get_current_user();
        $dir = preg_replace('/^~/', '/Users/'.$user, $dir);

        return $dir;
    }


    /**
     * ヘルプメッセージの表示
     *
     * @return String
     **/
    public static function help ()
    {
        return '';
    }
}
