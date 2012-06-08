--TEST--
Check protected method
--FILE--
<?php
if (!extension_loaded('extmethod')) {
    dl('extmethod.' . PHP_SHLIB_SUFFIX);
}

class Test {
    function test() {
        if (method_exists($this, 'protected_test')) {
            $this->protected_test();
        } else {
            echo "unknown method: protected_test\n";
        }
    }
}

$test = new Test();

$test->test();
if (is_callable(array($test, 'protected_test'))) {
    $test->protected_test();
} else {
    echo "no is_callable: protected_test\n";
}

$retval = extmethod_factory('Test', 'protected_test',
                            function() { echo "protected_test\n"; },
                            EXTMETHOD_PROTECTED);
var_dump($retval);

$test->test();
if (is_callable(array($test, 'protected_test'))) {
    $test->protected_test();
} else {
    echo "no is_callable: protected_test\n";
}
?>
--EXPECTF--
unknown method: protected_test
unknown method: protected_test
no is_callable: protected_test
bool(true)
protected_test
no is_callable: protected_test
