<?php

namespace App\Domain\Entity;

use App\Infrastructure\Persistence\Repository\TaskSkillRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TaskSkillRepository::class)]
#[ORM\UniqueConstraint(name: 'tasks_skills_unique', columns: ['task_id', 'skill_id'])]
#[ORM\Index('tasks_skills_task_id_idx', ['task_id'])]
#[ORM\Index('tasks_skills_skill_id_idx', ['skill_id'])]
class TaskSkill
{
    public function __construct(
        #[ORM\Id]
        #[ORM\ManyToOne(targetEntity: Task::class, inversedBy: 'taskSkills')]
        #[ORM\JoinColumn(nullable: false)]
        private Task $task,
        #[ORM\Id]
        #[ORM\ManyToOne(targetEntity: Skill::class, inversedBy: 'skillTasks')]
        #[ORM\JoinColumn(nullable: false)]
        private Skill $skill,
        #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2)]
        private float $weight
    ) {
    }

    public function getTask(): ?Task
    {
        return $this->task;
    }

    public function setTask(?Task $task): static
    {
        $this->task = $task;

        return $this;
    }

    public function getSkill(): ?Skill
    {
        return $this->skill;
    }

    public function setSkill(?Skill $skill): static
    {
        $this->skill = $skill;

        return $this;
    }

    public function getWeight(): float
    {
        return $this->weight;
    }

    public function setWeight(float $weight): static
    {
        $this->weight = $weight;

        return $this;
    }
}
