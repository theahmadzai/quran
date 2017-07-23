<?php

namespace Quran;

use Quran\Http\Request;

class Chapter
{
    const TOTAL_CHAPTERS = 114;

    private $request;

    private $chapter;

    protected $data;

    public function __construct(Request $request, int $chapter = 0)
    {
        if ($chapter < 0 || $chapter > self::TOTAL_CHAPTERS) {
            throw new \InvalidArgumentException(sprintf("Excepted chapter number between 1 and 114."));
        }

        $this->request = $request;
        $this->chapter = $chapter;

        $this->data = $request->chapter($chapter);
    }

    public function info()
    {
        if ($this->chapter === 0) {
            return false;
        }
        echo 'info';
        // return $this->request->chapter($this->chapter, 'info');
    }
    public function abc()
    {
        echo 'abc';
    }

    // public function __call(string $name = null, array $args = [])
    // {
    //     if ($name === 'info') {
    //         $this->data = $this->request->chapter($this->chapter, $name);

    //         return $this->data;
    //     } else {
    //         $this->data = $this->request->chapter($this->chapter);
    //     }

    //     $chapter = $this->data;

    //     if ($name === 'with') {
    //         pr($args);
    //         // if (property_exists($chapter, 'chapters')) {
    //         //     $response = [];
    //         //     foreach ($chapter->chapters as $key => $value) {
    //         //         $response[] = $value->$name;
    //         //     }

    //         //     return $response;
    //         // }

    //         // return $chapter->chapter->{$name};
    //     }

    public function data()
    {
        return $this->data;
    }

    // public function __destruct()
    // {
    //     echo 'hi';

    //     return [false, $this->data];
    // }
}
