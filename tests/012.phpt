--TEST--
Check Trait
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

trait Hello {
    public function sayHello() {
        echo "public::Hello\n";
    }

    protected function _proHello() {
        echo "protected::Hello\n";
    }

    private function _priHello() {
        echo "privaate::Hello\n";
    }
}

class Test {
    public function test_pro() {
        $this->_proHello();
    }
    public function test_pri() {
        $this->_priHello();
    }
}

var_dump(extmethod_factory('Test', 'Hello'));

(new Test)->sayHello();
(new Test)->test_pro();
(new Test)->test_pri();
?>
--EXPECTF--
bool(true)
public::Hello
protected::Hello
privaate::Hello
