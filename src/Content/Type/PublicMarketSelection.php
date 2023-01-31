<?php

declare(strict_types=1);

namespace Pixel\TownHallBundle\Content\Type;

use Doctrine\ORM\EntityManagerInterface;
use Pixel\TownHallBundle\Entity\PublicMarket;
use Sulu\Component\Content\Compat\PropertyInterface;
use Sulu\Component\Content\SimpleContentType;

class PublicMarketSelection extends SimpleContentType
{
    protected EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        parent::__construct('public_market_selection', []);
    }

    public function getContentData(PropertyInterface $property)
    {
        $ids = $property->getValue();

        if (empty($ids)) {
            return [];
        }

        $publicsMarkets = $this->entityManager->getRepository(PublicMarket::class)->findBy([
            'id' => $ids,
        ]);
        $idPositions = array_flip($ids);
        usort($publicsMarkets, function (PublicMarket $a, PublicMarket $b) use ($idPositions) {
            return $idPositions[$a->getId()] - $idPositions[$b->getId()];
        });
        return $publicsMarkets;
    }

    public function getViewData(PropertyInterface $property)
    {
        return [
            'ids' => $property->getValue(),
        ];
    }
}
