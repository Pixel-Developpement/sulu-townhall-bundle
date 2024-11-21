<?php

declare(strict_types=1);

namespace Pixel\TownHallBundle\Content\Type;

use Doctrine\ORM\EntityManagerInterface;
use Pixel\TownHallBundle\Entity\Decree;
use Sulu\Bundle\ReferenceBundle\Application\Collector\ReferenceCollectorInterface;
use Sulu\Bundle\ReferenceBundle\Infrastructure\Sulu\ContentType\ReferenceContentTypeInterface;
use Sulu\Component\Content\Compat\PropertyInterface;
use Sulu\Component\Content\SimpleContentType;

class DecreeSelection extends SimpleContentType implements ReferenceContentTypeInterface
{
    protected EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

        parent::__construct('decree_selection', []);
    }

    /**
     * @return Decree[]
     */
    public function getContentData(PropertyInterface $property): array
    {
        $ids = $property->getValue();

        if (empty($ids)) {
            return [];
        }

        $decrees = $this->entityManager->getRepository(Decree::class)->findBy([
            'id' => $ids,
        ]);

        $idPositions = array_flip($ids);
        usort($decrees, function (Decree $a, Decree $b) use ($idPositions) {
            return $idPositions[$a->getId()] - $idPositions[$b->getId()];
        });

        return $decrees;
    }

    /**
     * @return array<string, array<int>|null>
     */
    public function getViewData(PropertyInterface $property): array
    {
        return [
            'ids' => $property->getValue(),
        ];
    }

    public function getReferences(PropertyInterface $property, ReferenceCollectorInterface $referenceCollector, string $propertyPrefix = ''): void
    {
        $data = $property->getValue();
        if (!isset($data) || !is_array($data)) {
            return;
        }

        foreach ($data as $id) {
            $referenceCollector->addReference(
                Decree::RESOURCE_KEY,
                (string) $id,
                $propertyPrefix . $property->getName()
            );
        }
    }
}
