<?php

namespace Hiyokko2;

// ini_set("display_errors", 0);
// error_reporting(0);
error_reporting(E_ERROR & ~E_WARNING & ~E_PARSE & ~E_NOTICE);

require "../src/TextParser.php";
// use Hiyokko2\TextParser;

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

function throw_exception($ex_class, $func)
{
    try {
        $func();
    } catch (\Exception $e) {
        if (get_class($e) == "Hiyokko2\\" . $ex_class) {
            return true;
        }
    }
    return false;
}

// test_not_exist_title();

$ok = true;
foreach (get_defined_functions()[user] as $test_func) {
    if (substr($test_func, 0, 14) == "hiyokko2\\test_") {
        echo $test_func . "\n";
        if ($test_func()) {
            echo "OK! OOOOO\n";
        } else {
            $ok = false;
            echo "NG! XXXXX\n";
        }
        echo "-------------------------\n\n";
    }
}
if ($ok) {
    echo "ALL OK\n";
} else {
    echo "NOT OK\n";
}
