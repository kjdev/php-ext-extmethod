--TEST--
Check accesor:self
--FILE--
<?php
if (!extension_loaded('extmethod')) {
    dl('extmethod.' . PHP_SHLIB_SUFFIX);
}

class Test {
    static $count = 0;
}

$test1 = new Test();

$retval = extmethod_factory('Test', 'test',
                            function() {
                                echo "count => ", self::$count, "\n";
                                self::$count++;
                            });
var_dump($retval);

$test2 = new Test();


echo "== TEST1 ==\n";
$test1->test();
$test2->test();

echo "== TEST2 ==\n";
$test1->test();
$test2->test();
?>
--EXPECTF--
bool(true)
== TEST1 ==
count => 0
count => 1
== TEST2 ==
count => 2
count => 3
