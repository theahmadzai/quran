<?php

namespace Quran\Http;

use Quran\Http\Interfaces\UrlInterface;

class Url implements UrlInterface
{
    /**
     * Url scheme
     * @var string
     */
    private $scheme;

    /**
     * Url host
     * @var string
     */
    private $host;

    /**
     * Url port
     * @var string
     */
    private $port;

    /**
     * Url path
     * @var string
     */
    private $path;

    /**
     * Url http query params
     * @var string
     */
    private $query;

    /**
     * Url class - takes a url string and parses it to small components like scheme,
     * hots etc.. if url is not provided then it makes a url by default constants declared
     * in URL interface.
     * @param string $url - An Url string
     */
    public function __construct(string $url)
    {
        if (!is_string($url)) {
            throw new \InvalidArgumentException(sprintf("Expected url to be string."));
        }

        $url = parse_url($url);

        $this->scheme = !empty($url['scheme']) ? $url['scheme'] : self::DEFAULT_SCHEME;
        $this->host   = !empty($url['host']) ? $url['host'] : self::DEFAULT_HOST;
        $this->port   = !empty($url['port']) ? $url['port'] : self::DEFAULT_PORT;
        $this->path   = !empty($url['path']) ? $url['path'] : self::DEFAULT_BASE_PATH;
        $this->query  = !empty($url['query']) ? $url['query'] : self::DEFAULT_QUERY;
    }

    /**
     * Getter for Url scheme
     * @return string - Returns scheme
     */
    public function getScheme()
    {
        return $this->scheme;
    }

    /**
     * Getter for Url host
     * @return string - Returns host
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Getter for Url port
     * @return string - Returns port
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * Getter for Url path
     * @return string - Returns path
     */
    public function getPath()
    {
        return $this->basePath;
    }

    /**
     * Getter for Url query params
     * @return string - Returns Http query params
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Getter for complete URL
     * @return string - Returns a string of complete url composed of URL components
     */
    public function getUrl()
    {
        return $this->scheme . '://' . $this->host . ':' . $this->port . '/' . trim($this->path, '/');
    }
}
