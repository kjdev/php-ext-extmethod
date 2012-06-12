# php-ext-extmethod #

This PHP extension is Extension method by closure.

## Build ##

    % phpize
    % ./configure
    % make
    % make install

## Configration ##

extmethod.ini:

    extension=extmethod.so

## Function ##

bool extmethod_factory( mixed $object, string $method, [, Closure $closure, int $flags = EXTMETHOD_PUBLIC ] )

### parameters ###

object:

    An object instance or a class name

method:

    The method name or trait name

closure:

    Closure object

flags:

    The class scope

* EXTMETHOD_PUBLIC
* EXTMETHOD_PROTECTED
* EXTMETHOD_PRIVATE
* EXTMETHOD_STATIC

### return values ###

Returns TRUE if the method succeeded in adding, FALSE otherwise.

## Example ##

    class Test {};

    extmethod_factory('Test', 'test', function() { echo "test\n"; });

    (new Test)->test();

    //trait : PHP 5.4
    trait Hello { public function sayHello() { echo "hello\n"; } }
    extmethod_factory('Test', 'Hello');

    (new Test)->sayHello();
