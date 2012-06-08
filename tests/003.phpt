--TEST--
Check factory success
--FILE--
<?php
if (!extension_loaded('extmethod')) {
    dl('extmethod.' . PHP_SHLIB_SUFFIX);
}

class Test {}
$closure = function() { echo "test\n"; };

var_dump(extmethod_factory('Test', 'test1', function() { echo "test\n"; }));
var_dump(extmethod_factory('Test', 'test2', $closure));

var_dump(extmethod_factory(new Test, 'test3', function() { echo "test\n"; }));
var_dump(extmethod_factory(new Test, 'test4', $closure));
?>
--EXPECTF--
bool(true)
bool(true)
bool(true)
bool(true)
