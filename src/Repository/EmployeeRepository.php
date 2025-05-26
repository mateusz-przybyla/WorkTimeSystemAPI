<?php

namespace App\Repository;

use App\Entity\Employee;
use Symfony\Component\Uid\Uuid;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Employee>
 */
class EmployeeRepository extends ServiceEntityRepository
{
  public function __construct(ManagerRegistry $registry)
  {
    parent::__construct($registry, Employee::class);
  }

  public function findOneByUuid(string $uuid): ?Employee
  {
    return $this->findOneBy(['uuid' => $uuid]);
  }

  //    /**
  //     * @return Employee[] Returns an array of Employee objects
  //     */
  //    public function findByExampleField($value): array
  //    {
  //        return $this->createQueryBuilder('e')
  //            ->andWhere('e.exampleField = :val')
  //            ->setParameter('val', $value)
  //            ->orderBy('e.id', 'ASC')
  //            ->setMaxResults(10)
  //            ->getQuery()
  //            ->getResult()
  //        ;
  //    }

  //    public function findOneBySomeField($value): ?Employee
  //    {
  //        return $this->createQueryBuilder('e')
  //            ->andWhere('e.exampleField = :val')
  //            ->setParameter('val', $value)
  //            ->getQuery()
  //            ->getOneOrNullResult()
  //        ;
  //    }
}
