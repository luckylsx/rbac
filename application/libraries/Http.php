<?php

namespace App\Libraries;
require_once(dirname(dirname(dirname(__FILE__))).'/vendor/autoload.php');
use \GuzzleHttp\Client as HttpClient;
use Psr\Http\Message\ResponseInterface;

/**
 * Class Http.
 */
class Http
{
    protected static $accessToken;

    /**
     * Http client.
     *
     * @var HttpClient
     */
    protected $client;

    /**
     * @var array
     */
    protected static $globals = [
        'curl' => [
            CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
        ],
    ];

    /**
     * Guzzle client default settings.
     *
     * @var array
     */
    protected static $defaults = [];

    public function __construct($accessToken = '')
    {
        self::$accessToken = $accessToken;
    }

    /**
     * Set guzzle default settings.
     *
     * @param array $defaults
     */
    public static function setDefaultOptions($defaults = [])
    {
        self::$defaults = array_merge(self::$globals, $defaults);
    }

    /**
     * Return current guzzle default settings.
     *
     * @return array
     */
    public static function getDefaultOptions()
    {
        return self::$defaults;
    }

    public function setAccessToken($accessToken = '')
    {
        self::$accessToken = $accessToken;

        return $this;
    }

    /**
     * GET request.
     *
     * @param string $url
     * @param array $options
     *
     * @return ResponseInterface
     *
     * @throws HttpException
     */
    public function get($url, array $options = [])
    {
        return $this->request($url, 'GET', ['query' => $options]);
    }

    /**
     * POST request.
     *
     * @param string $url
     * @param array|string $options
     *
     * @return ResponseInterface
     *
     * @throws HttpException
     */
    public function post($url, $options = [])
    {
        $key = is_array($options) ? 'form_params' : 'body';

        return $this->request($url, 'POST', [$key => $options]);
    }

    /**
     * JSON request.
     *
     * @param string $url
     * @param string|array $options
     * @param array $queries
     * @param int $encodeOption
     *
     * @return ResponseInterface
     *
     * @throws HttpException
     */
    public function json($url, $options = '{}', $queries = [], $encodeOption = JSON_UNESCAPED_UNICODE)
    {
        is_array($options) && $options = json_encode($options, $encodeOption);

        return $this->request($url, 'POST', ['query' => $queries, 'body' => $options, 'headers' => ['content-type' => 'application/json']]);
    }

    /**
     * Upload file.
     *
     * @param string $url
     * @param array $files
     * @param array $form
     *
     * @return ResponseInterface
     *
     * @throws HttpException
     */
    public function upload($url, array $files = [], array $form = [], array $queries = [])
    {
        $multipart = [];

        foreach ($files as $name => $path) {
            $multipart[] = [
                'name' => $name,
                'contents' => fopen($path, 'r'),
            ];
        }

        foreach ($form as $name => $contents) {
            $multipart[] = compact('name', 'contents');
        }

        return $this->request($url, 'POST', ['query' => $queries, 'multipart' => $multipart]);
    }

    /**
     * Set GuzzleHttp\Client.
     *
     * @param \GuzzleHttp\Client $client
     *
     * @return Http
     */
    public function setClient(HttpClient $client)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Return GuzzleHttp\Client instance.
     *
     * @return \GuzzleHttp\Client
     */
    public function getClient()
    {
        if (!($this->client instanceof HttpClient)) {
            $this->client = new HttpClient();
        }

        return $this->client;
    }

    /**
     * Make a request.
     *
     * @param string $url
     * @param string $method
     * @param array $options
     *
     * @return ResponseInterface
     *
     * @throws HttpException
     */
    public function request($url, $method = 'GET', $options = [])
    {
        $method = strtoupper($method);

        $options = array_merge(self::$defaults, $options);
        if (self::$accessToken) {
            $options['query']['access_token'] = self::$accessToken;
        }

        $response = $this->getClient()->request($method, $url, $options);

        return $response;
    }

    /**
     * Parse JSON from response and check error.
     *
     * @param string $method
     * @param array $args
     *
     * @return array
     */
    public function parseJSON($method, array $args)
    {
        $body = call_user_func_array([$this, $method], $args);
        if ($body instanceof ResponseInterface) {
            $body = $body->getBody();
        }

        if (empty($body)) {
            return false;
        }

        $contents = $body->getContents();
        $res = json_decode($contents, true);

        if (JSON_ERROR_NONE !== json_last_error()) {
            return false;
        }

        return $res;
    }

}
