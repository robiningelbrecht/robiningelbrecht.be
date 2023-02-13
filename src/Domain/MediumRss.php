<?php

namespace App\Domain;

use App\Infrastructure\Serialization\Json;
use GuzzleHttp\Client;

class MediumRss
{
    public function __construct(
        private readonly Client $client,
    )
    {
    }

    private function request(
        string $path,
        string $method = 'GET',
        array $options = []): string
    {
        $options = array_merge([
            'base_uri' => 'https://medium.com/',
        ], $options);
        $response = $this->client->request($method, $path, $options);

        return $response->getBody()->getContents();
    }

    public function getFeed(): array
    {
        $content = $this->request('feed/@ingelbrechtrobin');
        $feed = new \SimpleXMLElement($content);

        $items = [];
        foreach ($feed->channel->item as $item) {
            $items[] = new RssItem($item);
        }

        return $items;
    }
}