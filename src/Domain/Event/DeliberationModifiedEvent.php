<?php

declare(strict_types=1);

namespace Pixel\TownHallBundle\Domain\Event;

use Pixel\TownHallBundle\Entity\Deliberation;
use Sulu\Bundle\ActivityBundle\Domain\Event\DomainEvent;

class DeliberationModifiedEvent extends DomainEvent
{
    private Deliberation $deliberation;

    /**
     * @var array<mixed>
     */
    private array $payload;

    /**
     * @param array<mixed> $payload
     */
    public function __construct(Deliberation $deliberation, array $payload)
    {
        parent::__construct();
        $this->deliberation = $deliberation;
        $this->payload = $payload;
    }

    public function getDeliberation(): Deliberation
    {
        return $this->deliberation;
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
        return Deliberation::RESOURCE_KEY;
    }

    public function getResourceId(): string
    {
        return (string) $this->deliberation->getId();
    }

    public function getResourceTitle(): ?string
    {
        return $this->deliberation->getTitle();
    }

    public function getResourceSecurityContext(): ?string
    {
        return Deliberation::SECURITY_CONTEXT;
    }
}
