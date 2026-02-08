<?php

namespace App\Domain\Repository;

use App\Domain\Entity\Module;

interface ModuleRepositoryInterface
{
    public function create(Module $entity): int;

    public function findWithLessons(): array;
}
