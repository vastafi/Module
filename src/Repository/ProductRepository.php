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
     * @param $category
     * @param $name
     * @param $limit
     * @param $page
     * @return Product[] Returns an array of Product objects
     */

    public function filter($category, $name, $limit, $page)
    {
        $query = $this->createQueryBuilder('p');
        if(!($name) and $category){
            $query = $query->andWhere('LOWER(p.category) = :category')
                ->setParameter('category', strtolower($category));
        }
        else if((!$category) and $name){
            $query = $query->andWhere('LOWER(p.name) LIKE :name')
                ->setParameter('name', strtolower($name)."%");
        }
        else if($category and $name){
            $query =  $query ->
            andWhere('LOWER(p.category) = :category AND LOWER(p.name) LIKE :name')
                ->setParameters(array('category' => strtolower($category), 'name' => strtolower($name).'%'));
        }
        $query = $query->orderBy('p.id', 'ASC')
            ->setFirstResult($limit * ($page - 1))
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
        return $query;
   }

    public function countProducts($name, $category):int {
        $query = $this->createQueryBuilder('p');
        if(!($name) and $category){
            $query = $query->andWhere('LOWER(p.category) = :category')
                ->setParameter('category', strtolower($category));
        }
        else if((!$category) and $name){
            $query = $query->andWhere('LOWER(p.name) LIKE :name')
                ->setParameter('name', strtolower($name)."%");
        }
        else if($category and $name){
            $query =  $query ->
            andWhere('LOWER(p.category) = :category AND LOWER(p.name) LIKE :name')
                ->setParameters(array('category' => strtolower($category), 'name' => strtolower($name).'%'));
        }
        $query->add('select', $query->expr()->count('p'));
        $q = $query->getQuery();
        return $q->getSingleScalarResult();
    }

    // /**
    //  * @return Product[] Returns an array of Product objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Product
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
