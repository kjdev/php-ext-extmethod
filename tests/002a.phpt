--TEST--
Check factory failure 2
--SKIPIF--
<?php
if (version_compare(PHP_VERSION, '5.4.0') < 0) {
    echo "skip supported PHP 5.4 or newer";
}
?>
--FILE--
<?php
if (!extension_loaded('extmethod')) {
    dl('extmethod.' . PHP_SHLIB_SUFFIX);
}

class Test {}
var_dump(extmethod_factory('Test', 'test'));
var_dump(extmethod_factory(new Test, 'test'));
?>
--EXPECTF--
Warning: Trait test not found in %s002a.php on line %d
bool(false)

Warning: Trait test not found in %s002a.php on line %d
bool(false)
