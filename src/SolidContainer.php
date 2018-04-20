<?php

namespace PatrickBierans\Container;

/**
 * Minimal Variant. Not compliant to PSR-11.
 * Lookup the tests for examples!
 * You can have a nested array for data and may use it as a deep config.
 * The get() is an array of string being used as array keys for traversing down into the data structure.
 * If the value is not found inside the data structure you will get null here. No exception thrown.
 * The following two things speed up things:
 * The first level of the array can be accessed as properties via __get().
 * If you have an array of callable you can directly access them via __call().
 * If you call a method and it is not defined you _will_ get an exception.
 * Look at PatrickBierans\Template where I use PatrickBierans\ContainerIntegration twice so I have
 * one Container as a scope full of variables based on a config array
 * and another Container with callables to inject Filters into the template files.
 * @see \PatrickBierans\Template\DefaultTemplateTest for an integration example
 */
class SolidContainer {

    /**
     * @var array
     */
    protected $data;

    /**
     * @param array $data
     */
    public function __construct(array $data = []) {
        $this->data = $data;
    }

    /**
     * @param string[] $keys
     *
     * @return bool
     */
    public function has($keys): bool {
        return null !== $this->get($keys);
    }

    /**
     * @param string[] $keys
     *
     * @return array|mixed|null
     */
    public function get($keys) {
        $data = $this->data;
        foreach ($keys as $key) {
            if (!isset($data[$key])) {
                $data = null;
                break;
            }
            $data = $data[$key];
        }
        return $data;
    }

    /**
     * @param $var
     *
     * @return array|mixed|null
     */
    public function __get($var) {
        return $this->get([$var]);
    }

    /**
     * @param string $name
     * @param array $arguments
     *
     * @return mixed
     * @throws \RuntimeException|\Exception
     */
    public function __call($name, array $arguments) {
        if ($this->empty()) {
            throw new \RuntimeException('empty container');
        }
        $c = $this->get([$name]);
        if ($c === null) {
            throw new \RuntimeException('method not found: ' . $name);
        }
        if (!\is_callable($c)) {
            throw new \RuntimeException('not a callable public method for: ' . $name);
        }
        return \call_user_func_array($c, $arguments);
    }

    /**
     * @return bool
     */
    public function empty(): bool {
        return $this->count() === 0;
    }

    /**
     * @return int
     */
    public function count(): int {
        return \count($this->data);
    }

    /**
     *
     */
    public function dump(): void {
        /** @noinspection ForgottenDebugOutputInspection */
        var_dump($this->data);
    }
}