<?php

namespace Pixel\TownHallBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Sulu\Bundle\CategoryBundle\Entity\Category;
use Sulu\Bundle\CategoryBundle\Entity\CategoryInterface;
use Sulu\Bundle\MediaBundle\Entity\MediaInterface;

/**
 * @ORM\Entity()
 * @ORM\Table(name="townhall_procedure")
 * @ORM\Entity(repositoryClass="Pixel\TownHallBundle\Repository\ProcedureRepository")
 * @Serializer\ExclusionPolicy("all")
 */
class Procedure
{
    public const RESOURCE_KEY = 'procedures';
    public const FORM_KEY = 'procedure_details';
    public const LIST_KEY = 'procedures';
    public const SECURITY_CONTEXT = 'townhall.procedures';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Serializer\Expose()
     */
    private ?int $id = null;

    /**
     * @ORM\ManyToOne(targetEntity=MediaInterface::class)
     * @ORM\JoinColumn(onDelete="SET NULL")
     * @Serializer\Expose()
     */
    private ?MediaInterface $cover = null;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @Serializer\Expose()
     */
    private ?bool $state;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Serializer\Expose()
     */
    private ?string $externalLink = null;

    /**
     * @ORM\ManyToOne(targetEntity=MediaInterface::class)
     * @ORM\JoinColumn(onDelete="SET NULL")
     * @Serializer\Expose()
     */
    private ?MediaInterface $document = null;

    /**
     * @ORM\ManyToOne(targetEntity=CategoryInterface::class)
     * @ORM\JoinColumn(nullable=false)
     * @Serializer\Expose()
     */
    private Category $category;

    /**
     * @var Collection<string, ProcedureTranslation>
     * @ORM\OneToMany(targetEntity="Pixel\TownHallBundle\Entity\ProcedureTranslation", mappedBy="procedure", cascade={"ALL"}, indexBy="locale")
     * @Serializer\Exclude
     */
    private $translations;

    private string $locale = 'fr';

    public function __construct()
    {
        $this->state = true;
        $this->translations = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @Serializer\VirtualProperty(name="title")
     * @return string
     */
    public function getTitle(): ?string
    {
        $translation = $this->getTranslation($this->locale);
        if (!$translation) {
            return null;
        }
        return $translation->getTitle();
    }

    protected function getTranslation(string $locale = 'fr'): ?ProcedureTranslation
    {
        if (!$this->translations->containsKey($locale)) {
            return null;
        }
        return $this->translations->get($locale);
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): self
    {
        $translation = $this->getTranslation($this->locale);
        if (!$translation) {
            $translation = $this->createTranslation($this->locale);
        }
        $translation->setTitle($title);
        return $this;
    }

    protected function createTranslation(string $locale): ProcedureTranslation
    {
        $translation = new ProcedureTranslation($this, $locale);
        $this->translations->set($locale, $translation);
        return $translation;
    }

    /**
     * @Serializer\VirtualProperty(name="description")
     * @return string|null
     */
    public function getDescription(): ?string
    {
        $translation = $this->getTranslation($this->locale);
        if (!$translation) {
            return null;
        }
        return $translation->getDescription();
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): self
    {
        $translation = $this->getTranslation($this->locale);
        if (!$translation) {
            $translation = $this->createTranslation($this->locale);
        }
        $translation->setDescription($description);
        return $this;
    }

    /**
     * @Serializer\VirtualProperty(name="route")
     * @return string|null
     */
    public function getRoutePath(): ?string
    {
        $translation = $this->getTranslation($this->locale);
        if (!$translation) {
            return null;
        }
        return $translation->getRoutePath();
    }

    public function setRoutePath(string $routePath): self
    {
        $translation = $this->getTranslation($this->locale);
        if (!$translation) {
            $translation = $this->createTranslation($this->locale);
        }
        $translation->setRoutePath($routePath);
        return $this;
    }

    /**
     * @Serializer\VirtualProperty(name="seo")
     * @return array|null
     */
    public function getSeo(): ?array
    {
        $translation = $this->getTranslation($this->locale);
        if (!$translation) {
            return null;
        }
        return $translation->getSeo();
    }

    protected function emptySeo(): array
    {
        return [
            "seo" => [
                "title" => "",
                "description" => "",
                "keywords" => "",
                "canonicalUrl" => "",
                "noIndex" => "",
                "noFollow" => "",
                "hideinSitemap" => ""
            ]
        ];
    }

    /**
     * @Serializer\VirtualProperty(name="ext")
     * @return array|null
     */
    public function getExt(): ?array
    {
        $translation = $this->getTranslation($this->locale);
        if(!$translation){
            return null;
        }
        return ($translation->getSeo()) ? ['seo' => $translation->getSeo()] : $this->emptySeo();
    }

    /**
     * @param array|null $seo
     * @return self
     */
    public function setSeo(?array $seo): self
    {
        $translation = $this->getTranslation($this->locale);
        if (!$translation) {
            $translation = $this->createTranslation($this->locale);
        }
        $translation->setSeo($seo);
        return $this;
    }

    /**
     * @return MediaInterface|null
     */
    public function getCover(): ?MediaInterface
    {
        return $this->cover;
    }

    /**
     * @param MediaInterface|null $cover
     */
    public function setCover(?MediaInterface $cover): void
    {
        $this->cover = $cover;
    }

    /**
     * @return bool|null
     */
    public function getState(): ?bool
    {
        return $this->state;
    }

    /**
     * @param bool|null $state
     */
    public function setState(?bool $state): void
    {
        $this->state = $state;
    }

    /**
     * @return string|null
     */
    public function getExternalLink(): ?string
    {
        return $this->externalLink;
    }

    /**
     * @param string|null $externalLink
     */
    public function setExternalLink(?string $externalLink): void
    {
        $this->externalLink = $externalLink;
    }

    /**
     * @return MediaInterface|null
     */
    public function getDocument(): ?MediaInterface
    {
        return $this->document;
    }

    /**
     * @param MediaInterface|null $document
     */
    public function setDocument(?MediaInterface $document): void
    {
        $this->document = $document;
    }

    /**
     * @return Category|null
     */
    public function getCategory(): Category
    {
        return $this->category;
    }

    /**
     * @param Category|null $category
     */
    public function setCategory(CategoryInterface $category): void
    {
        $this->category = $category;
    }

    public function getTranslations(): array
    {
        return $this->translations->toArray();
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function setLocale(string $locale): self
    {
        $this->locale = $locale;
        return $this;
    }
}
