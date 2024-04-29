<?php

declare(strict_types=1);

namespace Pixel\TownHallBundle\Twig;

use Pixel\TownHallBundle\Service\JobOfferService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class JobOfferExtension extends AbstractExtension
{
    private JobOfferService $jobOfferService;

    public function __construct(JobOfferService $jobOfferService)
    {
        $this->jobOfferService = $jobOfferService;
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('contract_type_label', [$this, "getContractTypeLabel"]),
        ];
    }

    public function getContractTypeLabel(int $type): string
    {
        return $this->jobOfferService->getContractTypeLabel($type);
    }
}
