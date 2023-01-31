<?php

declare(strict_types=1);

namespace Pixel\TownHallBundle\Domain\Event;

use Pixel\TownHallBundle\Entity\Decree;
use Sulu\Bundle\ActivityBundle\Domain\Event\DomainEvent;

class DecreeCreatedEvent extends DomainEvent
{
    private Decree $decree;

    /**
     * @var array<mixed>
     */
    private array $payload;

    /**
     * @param array<mixed> $payload
     */
    public function __construct(Decree $decree, array $payload)
    {
        parent::__construct();
        $this->decree = $decree;
        $this->payload = $payload;
    }

    public function getDecree(): Decree
    {
        return $this->decree;
    }

    public function getEventPayload(): ?array
    {
        return $this->payload;
    }

    public function getEventType(): string
    {
        return 'created';
    }

    public function getResourceKey(): string
    {
        return Decree::RESOURCE_KEY;
    }

    public function getResourceId(): string
    {
        return (string) $this->decree->getId();
    }

    public function getResourceTitle(): ?string
    {
        return $this->decree->getTitle();
    }

    public function getResourceSecurityContext(): ?string
    {
        return Decree::SECURITY_CONTEXT;
    }
}
