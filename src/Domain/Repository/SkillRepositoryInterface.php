<?php

namespace App\Domain\Repository;

use App\Domain\Entity\Skill;

interface SkillRepositoryInterface
{
    public function getById(int $id): ?Skill;

    public function create(Skill $entity): int;
}
