<?php

namespace App\Domain;

use App\Infrastructure\Environment\Settings;
use App\Infrastructure\Serialization\Json;

class MediumRss
{
    // https://api.rss2json.com/v1/api.json?rss_url=https://medium.com/feed/@ingelbrechtrobin
    private const FEED_PATH = '/resources/medium-feed.json';

    public function getFeed(): array
    {
        $feed = Json::decode(\Safe\file_get_contents(Settings::getAppRoot().self::FEED_PATH));

        $items = [];
        foreach ($feed['items'] as $item) {
            $items[] = new RssItem($item);
        }

        return $items;
    }
}
