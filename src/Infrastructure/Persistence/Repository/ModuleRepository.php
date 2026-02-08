<?php

namespace App\Infrastructure\Persistence\Repository;

use App\Domain\Entity\Module;
use App\Domain\Repository\ModuleRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Module>
 * @implements ModuleRepositoryInterface
 */
class ModuleRepository extends ServiceEntityRepository implements ModuleRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Module::class);
    }

    public function create(Module $entity): int
    {
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();

        return $entity->getId();
    }

    public function findWithLessons(): array
    {
        return $this->createQueryBuilder('m')
            ->leftJoin('m.lessons', 'l')
            ->addSelect('l')
            ->getQuery()
            ->getResult();
    }
}
