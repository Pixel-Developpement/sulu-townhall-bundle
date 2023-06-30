<?php

namespace Pixel\TownHallBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Sulu\Component\Persistence\Model\AuditableInterface;
use Sulu\Component\Persistence\Model\AuditableTrait;

/**
 * @ORM\Entity()
 * @ORM\Table(name="townhall_flash_info_translation")
 * @ORM\Entity(repositoryClass="Pixel\TownHallBundle\Repository\FlashInfoRepository")
 * @Serializer\ExclusionPolicy("all")
 */
class FlashInfoTranslation implements AuditableInterface
{
    use AuditableTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Serializer\Expose()
     */
    private ?int $id = null;

    /**
     * @ORM\ManyToOne(targetEntity="Pixel\TownHallBundle\Entity\FlashInfo", inversedBy="translations")
     * @ORM\JoinColumn(nullable=true)
     */
    private FlashInfo $flashInfo;

    /**
     * @ORM\Column(type="string", length=5)
     * @Serializer\Expose()
     */
    private string $locale;

    /**
     * @ORM\Column(type="string", length=255)
     * @Serializer\Expose()
     */
    private string $title;

    /**
     * @ORM\Column(type="text")
     * @Serializer\Expose()
     */
    private string $description;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @Serializer\Expose()
     */
    private ?bool $isActive = null;

    /**
     * @ORM\Column(type="date_immutable", nullable=true)
     * @Serializer\Expose()
     */
    private ?\DateTimeImmutable $publishedAt;

    public function __construct(FlashInfo $flashInfo, string $locale)
    {
        $this->flashInfo = $flashInfo;
        $this->locale = $locale;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFlashInfo(): FlashInfo
    {
        return $this->flashInfo;
    }

    public function setFlashInfo(FlashInfo $flashInfo): void
    {
        $this->flashInfo = $flashInfo;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function setLocale(string $locale): void
    {
        $this->locale = $locale;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(?bool $isActive): void
    {
        $this->isActive = $isActive;
        if ($isActive === true) {
            $this->setPublishedAt(new \DateTimeImmutable());
        } else {
            $this->setPublishedAt(null);
        }
    }

    public function getPublishedAt(): ?\DateTimeImmutable
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(?\DateTimeImmutable $publishedAt): void
    {
        $this->publishedAt = $publishedAt;
    }
}
