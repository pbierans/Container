<?php

namespace PatrickBierans\Container;

/**
 * Extended Variant. Still not compliant to PSR-11.
 * This alternative also allows to modify the data structure.
 * No support for __set() (magic setter) here. The key is an array
 * describing where to put the $value inside the deep data tree
 * @see \PatrickBierans\Template\DefaultTemplateTest for an integration example
 */
class VariableContainer extends SolidContainer {

    /**
     * @param string[] $keys
     * @param mixed $value
     */
    public function set(array $keys, $value): void {
        $data = &$this->data;
        $remaining = \count($keys);
        foreach ($keys as $key) {
            $remaining--;
            if ($remaining > 0) {
                if (!isset($data[$key])) {
                    $data[$key] = [];
                }
                $data = &$data[$key];
            } else {
                $data[$key] = $value;
            }
        }
    }

}