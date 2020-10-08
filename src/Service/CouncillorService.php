<?php

namespace App\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use App\HttpModel\CouncillorCollection;

class CouncillorService implements Strategy
{
    const API_BASE_URL = 'http://ws-old.parlament.ch/councillors';
    const DEFAULT_OUTPUT_FORMAT = 'json';
    const DEFAULT_SORT = 'lastname';

    private $httpClient;
    private $sortBy = 'lastname';
    private $page = 1;

    public function __construct(Client $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function getList(): array
    {
        $councillors = $this->getCouncillors();

        return $this->sort($councillors);
    }


    private function getCouncillors(): array
    {
        try {
            $response = $this->httpClient->request(
                'GET',
                self::API_BASE_URL,
                [
                    'query' => ['entryDateFilter' => '2018/12/31', 'format' => self::DEFAULT_OUTPUT_FORMAT, 'pageNumber' => $this->getPage()],
                    'headers' => [
                        'Accept' => 'application/json',
                    ],
                ]
            );


            $jsonData = json_decode($response->getBody(), true);
            $collection = [];
            foreach ($jsonData as $arr) {
                $c = new CouncillorCollection();
                $c->setId($arr['id']);
                $c->setUpdated($arr['updated']);
                $c->setActive($arr['active']);
                $c->setCode($arr['code']);
                $c->setFirstName($arr['first_name']);
                $c->setLastName($arr['last_name']);
                $c->setNumber($arr['number']);

                $collection[] = $c->toArray();
            }

            return $collection;

        } catch (GuzzleException $e) {
        }


        // should be an error
        return [];
    }

    public function sort(array $councillors): array
    {
        usort($councillors, function ($a, $b) {
            return strcmp(
                call_user_func_array([$a, strtolower('get' . $this->getSortBy())], []),
                call_user_func_array([$b, strtolower('get' . $this->getSortBy())], [])
            );
        });

        return $councillors;
    }

    public function setSortBy(string $sortBy = self::DEFAULT_SORT): void
    {
        $this->sortBy = $sortBy;
    }

    public function getSortBy(): string
    {
        return $this->sortBy;
    }

    public function setPage(int $page): void
    {
        $this->page = $page;
    }

    public function getPage(): int
    {
        return $this->page;
    }
}