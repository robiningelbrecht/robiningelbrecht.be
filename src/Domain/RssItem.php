<?php

namespace App\Domain;

class RssItem
{
    private string $title;
    private string $link;
    private \DateTimeImmutable $pubDate;
    private string $creator;
    private ?string $image;
    private string $summary;

    public function __construct(
        array $item,
    ) {
        $this->title = $item['title'];
        $this->link =  $item['link'];
        $this->pubDate = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s',  $item['pubDate']);
        $this->creator = $item['author'];

        $content = $item['content'];
        $this->image = $this->extractImageSource($content);
        $this->summary = $this->extractSummary($content);
    }

    public function getTitle(): string
    {
        return (string) $this->title;
    }

    public function getLink(): string
    {
        return (string) $this->link;
    }

    public function getPubDate(): \DateTimeImmutable
    {
        return $this->pubDate;
    }

    public function getCreator(): string
    {
        return (string) $this->creator;
    }

    public function getImage(): string
    {
        return $this->image;
    }

    public function getSummary(): string
    {
        return $this->summary;
    }

    private function extractImageSource(string $content): ?string
    {
        $regex = '#src="(.*?)"#';
        preg_match($regex, $content, $matches);

        if (empty($matches[1])) {
            return null;
        }

        return $matches[1];
    }

    private function extractSummary($content): ?string
    {
        preg_match_all('/<p>(.*?)<\/p>/', $content, $matches);

        if (empty($matches[1][0])) {
            return null;
        }
        $content = $matches[1][0];
        if (strlen(strip_tags($content)) < 20 && isset($matches[1][1])) {
            $content = $matches[1][1];
        }

        return strip_tags($content);
    }
}
