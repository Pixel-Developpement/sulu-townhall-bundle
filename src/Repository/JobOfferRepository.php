<?php

namespace Pixel\TownHallBundle\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\QueryBuilder;
use Pixel\TownHallBundle\Entity\JobOffer;
use Sulu\Component\SmartContent\Orm\DataProviderRepositoryInterface;
use Sulu\Component\SmartContent\Orm\DataProviderRepositoryTrait;

class JobOfferRepository extends EntityRepository implements DataProviderRepositoryInterface
{
    use DataProviderRepositoryTrait;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct($entityManager, new ClassMetadata(JobOffer::class));
    }

    public function findById(int $id): ?JobOffer
    {
        $jobOffer = $this->find($id);
        if (! $jobOffer) {
            return null;
        }
        return $jobOffer;
    }

    /**
     * @param string $alias
     * @param string $locale
     * @return void
     */
    public function appendJoins(QueryBuilder $queryBuilder, $alias, $locale)
    {
    }
}
