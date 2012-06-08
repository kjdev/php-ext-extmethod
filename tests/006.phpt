--TEST--
Check accesor:this
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

class Test {
    public $pub;
    protected $_pro;
    private $_pri;

    function __construct($pub, $pro, $pri) {
        $this->pub = $pub;
        $this->setPro($pro);
        $this->setPri($pri);
    }

    function setPro($pro) {
        $this->_pro = $pro;
    }

    function setPri($pri) {
        $this->_pri = $pri;
    }
}

$test1 = new Test('pub-1', 'pro-1', 'pri-1');
$test2 = new Test('pub-2', 'pro-2', 'pri-2');

$retval = extmethod_factory('Test', 'test',
                            function() {
                                echo "public => ", $this->pub, "\n";
                                echo "protected => ", $this->_pro, "\n";
                                echo "private => ", $this->_pri, "\n";
                            });
var_dump($retval);

$test3 = new Test('pub-3', 'pro-3', 'pri-3');


echo "== TEST1 ==\n";
$test1->test();
$test2->test();
$test3->test();

echo "== TEST2 ==\n";
$test1->pub = "PUB-1";

$test1->test();
$test2->test();
$test3->test();

echo "== TEST3 ==\n";
$test2->setPro("PRO-2");

$test1->test();
$test2->test();
$test3->test();

echo "== TEST4 ==\n";
$test3->setPri("PRI-3");

$test1->test();
$test2->test();
$test3->test();
?>
--EXPECTF--
bool(true)
== TEST1 ==
public => pub-1
protected => pro-1
private => pri-1
public => pub-2
protected => pro-2
private => pri-2
public => pub-3
protected => pro-3
private => pri-3
== TEST2 ==
public => PUB-1
protected => pro-1
private => pri-1
public => pub-2
protected => pro-2
private => pri-2
public => pub-3
protected => pro-3
private => pri-3
== TEST3 ==
public => PUB-1
protected => pro-1
private => pri-1
public => pub-2
protected => PRO-2
private => pri-2
public => pub-3
protected => pro-3
private => pri-3
== TEST4 ==
public => PUB-1
protected => pro-1
private => pri-1
public => pub-2
protected => PRO-2
private => pri-2
public => pub-3
protected => pro-3
private => PRI-3
