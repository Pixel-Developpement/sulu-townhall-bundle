<?php

declare(strict_types=1);

namespace Pixel\TownHallBundle\Domain\Event;

use Pixel\TownHallBundle\Entity\FlashInfo;
use Sulu\Bundle\ActivityBundle\Domain\Event\DomainEvent;

class FlashInfoRestoredEvent extends DomainEvent
{
    private FlashInfo $flashInfo;
    private array $payload;

    public function __construct(FlashInfo $flashInfo, array $payload)
    {
        parent::__construct();
        $this->flashInfo = $flashInfo;
        $this->payload = $payload;
    }

    public function getFlashInfo(): FlashInfo
    {
        return $this->flashInfo;
    }

    public function getEventPayload(): ?array
    {
        return $this->payload;
    }

    public function getEventType(): string
    {
        return 'restored';
    }

    public function getResourceKey(): string
    {
        return FlashInfo::RESOURCE_KEY;
    }

    public function getResourceId(): string
    {
        return (string)$this->flashInfo->getId();
    }

    public function getResourceTitle(): ?string
    {
        return $this->flashInfo->getTitle();
    }

    public function getResourceSecurityContext(): ?string
    {
        return FlashInfo::SECURITY_CONTEXT;
    }
}
