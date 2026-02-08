<?php

namespace App\Domain\Repository;

use App\Domain\Entity\Lesson;

interface LessonRepositoryInterface
{
    public function create(Lesson $entity): int;

    public function findWithTasks(): array;
}
