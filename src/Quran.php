<?php

namespace Quran;

use Exception;
use ReflectionClass;

class Quran extends Base
{
    protected $fragments;

    public static function settings(array $settings)
    {

    }

    public function get(array $arguments = [])
    {
        $total = [];
        foreach ($this->fragments as $fragment) {
            $total[] = $fragment->build();
        }

        pr($this->fetchData($total));

        return $this->data;
    }

    public static function __callStatic(string $name, array $arguments)
    {
        return (new static )->__call($name, $arguments);
    }

    public function __call(string $name, array $arguments)
    {
        try {
            $className = 'Quran\\Fragments\\' . ucfirst($name);

            if (!class_exists($className)) {
                throw new Exception(
                    sprintf('"%s" is not a valid chain', $name)
                );
            }

            $fragment = (new ReflectionClass($className))->newInstanceArgs([$name, $arguments]);

            $this->fragments[spl_object_hash($fragment)] = $fragment;

        } catch (Exception $exception) {
            throw new Exception(
                $exception->getMessage(),
                $exception->getCode(),
                $exception
            );
        }

        return $this;
    }
}
