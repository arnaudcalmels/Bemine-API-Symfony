<?php

namespace App\Repository;

use App\Entity\Mail;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Mail|null find($id, $lockMode = null, $lockVersion = null)
 * @method Mail|null findOneBy(array $criteria, array $orderBy = null)
 * @method Mail[]    findAll()
 * @method Mail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MailRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Mail::class);
    }

    /**
     * @return Mail[] Returns an array of GuestGroup objects
     */
    public function findAllQueryBuilder()
    {
        $qb = $this->createQueryBuilder('m')
            ->select('m.id', 'm.name')
            ->getQuery()
            // ->setHint(\Doctrine\ORM\Query::HINT_INCLUDE_META_COLUMNS, true)
            ;
    
        return $qb->getArrayResult();
    }


    // /**
    //  * @return Mail[] Returns an array of Mail objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Mail
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
