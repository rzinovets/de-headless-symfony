<?php

namespace App\Entity;

class DataObject
{
    /**
     * @var mixed|null
     */
    protected array $data = [];

    /**
     * @param array $args
     */
    public function __construct(array $args = []) {
        $this->data = $args;
    }

    /**
     * @param string $name
     * @return mixed|null
     */
    public function __get(string $name) {
        if (!isset($this->data[$name])) {
            return null;
        }
        return $this->data[$name];
    }

    /**
     * @param string $name
     * @param $value
     * @return void
     */
    public function __set(string $name, $value) {
        $this->data[$name] = $value;
        //return $this;
    }
}
