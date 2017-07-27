<?php

namespace Quran\Http;

use Quran\Http\Interfaces\RequestInterface;

class Request implements RequestInterface
{
    private $haders = [];

    private $method;

    private $url;

    private $port;

    private $query;

    public function __construct(Url $url)
    {
        $this->headers = [
            "content-type: application/json",
        ];
        $this->method = 'GET';
        $this->url    = $url->getUrl();
        $this->port   = $url->getPort();
        $this->query  = $url->getQuery();
    }

    public function send(string $path, string $query = null)
    {
        $url = "{$this->url}/{$path}?{$query}";

        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new \InvalidArgumentException(sprintf("Expected a valid Url."));
        }

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_PORT           => $this->port,
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => "",
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => $this->method,
            CURLOPT_POSTFIELDS     => "{}",
            CURLOPT_HTTPHEADER     => $this->headers,
        ]);

        $response = curl_exec($curl);
        $error    = curl_error($curl);

        curl_close($curl);

        if ($error) {
            throw new \Exception("cURL error: {$error}");
        }

        return json_decode($response, JSON_OBJECT_AS_ARRAY);
    }
}
