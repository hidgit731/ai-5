<?php

namespace App\Infrastructure\Persistence\Repository;

use App\Domain\Entity\Lesson;
use App\Domain\Repository\LessonRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Lesson>
 * @implements LessonRepositoryInterface
 */
class LessonRepository extends ServiceEntityRepository implements LessonRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Lesson::class);
    }


    public function create(Lesson $entity): int
    {
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();

        return $entity->getId();
    }

    public function findWithTasks(): array
    {
        return $this->createQueryBuilder('l')
            ->leftJoin('l.tasks', 't')
            ->addSelect('t')
            ->getQuery()
            ->getResult();
    }
}
