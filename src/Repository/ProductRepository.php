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
            $query = $query->andWhere('p.category = :category')
                ->setParameter('category', $category);
        }
        else if((!$category) and $name){
            $query = $query->andWhere('p.name LIKE :name')
                ->setParameter('name', $name."%");
        }
        else if($category and $name){
            $query =  $query ->
            andWhere('p.category = :category AND p.name LIKE :name')
                ->setParameters(array('category' => $category, 'name' => $name));
        }
        $query = $query->orderBy('p.id', 'ASC')
            ->setFirstResult($limit * ($page - 1))
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
        return $query;
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
