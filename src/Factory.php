<?php

namespace Quran;

class Factory
{
    private $fragmentNamespace = 'Quran\\Fragments\\';

    public function create(string $name, array $arguments)
    {
        $className = $this->fragmentNamespace . ucfirst($name);

        if (!class_exists($className)) {
            throw new \Exception(sprintf('"%s" is not a valid chain', $className));
        }

        $reflection = new \ReflectionClass($className);

        return $reflection->newInstanceArgs($arguments);
    }
}
