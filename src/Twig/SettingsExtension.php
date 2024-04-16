<?php

declare(strict_types=1);

namespace Pixel\TownHallBundle\Twig;

use Doctrine\ORM\EntityManagerInterface;
use Pixel\TownHallBundle\Entity\Setting;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class SettingsExtension extends AbstractExtension
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('townhall_settings', [$this, "townhallSettings"]),
        ];
    }

    public function townhallSettings(): Setting
    {
        return $this->entityManager->getRepository(Setting::class)->find(1);
    }
}
