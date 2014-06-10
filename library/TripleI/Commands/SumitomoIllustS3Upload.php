<?php

use Emerald\Command\AbstractCommand;
use Emerald\Command\CommandInterface;

use Aws\Common\Enum\Region;
use TripleI\Aws\S3;

/**
 * EPSのバージョン更新などで大量に素材イラストをアップロードするときに使う
 *
 * 引数にイラストを格納したディレクトリを与えて実行する
 * 再帰的な処理は行わない
 *
 **/
class SumitomoIllustS3Upload extends AbstractCommand implements CommandInterface
{

    /**
     * @var array
     **/
    private $params;


    /**
     * アップロード済みのイラストを格納するディレクトリ
     *
     * @var string
     **/
    private $upload_dir;


    /**
     * アップロードに失敗したイラストを格納するディレクトリ
     *
     * @var string
     **/
    private $failed_dir;


    /**
     * @var TripleI\Aws\S3
     **/
    private $S3;


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
            array_shift($params);
            $this->params = $params;
            $this->_validateParameters();

            $this->_initDirectory();
            $this->_initS3Client();

            $this->_upload();

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
        if (! isset($this->params[0])) {
            throw new \Exception('イラストディレクトリを指定してください');
        }

        if (! is_dir($this->params[0])) {
            throw new \Exception('ディレクトリを指定してください');
        }
    }


    /**
     * アップロードに必要なディレクトリを生成する
     *
     * @return void
     **/
    private function _initDirectory ()
    {
        $dir = $this->params[0];
        $dir = preg_replace('/\/$/', '', $dir);
        $dir = preg_replace('/^\.\//', '', $dir);

        $user = get_current_user();
        $dir = preg_replace('/^~/', '/Users/'.$user, $dir);


        $this->upload_dir = $dir.'/uploaded';
        $this->failed_dir = $dir.'/failed';

        if (! is_dir($this->upload_dir)) {
            mkdir($this->upload_dir);
        }

        if (! is_dir($this->failed_dir)) {
            mkdir($this->failed_dir);
        }

        $this->params[0] = $dir;
    }


    /**
     * S3クライアントを初期化する
     *
     * @return void
     **/
    private function _initS3Client ()
    {
        $s3 = new S3();
        $s3->setBucket('gemini-material');
        $s3->setRegion(Region::AP_NORTHEAST_1);

        $this->S3 = $s3;
    }


    /**
     * アップロード処理
     *
     * @return void
     **/
    private function _upload ()
    {
        if ($dh = opendir($this->params[0])) {
            while ($file = readdir($dh)) {
                if (preg_match('/^.*\.(eps|EPS)$/', $file)) {
                    $parent_dir = strtolower(substr($file, 0, 1));
                    $illust_name = preg_replace('/\.(eps|EPS)/', '', $file);
                    $to_path = $parent_dir.'/'.$illust_name.'/'.$file;
                    $from_path = $this->params[0].'/'.$file;

                    // 絶対パスでない場合は絶対パスに
                    if (! preg_match('/^\//', $from_path)) {
                        $from_path = getcwd().'/'.$from_path;
                    }

                    try {
                        $this->S3->upload($from_path, $to_path);
                        $command = sprintf(
                            'mv %s %s',
                            $this->params[0].'/'.$file,
                            $this->upload_dir.'/'.$file
                        );

                    } catch (\Exception $e) {
                        $command = sprintf(
                            'mv %s %s',
                            $this->params[0].'/'.$file,
                            $failed_dir.'/'.$file
                        );
                        $this->errorLog('failed '.$file);
                    }

                    passthru($command);
                }
            }
            closedir($dh);
        }
    }


    /**
     * ヘルプメッセージの表示
     *
     * @return String
     **/
    public static function help ()
    {
        return '指定ディレクトリのイラストを住友S3へアップロードする';
    }
}
