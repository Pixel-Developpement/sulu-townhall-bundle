<?php

namespace Pixel\TownHallBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Sulu\Bundle\MediaBundle\Entity\MediaInterface;
use Sulu\Component\Persistence\Model\AuditableInterface;
use Sulu\Component\Persistence\Model\AuditableTrait;

/**
 * @ORM\Entity()
 * @ORM\Table(name="townhall_job_offer")
 * @ORM\Entity(repositoryClass="Pixel\TownHallBundle\Repository\JobOfferRepository")
 * @Serializer\ExclusionPolicy("all")
 */
class JobOffer implements AuditableInterface
{
    use AuditableTrait;

    public const RESOURCE_KEY = "jobs_offers";

    public const FORM_KEY = "job_offer_details";

    public const LIST_KEY = "jobs_offers";

    public const SECURITY_CONTEXT = "townhall.job_offers";

    public const CONTRACT_CDI = 1;

    public const CONTRACT_CDD = 2;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Serializer\Expose()
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=255)
     * @Serializer\Expose()
     */
    private string $name;

    /**
     * @ORM\Column(type="text")
     * @Serializer\Expose()
     */
    private string $description;

    /**
     * @ORM\Column(type="integer")
     * @Serializer\Expose()
     */
    private int $contractType;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Serializer\Expose()
     */
    private ?string $duration;

    /**
     * @ORM\ManyToOne(targetEntity=MediaInterface::class)
     * @ORM\JoinColumn(onDelete="SET NULL")
     * @Serializer\Expose()
     */
    private ?MediaInterface $pdf;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @Serializer\Expose()
     */
    private ?bool $isActive;

    /**
     * @ORM\Column(type="date_immutable")
     * @Serializer\Expose()
     */
    private \DateTimeImmutable $publishedAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getContractType(): int
    {
        return $this->contractType;
    }

    public function setContractType(int $contractType): void
    {
        $this->contractType = $contractType;
    }

    public function getDuration(): ?string
    {
        return $this->duration;
    }

    public function setDuration(?string $duration): void
    {
        $this->duration = $duration;
    }

    public function getPdf(): ?MediaInterface
    {
        return $this->pdf;
    }

    public function setPdf(?MediaInterface $pdf): void
    {
        $this->pdf = $pdf;
    }

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(?bool $isActive): void
    {
        $this->isActive = $isActive;
    }

    public function getPublishedAt(): \DateTimeImmutable
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(\DateTimeImmutable $publishedAt): void
    {
        $this->publishedAt = $publishedAt;
    }
}
