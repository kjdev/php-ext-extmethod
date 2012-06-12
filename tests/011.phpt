--TEST--
Check empty method
--FILE--
<?php
if (!extension_loaded('extmethod')) {
    dl('extmethod.' . PHP_SHLIB_SUFFIX);
}

class Test {}

$retval = extmethod_factory('Test', '', function() { echo "static_test\n"; });
var_dump($retval);
?>
--EXPECTF--
Warning: Cannot empty method name in %s011.php on line %d
bool(false)
