<?php

namespace App\Application\Service;

use App\Domain\Entity\Course;
use App\Domain\Entity\Module;
use App\Domain\Repository\ModuleRepositoryInterface;

class ModuleService
{
    public function __construct(
        private readonly ModuleRepositoryInterface $entityRepository
    ) {
    }

    public function create(string $title, Course $course): Module
    {
        $entity = new Module();
        $entity->setTitle($title)
            ->setCourse($course)
            ->setCreatedAt(new \DateTimeImmutable())
            ->setUpdatedAt(new \DateTimeImmutable());

        $this->entityRepository->create($entity);

        return $entity;
    }
}
