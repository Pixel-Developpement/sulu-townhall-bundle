<?php

declare(strict_types=1);

namespace Pixel\TownHallBundle\Domain\Event;

use Pixel\TownHallBundle\Entity\Procedure;
use Sulu\Bundle\ActivityBundle\Domain\Event\DomainEvent;

class ProcedureCreatedEvent extends DomainEvent
{
    private Procedure $procedure;
    private array $payload;

    public function __construct(Procedure $procedure, array $payload)
    {
        parent::__construct();
        $this->procedure = $procedure;
        $this->payload = $payload;
    }

    public function getProcedure(): Procedure
    {
        return $this->procedure;
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
        return Procedure::RESOURCE_KEY;
    }

    public function getResourceId(): string
    {
        return (string)$this->procedure->getId();
    }

    public function getResourceTitle(): ?string
    {
        return $this->procedure->getTitle();
    }

    public function getResourceSecurityContext(): ?string
    {
        return Procedure::SECURITY_CONTEXT;
    }
}
