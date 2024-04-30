<?php

namespace Pixel\TownHallBundle\Content;

use JMS\Serializer\Annotation as Serializer;
use Pixel\TownHallBundle\Entity\JobOffer;
use Sulu\Component\SmartContent\ItemInterface;

/**
 * @Serializer\ExclusionPolicy("all")
 */
class JobOfferDataItem implements ItemInterface
{
    private JobOffer $entity;

    public function __construct(JobOffer $entity)
    {
        $this->entity = $entity;
    }

    /**
     * @Serializer\VirtualProperty
     */
    public function getId(): string
    {
        return (string) $this->entity->getId();
    }

    /**
     * @Serializer\VirtualProperty
     */
    public function getTitle(): string
    {
        return (string) $this->entity->getName();
    }

    /**
     * @Serializer\VirtualProperty
     */
    public function getImage(): ?string
    {
        return null;
    }

    public function getResource(): JobOffer
    {
        return $this->entity;
    }
}
