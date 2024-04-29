<?php

namespace Pixel\TownHallBundle\Service;

use Pixel\TownHallBundle\Entity\JobOffer;

class JobOfferService
{
    public function getContractTypeValues(string $locale): array
    {
        return [
            [
                'name' => JobOffer::CONTRACT_CDI,
                'title' => "CDI",
            ],
            [
                'name' => JobOffer::CONTRACT_CDD,
                'title' => "CDD",
            ],
        ];
    }

    public function getContractTypeLabel(int $type): string
    {
        switch ($type) {
            case JobOffer::CONTRACT_CDI: return "CDI";
            case JobOffer::CONTRACT_CDD: return "CDD";
            default: return "";
        }
    }
}
