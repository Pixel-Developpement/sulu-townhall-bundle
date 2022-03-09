<?php

namespace Pixel\TownHallBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Sulu\Bundle\MediaBundle\Entity\MediaInterface;
use Sulu\Component\Persistence\Model\AuditableInterface;
use Sulu\Component\Persistence\Model\AuditableTrait;

/**
 * @ORM\Entity()
 * @ORM\Table(name="townall_report")
 * @Serializer\ExclusionPolicy("all")
 */
class Report implements AuditableInterface
{
    use AuditableTrait;

    public const RESOURCE_KEY = 'reports';
    public const FORM_KEY = 'report_details';
    public const LIST_KEY = 'reports';
    public const SECURITY_CONTEXT = 'townhall_reports.reports';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     *
     * @Serializer\Expose()
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Serializer\Expose()
     */
    private ?string $title = null;

    /**
     * @ORM\Column(type="datetime_immutable")
     *
     * @Serializer\Expose()
     */
    private ?\DateTimeImmutable $dateReport = null;

    /**
     * @ORM\ManyToOne(targetEntity=MediaInterface::class)
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private ?MediaInterface $document = null;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Serializer\Expose()
     */
    private ?string $description = null;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @Serializer\Expose()
     */
    private ?bool $isActive = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @Serializer\VirtualProperty(name="title")
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string|null $title
     */
    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string|null
     */
    public function getDateReport(): ?\DateTimeImmutable
    {
        return $this->dateReport;
    }

    /**
     * @param string|null $dateReport
     */
    public function setDateReport(?\DateTimeImmutable $dateReport): void
    {
        $this->dateReport = $dateReport;
    }

    /**
     * @return array<string, mixed>
     *
     * @Serializer\VirtualProperty()
     * @Serializer\SerializedName("document")
     */
    public function getDocumentData(): ?array
    {
        if ($document = $this->getDocument()) {
            return [
                'id' => $document->getId(),
            ];
        }

        return null;
    }

    public function getDocument(): ?MediaInterface
    {
        return $this->document;
    }

    public function setDocument(?MediaInterface $document): void
    {
        $this->document = $document;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return bool|null
     */
    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    /**
     * @param bool|null $isActive
     */
    public function setIsActive(?bool $isActive): void
    {
        $this->isActive = $isActive;
    }
}