<?php

namespace Quran\Http\Interfaces;

interface RequestInterface
{
    public function send(string $path, string $query = null);

    public function chapter($chapter, $info);

    public function search(string $query = null, int $size = 20, int $page = 0);

    public function options(string $option);
}
