<?php

namespace Quran;

class Base
{
    protected $query = [];
    protected $data  = [];

    public function build()
    {
        return $this->query;
    }

    protected function fetchData(array $query)
    {
        return $query;
    }
}
