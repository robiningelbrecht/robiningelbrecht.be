<?php

namespace App\Domain;

use App\Infrastructure\Serialization\Json;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;

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

    public function getUserRepos($user): array
    {
        return $this->request(
            sprintf('users/%s/repos', $user),
            'GET',
            [
                RequestOptions::QUERY=> [
                    'per_page'=> 100,
                ]
            ],
        );
    }
}
