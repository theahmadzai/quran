<?php

namespace Quran\Fragments;

abstract class AbstractFragment
{
    public function __construct($args)
    {
        pr($args);
    }
}
