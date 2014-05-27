<?php

use Emerald\Command\AbstractCommand;
use Emerald\Command\CommandInterface;

class BuildAftamaIndexXml extends AbstractCommand implements CommandInterface
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

            $this->_buildIndex();

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
            throw new \Exception('対象のcsvを指定してください');
        }

        $csv = $this->params[1];
        if (! preg_match('/\.csv$/', $csv)) {
            throw new \Exception('csvファイルを指定してください');
        }

        if (! file_exists($csv)) {
            throw new \Exception('csvファイルが存在しません');
        }
    }


    /**
     * 目次インデックスを構築する
     *
     * @return void
     **/
    private function _buildIndex ()
    {
        $csv_path = $this->_formatCsvPath();
        $csv_info = pathinfo($csv_path);

        $dom  = new \DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;

        $book = $dom->createElement('Book');
        $book->setAttribute('title', $csv_info['filename']);
        $dom->appendChild($book);

        $stock_nodes = array(0 => $book);

        $fp = fopen($csv_path, 'r');
        while ($row = fgetcsv($fp, 1000, ',')) {
            $file_name = $row[0].'.xml';
            array_shift($row);
            array_shift($row);

            foreach ($row as $level => $r) {
                if ($r != '') {
                    $title = trim($r);
                    break;
                }
            }

            $item = $dom->createElement('ChapterItem');
            $item->setAttribute('title', $title);
            $item->setAttribute('id_p', $file_name);

            $stock_nodes[$level]->appendChild($item);
            $stock_nodes[($level + 1)] = $item;
        }

        $output_path = '/Users/suguru/Desktop/'.$csv_info['filename'].'@INDEX.xml';
        file_put_contents($output_path, $dom->saveXML());
        fclose($fp);

        $this->log('build!');
    }


    /**
     * csvファイルのパスを整形する
     *
     * @return string
     **/
    private function _formatCsvPath ()
    {
        $csv = $this->params[1];
        $csv = preg_replace('/\/$/', '', $csv);
        $csv = preg_replace('/^\.\//', '', $csv);

        $user = get_current_user();
        $csv = preg_replace('/^~/', '/Users/'.$user, $csv);

        return $csv;
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
