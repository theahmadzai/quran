<?php

namespace Quran\Fragments;

class Search
{
    public $abc;

    public function __construct($args)
    {
        $this->abc = $args;
    }

    public function build($input)
    {
        return $input;
    }
}
