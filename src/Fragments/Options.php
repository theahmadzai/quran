<?php

namespace Quran\Fragments;

use Quran\Base;

class Options extends Base
{
    public function __construct(string $name, array $arguments = [])
    {
        $this->query[$name] = $arguments;
    }

    public function cache()
    {

    }
}
