<?php

namespace App\Service;


interface Strategy
{
    public function getList(): array;
    public function setSortBy(string $sortBy): void;
    public function getSortBy(): string;
    public function setPage(int $page): void;
    public function getPage(): int;
    public function sort(array $data): array;
}