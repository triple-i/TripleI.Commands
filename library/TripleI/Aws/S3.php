<?php


namespace TripleI\Aws;

use Aws\S3\S3Client;
use Guzzle\Http\EntityBody;

class S3
{

    /**
     * @var S3Client
     **/
    private $client;


    /**
     * @var string
     **/
    private $bucket;


    /**
     * @var string
     **/
    private $aws_ini_path = 'config/aws.ini';



    /**
     * @param  string $bucket
     * @return void
     **/
    public function setBucket ($bucket)
    {
        $this->bucket = $bucket;
    }


    /**
     * @param  Region $region
     * @return void
     **/
    public function setRegion ($region)
    {
        $this->region = $region;
    }


    /**
     * パラメータのバリデートを行う
     *
     * @return void
     **/
    private function _validateParameters ()
    {
        if (! file_exists(ROOT.'/'.$this->aws_ini_path)) {
            throw new \Exception('config/aws.ini ファイルが存在しません');
        }

        if (! isset($this->region)) {
            throw new \Exception('リージョンが指定されていません');
        }

        if (! isset($this->bucket)) {
            throw new \Exception('バケットが指定されていません');
        }
    }


    /**
     * S3Client を初期化する
     *
     * @param  boolean $force S3Clientの再生成
     * @return void
     **/
    private function _initS3Client ($force = false)
    {
        if (! is_null($this->client)) return;

        // AWSクライアントの設定
        $ini = parse_ini_file(ROOT.'/'.$this->aws_ini_path);

        $this->client = S3Client::factory(
            array(
                'key' => $ini['key'],
                'secret' => $ini['secret'],
                'region' => $this->region
            )
        );
    }


    /**
     * 指定パスのファイルがS3に存在するかどうか
     *
     * @param String $path  ファイルパス
     * @return boolean
     **/
    public function doesObjectExist ($path)
    {
        $this->_validateParameters();
        $this->_initS3Client();

        return $this->client->doesObjectExist($this->bucket, $path);
    }


    /**
     * 指定パスのファイルをダウンロードする
     *
     * @param String $path  S3のパス
     * @return Guzzle\Service\Resource\Model
     **/
    public function download ($path)
    {
        try {
            $this->_validateParameters();
            $this->_initS3Client();

            $response = $this->client->getObject(array(
                'Bucket' => $this->bucket,
                'Key' => $path
            ));
        
        } catch (\Exception $e) {
            throw $e;
        }

        return $response;
    }


    /**
     * 指定パスにファイルをアップロードする
     *
     * @param  string $from_path  アップロードするファイルへのパス
     * @param  string $to_path  アップロード先のパス
     * @return void
     **/
    public function upload ($from_path, $to_path)
    {
        try {
            $this->_validateParameters();
            $this->_initS3Client();

            $this->client->putObject(array(
                'SourceFile' => $from_path,
                'Key' => $to_path,
                'Bucket' => $this->bucket
            ));

        } catch (\Exception $e) {
            throw $e;
        }

        return true;
    }


    /**
     * 指定パスのファイルを削除する
     *
     * @param String $path  S3のパス
     * @return boolean
     **/
    public function delete ($path)
    {
        try {
            $this->_validateParameters();
            $this->_initS3Client();

            $this->client->deleteObject(array(
                'Bucket' => $this->bucket,
                'Key' => $path
            ));
        
        } catch (\Exception $e) {
            throw $e;
        }

        return true;
    }
}
