<?php
require_once 'PHPUnit/Extensions/OutputTestCase.php';

class OutputTest extends PHPUnit_Extensions_OutputTestCase
{
    public function testExpectFooActualFoo()
    {
        $this->expectOutputString('foo');
        print 'foo';
    }

    public function testExpectBarActualBaz()
    {
        $this->expectOutputString('bar');
        print 'baz';
    }
}
?>