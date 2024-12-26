<?php

namespace App\Infrastructure\Eventing;

abstract class AggregateRoot
{
    /** @var DomainEvent[] */
    private array $recordedEvents = [];

    protected function recordThat(DomainEvent $domainEvent): void
    {
        $this->recordedEvents[] = $domainEvent;
    }

    /**
     * @return DomainEvent[]
     */
    public function getRecordedEvents(): array
    {
        $recordedEvents = $this->recordedEvents;
        $this->recordedEvents = [];

        return $recordedEvents;
    }
}
