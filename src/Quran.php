<?php

namespace Quran;

use Exception;

class Quran
{
    public static function __callStatic(string $name, array $arguments)
    {
        return (new static())->__call($name, $arguments);
    }

    public function __call(string $name, array $arguments)
    {
        $classname = '\\Quran\\Rules\\' . ucfirst($name);

        if (!class_exists($classname)) {
            throw new Exception(
                sprintf("Class '%s' not found, Pleaes type valid chain.", $classname)
            );
        }

        return $this;
    }

    public function get(array $arguments)
    {
        return 'abc';
    }
}
