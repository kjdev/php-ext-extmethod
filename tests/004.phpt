--TEST--
Check factory exsits method
--FILE--
<?php
if (!extension_loaded('extmethod')) {
    dl('extmethod.' . PHP_SHLIB_SUFFIX);
}

class Test {
    function test () {}
}

var_dump(extmethod_factory('Test', 'test', function() { echo "test\n"; }));
var_dump(extmethod_factory(new Test, 'test', function() { echo "test\n"; }));

//first factory
var_dump(extmethod_factory('Test', 'test1', function() { echo "test\n"; }));
var_dump(extmethod_factory(new Test, 'test2', function() { echo "test\n"; }));

//second factory
var_dump(extmethod_factory('Test', 'test1', function() { echo "test\n"; }));
var_dump(extmethod_factory(new Test, 'test2', function() { echo "test\n"; }));
?>
--EXPECTF--
Warning: Test::test() is exsits in %s004.php on line %d
bool(false)

Warning: Test::test() is exsits in %s004.php on line %d
bool(false)
bool(true)
bool(true)

Warning: Test::test1() is exsits in %s004.php on line %d
bool(false)

Warning: Test::test2() is exsits in %s004.php on line %d
bool(false)
