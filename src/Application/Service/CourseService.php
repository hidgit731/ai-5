<?php

namespace App\Application\Service;

use App\Domain\Entity\Course;
use App\Domain\Repository\CourseRepositoryInterface;

class CourseService
{
    public function __construct(
        private readonly CourseRepositoryInterface $entityRepository
    ) {
    }

    public function create(string $title): Course
    {
        $course = new Course();
        $course->setTitle($title)
            ->setCreatedAt(new \DateTimeImmutable())
            ->setUpdatedAt(new \DateTimeImmutable());

        $this->entityRepository->create($course);

        return $course;
    }
}
