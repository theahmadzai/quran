<?php

namespace Quran;

class Quran
{
    private static $factory;
    private $fragments;

    private static function fragmentsFactory()
    {
        if (!static::$factory instanceof Factory) {
            static::$factory = new Factory();
        }

        return static::$factory;
    }

    public static function __callStatic(string $name, array $arguments)
    {
        $quran = new static();

        return $quran->__call($name, $arguments);
    }

    public function __call(string $name, array $arguments)
    {
        try {
            $fragment = static::fragmentsFactory()->create($name, $arguments);

            $this->fragments[spl_object_hash($fragment)] = $fragment;
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage(), $exception->getCode(), $exception);
        }

        return $this;
    }

    public function get()
    {
        return $this->fragments;
    }
}
