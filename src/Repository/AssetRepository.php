<?php

namespace App\Repository;

use App\Entity\Asset;
use App\Enum\AssetCategoryTypeEnum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Asset>
 *
 * @method Asset|null find($id, $lockMode = null, $lockVersion = null)
 * @method Asset|null findOneBy(array $criteria, array $orderBy = null)
 * @method Asset[]    findAll()
 * @method Asset[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AssetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Asset::class);
    }

    /**
     * Get assets by filter.
     *
     * @param array $filter
     * @return array
     */
    public function getByFilter(array $filter)
    {
        $query = $this->createQueryBuilder('a');

        // limit
        $results = $filter['filter_results'];
        if ($results) {
            $query
                ->setFirstResult($filter['filter_results'] * ($filter['filter_page'] - 1))
                ->setMaxResults($filter['filter_results']);
        }

        // categories
        $categoryId = (int)$filter['filter_category'];
        if ($categoryId >= 0) {
            $query
                ->setParameter('categoryId', $categoryId)
                ->andWhere('a.category = :categoryId');
        }

        // location
        $locationId = (int)$filter['filter_location'];
        if ($locationId) {
            $query
                ->setParameter('locationId', $locationId)
                ->join('a.location', 'location')
                ->andWhere('location.id = :locationId');
        }

        // name
        $name = $filter['filter_name'];
        if ($name) {
            $query
                ->setParameter('name', '%' . $name . '%')
                ->andWhere('a.name LIKE :name');
        }

        // order
        $query->orderBy('a.' . $filter['filter_order'], $filter['filter_direction']);

        // paginate
        $paginator = new Paginator($query);
        $total = count($paginator);
        $pages = $results ? ceil($total / $results) : 1;

        return [$query->getQuery()->getResult(), $total, $pages];
    }

    public function fetchByIdArray(array $ids)
    {
        $query = $this->createQueryBuilder('a');

        $query
            ->setParameter('ids', $ids)
            ->where('a.id IN (:ids)');

        return $query->getQuery()->getResult();
    }

    public function findAvailableGames()
    {
        return $this->createQueryBuilder('a')
            ->setParameter('category', AssetCategoryTypeEnum::CATEGORY_GAMES)
            ->where('a.game IS NULL')
            ->andWhere('a.category = :category')
            ->getQuery()
            ->getResult();
    }
}
