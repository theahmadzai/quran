<?php

namespace Quran\Interfaces;

interface ChapterInterface
{
    /**
     * Total number of chapters in the Quran
     */
    const TOTAL_CHAPTERS = 114;

    /**
     * Max number of verses of the longest chapter
     */
    const MAX_VERSES = 255;

    /**
     * Total number of verses in the Quran
     */
    const TOTAL_VERSES = 6236;

    /**
     * Returns chapter
     * @return [array] return an array of chapter, according to the parameters provided
     */
    public function chapter();

    public function verse();

    public function tafsir();
}
