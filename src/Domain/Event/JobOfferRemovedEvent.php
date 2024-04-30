<?php

declare(strict_types=1);

namespace Pixel\TownHallBundle\Domain\Event;

use Pixel\TownHallBundle\Entity\JobOffer;
use Sulu\Bundle\ActivityBundle\Domain\Event\DomainEvent;

class JobOfferRemovedEvent extends DomainEvent
{
    private int $id;

    private string $name;

    public function __construct(int $id, string $name)
    {
        parent::__construct();
        $this->id = $id;
        $this->name = $name;
    }

    public function getEventType(): string
    {
        return 'removed';
    }

    public function getResourceKey(): string
    {
        return JobOffer::RESOURCE_KEY;
    }

    public function getResourceId(): string
    {
        return (string) $this->id;
    }

    public function getResourceTitle(): ?string
    {
        return $this->name;
    }

    public function getResourceSecurityContext(): ?string
    {
        return JobOffer::SECURITY_CONTEXT;
    }
}
