<?php

namespace Quran;

use Quran\Http\Interfaces\RequestInterface;
use Quran\Http\Interfaces\UrlInterface;
use Quran\Http\Request;
use Quran\Http\Url;
use Quran\Interfaces\QuranInterface;

class Quran implements QuranInterface
{
    /**
     * Instanec of Request class
     * @var object
     */
    private $request;

    /**
     * Instance of Chapter class
     * @var object
     */
    private $chapter;

    /**
     * Cache directory/path
     * @var string
     */
    private $cache;

    /**
     * List of recitations fetched
     * @var array
     */
    private $recitations;

    /**
     * List of translations fetched
     * @var array
     */
    private $translations;

    /**
     * List of languages fetched
     * @var array
     */
    private $languages;

    /**
     * List of tafsirs fetched
     * @var array
     */
    private $tafsirs;

    /**
     * Quran class constructor
     * @param array $settings - Array of user defined settings that is merged
     * with default settings.
     */
    public function __construct(array $settings = [])
    {
        if (isset($settings['cache'])) {
            $cache = $settings['cache'];

            if (!is_dir($cache) && !mkdir($cache)) {
                throw new \RuntimeException(
                    sprintf("Invalid cache path '%s'", $cache)
                );
            }
            $this->cache = $cache;
        }

        $this->request = new Request(new Url(self::URL));
        $this->chapter = new Chapter($this->request, $settings);
    }

    /**
     * Gets calls to options i.e list of recitations, translations etc..
     * and checks the method if it exists in the chapter class then returns
     * the chapter class instance.
     * @param  string $name - Name of the function
     * @param  array  $args - Arguments of the function
     * @return object/array - If not option, returns an array from chapter class,
     * othewise object of chapter class.
     */
    public function __call($name, $args)
    {
        if (in_array($name, self::OPTIONS)) {
            return $this->options($name);
        }

        if (method_exists(Chapter::class, $name)) {
            $data = $this->chapter->$name(...$args);

            if (!$data instanceof Chapter) {
                return $data;
            }

            return $this->chapter;
        }

        throw new \Exception(
            sprintf("Invalid function call '%s()'", $name)
        );
    }

    //--------------------------------------------------------------------------------------
    // API: /options/option[recitations,translations,languages,tafsirs]
    //--------------------------------------------------------------------------------------

    /**
     * Returns list of options i.e recitations, languages, list of tafsirs and translations
     * If the cache is enable it puts the content in the cache after requesting from API
     * OPTIONS - Recitations, Languages, Translations, Tafsirs
     * @param  string $option - Name of the option
     * @return array          - List of the option specified
     */
    private function options(string $option)
    {
        if ($this->{$option} === null) {

            if (isset($this->cache)) {
                $file = "{$this->cache}/{$option}.cache";

                if (!file_exists($file) && !fopen($file, 'w')) {
                    throw new \RuntimeException(
                        sprintf('Invalid cache file.')
                    );
                }

                if (filesize($file) !== 0) {
                    $this->{$option} = require $file;

                    if (isset($this->{$option}[$option])) {
                        return $this->{$option}[$option];
                    }
                }
            }

            $this->{$option} = $this->request->send("options/{$option}");

            if (isset($this->cache)) {
                file_put_contents(
                    $file,
                    '<?php return ' . var_export($this->{$option}, true) . ';'
                );
            }
        }

        return $this->{$option}[$option];
    }

    //--------------------------------------------------------------------------------------
    // API: /path?query - custom query
    //--------------------------------------------------------------------------------------

    /**
     * Crate custom query by specifing path and the http query
     * @param  string      $path  - Path of the Url
     * @param  string|null $query - Query of the Url
     * @return array              - Returns an array of result
     */
    public function get(string $path, string $query = null)
    {
        return $this->request->send($path, $query);
    }

    //--------------------------------------------------------------------------------------
    // API: /search?q=string&size=20&page=0
    //--------------------------------------------------------------------------------------

    /**
     * Search a keyword in the quran, and returns the array of result
     * Query options - query, size, page
     * @param  array  $options - Array of specified options with keys
     * @return array           - Returns an array of result
     */
    public function search(array $options = [])
    {
        $query = isset($options['query']) ? $options['query'] : null;
        $size  = isset($options['size']) ? $options['size'] : 20;
        $page  = isset($options['page']) ? $options['page'] : 0;

        return $this->request->send(
            "search",
            "q={$query}&size={$size}&page={$page}"
        );
    }

    /**
     * Returns information about cache
     * filesize, total filezie, file names etc...
     * @return string - Returns HTML string in a styled format.
     */
    public function cache(string $arg = null)
    {
        if ($arg === 'clear') {

            $files = glob($this->cache . '/*.cache');

            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }

            return true;
        }

        $totalSize = 0;
        $data      = "";
        $data .= <<<EOT
<pre>
<table>
    <tr>
        <td style='background:#ccc;width:250px;'>
            <b>File Name</b>
        </td>
        <td style='background:#ccc;width:250px;'>
            <b>File Size</b>
        </td>
        <td style='background:#ccc;width:250px;'>
            <b>Last Cached</b>
        </td>
        <td style='background:#ccc;width:250px;'>
            <b>Last Fetched</b>
        </td>
    </tr>
EOT;
        foreach (new \DirectoryIterator($this->cache) as $file) {
            if (!$file->isFile() || $file->isDot() || $file->getExtension() !== 'cache') {
                continue;
            }

            $totalSize += $file->getSize();
            $fileSize = round($file->getSize() / 1024, 2);
            $MTime    = date("F j, Y, g:i:s a", $file->getMTime());
            $ATime    = date("F j, Y, g:i:s a", $file->getATime());

            $data .= <<<EOT
<tr style='background:#efefef;'>
    <td style='padding:10px;width:250px;'>{$file->getFilename()}</td>
    <td style='padding:10px;width:250px;'>{$fileSize} KB</td>
    <td style='padding:10px;width:250px;'>{$MTime}</td>
    <td style='padding:10px;width:250px;'>{$ATime}</td>
</tr>
EOT;
        }

        $sizeInMb    = round($totalSize / pow(1024, 2), 2);
        $sizeInKb    = round($totalSize / 1024, 2);
        $memoryUsage = sprintf('%4.2f MB', memory_get_peak_usage(true) / 1048576);

        $data .= <<<EOT
</table>
</pre>
<pre style='font-size:16px;'><h3>Total cache size:</h3>
    {$sizeInMb} MB<br>
    {$sizeInKb} KB<br>
    {$totalSize} Bytes<br>
    <b>Memory:<b> {$memoryUsage}
</pre>
EOT;

        return $data;
    }
}
