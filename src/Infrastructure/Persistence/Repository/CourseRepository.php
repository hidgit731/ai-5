<?php

namespace App\Infrastructure\Persistence\Repository;

use App\Domain\Entity\Course;
use App\Domain\Repository\CourseRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Course>
 * @implements CourseRepositoryInterface
 */
class CourseRepository extends ServiceEntityRepository implements CourseRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Course::class);
    }

    public function create(Course $entity): int
    {
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();

        return $entity->getId();
    }

    public function searchByTitle(string $query): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.title LIKE :searchQuery')
            ->setParameter('searchQuery', '%' . $query . '%')
            ->orderBy('c.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findWithModules(): array
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.modules', 'm')
            ->addSelect('m')
            ->getQuery()
            ->getResult();
    }

    public function findLatest(int $limit): array
    {
        return $this->createQueryBuilder('c')
            ->orderBy('c.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function delete(Course $entity): void
    {
        $this->getEntityManager()->remove($entity);
        $this->getEntityManager()->flush();
    }
}
