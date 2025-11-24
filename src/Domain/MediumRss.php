<?php

namespace App\Domain;

use App\Infrastructure\Serialization\Json;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;

class MediumRss
{
    public function __construct(
        private readonly Client $client,
    ) {
    }

    private function request(
        string $path,
        string $method = 'GET',
        array $options = []): string
    {
        $options = array_merge([
            RequestOptions::HEADERS => [
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
                'User-Agent'=> 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36',
            ],
            'base_uri' => 'https://api.rss2json.com/',
        ], $options);
        $response = $this->client->request($method, $path, $options);

        return $response->getBody()->getContents();
    }

    public function getFeed(): array
    {
        $feed = Json::decode($this->request('v1/api.json?rss_url=https://medium.com/feed/@ingelbrechtrobin'));


        $items = [];
        foreach ($feed['items'] as $item) {
            $items[] = new RssItem($item);
        }

        return $items;
    }
}
