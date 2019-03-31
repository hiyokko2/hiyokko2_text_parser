# hiyokko2_text_parser
個人的に使っているオレオレマークダウンのパーサーです。

## インストール方法
composer require hiyokko2/hiyokko2_text_parser

## 使い方
```
require_once "vendor/autoload.php";

$markdown = <<<EOF
#title(このライブラリの使い方)
#cat(プログラミング,PHP)
#thumb(/images/programming.jpg)
#pickup(100)

*見出し
このライブラリは・・・
EOF;

$parsed = Hiyokko2\TextParser::parse($markdown);
```
$parsedには次のようなデータ構造が入ります。
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



## オレオレマークダウンの記法

### メタデータ
頭が#の行はメタデータなのでHTMLに変換されない。
例：
```
#title(このライブラリの使い方)
#cat(プログラミング,PHP)
```
は空のHTMLになる。

全てのメタデータの記法は以下の通り。
※#titleは必須
```
#title(記事タイトル) 
#cat(カテゴリ1,カテゴリ2,カテゴリ3)
#thumb(/images/programming.jpg)
#pickup(50)
```
ちなみにこれをパースすると以下のようなデータ構造が得られる。
```
Array
(
    [title] => 記事タイトル
    [thumbnail] => /images/programming.jpg
    [pickup] => 50
    [content_no_tag] => 
    [description] => 
    [html] => 
    [categories] => Array
        (
            [0] => カテゴリ1
            [1] => カテゴリ2
            [2] => カテゴリ3
        )

)
```

### 見出し
```
*aaa
**bbb
***ccc
```
は次のように変換される。*がh2なのは、基本的に#titleで指定したタイトルがh1になるだろうから。
```
<h2>aaa</h2>
<h3>bbb</h3>
<h4>ccc</h4>
```

### 順序なしリスト(ul)
```
#ul
aaa
bbb
ccc
#ul_end
```
は次のように変換される。
```
<ul>
    <li>aaa</li>
    <li>bbb</li>
    <li>ccc</li>
</ul>
```

### 順序ありリスト(ol)
```
#ol
aaa
bbb
ccc
#ol_end
```
は次のように変換される。
```
<ol>
    <li>aaa</li>
    <li>bbb</li>
    <li>ccc</li>
</ol>
```

### blankリンク
```
blank(リンク表示名,https://aaa.com)
```
は次のように変換される。
```
<a href="https://aaa.com" target="_blank">リンク表示名</a>
```

### 太字
```
b(太字にしたい文字列)
```
は次のように変換される。
```
<b>太字にしたい文字列</b>
```
