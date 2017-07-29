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
     * @param array   $options - An array of options which is going to be converted
     * to Url query params.
     */
    public function __construct(Request $request, array $options = [])
    {
        $this->request = $request;

        if (isset($options['cache'])) {
            $this->cache = $options['cache'];
            unset($options['cache']);
        }
        $this->options = $options;
    }

    //--------------------------------------------------------------------------------------
    // API: /chapters                          = chapter('about');
    // API: /chapters/{id}                     = chapter(id, 'about');
    // API: /chapters/{id}/info                = chapter(id, 'info');
    //--------------------------------------------------------------------------------------

    public function chapter($chapter = null, $options = [])
    {
        if ((int) $chapter < 0 || (int) $chapter > self::TOTAL_CHAPTERS) {
            throw new \InvalidArgumentException(
                sprintf("Excepted chapter between 1 - 114.")
            );
        }

        $this->chapter = ($chapter !== null && !is_string($chapter) && $chapter !== 0) ? $chapter : null;

        if (is_string($chapter) && $chapter === 'about' ||
            is_string($options) && $options === 'about') {

            if (isset($this->cache)) {
                $file = "{$this->cache}/about.cache";

                if (!file_exists($file) && !fopen($file, 'w')) {
                    throw new \RuntimeException(
                        sprintf('Invalid cache file.')
                    );
                }

                if (filesize($file) !== 0) {
                    $data = require $file;

                    if ($this->chapter === null && count($data) === 114) {
                        return array_values($data);
                    }
                    if (isset($data[$this->chapter])) {
                        return $data[$this->chapter];
                    }
                }
            }

            $query = $this->request->send("chapters/{$this->chapter}");

            if ($this->chapter === null) {
                $data = $query['chapters'];
            } else {
                $data[$this->chapter] = $query['chapter'];
            }

            if (isset($this->cache)) {
                if ($this->chapter === null) {
                    $data = array_combine(range(1, count($data), array_values($data)));
                }
                file_put_contents(
                    $file,
                    "<?php return " . var_export($data, true) . ';'
                );
            }

            if ($this->chapter !== null) {
                $data = $data[$this->chapter];
            }

            return $data;
        }

        if ($chapter === null) {
            throw new \InvalidArgumentException(
                sprintf("Please specify chapter number")
            );
        }

        if (is_string($options) && $options === 'info') {

            if (isset($this->cache)) {
                $file = "{$this->cache}/info.cache";

                if (!file_exists($file) && !fopen($file, 'w')) {
                    throw new \RuntimeException(
                        sprintf('Invalid cache file.')
                    );
                }

                if (filesize($file) !== 0) {
                    $data = require $file;

                    if (isset($data[$this->chapter])) {
                        return $data[$this->chapter];
                    }
                }
            }

            $query = $this->request->send("chapters/{$this->chapter}/info");

            $data[$this->chapter] = $query['chapter_info'];

            if (isset($this->cache)) {
                file_put_contents(
                    $file,
                    '<?php return ' . var_export($data, true) . ';'
                );
            }

            return $data[$this->chapter];

        } elseif (is_int($options)) {
            $this->verse = $options;

        } elseif (is_array($options)) {
            $this->options = array_merge($this->options, $options);

        } else {
            throw new \InvalidArgumentException(
                sprintf("Please specify correct argument")
            );
        }

        return $this;
    }

    //--------------------------------------------------------------------------------------
    // API: /chapters/{id}/verses/             = chapter(id, [args])->verse([args]);
    //--------------------------------------------------------------------------------------

    public function verse(array $options = [], array $tokens = [])
    {
        if (!isset($options['page']) && !isset($options['offset']) && !isset($options['limit'])) {
            if (!empty($options)) {
                $tokens  = $options;
                $options = [];
            }
        }

        $options = array_merge($this->options, $options);
        if (isset($options['tafsirs'])) {
            unset($options['tafsirs']);
        }

        $translations = $options['translations'];
        if (is_array($translations)) {
            array_walk($translations, function ($key, $value, $default = 'translations') use (&$build_query) {
                $build_query[] = http_build_query([$default => $key]);
            });
            unset($options['translations']);
            $http_query = http_build_query($options) . '&' . implode('&', $build_query);
        } else {
            $http_query = http_build_query($options);
        }

        $data = $this->request->send(
            "chapters/{$this->chapter}/verses",
            $http_query
        );

        if (!empty($tokens)) {
            $collection = [];

            foreach ($data['verses'] as $verse_key => $verse) {
                foreach ($tokens as $key => $value) {
                    if (isset($verse[$key]['0'])) {
                        if (is_array($value)) {
                            foreach ($value as $val) {
                                $collection[$verse_key]["{$key}_{$val}"] = $verse[$key][0][$val];
                            }
                        } else {
                            $collection[$verse_key]["{$key}_{$value}"] = $verse[$key][0][$value];
                        }

                    } elseif (isset($verse[$key][$value])) {
                        $collection[$verse_key]["{$key}_{$value}"] = $verse[$key][$value];

                    } elseif (is_string($value)) {
                        $collection[$verse_key][$value] = $verse[$value];
                    }
                }
            }

            return $collection;
        }

        return $data;
    }

    //--------------------------------------------------------------------------------------
    // API: /chapters/{id}/verses/{id}/tafsirs = chapter(id, [args])->tafsir([args]);
    //--------------------------------------------------------------------------------------

    public function tafsir($tafsirs = [])
    {
        if (empty($tafsirs)) {
            if (isset($this->options['tafsirs'])) {
                $tafsirs = $this->options['tafsirs'];
            }
        }
        if (is_array($tafsirs)) {
            array_walk($tafsirs, function ($key, $value, $default = 'tafsirs') use (&$build_query) {
                $build_query[] = http_build_query([$default => $key]);
            });
            $http_query = implode('&', $build_query);

        } else {
            $http_query = "tafsirs={$tafsirs}";
        }

        $data = $this->request->send(
            "chapters/{$this->chapter}/verses/{$this->verse}/tafsirs",
            $http_query
        );

        return $data['tafsirs'];
    }
}
