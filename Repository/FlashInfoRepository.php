<?php

namespace Pixel\TownHallBundle\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Pixel\TownHallBundle\Entity\FlashInfo;
use Sulu\Component\SmartContent\Orm\DataProviderRepositoryInterface;
use Sulu\Component\SmartContent\Orm\DataProviderRepositoryTrait;

class FlashInfoRepository extends EntityRepository implements DataProviderRepositoryInterface
{
    use DataProviderRepositoryTrait;

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct($em, new ClassMetadata(FlashInfo::class));
    }

    public function create(string $locale): FlashInfo
    {
        $flashInfo = new FlashInfo();
        $flashInfo->setLocale($locale);
        return $flashInfo;
    }

    public function save(FlashInfo $flashInfo): void
    {
        $this->getEntityManager()->persist($flashInfo);
        $this->getEntityManager()->flush();
    }

    public function findById(int $id, string $locale): ?FlashInfo
    {
        $flashInfo = $this->find($id);
        if(!$flashInfo){
            return null;
        }
        $flashInfo->setLocale($locale);
        return $flashInfo;
    }

    /**
     * {@inheritdoc}
     */
    public function appendJoins(QueryBuilder $queryBuilder, $alias, $locale)
    {
    }

    public function findByFilters($filters, $page, $pageSize, $limit, $locale, $options = []): array
    {
        return $this->getPublishedFlashInfo($filters, $locale);
    }

    public function getPublishedFlashInfo(array $filters, string $locale): array
    {
        $query = $this->createQueryBuilder('f')
            ->leftJoin("f.translations", "translation")
            ->where('translation.isActive = 1');
        if (isset($filters['sortBy'])) $query->orderBy($filters['sortBy'], $filters['sortMethod']);
        $flashInfos = $query->getQuery()->getResult();
        if (!$flashInfos) {
            return [];
        }
        return $flashInfos;
    }

    protected function appendSortByJoins(QueryBuilder $queryBuilder, string $alias, string $locale): void
    {
        $queryBuilder->innerJoin($alias . '.translations', 'translation', Join::WITH, 'translation.locale = :locale');
        $queryBuilder->setParameter('locale', $locale);
    }
}
