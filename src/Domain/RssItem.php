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
        \SimpleXMLElement $item,
    )
    {

        $this->title = $item->title;
        $this->link = $item->link;
        $this->pubDate = \DateTimeImmutable::createFromFormat('D, d M Y H:i:s e', $item->pubDate);
        $this->creator = $item->children('dc', true)->creator;

        $content = (string)$item->children('http://purl.org/rss/1.0/modules/content/')->encoded;
        $this->image = $this->extractImageSource($content);
        $this->summary = $this->extractSummary($content);
    }

    public function getTitle(): string
    {
        return (string)$this->title;
    }

    public function getLink(): string
    {
        return (string)$this->link;
    }

    public function getPubDate(): \DateTimeImmutable
    {
        return $this->pubDate;
    }

    public function getCreator(): string
    {
        return (string)$this->creator;
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
        $regex = '#<p>(.*?)</p>#';
        preg_match($regex, $content, $matches);

        if (empty($matches[1])) {
            return null;
        }

        return strip_tags($matches[1]);
    }
}