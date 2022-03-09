<?php

namespace Pixel\TownHallBundle\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Pixel\TownHallBundle\Entity\Procedure;
use Sulu\Component\SmartContent\Orm\DataProviderRepositoryInterface;
use Sulu\Component\SmartContent\Orm\DataProviderRepositoryTrait;

class ProcedureRepository extends EntityRepository implements DataProviderRepositoryInterface
{
    use DataProviderRepositoryTrait;

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct($em, new ClassMetadata(Procedure::class));
    }

    public function create(string $locale): Procedure
    {
        $procedure = new Procedure();
        $procedure->setLocale($locale);
        return $procedure;
    }

    public function save(Procedure $procedure): void
    {
        $this->getEntityManager()->persist($procedure);
        $this->getEntityManager()->flush();
    }

    public function findById(int $id, string $locale): ?Procedure
    {
        $procedure = $this->find($id);
        if (!$procedure) {
            return null;
        }
        $procedure->setLocale($locale);
        return $procedure;
    }

    public function findAllForSitemap(int $page, int $limit): array
    {
        $offset = ($page * $limit) - $limit;
        $criteria = [
            'state' => true,
        ];
        return $this->findBy($criteria, [], $limit, $offset);
    }

    public function countForSitemap()
    {
        $query = $this->createQueryBuilder('p')
            ->select('count(p)');
        return $query->getQuery()->getSingleScalarResult();
    }

    /**
     * {@inheritdoc}
     */
    public function appendJoins(QueryBuilder $queryBuilder, $alias, $locale)
    {
        $queryBuilder->addSelect('category')->leftJoin($alias . '.category', 'category');
        //$queryBuilder->addSelect($alias.'.category');
    }

    public function appendCategoriesRelation(QueryBuilder $queryBuilder, $alias)
    {
        return $alias . '.category';
        //$queryBuilder->addSelect($alias.'.category');
    }

    protected function appendSortByJoins(QueryBuilder $queryBuilder, string $alias, string $locale): void
    {
        $queryBuilder->innerJoin($alias . '.translations', 'translation', Join::WITH, 'translation.locale = :locale');
        $queryBuilder->setParameter('locale', $locale);
    }
}
