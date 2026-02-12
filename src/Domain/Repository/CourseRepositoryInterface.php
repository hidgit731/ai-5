<?php

namespace App\Domain\Repository;

use App\Domain\Entity\Course;

interface CourseRepositoryInterface
{
    public function create(Course $entity): int;

    public function searchByTitle(string $query): array;

    public function findWithModules(): array;

    /**
     * @return Course[]
     */
    public function findLatest(int $limit): array;

    public function delete(Course $entity): void;
}
