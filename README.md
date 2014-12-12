TripleI.Commands [![Build Status](https://travis-ci.org/triple-i/TripleI.Commands.svg?branch=master)](https://travis-ci.org/triple-i/TripleI.Commands)
================

ルーチンワークはコマンド作って時短する！！！！


## SetUp
Composer で必要なライブラリをインストールしろ。

```
$ composer install
```

準備はそれだけだ。


## Usage
bin ディレクトリに置いてある triplei が起動スクリプトだ。  
これを動かせ。

```
$ bin/triplei
Console Tool

Usage:
 [options] command [arguments]....
```

generate コマンドで新しいコマンドを作ることが可能だ。

```
$ triplei generate rename
```

src/TripleI/Command/Rename.php というコマンドファイルが生成される。  
それを開いて実行したいコマンドの処理を記述しろ。  

詳しい記述方法は、Symfony2 の [Console](http://docs.symfony.gr.jp/symfony2/components/console/introduction.html) ページで確認出来る。  
引数やオプションのやり方、テストの書き方が載っている。


## Author

[TripleI](https://github.com/triple-i)  


## LICENSE

[MIT](https://github.com/triple-i/TripleI.Commands/blob/master/LICENSE)