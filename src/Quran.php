<?php

namespace Quran;

class Quran extends Chain
{
    protected static $factory;

    public static function __callStatic(string $name, array $arguments)
    {
        $chain = new static();

        return $chain->__call($name, $arguments);
    }

    public function __call(string $name, array $arguments)
    {
        return $this->addChain(static::buildChain($name, $arguments));
    }

    public static function buildChain(string $method, array $arguments = [])
    {
        try {
            return static::getFactory()->chain($method, $arguments);
        } catch (\Exception $e) {
            throw new \Exception("ERROR IN CHAIN: " . $e);
        }
    }

    public static function getFactory()
    {
        if (!static::$factory instanceof Factory) {
            static::$factory = new Factory();
        }

        return static::$factory;
    }
}
