<?php

namespace App\Application\Service;

use App\Domain\Entity\Course;
use App\Domain\Entity\Lesson;
use App\Domain\Entity\Module;
use App\Domain\Repository\LessonRepositoryInterface;

class LessonService
{
    public function __construct(
        private readonly LessonRepositoryInterface $entityRepository
    ) {
    }

    public function find(int $id): ?Lesson
    {
        return $this->entityRepository->find($id);
    }

    public function create(
        string $title,
        Course $course,
        ?Module $module
    ): Lesson
    {
        $entity = new Lesson();
        $entity->setTitle($title)
            ->setCourse($course)
            ->setModule($module)
            ->setCreatedAt(new \DateTimeImmutable())
            ->setUpdatedAt(new \DateTimeImmutable());

        $this->entityRepository->create($entity);

        return $entity;
    }
}
