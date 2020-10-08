<?php

namespace App\Service;


use GuzzleHttp\Client;

class Context
{
    private $strategy;
    private $httpClient;

    /**
     * @return mixed
     */
    public function getStrategy(): Strategy
    {
        return $this->strategy;
    }

    /**
     * @param mixed $strategy
     */
    public function setStrategy(Strategy $strategy): void
    {
        $this->strategy = $strategy;
    }

    /**
     * @return Client
     */
    public function getHttpClient(): Client
    {
        return $this->httpClient;
    }

    /**
     * @param Client $httpClient
     */
    public function setHttpClient(Client $httpClient): void
    {
        $this->httpClient = $httpClient;
    }

}