<?php

namespace App\Application\Service;

use App\Domain\Entity\Skill;
use App\Domain\Repository\SkillRepositoryInterface;

class SkillService
{
    public function __construct(
        private readonly SkillRepositoryInterface $entityRepository
    ) {
    }

    public function findOneById(int $id): ?Skill
    {
        return $this->entityRepository->findOneById($id);
    }
}
