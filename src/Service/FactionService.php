<?php


namespace App\Service;


use App\HttpModel\FactionCollection;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class FactionService implements Strategy
{
    const API_BASE_URL = 'http://ws-old.parlament.ch/factions';
    const DEFAULT_OUTPUT_FORMAT = 'json';
    const DEFAULT_SORT = 'name';

    private $httpClient;
    private $sortBy = 'name';
    private $page = 1;

    public function __construct(Client $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function getList(): array
    {
        $factions = $this->getFactions();

        return $this->sort($factions);
    }


    private function getFactions(): array
    {
        try {
            $response = $this->httpClient->request(
                'GET',
                self::API_BASE_URL,
                [
                    'query' => ['format' => self::DEFAULT_OUTPUT_FORMAT, 'pageNumber' => $this->getPage()],
                    'headers' => [
                        'Accept' => 'application/json',
                    ],
                ]
            );


            $jsonData = json_decode($response->getBody(), true);
            $collection = [];
            foreach ($jsonData as $arr) {
                $f = new FactionCollection();
                $f->setId($arr['id']);
                $f->setUpdated($arr['updated']);
                $f->setAbbreviation($arr['abbreviation']);
                $f->setCode($arr['code']);
                $f->setName($arr['name']);
                $f->setShortName($arr['short_name']);

                $collection[] = $f->toArray();
            }

            return $collection;

        } catch (GuzzleException $e) {
        }


        // should be an error
        return [];
    }

    public function sort(array $factions): array
    {
        usort($factions, function ($a, $b) {
            return strcmp(
                call_user_func_array([$a, strtolower('get' . $this->getSortBy())], []),
                call_user_func_array([$b, strtolower('get' . $this->getSortBy())], [])
            );
        });

        return $factions;
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