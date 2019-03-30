# hiyokko2_text_parser
個人的に使っているオレオレマークダウンのパーサーです。

## インストール方法
composer require hiyokko2/hiyokko2_text_parser

## 使い方
example/example1.phpを見てください。  
下のようなオレオレマークダウンを

```
#title(このライブラリの使い方)
#cat(プログラミング,PHP)
#thumb(/images/programming.jpg)
#pickup(100)

*見出し
このライブラリは・・・
```

このようなデータ構造に変換します。

```
Array
(
    [title] => このライブラリの使い方
    [thumbnail] => /images/programming.jpg
    [pickup] => 100
    [content_no_tag] => 見出しこのライブラリは・・・
    [description] => 見出しこのライブラリは・・・
    [html] => <h2>見出し</h2>このライブラリは・・・<br>
    [categories] => Array
        (
            [0] => プログラミング
            [1] => PHP
        )

)
```
