<?php

use Emerald\Command\AbstractCommand;
use Emerald\Command\CommandInterface;


/**
 * 月末にアップを行ったSnavコンテンツの集計を行う
 *
 * エクセルデータをsummary.txtとしてデスクトップに配置して
 * コマンドを実行すれば集計データが得られる
 *
 **/
class SnavSummary extends AbstractCommand implements CommandInterface
{

    /**
     * @var string
     **/
    private $user;


    /**
     * @var string
     **/
    private $file_path;


    /**
     * 集計データ
     * キーにマニュアル名を入れて、値にはブック名を入れる
     *
     * @var array
     **/
    private $summary;


    /**
     * コマンドの実行
     *
     * @param Array $params  パラメータ配列
     * @return void
     **/
    public function execute (Array $params)
    {
        try {
            $this->_validateParameters();

            $this->_parseSummaryText();
            $this->_renderSummaryData();

        } catch (\Exception $e) {
            $this->errorLog($e->getMessage());
        }
    }


    /**
     * パラメータバリデート
     *
     * @return void
     **/
    private function _validateParameters ()
    {
        $this->user = get_current_user();
        $this->file_path = sprintf('/Users/%s/Desktop/summary.txt', $this->user);

        if (! file_exists($this->file_path)) {
            throw new \Exception('デスクトップにsummay.txtが存在しません');
        }
    }


    /**
     * summaryテキストを解析してデータを集計する
     *
     * @return void
     **/
    private function _parseSummaryText ()
    {
        $summary_text = file_get_contents($this->file_path);
        $summary_text = explode(PHP_EOL, $summary_text);
        $summary = array();
        $manual  = '';

        foreach ($summary_text as $text) {
            // タブ区切りを配列に変換
            $text = preg_replace('/\t/', '    ', $text);
            $text = explode('    ', $text);
            if (! is_array($text)) continue;
            if (!isset($text[0])) continue;

            // 現在集計しているマニュアル名を取得
            if ($text[0] != '') $manual = $text[0];

            // ブック名が記載されているかどうか
            if (!isset($text[1]) || $text[1] == '') continue;

            // 既に集計されているかどうか
            if (! isset($summary[$manual])) $summary[$manual] = array();
            if (in_array($text[1], $summary[$manual])) continue;

            // 集計
            $summary[$manual][] = $text[1];
        }

        $this->summary = $summary;
    }


    /**
     * 集計データを表示する
     *
     * @return void
     **/
    private function _renderSummaryData ()
    {
        echo PHP_EOL;

        foreach ($this->summary as $manual => $books) {
            $this->log(count($books), $manual);
        }

        echo PHP_EOL;
        $this->log('Snavエクセルの備考も確認して集計に間違いがないか確かめておくこと！');
        echo PHP_EOL;
    }


    /**
     * ヘルプメッセージの表示
     *
     * @return String
     **/
    public static function help ()
    {
        return 'Snav集計用コマンド。デスクトップにあるsummary.txtから集計する';
    }
}
