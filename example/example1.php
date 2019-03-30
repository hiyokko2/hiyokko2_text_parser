<?php

error_reporting(E_ERROR & ~E_WARNING & ~E_PARSE & ~E_NOTICE);

require "../src/TextParser.php";
use Hiyokko2\TextParser;

$markdown = <<<EOF
#title(このライブラリの使い方)
#cat(プログラミング,PHP)
#thumb(/images/programming.jpg)
#pickup(100)

*見出し
このライブラリは・・・
EOF;

print_r(TextParser::parse($markdown));
