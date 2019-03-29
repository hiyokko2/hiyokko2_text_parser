<?php

namespace Hiyokko2;

class Tako
{
    public static function aaa() {
        echo "aaa";
    }
}

/*
require_once "/var/www/blog/vendor/autoload.php";

//★★★大本命の実装　エバーノートに確定仕様を記述！！！
function markdown_parse_kakutei_siyou($markdown)
{
    $categories = my_preg_match('/#cat\(([^\)]+)\)/s', $markdown);
    if ($categories != "") {
        $categories = explode(",", $categories);
    }

    $html = markdown_parse_kakutei_siyou_html($markdown);
    $html_no_tag = strip_tags($html);
    $content_no_tag = str_replace("\n", "", $html_no_tag);
    $description = mb_substr($content_no_tag, 0, 120, "UTF-8");

    return [
        title => my_preg_match('/#title\(([^\)]*)\)/s', $markdown),
        thumbnail => my_preg_match('/#thumb\(([^\)]+)\)/s', $markdown),
        status => my_preg_match('/#status\(([^\)]+)\)/', $markdown, "draft"),
        pickup => intval(my_preg_match('/#pickup\(([^\)]+)\)/', $markdown, "0")),
        content_no_tag => $content_no_tag,
        description => $description,
        html => $html,
        categories => $categories,
    ];
}

function markdown_parse_kakutei_siyou_html($markdown)
{
    //行ごとではない一括置換
    //b()はコードブロックに悪影響を与えそうなので後で行う
    $markdown = preg_replace("/blank\(([^,]+),([^\)]+)\)/", "<a href=\"\$2\" target=\"_blank\">\$1</a>", $markdown);

    // まずは複数行ブロックを置換する
    // #python
    // import numpy as np
    // print("Hello World")
    // #python_end
    // ↓
    // #python_0
    //返り値の$markdownには置換後のマークダウンが、$matchesには置換した部分に入るコンテンツが入る
    list($markdown, $matches) = markdown_parse_kakutei_siyou_html_replace_block_before($markdown);

    // file_put_contents("zzzzzzzzzzzz.txt", $markdown);

    //行ごとではない一括置換
    //b()はブロックを置換した後にする
    $markdown = preg_replace("/b\(([^\)]+)\)/", "<b>\$1</b>", $markdown);

    //行ごとの処理
    $html = markdown_parse_kakutei_siyou_html_line_process($markdown);

    //タグに置換されていた複数行ブロックを元に戻す
    $html = markdown_parse_kakutei_siyou_html_replace_block_after($html, $matches);

    return $html;
}

$block_starter = ["python", "susiki", "wide_susiki", "ul", "ol"];

function markdown_parse_kakutei_siyou_html_replace_block_before($markdown)
{
    global $block_starter;

    $matches = [];
    foreach ($block_starter as $starter) {
        preg_match_all("/#{$starter}(.*?)#{$starter}_end/s", $markdown, $match);
        $matches[$starter] = $match[1];
        // $aaaaa = count($matches[$starter]);
        // echo "AAAA{$aaaaa}AAAAA";
        for ($i = 0; $i < count($matches[$starter]); $i++) {
            $markdown = str_replace($match[0][$i], "___{$starter}_" . strval($i), $markdown);
        }
    }

    return [$markdown, $matches];
}

function markdown_parse_kakutei_siyou_html_line_process($markdown)
{
    $html = "";
    $lines = explode("\n", $markdown);

    foreach ($lines as $line) {
        if (substr($line, 0, 1) == "#") {
            continue;
        } else if ($line == "") {
            continue;
        } else if ($line == "-") {
            $line = "<br>";
        } else if (preg_match('/^\*\*\*(.+)$/', $line, $result)) {
            $line = "<h4>{$result[1]}</h4>";
        } else if (preg_match('/^\*\*(.+)$/', $line, $result)) {
            // $line = "<h3 style=\"border-bottom: solid 2px midnightblue; font-size: 1.2rem; background: white;\">{$result[1]}</h2>";
            $line = "<h3>{$result[1]}</h3>";
        } else if (preg_match('/^\*(.+)$/', $line, $result)) {
            // $line = "<h2 style=\"border-bottom: double 5px midnightblue; font-size: 1.7rem;\">{$result[1]}</h1>";
            $line = "<h2>{$result[1]}</h2>";
        } else if (substr($line, 0, 3) == "___") {
            // この何もしないところがないと<br>が挿入されてしまう
        } else {
            $line .= "<br class=\"bbb\">";
        }

        $html .= $line;
    }
    return $html;
}

function markdown_parse_kakutei_siyou_html_replace_block_after($html, $matches)
{
    global $block_starter;

    foreach ($block_starter as $starter) {
        for ($i = 0; $i < count($matches[$starter]); $i++) {
            if ($starter == 'python') {
                $replace = '<pre style="line-height: 1.4rem; font-size: 1.3rem;"><code class="python">';
                $replace .= $matches[$starter][$i];
                $replace .= '</code></pre>';
            } else if ($starter == 'susiki') {
                $replace = '<div class="susiki">$';
                $replace .= $matches[$starter][$i];
                $replace .= '$</div>';
            } else if ($starter == 'wide_susiki') {
                $replace = '<div class="wide_susiki">$';
                $replace .= $matches[$starter][$i];
                $replace .= '$</div>';
            } else if ($starter == 'ul') {
                $list = explode("\n", $matches[$starter][$i]);
                $replace = '<ul>';
                foreach ($list as $li) {
                    if ($li == "") {
                        continue;
                    }
                    $replace .= "<li>{$li}</li>";
                }
                $replace .= '</ul>';
            } else if ($starter == 'ol') {
                $list = explode("\n", $matches[$starter][$i]);
                $replace = '<ol>';
                foreach ($list as $li) {
                    if ($li == "") {
                        continue;
                    }
                    $replace .= "<li>{$li}</li>";
                }
                $replace .= '</ol>';
            }
            $html = str_replace("___{$starter}_" . strval($i), $replace, $html);
        }
    }

    return $html;
}

function my_preg_match($pat, $markdown, $default = "")
{
    preg_match($pat, $markdown, $result);
    return $result[1] ? $result[1] : $default;
}
<?php

require_once "/var/www/blog/vendor/autoload.php";

//★★★大本命の実装　エバーノートに確定仕様を記述！！！
function markdown_parse_kakutei_siyou($markdown)
{
    $categories = my_preg_match('/#cat\(([^\)]+)\)/s', $markdown);
    if ($categories != "") {
        $categories = explode(",", $categories);
    }

    $html = markdown_parse_kakutei_siyou_html($markdown);
    $html_no_tag = strip_tags($html);
    $content_no_tag = str_replace("\n", "", $html_no_tag);
    $description = mb_substr($content_no_tag, 0, 120, "UTF-8");

    return [
        title => my_preg_match('/#title\(([^\)]*)\)/s', $markdown),
        thumbnail => my_preg_match('/#thumb\(([^\)]+)\)/s', $markdown),
        status => my_preg_match('/#status\(([^\)]+)\)/', $markdown, "draft"),
        pickup => intval(my_preg_match('/#pickup\(([^\)]+)\)/', $markdown, "0")),
        content_no_tag => $content_no_tag,
        description => $description,
        html => $html,
        categories => $categories,
    ];
}

function markdown_parse_kakutei_siyou_html($markdown)
{
    //行ごとではない一括置換
    //b()はコードブロックに悪影響を与えそうなので後で行う
    $markdown = preg_replace("/blank\(([^,]+),([^\)]+)\)/", "<a href=\"\$2\" target=\"_blank\">\$1</a>", $markdown);

    // まずは複数行ブロックを置換する
    // #python
    // import numpy as np
    // print("Hello World")
    // #python_end
    // ↓
    // #python_0
    //返り値の$markdownには置換後のマークダウンが、$matchesには置換した部分に入るコンテンツが入る
    list($markdown, $matches) = markdown_parse_kakutei_siyou_html_replace_block_before($markdown);

    // file_put_contents("zzzzzzzzzzzz.txt", $markdown);

    //行ごとではない一括置換
    //b()はブロックを置換した後にする
    $markdown = preg_replace("/b\(([^\)]+)\)/", "<b>\$1</b>", $markdown);

    //行ごとの処理
    $html = markdown_parse_kakutei_siyou_html_line_process($markdown);

    //タグに置換されていた複数行ブロックを元に戻す
    $html = markdown_parse_kakutei_siyou_html_replace_block_after($html, $matches);

    return $html;
}

$block_starter = ["python", "susiki", "wide_susiki", "ul", "ol"];

function markdown_parse_kakutei_siyou_html_replace_block_before($markdown)
{
    global $block_starter;

    $matches = [];
    foreach ($block_starter as $starter) {
        preg_match_all("/#{$starter}(.*?)#{$starter}_end/s", $markdown, $match);
        $matches[$starter] = $match[1];
        // $aaaaa = count($matches[$starter]);
        // echo "AAAA{$aaaaa}AAAAA";
        for ($i = 0; $i < count($matches[$starter]); $i++) {
            $markdown = str_replace($match[0][$i], "___{$starter}_" . strval($i), $markdown);
        }
    }

    return [$markdown, $matches];
}

function markdown_parse_kakutei_siyou_html_line_process($markdown)
{
    $html = "";
    $lines = explode("\n", $markdown);

    foreach ($lines as $line) {
        if (substr($line, 0, 1) == "#") {
            continue;
        } else if ($line == "") {
            continue;
        } else if ($line == "-") {
            $line = "<br>";
        } else if (preg_match('/^\*\*\*(.+)$/', $line, $result)) {
            $line = "<h4>{$result[1]}</h4>";
        } else if (preg_match('/^\*\*(.+)$/', $line, $result)) {
            // $line = "<h3 style=\"border-bottom: solid 2px midnightblue; font-size: 1.2rem; background: white;\">{$result[1]}</h2>";
            $line = "<h3>{$result[1]}</h3>";
        } else if (preg_match('/^\*(.+)$/', $line, $result)) {
            // $line = "<h2 style=\"border-bottom: double 5px midnightblue; font-size: 1.7rem;\">{$result[1]}</h1>";
            $line = "<h2>{$result[1]}</h2>";
        } else if (substr($line, 0, 3) == "___") {
            // この何もしないところがないと<br>が挿入されてしまう
        } else {
            $line .= "<br class=\"bbb\">";
        }

        $html .= $line;
    }
    return $html;
}

function markdown_parse_kakutei_siyou_html_replace_block_after($html, $matches)
{
    global $block_starter;

    foreach ($block_starter as $starter) {
        for ($i = 0; $i < count($matches[$starter]); $i++) {
            if ($starter == 'python') {
                $replace = '<pre style="line-height: 1.4rem; font-size: 1.3rem;"><code class="python">';
                $replace .= $matches[$starter][$i];
                $replace .= '</code></pre>';
            } else if ($starter == 'susiki') {
                $replace = '<div class="susiki">$';
                $replace .= $matches[$starter][$i];
                $replace .= '$</div>';
            } else if ($starter == 'wide_susiki') {
                $replace = '<div class="wide_susiki">$';
                $replace .= $matches[$starter][$i];
                $replace .= '$</div>';
            } else if ($starter == 'ul') {
                $list = explode("\n", $matches[$starter][$i]);
                $replace = '<ul>';
                foreach ($list as $li) {
                    if ($li == "") {
                        continue;
                    }
                    $replace .= "<li>{$li}</li>";
                }
                $replace .= '</ul>';
            } else if ($starter == 'ol') {
                $list = explode("\n", $matches[$starter][$i]);
                $replace = '<ol>';
                foreach ($list as $li) {
                    if ($li == "") {
                        continue;
                    }
                    $replace .= "<li>{$li}</li>";
                }
                $replace .= '</ol>';
            }
            $html = str_replace("___{$starter}_" . strval($i), $replace, $html);
        }
    }

    return $html;
}

function my_preg_match($pat, $markdown, $default = "")
{
    preg_match($pat, $markdown, $result);
    return $result[1] ? $result[1] : $default;
}
 */
