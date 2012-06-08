--TEST--
Check functions
--FILE--
<?php
if (!extension_loaded('extmethod')) {
    dl('extmethod.' . PHP_SHLIB_SUFFIX);
}

var_dump(function_exists('extmethod_factory'));
?>
--EXPECT--
bool(true)
