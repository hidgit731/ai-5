<?php

namespace App\Infrastructure\Persistence\Repository;

use App\Domain\Entity\Skill;
use App\Domain\Repository\SkillRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Skill>
 * @implements SkillRepositoryInterface
 */
class SkillRepository extends ServiceEntityRepository implements SkillRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Skill::class);
    }

    public function getById(int $id): ?Skill
    {
        return $this->find($id);
    }

    public function create(Skill $entity): int
    {
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();

        return $entity->getId();
    }
}
