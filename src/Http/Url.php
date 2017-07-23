<?php

namespace Quran\Http;

use Quran\Http\Interfaces\UrlInterface;

class Url implements UrlInterface
{
    private $scheme;

    private $host;

    private $port;

    private $path;

    private $query;

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

    public function getScheme()
    {
        return $this->scheme;
    }

    public function getHost()
    {
        return $this->host;
    }

    public function getPort()
    {
        return $this->port;
    }

    public function getPath()
    {
        return $this->basePath;
    }

    public function getQuery()
    {
        return $this->query;
    }

    public function getUrl()
    {
        return $this->scheme . '://' . $this->host . ':' . $this->port . '/' . trim($this->path, '/');
    }
}
