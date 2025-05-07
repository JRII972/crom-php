<?xml version="1.0" encoding="UTF-8"?>
<phpunit bootstrap="vendor/autoload.php">
    <testsuites>
        <testsuite name="Application Test Suite">
            <directory>./tests</directory>
        </testsuite>
    </testsuites>
</phpunit>

<?php
use PHPUnit\Framework\TestCase;

class ExampleTest extends TestCase
{
    public function testHelloWorld()
    {
        $this->assertEquals(1, 1);
    }
}