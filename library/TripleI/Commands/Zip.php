<?php

use Emerald\Command\AbstractCommand;
use Emerald\Command\CommandInterface;

class Zip extends AbstractCommand implements CommandInterface
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

            $this->_zip();
            $this->log('success!');

        } catch (\Exception $e) {
            $this->errorLog($e->getMessage());
        }
    }


    /**
     * パラメータのバリデート
     *
     * @return void
     **/
    private function _validateParameters ()
    {
        if (! isset($this->params[1])) {
            throw new \Exception('zip化させるディレクトリの入っているディレクトリを指定してください');
        }

        $path = $this->params[1];
        if (! is_dir($path)) {
            throw new \Exception('ディレクトリを指定してください');
        }
    }


    /**
     * 圧縮処理を行う
     *
     * @return void
     **/
    private function _zip ()
    {
        $current_dir = $this->_formatDirectoryPath();

        if ($dh = opendir($current_dir)) {
            chdir($current_dir);

            while ($entry = readdir($dh)) {
                if ($entry != '.' && $entry != '..' && is_dir($entry)) {
                    $command = 'zip -r '.$entry.'.zip '.$entry;
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
        $dir  = preg_replace('/^~/', '/Users/'.$user, $dir);

        return $dir;
    }



    /**
     * ヘルプメッセージの表示
     *
     * @return String
     **/
    public static function help ()
    {
        return '指定ディレクトリ内にあるディレクトリをzip化させる';
    }
}
