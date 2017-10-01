<?php

namespace Quran;

class Factory
{
    public function chain(string $method, array $arguments = [])
    {
        $class = '\\Quran\\Fragments\\' . ucfirst($method);
        if (!class_exists($class)) {
            throw new \Exception(sprintf('"%s is not valid name', $method));
        }

        $reflection = new \ReflectionClass($class);

        return $reflection->newInstanceArgs($arguments);
    }
}
