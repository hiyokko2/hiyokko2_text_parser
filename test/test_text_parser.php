<?php

namespace Hiyokko2;

// ini_set("display_errors", 0);
// error_reporting(0);
error_reporting(E_ERROR & ~E_WARNING & ~E_PARSE & ~E_NOTICE);

require "../src/TextParser.php";
// use Hiyokko2\TextParser;
require "test_funcs.php";

function test_not_exist_title()
{
    return throw_exception("NoTitleException", function () {
        $mark = "#cat(aaa,bbb)";
        TextParser::parse($mark);
    });
}

function test_exist_title()
{
    return !throw_exception("NoTitleException", function () {
        $mark = "#title(aaa)";
        TextParser::parse($mark);
    });
}

function test_equal_title()
{
    return "aaa" == TextParser::parse("#title(aaa)")[title];
}

function test_replace_block_before()
{
    $mark = "#python
print(123)
#python_end
#python
for i in range(10):
#python_end
";
    // var_dump(TextParser::parse_html_replace_block_before($mark));
    list($res_mark, $res_match) = TextParser::parse_html_replace_block_before($mark);
    // var_dump($res_mark);
    return include_string($res_mark, "___python_0") && include_string($res_mark, "___python_1");
}

function test_parse_html_ul()
{
    $mark = "#ul
aaa
bbb
ccc
#ul_end
";

    $html = TextParser::parse_html($mark);
    return include_string($html,
        "<ul>",
        "<li>aaa</li>",
        "<li>bbb</li>",
        "<li>ccc</li>",
        "</ul>"
    );
}

do_test_all();

