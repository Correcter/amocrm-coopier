<?php

namespace AmoCrm\Request;

use AmoCrm\Exceptions\HasNoResponse;
use AmoCrm\Exceptions\InvalidRequest;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\Psr7\Response;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

/**
 * @author Vitaly Dergunov
 */
abstract class AbstractRequest
{
    /**
     * @var ParameterBag
     */
    protected $parameterBag;

    /**
     * @var Client
     */
    protected $guzzleClient;

    /**
     * @var string
     */
    protected $baseUrl;
    /**
     * @var string
     */
    protected $httpMethod;

    /**
     * @var string
     */
    protected $requstUri;

    /**
     * @var array
     */
    protected $queryParams;

    /**
     * @var array
     */
    protected $formParams;

    /**
     * @var string
     */
    protected $body;

    /**
     * @var array
     */
    protected $headers;

    /**
     * @var int
     */
    protected $readTimeout;

    /**
     * @var int
     */
    protected $connectTimeout;

    /**
     * @var CookieJar
     */
    protected $cookie;

    /**
     * @var bool
     */
    protected $verify;

    /**
     * @var bool
     */
    protected $stream;

    /**
     * AbstractRequest constructor.
     *
     * @param ParameterBag $parameterBag
     */
    public function __construct(ParameterBag $parameterBag)
    {
        $this->headers = [];
        $this->queryParams = [];
        $this->cookie = new CookieJar();
        $this->verify = false;
        $this->stream = false;
        $this->readTimeout = 0;
        $this->connectTimeout = 0;
        $this->parameterBag = $parameterBag;
    }

    /**
     * @param null|string $hostType
     *
     * @return AbstractRequest
     */
    public function createClient(string $hostType = null): self
    {
        if (!$hostType) {
            throw new \RuntimeException('Базовый URL не передан');
        }

        $this->guzzleClient = new Client([
            'base_uri' => $this->parameterBag->get($hostType),
        ]);

        return $this;
    }

    /**
     * @return null|string
     */
    public function getBaseUrl(): ?string
    {
        return $this->baseUrl;
    }

    /**
     * @param null|string $baseUrl
     *
     * @return AbstractRequest
     */
    public function setBaseUrl(string $baseUrl = null): self
    {
        $this->baseUrl = $baseUrl;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getHttpMethod(): ?string
    {
        return $this->httpMethod;
    }

    /**
     * @param null|string $httpMethod
     *
     * @return AbstractRequest
     */
    public function setHttpMethod(string $httpMethod = null): self
    {
        $this->httpMethod = $httpMethod;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getRequstUri(): ?string
    {
        return $this->requstUri;
    }

    /**
     * @param null|string $requstUri
     *
     * @return AbstractRequest
     */
    public function setRequstUri(string $requstUri = null): self
    {
        $this->requstUri = $requstUri;

        return $this;
    }

    /**
     * @return array
     */
    public function getQueryParams(): ?array
    {
        return $this->queryParams;
    }

    /**
     * @param array $queryParams
     *
     * @return AbstractRequest
     */
    public function setQueryParams(array $queryParams = []): self
    {
        $this->queryParams = $queryParams;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getBody(): ?string
    {
        return $this->body;
    }

    /**
     * @param null|string $body
     *
     * @return AbstractRequest
     */
    public function setBody(string $body = null): self
    {
        $this->body = $body;

        return $this;
    }

    /**
     * @return null|array
     */
    public function getFormParams(): ?array
    {
        return $this->formParams;
    }

    /**
     * @param array $formParams
     *
     * @return AbstractRequest
     */
    public function setFormParams(array $formParams = []): self
    {
        $this->formParams = $formParams;

        return $this;
    }

    /**
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @param null|string $key
     * @param null        $value
     *
     * @return AbstractRequest
     */
    public function addHeader(string $key = null, $value = null): self
    {
        if (!isset($this->headers[$key])) {
            $this->headers[$key] = $value;
        }

        return $this;
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function removeHeader(string $key): bool
    {
        return $this->headers[$key];
    }

    /**
     * @return bool
     */
    public function isStream(): bool
    {
        return $this->stream;
    }

    /**
     * @param bool $stream
     *
     * @return AbstractRequest
     */
    public function setStream(bool $stream = null): self
    {
        $this->stream = $stream;

        return $this;
    }

    /**
     * @return null|int
     */
    public function getReadTimeout(): ?int
    {
        return $this->readTimeout;
    }

    /**
     * @param null|int $readTimeout
     *
     * @return AbstractRequest
     */
    public function setReadTimeout(int $readTimeout = null): self
    {
        $this->readTimeout = $readTimeout;

        return $this;
    }

    /**
     * @return null|int
     */
    public function getConnectTimeout(): ?int
    {
        return $this->connectTimeout;
    }

    /**
     * @param null|int $connectTimeout
     *
     * @return AbstractRequest
     */
    public function setConnectTimeout(int $connectTimeout = null): self
    {
        $this->connectTimeout = $connectTimeout;

        return $this;
    }

    /**
     * @return CookieJar
     */
    public function getCookie(): CookieJar
    {
        return $this->cookie;
    }

    /**
     * @param CookieJar $cookie
     *
     * @return AbstractRequest
     */
    public function setCookie(CookieJar $cookie): self
    {
        $this->cookie = $cookie;

        return $this;
    }

    /**
     * @return bool
     */
    public function isVerify(): bool
    {
        return $this->verify;
    }

    /**
     * @param bool $verify
     *
     * @return AbstractRequest
     */
    public function setVerify(bool $verify = false): self
    {
        $this->verify = $verify;

        return $this;
    }

    /**
     * @param bool $async
     *
     * @throws InvalidRequest
     *
     * @return Response
     */
    public function request($async = false)
    {
        try {
            $method = ($async) ? 'requestAsync' : 'request';

            if (!$this->guzzleClient) {
                throw new \RuntimeException('Guzzle client is empty');
            }

            return
                $this->guzzleClient->{$method}(
                    $this->getHttpMethod(),
                    $this->getRequstUri(),
                    [
                        'headers' => $this->getHeaders(),
                        'query' => $this->getQueryParams(),
                        'body' => $this->getBody(),
                        'form_params' => $this->getFormParams(),
                        'stream' => $this->isStream(),
                        'read_timeout' => $this->getReadTimeout(),
                        'connect_timeout' => $this->getConnectTimeout(),
                        'cookies' => $this->getCookie(),
                        'verify' => $this->isVerify(),
                    ]
                );
        } catch (TransferException $exc) {
            if (!$exc->hasResponse()) {
                throw new HasNoResponse(json_encode([
                    'error' => 'The service is not responding',
                ]), 400);
            }

            if (404 === $exc->getCode()) {
                throw new InvalidRequest(json_encode([
                    'error' => $exc->getMessage(),
                ]), 404);
            }

            throw new InvalidRequest($exc->getResponse()->getBody(true)->getContents(), 400);
        }
    }
}
