<?php

declare(strict_types=1);

namespace Pixel\TownHallBundle\Domain\Event;

use Pixel\TownHallBundle\Entity\Report;
use Sulu\Bundle\ActivityBundle\Domain\Event\DomainEvent;

class ReportModifiedEvent extends DomainEvent
{
    private Report $report;
    private array $payload;

    public function __construct(Report $report, array $payload)
    {
        parent::__construct();
        $this->report = $report;
        $this->payload = $payload;
    }

    public function getReport(): Report
    {
        return $this->report;
    }

    public function getEventPayload(): ?array
    {
        return $this->payload;
    }

    public function getEventType(): string
    {
        return 'modified';
    }

    public function getResourceKey(): string
    {
        return Report::RESOURCE_KEY;
    }

    public function getResourceId(): string
    {
        return (string)$this->report->getId();
    }

    public function getResourceTitle(): ?string
    {
        return $this->report->getTitle();
    }

    public function getResourceSecurityContext(): ?string
    {
        return Report::SECURITY_CONTEXT;
    }
}
