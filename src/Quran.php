<?php

namespace Quran;

use Quran\Http\Interfaces\RequestInterface;
use Quran\Http\Interfaces\UrlInterface;
use Quran\Http\Request;
use Quran\Http\Url;
use Quran\Interfaces\QuranInterface;

class Quran implements QuranInterface
{
    private $request;

    private $language;

    private $cache;

    private $cacheData;

    private $chapter;

    private $verse;

    private $recitations;

    private $translations;

    private $languages;

    private $tafsirs;

    public function __construct($settings)
    {
        if (is_array($settings)) {
            if (isset($settings['language']) && strlen($settings['language']) === 2) {
                $this->language = $settings['language'];
            } else {
                $this->language = self::DEFAULT_LANGUAGE;
            }

            if (isset($settings['cache'])) {
                if (file_exists($settings['cache'])) {
                    if (filesize($settings['cache']) === 0) {
                        file_put_contents($settings['cache'], '<?php return array();');
                    }
                    $this->cache     = $settings['cache'];
                    $this->cacheData = require $settings['cache'];
                } else {
                    throw new \RuntimeException(sprintf('Invalid cache file.'));
                }
            }
        }

        $this->request = new Request(new Url("http://staging.quran.com:3000/api/v3/?language={$this->language}"));
    }

    public function __call($name, $args)
    {
        if (in_array($name, self::DEFAULT_OPTIONS)) {

            return $this->options($name);
        }

        $this->chapter->$name();

        return $this->chapter;
    }

    //----------------------------------------------------------------------------------
    // API: /chapters
    // API: /chapters/{id}
    // API: /chapters/{id}/info
    // API: /chapters/{id}/verses/
    // API: /chapters/{id}/verses/{id}
    // API: /chapters/{id}/verses/tafsirs
    // API: /chapters/{id}/verses/{id}/tafsirs
    //----------------------------------------------------------------------------------

    public function chapter(int $chapter = 0)
    {
        $this->chapter = new Chapter($this->request, $chapter);

        return $this;
    }

    public function get($handler)
    {
        $handler($this->chapter->data());
    }

    //----------------------------------------------------------------------------------
    // API: /search?q=string&size=20&page=0
    //----------------------------------------------------------------------------------

    public function search(string $query = '', int $size = 20, int $page = 0)
    {
        return $this->request->search($query, $size, $page);
    }

    //----------------------------------------------------------------------------------
    // API: /options/option[recitations,translations,languages,tafsirs]
    //----------------------------------------------------------------------------------

    public function options($option)
    {
        if ($this->{$option} === null) {

            if (isset($this->cache)) {
                if (isset($this->cacheData[$option])) {

                    return $this->cacheData[$option];
                }
            }

            $this->{$option} = $this->request->options($option);

            if (isset($this->cache)) {
                $this->cacheData[$option] = $this->{$option};

                file_put_contents(
                    $this->cache,
                    '<?php return ' . var_export($this->cacheData, true) . ';'
                );
            }
        }

        return $this->{$option};
    }

    //----------------------------------------------------------------------------------
    // Setting options
    //----------------------------------------------------------------------------------

    public function language($language = self::DEFAULT_LANGUAGE)
    {
        $this->language = $language;
    }

    public function cache($cache = null)
    {
        $this->cache = $cache;
    }

}
