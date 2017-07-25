<?php

namespace Quran;

use Quran\Http\Request;
use Quran\Interfaces\ChapterInterface;

class Chapter implements ChapterInterface
{
    private $request;

    private $chapter_number;

    private $data;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function chapter(int $chapter_number = null)
    {
        if ($chapter_number < 0 || $chapter_number > self::TOTAL_CHAPTERS) {
            throw new \InvalidArgumentException(sprintf("Excepted chapter number between 1 and 114."));
        }
        if ($chapter_number === 0) {
            $this->chapter_number = null;
        } else {
            $this->chapter_number = $chapter_number;
        }

        return $this;
    }

    public function about()
    {
        return $this->request->about($this->chapter_number);
    }

    public function info()
    {
        if ($this->chapter_number === null) {
            throw new \InvalidArgumentException(sprintf("Please specify chapter number"));
        }

        return $this->request->info($this->chapter_number);
    }

    public function verse(array $args = [])
    {
        echo 'hi verse<br>';

        // return $this;
    }

    public function with(array $args = [])
    {
        echo 'hi with<br>';

        // return $this;
    }

    public function media(array $args = [])
    {
        echo 'hi media<br>';

        // return $this;
    }

    public function tafsir(array $args = [])
    {
        echo 'hi tafsir<br>';
    }

    public function request()
    {
        echo 'Request';
    }
}
