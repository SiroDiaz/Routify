<?php

class SimpleTest extends PHPUnit_Framework_TestCase {
    public function testSimple() {
        $name = 'Siro';
        $this->assertEquals('Siro', $name);
    }
}