<?php

declare(strict_types=1);

namespace Pixel\TownHallBundle\Domain\Event;

use Pixel\TownHallBundle\Entity\JobOffer;
use Sulu\Bundle\ActivityBundle\Domain\Event\DomainEvent;

class JobOfferCreatedEvent extends DomainEvent
{
    private JobOffer $jobOffer;

    /**
     * @var array<mixed>
     */
    private array $payload;

    /**
     * @param array<mixed> $payload
     */
    public function __construct(JobOffer $jobOffer, array $payload)
    {
        parent::__construct();
        $this->jobOffer = $jobOffer;
        $this->payload = $payload;
    }

    public function getJobOffer(): JobOffer
    {
        return $this->jobOffer;
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
        return JobOffer::RESOURCE_KEY;
    }

    public function getResourceId(): string
    {
        return (string) $this->jobOffer->getId();
    }

    public function getResourceTitle(): ?string
    {
        return $this->jobOffer->getName();
    }

    public function getResourceSecurityContext(): ?string
    {
        return JobOffer::SECURITY_CONTEXT;
    }
}
