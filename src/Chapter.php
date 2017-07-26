<?php

namespace Quran;

use Quran\Http\Request;
use Quran\Interfaces\ChapterInterface;

class Chapter implements ChapterInterface
{
    /**
     * Instance of Request class
     * @var object
     */
    private $request;

    /**
     * Chapter number
     * @var int
     */
    private $chapter;

    /**
     * Verse number
     * @var int
     */
    private $verse;

    /**
     * Url query string vars
     * @var array
     */
    private $options = [];

    /**
     * Chapter class constructor
     * @param Request $request - Inject instance of Request class to be able to
     * send requests to the API.
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    //--------------------------------------------------------------------------------------
    // API: /chapters                          = chapter();
    // API: /chapters/{id}                     = chapter(id);
    // API: /chapters/{id}/info                = chapter(id, 'info');
    //--------------------------------------------------------------------------------------

    public function chapter(int $chapter = null, $options = [])
    {
        if ($chapter < 0 || $chapter > self::TOTAL_CHAPTERS) {
            throw new \InvalidArgumentException(
                sprintf("Excepted chapter between 1 - 114.")
            );
        }

        $this->chapter = ($chapter !== null) ? $chapter : null;

        if (!empty($options)) {
            if (is_string($options) && $options === 'info') {
                if ($chapter === null) {
                    throw new \InvalidArgumentException(
                        sprintf("Please specify chapter number")
                    );
                }

                return $this->request->send("chapters/{$this->chapter}/info");

            } elseif ((is_int($options) > 0) && $options <= self::MAX_VERSES) {
                $this->verse = $options;
            } else {
                $this->options = $options;
            }

            return $this;
        }

        return $this->request->send("chapters/{$this->chapter}");

    }

    //--------------------------------------------------------------------------------------
    // API: /chapters/{id}/verses/             = chapter(id, [args])->verse([args]);
    //--------------------------------------------------------------------------------------

    public function verse(array $options = [], callable $callback = null)
    {
        $array = array_merge($this->options, $options);

        $query = implode('&', array_map(function ($k, $v) {

            return sprintf("%s=%s", $k, $v);
        },
            array_keys($array),
            $array
        ));

        $data = $this->request->send(
            "chapters/{$this->chapter}/verses",
            $query
        );

        if (is_callable($callback)) {

            return $callback($data);
        }

        return $data;
    }

    //--------------------------------------------------------------------------------------
    // API: /chapters/{id}/verses/{id}/tafsirs = chapter(id, [args])->tafsir([args]);
    //--------------------------------------------------------------------------------------

    public function tafsir(int $tafsir = null, callable $callback = null)
    {
        return $this->request->send(
            "chapters/{$this->chapter}/verses/{$this->verse}/tafsirs",
            "tafsirs={$tafsir}"
        );
    }
}
