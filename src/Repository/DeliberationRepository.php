<?php

namespace Pixel\TownHallBundle\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\QueryBuilder;
use Pixel\TownHallBundle\Entity\Deliberation;
use Sulu\Component\SmartContent\Orm\DataProviderRepositoryInterface;
use Sulu\Component\SmartContent\Orm\DataProviderRepositoryTrait;

class DeliberationRepository extends EntityRepository implements DataProviderRepositoryInterface
{
    use DataProviderRepositoryTrait;

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct($em, new ClassMetadata(Deliberation::class));
    }

    /**
     * @param string $alias
     * @param string $locale
     */
    public function appendJoins(QueryBuilder $queryBuilder, $alias, $locale): void
    {
    }
}
