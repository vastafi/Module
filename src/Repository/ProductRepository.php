<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     * @param string|null $category
     * @param string|null $name
     * @param int $limit
     * @param int $page
     * @return Product[] Returns an array of Product objects
     */
    public function filter(?string $category, ?string $name, int $limit, int $page): array
    {
        return $this
            ->getFiltrationQuery($category, $name)
            ->setFirstResult($limit * ($page - 1))
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param string|null $category
     * @param string|null $name
     * @param int $limit
     * @return int
     */
    public function countPages(?string $category, ?string $name, int $limit): int
    {
        $amountOfProducts = $this
            ->getFiltrationQuery($category, $name)
            ->select("COUNT(p)")
            ->getQuery()
            ->getSingleScalarResult();

        return ceil($amountOfProducts / $limit);
    }

    /**
     * @param string|null $category
     * @param string|null $name
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function getFiltrationQuery(?string $category, ?string $name): \Doctrine\ORM\QueryBuilder
    {
        $query = $this->createQueryBuilder('p');

        if ($category) {
            $query->andWhere('p.category = :category')
                ->setParameter('category', $category);
        }
        if ($name) {
            $query->andWhere('p.name LIKE :name')
                ->setParameter('name', $name . "%");
        }

        $query->orderBy('p.id', 'ASC');
        return $query;
   }

    public function getCategories(): array
    {
        $categories = $this
            ->createQueryBuilder('product')
            ->select("product.category")
            ->getQuery()
            ->getResult();

        return array_column($categories, 'category');
    }
}
