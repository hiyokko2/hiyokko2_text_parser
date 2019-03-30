<?php

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

function include_string()
{
    $args = func_get_args();
    $subject = $args[0];
    for ($i = 1; $i < count($args); $i++) {
        if (strpos($subject, $args[$i]) === false) {
            return false;
        }
    }
    return true;
}

function do_test_all()
{
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
}
