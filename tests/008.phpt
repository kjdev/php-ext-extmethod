--TEST--
Check private method
--FILE--
<?php
if (!extension_loaded('extmethod')) {
    dl('extmethod.' . PHP_SHLIB_SUFFIX);
}

class Test {
    function test() {
        if (method_exists($this, 'private_test')) {
            $this->private_test();
        } else {
            echo "unknown method: private_test\n";
        }
    }
}

$test = new Test();

$test->test();
if (is_callable(array($test, 'private_test'))) {
    $test->private_test();
} else {
    echo "no is_callable: private_test\n";
}

$retval = extmethod_factory('Test', 'private_test',
                            function() { echo "private_test\n"; },
                            EXTMETHOD_PRIVATE);
var_dump($retval);

$test->test();
if (is_callable(array($test, 'private_test'))) {
    $test->private_test();
} else {
    echo "no is_callable: private_test\n";
}
?>
--EXPECTF--
unknown method: private_test
unknown method: private_test
no is_callable: private_test
bool(true)
private_test
no is_callable: private_test
