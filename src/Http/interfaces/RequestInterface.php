<?php

namespace Quran\Http\Interfaces;

interface RequestInterface
{
    public function send(string $path, string $query = null);
}
