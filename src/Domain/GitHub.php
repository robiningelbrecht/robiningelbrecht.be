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
        $repos = array_filter(
            $this->request('users/robiningelbrecht/repos'),
            fn (array $repo) => in_array('website', $repo['topics'])
        );

        $repos = array_map(function (array $repo) {
            $repo['topics'] = array_filter($repo['topics'], fn (string $topic) => 'website' !== $topic);

            return $repo;
        }, $repos);

        usort($repos, function (array $a, array $b) {
            return (new \DateTimeImmutable($a['created_at']))->getTimestamp() < (new \DateTimeImmutable($b['created_at']))->getTimestamp() ? 1 : -1;
        });

        return $repos;
    }
}
