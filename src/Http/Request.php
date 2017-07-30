<?php

namespace Quran\Http;

use Quran\Http\Interfaces\RequestInterface;

class Request implements RequestInterface
{
    /**
     * HTTP Headers
     * @var array
     */
    private $haders = [];

    /**
     * HTTP Method
     * @var string
     */
    private $method;

    /**
     * HTTP Url
     * @var string
     */
    private $url;

    /**
     * HTTP Port
     * @var string
     */
    private $port;

    /**
     * HTTP Query params
     * @var string
     */
    private $query;

    /**
     * Request class - It takes a URL of the target API then sends requests to it.
     * @param Url $url - An instance of URL class
     */
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

    /**
     * Finilizes the request, sends the request after URL, query params and everything is
     * provided and completed. It returns the response to the method that calls it.
     * @param  string      $path  - Target Url path
     * @param  string|null $query - Target Url query params
     * @return array              - Returns an array of results
     */
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
