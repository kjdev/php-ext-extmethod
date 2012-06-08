--TEST--
Check static method
--FILE--
<?php
if (!extension_loaded('extmethod')) {
    dl('extmethod.' . PHP_SHLIB_SUFFIX);
}

class Test {}

$test = new Test();

echo "==TEST1==\n";
$retval = extmethod_factory('Test', 'test',
                            function() { echo "static_test\n"; },
                            EXTMETHOD_STATIC);
var_dump($retval);

Test::test();
$test::test();
$test->test();

echo "==TEST2==\n";
$retval = extmethod_factory('Test', 'test_protected',
                            function() { echo "static_test_protected\n"; },
                            EXTMETHOD_STATIC|EXTMETHOD_PROTECTED);
var_dump($retval);

$retval = extmethod_factory('Test', 'test2',
                            function() { self::test_protected(); },
                            EXTMETHOD_STATIC);
var_dump($retval);

Test::test2();
$test::test2();
$test->test2();

echo "==TEST3==\n";
$retval = extmethod_factory('Test', 'test_private',
                            function() { echo "static_test_private\n"; },
                            EXTMETHOD_STATIC|EXTMETHOD_PRIVATE);
var_dump($retval);

$retval = extmethod_factory('Test', 'test3',
                            function() { self::test_private(); },
                            EXTMETHOD_STATIC);
var_dump($retval);

Test::test3();
$test::test3();
$test->test3();
?>
--EXPECTF--
==TEST1==
bool(true)
static_test
static_test
static_test
==TEST2==
bool(true)
bool(true)
static_test_protected
static_test_protected
static_test_protected
==TEST3==
bool(true)
bool(true)
static_test_private
static_test_private
static_test_private