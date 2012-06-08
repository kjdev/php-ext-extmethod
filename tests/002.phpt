--TEST--
Check factory failure
--FILE--
<?php
if (!extension_loaded('extmethod')) {
    dl('extmethod.' . PHP_SHLIB_SUFFIX);
}

var_dump(extmethod_factory());
var_dump(extmethod_factory('Test'));

class Test {}
var_dump(extmethod_factory('Test'));
var_dump(extmethod_factory('Test', 'test'));
var_dump(extmethod_factory('Test', null));
var_dump(extmethod_factory('Test', array()));
var_dump(extmethod_factory('Test', new stdClass));

var_dump(extmethod_factory(new Test));
var_dump(extmethod_factory(new Test, 'test'));
var_dump(extmethod_factory(new Test, null));
var_dump(extmethod_factory(new Test, array()));
var_dump(extmethod_factory(new Test, new stdClass));


var_dump(extmethod_factory('Test', 'test', 'function'));
var_dump(extmethod_factory('Test', 'test', 'echo "test\n"'));
var_dump(extmethod_factory('Test', 'test', '{ echo "test\n" }'));
var_dump(extmethod_factory('Test', 'test', null));
var_dump(extmethod_factory('Test', 'test', array()));
var_dump(extmethod_factory('Test', 'test', new stdClass));

var_dump(extmethod_factory(new Test, 'test', 'function'));
var_dump(extmethod_factory(new Test, 'test', 'echo "test\n"'));
var_dump(extmethod_factory(new Test, 'test', '{ echo "test\n" }'));
var_dump(extmethod_factory(new Test, 'test', null));
var_dump(extmethod_factory(new Test, 'test', array()));
var_dump(extmethod_factory(new Test, 'test', new stdClass));

?>
--EXPECTF--
Warning: extmethod_factory() expects at least 3 parameters, 0 given in %s002.php on line %d
bool(false)

Warning: extmethod_factory() expects at least 3 parameters, 1 given in %s002.php on line %d
bool(false)

Warning: extmethod_factory() expects at least 3 parameters, 1 given in %s002.php on line %d
bool(false)

Warning: extmethod_factory() expects at least 3 parameters, 2 given in %s002.php on line %d
bool(false)

Warning: extmethod_factory() expects at least 3 parameters, 2 given in %s002.php on line %d
bool(false)

Warning: extmethod_factory() expects at least 3 parameters, 2 given in %s002.php on line %d
bool(false)

Warning: extmethod_factory() expects at least 3 parameters, 2 given in %s002.php on line %d
bool(false)

Warning: extmethod_factory() expects at least 3 parameters, 1 given in %s002.php on line %d
bool(false)

Warning: extmethod_factory() expects at least 3 parameters, 2 given in %s002.php on line %d
bool(false)

Warning: extmethod_factory() expects at least 3 parameters, 2 given in %s002.php on line %d
bool(false)

Warning: extmethod_factory() expects at least 3 parameters, 2 given in %s002.php on line %d
bool(false)

Warning: extmethod_factory() expects at least 3 parameters, 2 given in %s002.php on line %d
bool(false)

Warning: function does not closure object in %s002.php on line %d
bool(false)

Warning: function does not closure object in %s002.php on line %d
bool(false)

Warning: function does not closure object in %s002.php on line %d
bool(false)

Warning: function does not closure object in %s002.php on line %d
bool(false)

Warning: function does not closure object in %s002.php on line %d
bool(false)

Warning: function does not closure object in %s002.php on line %d
bool(false)

Warning: function does not closure object in %s002.php on line %d
bool(false)

Warning: function does not closure object in %s002.php on line %d
bool(false)

Warning: function does not closure object in %s002.php on line %d
bool(false)

Warning: function does not closure object in %s002.php on line %d
bool(false)

Warning: function does not closure object in %s002.php on line %d
bool(false)

Warning: function does not closure object in %s002.php on line %d
bool(false)
