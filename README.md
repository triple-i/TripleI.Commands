TripleI.Commands
================

ルーチンワークはコマンド作って時短する！！！！


## SetUp
Composer で必要なライブラリをインストールしろ。

```
$ composer install
```

準備はそれだけだ。


## Usage
ルートに置いてある triplei が起動スクリプトだ。  
これを動かせ。

```
$ triplei

-- EmeraldBeans CommandsList --
  Generate:                      引数に指定した名前で新しいコマンドを生成します
  GenerateModel:                 引数に指定した名前のモデルクラスを生成します
```

EmeraldBeans とかはコマンドライブラリのやつだから特に気にするな。  
Generate コマンドで新しいコマンドを作る。

```
$ triplei Generate Rename
```

library/TripleI/Commands/Rename.php というコマンドファイルが生成される。  
それを開いて execute メソッドに行いたい処理を書けばいい。


## How to use AWS

なんか処理したくて AWS (S3) 使いたいときもあるだろう。  
まず設定ファイルを作る。

```
cp config/aws.ini.orig config/aws.ini
```

ファイルを開いて、認証キーを記述する。

```
key=xxxxxxxx
secret=xxxxxxxxxxxxx
```

コマンドファイルでこのように使え。

```
<?php

use TripleI\Aws\S3;
use Aws\Common\Enum\Region;

$S3 = new S3();
$S3->setBucket('bucket_name');
$S3->setRegion(Region::AP_NORTHEAST_1);
$S3->upload($from_path, $to_path);
```

アップロード、ダウンロード、削除が容易に出来る。  
