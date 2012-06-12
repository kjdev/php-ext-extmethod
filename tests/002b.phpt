--TEST--
Check factory failure 3
--SKIPIF--
<?php
if (version_compare(PHP_VERSION, '5.4.0') >= 0) {
    echo "skip supported PHP older";
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
Warning: Cannot empty function in %s002b.php on line %d
bool(false)

Warning: Cannot empty function in %s002b.php on line %d
bool(false)
