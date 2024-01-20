<?php

namespace Pixel\TownHallBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Sulu\Bundle\MediaBundle\Entity\MediaInterface;
use Sulu\Component\Persistence\Model\AuditableInterface;
use Sulu\Component\Persistence\Model\AuditableTrait;

/**
 * @ORM\Entity()
 * @ORM\Table("townhall_deliberation")
 * @Serializer\ExclusionPolicy("all")
 */
class Deliberation implements AuditableInterface
{
    use AuditableTrait;

    public const RESOURCE_KEY = "deliberations";

    public const LIST_KEY = "deliberations";

    public const FORM_KEY = "deliberation_details";

    public const SECURITY_CONTEXT = "townhall_deliberations.deliberations";

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Serializer\Expose()
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string")
     * @Serializer\Expose()
     */
    private string $title;

    /**
     * @ORM\Column(type="datetime_immutable")
     * @Serializer\Expose()
     */
    private \DateTimeImmutable $date;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Serializer\Expose()
     */
    private ?string $description = null;

    /**
     * @ORM\ManyToOne(targetEntity=MediaInterface::class)
     * @ORM\JoinColumn(onDelete="SET NULL")
     * @Serializer\Expose()
     */
    private MediaInterface $pdf;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @Serializer\Expose()
     */
    private ?bool $isActive = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getDate(): \DateTimeImmutable
    {
        return $this->date;
    }

    public function setDate(\DateTimeImmutable $date): void
    {
        $this->date = $date;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getPdf(): MediaInterface
    {
        return $this->pdf;
    }

    public function setPdf(MediaInterface $pdf): void
    {
        $this->pdf = $pdf;
    }

    public function isActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(?bool $isActive): void
    {
        $this->isActive = $isActive;
    }
}
