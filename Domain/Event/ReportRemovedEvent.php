<?php

declare(strict_types=1);

namespace Pixel\TownHallBundle\Domain\Event;

use Pixel\TownHallBundle\Entity\Report;
use Sulu\Bundle\ActivityBundle\Domain\Event\DomainEvent;

class ReportRemovedEvent extends DomainEvent
{
    private int $id;
    private \DateTimeImmutable $date;

    public function __construct(int $id, \DateTimeImmutable $date)
    {
        parent::__construct();
        $this->id = $id;
        $this->date = $date;
    }

    public function getEventType(): string
    {
        return 'removed';
    }

    public function getResourceKey(): string
    {
        return Report::RESOURCE_KEY;
    }

    public function getResourceId(): string
    {
        return (string)$this->id;
    }

    public function getResourceDate(): \DateTimeImmutable
    {
        return $this->date;
    }

    public function getResourceSecurityContext(): ?string
    {
        return Report::SECURITY_CONTEXT;
    }
}
