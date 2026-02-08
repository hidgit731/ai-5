<?php

namespace App\Application\Service;

use App\Domain\Entity\Course;
use App\Domain\Entity\Lesson;
use App\Domain\Entity\Skill;
use App\Domain\Entity\Task;
use App\Domain\Repository\CourseRepositoryInterface;
use App\Domain\Repository\LessonRepositoryInterface;
use App\Domain\Repository\SkillRepositoryInterface;
use App\Domain\Repository\TaskRepositoryInterface;

readonly class SeederService
{
    public function __construct(
        private CourseRepositoryInterface $courseRepository,
        private LessonRepositoryInterface $lessonRepository,
        private TaskRepositoryInterface $taskRepository,
        private SkillRepositoryInterface $skillRepository,
    ) {
    }

    public function createEntitiesFakeChain(): Course
    {
        $course = (new Course())
            ->setTitle(sha1(time()));
        $this->courseRepository->create($course);

        $lesson = (new Lesson())
            ->setTitle(sha1(time()))
            ->setCourse($course);
        $this->lessonRepository->create($lesson);

        // Создаём 2 навыка
        $skill_1 = (new Skill())
            ->setTitle(sha1(time().'1'));
        $this->skillRepository->create($skill_1);
        $skill_2 = (new Skill())
            ->setTitle(sha1(time().'2'));
        $this->skillRepository->create($skill_2);

        // Создаём 2 задания
        $task_1 = (new Task())
            ->setTitle(sha1(time().'1'))
            ->setLesson($lesson);
        $task_2 = (new Task())
            ->setTitle(sha1(time().'2'))
            ->setLesson($lesson);

        // Привязываем оба навыка к обоим заданиям через "многие ко мноим"
        $task_1->addSkill($skill_1, 20);
        $task_1->addSkill($skill_2, 80);
        $task_2->addSkill($skill_1, 40);
        $task_2->addSkill($skill_2, 60);
        $this->taskRepository->create($task_1);
        $this->taskRepository->create($task_2);

        return $course;
    }
}
