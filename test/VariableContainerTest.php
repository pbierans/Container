<?php

/** @noinspection PhpUnhandledExceptionInspection */

/** @noinspection PhpUndefinedFieldInspection */

namespace PatrickBierans\Container;

use PHPUnit\Framework\TestCase;

class VariableContainerTest extends TestCase {

    /**
     * @expectedException \RuntimeException
     */
    public function testExceptionUnset(): void {
        $container = new VariableContainer([]);
        /** @noinspection PhpUndefinedMethodInspection */
        $container->hello();
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testExceptionUndefined(): void {
        $container = new VariableContainer([
            'some' => function () {
            }
        ]);
        /** @noinspection PhpUndefinedMethodInspection */
        $container->hello();
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testExceptionNotCallable(): void {
        $container = new VariableContainer(['some' => 'var']);
        /** @noinspection PhpUndefinedMethodInspection */
        $container->some();
    }

    public function testIntegration(): void {
        $container = new VariableContainer(['message' => 'Hello World!']);

        $this->assertTrue($container->has(['message']), 'got message');
        /** @noinspection PhpUndefinedFieldInspection */
        $this->assertEquals($container->message, 'Hello World!', 'accessible as property via magic __get()');

        $container->set(['some'], 'value');
        $this->assertTrue($container->has(['message']), 'still have old message');
        $this->assertTrue($container->has(['some']), 'and have some new');

        $container->set(['go', 'deep', 'a'], 'value');
        $this->assertTrue($container->has(['go', 'deep', 'a']), 'we can set deep');
        $container->set(['go', 'deep', 'b'], 'value');
        $this->assertTrue($container->has(['go', 'deep']), 'we can set deep with partially existing path');

        $godeep = $container->get(['go', 'deep']);
        $this->assertInternalType('array', $godeep, 'merging behaviour');
        $this->assertSame(\count($godeep), 2, 'merging behaviour');
        $this->assertTrue(array_key_exists('a', $godeep), 'got key a');
        $this->assertTrue(array_key_exists('b', $godeep), 'got key b');

        $container->set(['highfive'], function () {
            return 5;
        });

        $this->assertTrue($container->has(['highfive']), 'we set a callback');

        /** @noinspection PhpUnhandledExceptionInspection */
        /** @noinspection PhpUndefinedMethodInspection */
        $this->assertEquals(5, $container->highfive(), 'we called a callback via magic __call()');
    }
}