<?php

error_reporting(E_ERROR & ~E_WARNING & ~E_PARSE & ~E_NOTICE);

require "../src/TextParser.php";
use Hiyokko2\TextParser;

$markdown = <<<EOF
#title(記事タイトル)
#cat(カテゴリ1,カテゴリ2,カテゴリ3)
#thumb(/images/programming.jpg)
#pickup(50)
EOF;

print_r(TextParser::parse($markdown));
