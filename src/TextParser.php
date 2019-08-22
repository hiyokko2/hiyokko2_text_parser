<?php

namespace Hiyokko2;

class NoTitleException extends \Exception
{
}

class AException extends \Exception
{
}

class TextParser
{
    //★★★大本命の実装　エバーノートに確定仕様を記述！！！
    // public static function markdown_parse_kakutei_siyou($markdown)
    public static function parse($markdown, $throw_exception = true)
    {
        $categories = my_preg_match('/#cat\(([^\)]+)\)/s', $markdown);
        if ($categories != "") {
            $categories = explode(",", $categories);
        }

        $html = self::parse_html($markdown);

        $html_no_tag = strip_tags($html);
        $content_no_tag = str_replace("\n", "", $html_no_tag);
        $description = mb_substr($content_no_tag, 0, 120, "UTF-8");

        $res = [
            "title" => my_preg_match('/#title\(([^\)]*)\)/s', $markdown),
            "thumbnail" => my_preg_match('/#thumb\(([^\)]+)\)/s', $markdown),
            // status => my_preg_match('/#status\(([^\)]+)\)/', $markdown, "draft"),
            "pickup" => intval(my_preg_match('/#pickup\(([^\)]+)\)/', $markdown, "0")),
            "content_no_tag" => $content_no_tag,
            "description" => $description,
            "html" => $html,
            "categories" => $categories,
        ];

        if ($throw_exception && $res["title"] == "") {
            throw new NoTitleException("titleが指定されていません。");
        }

        return $res;
    }

    public static function parse_html($markdown)
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
        list($markdown, $matches) = self::parse_html_replace_block_before($markdown);

        // file_put_contents("zzzzzzzzzzzz.txt", $markdown);

        //行ごとではない一括置換
        //b()はブロックを置換した後にする
        //！！！ここでやるとブロック内のbタグが変換されないのでparse_html_replace_block_after後に移動した
        //$markdown = preg_replace("/b\(([^\)]+)\)/", "<b>\$1</b>", $markdown);

        //行ごとの処理
        $html = self::parse_html_line_process($markdown);

        //タグに置換されていた複数行ブロックを元に戻す
        $html = self::parse_html_replace_block_after($html, $matches);

        $html = preg_replace("/b\(([^\)]+)\)/", "<b>\$1</b>", $html);

        return $html;
    }

    public static function parse_html_replace_block_before($markdown)
    {
        $block_starter = ["python", "susiki", "wide_susiki", "ul", "ol"];

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

    public static function parse_html_line_process($markdown)
    {
        $html = "";
        $lines = explode("\n", $markdown);

        $skip_br_flag = false;
        $blank_line_flag = false;
        foreach ($lines as $line) {
            $line = trim($line);
//            var_dump($line);

            if (substr($line, 0, 1) == "#") {
                continue;
            } elseif ($line == "") {
                if ($blank_line_flag) {
                    $line = "<br>";
                } else {
                    $blank_line_flag = true;
                    continue;
                }
            } elseif ($line == "-") {
                $line = "<br>";
            } elseif (preg_match('/^\*\*\*(.+)$/', $line, $result)) {
                $line = "<h4>{$result[1]}</h4>";
                $skip_br_flag = true;
            } elseif (preg_match('/^\*\*(.+)$/', $line, $result)) {
                // $line = "<h3 style=\"border-bottom: solid 2px midnightblue; font-size: 1.2rem; background: white;\">{$result[1]}</h2>";
                $line = "<h3>{$result[1]}</h3>";
                $skip_br_flag = true;
            } elseif (preg_match('/^\*(.+)$/', $line, $result)) {
                // $line = "<h2 style=\"border-bottom: double 5px midnightblue; font-size: 1.7rem;\">{$result[1]}</h1>";
                $line = "<h2>{$result[1]}</h2>";
                $skip_br_flag = true;
            } elseif (preg_match('/^\\\.+$/', $line)) {
                // 数式行のあとにbrしない
                $skip_br_flag = true;
            } elseif (substr($line, 0, 3) == "___") {
                // この何もしないところがないと<br>が挿入されてしまう
            } elseif (preg_match('/^\\\\\[.+\\\\\]$/', $line)) {
                // 何もせずelseでbrを追加しない
            } elseif (preg_match('/^<a .+<\/a>$/', $line)) {
                // aタグのみなら改行付ける
                $line .= "<br>";
            } elseif (preg_match('/^b(.+)$/', $line)) {
                // bタグのみなら改行付ける
                $line .= "<br>";
            } elseif (preg_match('/^<.+>$/', $line)) {
                // タグを直接書いている場合何もしない
            } else {
                if (!$skip_br_flag) {
                    $line .= "<br>";
                }
            }

            // 空行は連続2行以上から<br>に変換する
            if ($line != "") {
                $blank_line_flag = false;
            }

            // hタグでない場合$skip_br_flagをfalseにする
            if (!preg_match('/^\*(.+)$/', $line)) {
                $skip_br_flag = false;
            }
            // 数式行でない場合$skip_br_flagをfalseにする
            if (!preg_match('/^\\\.+$/', $line)) {
                $skip_br_flag = false;
            }

            $html .= $line;
        }
        return $html;
    }

    public static function parse_html_replace_block_after($html, $matches)
    {
        $block_starter = ["python", "susiki", "wide_susiki", "ul", "ol"];

        foreach ($block_starter as $starter) {
            for ($i = 0; $i < count($matches[$starter]); $i++) {
                if ($starter == 'python') {
                    $replace = '<pre style="line-height: 1.4rem; font-size: 1.3rem;"><code class="python">';
                    $replace .= $matches[$starter][$i];
                    $replace .= '</code></pre>';
                } elseif ($starter == 'susiki') {
                    $replace = '<div class="susiki">$';
                    $replace .= $matches[$starter][$i];
                    $replace .= '$</div>';
                } elseif ($starter == 'wide_susiki') {
                    $replace = '<div class="wide_susiki">$';
                    $replace .= $matches[$starter][$i];
                    $replace .= '$</div>';
                } elseif ($starter == 'ul') {
                    $list = explode("\n", trim($matches[$starter][$i]));
                    $replace = '<ul>';
                    foreach ($list as $li) {
                        if ($li == "") {
                            continue;
                        }
                        $replace .= "<li>{$li}</li>";
                    }
                    $replace .= '</ul>';
                } elseif ($starter == 'ol') {
                    $list = explode("\n", trim($matches[$starter][$i]));
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
}

function my_preg_match($pat, $markdown, $default = "")
{
    preg_match($pat, $markdown, $result);
    if (isset($result[1])) {
        return $result[1] ? $result[1] : $default;
    } else {
        return $default;
    }
}
