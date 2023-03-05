<?php

namespace App\Domain;

use App\Infrastructure\Serialization\Json;
use GuzzleHttp\Client;

class GitHub
{
    public function __construct(
        private readonly Client $client,
    ) {
    }

    private function request(
        string $path,
        string $method = 'GET',
        array $options = []): array
    {
        $options = array_merge([
            'base_uri' => 'https://api.github.com/',
        ], $options);
        $response = $this->client->request($method, $path, $options);

        return Json::decode($response->getBody()->getContents());
    }

    public function getRepos(): array
    {
        $response = $this->client->request(
            'GET',
            'https://raw.githubusercontent.com/robiningelbrecht/github-commit-history/master/build/repos-for-website.json'
        );

        return Json::decode($response->getBody()->getContents());
    }
}
