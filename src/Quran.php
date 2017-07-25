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

    private $chapter;

    private $language;

    private $cache;

    private $cacheData;

    private $recitations;

    private $translations;

    private $languages;

    private $tafsirs;

    private $data;

    public function __construct(array $settings = [])
    {
        $this->language = self::DEFAULT_LANGUAGE;

        if (isset($settings['language']) && strlen($settings['language']) === 2) {
            $this->language = $settings['language'];
        }

        if (isset($settings['cache'])) {
            if (!file_exists($settings['cache']) || !fopen($settings['cache'], 'w')) {
                throw new \RuntimeException(sprintf('Invalid cache file.'));
            }
            if (filesize($settings['cache']) === 0) {
                file_put_contents($settings['cache'], '<?php return array();');
            }
            $this->cache     = $settings['cache'];
            $this->cacheData = require $settings['cache'];
        }

        $this->request = new Request(new Url(self::DEFAULT_URL));

        $this->chapter = new Chapter($this->request);
    }

    public function __call($name, $args)
    {
        if (in_array($name, self::DEFAULT_OPTIONS)) {

            return $this->options($name);
        }

        if (method_exists(Chapter::class, $name)) {

            $data = $this->chapter->$name(...$args);

            // if (!$data instanceof Chapter) {

            //     return $data;
            // }

            $this->data = $this->request->chapter($this->chapter->request());

            return $this;
        }

        throw new \Exception(sprintf("Invalid function call '%s()'", $name));
    }

    //--------------------------------------------------------------------------------------
    // API: /chapters                          = chapter()->about();
    // API: /chapters/{id}                     = chapter(id)->about();
    // API: /chapters/{id}/info                = chapter(id)->info();
    // API: /chapters/{id}/verses/             = chapter(id)->verse([args]);
    // API: /chapters/{id}/verses/{id}         = chapter(id)->verse([args]);
    // API: /chapters/{id}/verses/{id}/tafsirs = chapter(id)->verse([args])->tafsir([args]);
    //--------------------------------------------------------------------------------------

    public function chapter($chapter_number = null, array $options = [])
    {
        return $this->chapter->chapter($chapter_number, $options);

        return $this;
    }

    //--------------------------------------------------------------------------------------
    // API: /search?q=string&size=20&page=0
    //--------------------------------------------------------------------------------------

    public function search($options = [])
    {
        $query = isset($options['query']) ? $options['query'] : null;
        $size  = isset($options['size']) ? $options['size'] : 20;
        $page  = isset($options['page']) ? $options['page'] : 0;

        return $this->request->search((string) $query, (int) $size, (int) $page);
    }

    //--------------------------------------------------------------------------------------
    // API: /options/option[recitations,translations,languages,tafsirs]
    //--------------------------------------------------------------------------------------

    private function options(string $option)
    {
        if ($this->{$option} === null) {

            if (isset($this->cache)) {

                if (isset($this->cacheData[$option])) {

                    $this->{$option} = $this->cacheData[$option];

                    return $this->{$option};
                }
            }
            $this->{$option} = $this->request->options($option)[$option];

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

    //--------------------------------------------------------------------------------------
    // Setting options
    //--------------------------------------------------------------------------------------

    public function language($language = self::DEFAULT_LANGUAGE)
    {
        $this->language = $language;
    }

    public function cache($cache = null)
    {
        $this->cache = $cache;
    }

}
