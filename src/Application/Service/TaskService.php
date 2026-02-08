<?php

namespace App\Application\Service;

use App\Domain\Entity\Task;
use App\Domain\Repository\TaskRepositoryInterface;

class TaskService
{
    public function __construct(
        private readonly TaskRepositoryInterface $entityRepository
    ) {
    }

    public function findOneById(int $id): ?Task
    {
        return $this->entityRepository->findOneById($id);
    }
}
