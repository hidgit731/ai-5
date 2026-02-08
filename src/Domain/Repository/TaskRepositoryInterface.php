<?php

namespace App\Domain\Repository;

use App\Domain\Entity\Task;

interface TaskRepositoryInterface
{
    public function getById(int $id): ?Task;

    public function create(Task $entity): int;
}
