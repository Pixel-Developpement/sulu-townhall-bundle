<?php

namespace Pixel\TownHallBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Sulu\Component\Persistence\Model\AuditableInterface;
use Sulu\Component\Persistence\Model\AuditableTrait;

/**
 * @ORM\Entity()
 * @ORM\Table(name="townhall_procedure_translation")
 * @ORM\Entity(repositoryClass="Pixel\TownHallBundle\Repository\ProcedureRepository")
 * @Serializer\ExclusionPolicy("all")
 */
class ProcedureTranslation implements AuditableInterface
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
     * @ORM\ManyToOne(targetEntity="Pixel\TownHallBundle\Entity\Procedure", inversedBy="translations")
     * @ORM\JoinColumn(nullable=true)
     */
    private Procedure $procedure;

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
     * @ORM\Column(type="string", length=255)
     * @Serializer\Expose()
     */
    private string $routePath;

    /**
     * @ORM\Column(type="json", nullable=true)
     * @Serializer\Expose()
     */
    private ?array $seo = null;

    public function __construct(Procedure $procedure, string $locale)
    {
        $this->procedure = $procedure;
        $this->locale = $locale;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Procedure
     */
    public function getProcedure(): Procedure
    {
        return $this->procedure;
    }

    /**
     * @return string
     */
    public function getLocale(): string
    {
        return $this->locale;
    }

    /**
     * @param string $locale
     */
    public function setLocale(string $locale): void
    {
        $this->locale = $locale;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getRoutePath(): string
    {
        return $this->routePath ?? '';
    }

    /**
     * @param string $routePath
     */
    public function setRoutePath(string $routePath): void
    {
        $this->routePath = $routePath;
    }

    /**
     * @return array|null
     */
    public function getSeo(): ?array
    {
        return $this->seo;
    }

    /**
     * @param array|null $seo
     */
    public function setSeo(?array $seo): void
    {
        $this->seo = $seo;
    }
}
