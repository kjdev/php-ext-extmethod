--TEST--
Check method call
--FILE--
<?php
if (!extension_loaded('extmethod')) {
    dl('extmethod.' . PHP_SHLIB_SUFFIX);
}

echo "== Test1 ==\n";
class Test1 {}

var_dump(extmethod_factory('Test1', 'test', function() { echo "test1\n"; }));

$test1 = new Test1();

var_dump(get_class_methods($test1));

var_dump(method_exists($test1, 'test'));

var_dump(is_callable(array($test1, 'test'), false));

$test1->test();


echo "== Test2 ==\n";
class Test2 {}
$test2 = new Test2();

var_dump(extmethod_factory('Test2', 'test', function() { echo "test2\n"; }));

var_dump(get_class_methods($test2));

var_dump(method_exists($test2, 'test'));

var_dump(is_callable(array($test2, 'test'), false));

$test2->test();


echo "== Test3 ==\n";
class Test3 {}

$f3 = function() { echo "test3\n"; };

var_dump(extmethod_factory('Test3', 'test', $f3));

$test3 = new Test3();

var_dump(get_class_methods($test3));

var_dump(method_exists($test3, 'test'));

var_dump(is_callable(array($test3, 'test'), false));

$test3->test();


echo "== Test4 ==\n";
class Test4 {}
$test4 = new Test4();

$f4 = function() { echo "test4\n"; };

var_dump(extmethod_factory('Test4', 'test', $f4));

var_dump(get_class_methods($test4));

var_dump(method_exists($test4, 'test'));

var_dump(is_callable(array($test4, 'test'), false));

$test4->test();
?>
--EXPECTF--
== Test1 ==
bool(true)
array(1) {
  [0]=>
  string(4) "test"
}
bool(true)
bool(true)
test1
== Test2 ==
bool(true)
array(1) {
  [0]=>
  string(4) "test"
}
bool(true)
bool(true)
test2
== Test3 ==
bool(true)
array(1) {
  [0]=>
  string(4) "test"
}
bool(true)
bool(true)
test3
== Test4 ==
bool(true)
array(1) {
  [0]=>
  string(4) "test"
}
bool(true)
bool(true)
test4
