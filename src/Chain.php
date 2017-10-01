<?php

namespace Quran;

class Chain
{
    public function addChain($method, array $arguments = [])
    {
        $this->appendChain($method);

        return $this;
    }

    public function appendChain($method)
    {
        $this->chains[spl_object_hash($method)] = $method;
    }

    public function get($input)
    {
        $data = [];
        foreach ($this->chains as $chain) {
            if (!$chain->build($input)) {
                return false;
            }
            $data[] = $chain->abc;
        }

        return $data;
    }
}
